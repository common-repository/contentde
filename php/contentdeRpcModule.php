<?php
/**
 * Copyright 2012  content.de AG  (email: info[YEAR]@content.de (eg: info2012@content.de))
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

abstract class contentdeRpcModule
{
	/**
	 * @param string $sMethod
	 * @param mixed ...
	 * @return mixed
	 */
	abstract public function doRequest($sMethod);
}

class contentdeRpcModuleSoap extends contentdeRpcModule
{
	/**
	 * @param SoapClient
	 */
	private $oSoapClient = null;

	/**
	 * @return void
	 */
	public function __construct()
	{
		$this->oSoapClient = new SoapClient(
			CONTENTDE_URL_WSDL,
			array('cache_wsdl' => WSDL_CACHE_NONE)
		);
	}

	/**
	 * @param string $sMethod
	 * @param mixed ...
	 * @return mixed
	 */
	public function doRequest($sMethod)
	{
		if(func_num_args() < 1)
		{
			throw new InvalidArgumentException('not enough arguments');
		}

		$aArguments = func_get_args();

		$sMethod = array_shift($aArguments);

		try
		{
			$mResult = call_user_func_array(array($this->oSoapClient, $sMethod), $aArguments);

			if(is_object($mResult) && isset($mResult->HasError) && $mResult->HasError == true)
			{
				throw new Exception($mResult->ErrorMsg);
			}

			if(is_object($mResult))
			{
				$mResult = $this->normalizeResult($mResult);
			}

			return $mResult;
		}
		catch(SoapFault $oSoapError)
		{
			throw new Exception($oSoapError->getMessage());
		}
	}

	/**
	 * @param stdClass $oResult
	 * @return array
	 */
	private function normalizeResult($oResult)
	{
		 return array(
		 	'HasError' => $oResult->HasError,
		 	'ErrorMsg' => $oResult->ErrorMsg,
		 	'Records' => $oResult->Records,
		 	'NumRecords' => $oResult->NumRecords
		 );
	}
}

class contentdeRpcModuleXmlrpc extends contentdeRpcModule
{
	/**
	 * @return void
	 */
	public function __construct()
	{
		if(!extension_loaded('curl'))
		{
			throw new Exception('the xmlrpc module requires cUrl to work properly');
		}
	}

	/**
	 * @param string $sMethod
	 * @param mixed ...
	 * @return mixed
	 */
	public function doRequest($sMethod)
	{
		if(func_num_args() < 1)
		{
			throw new InvalidArgumentException('not enough arguments');
		}

		$aArguments = func_get_args();

		$sMethod = $this->getRealMethod(array_shift($aArguments));

		return $this->executeRequest($sMethod, $aArguments);
	}

	/**
	 * @param string $sMethod
	 * @return string
	 */
	private function getRealMethod($sMethod)
	{
		if(strpos($sMethod, 'content.') === false)
		{
			$sMethod = 'content.' . $sMethod;
		}

		return $sMethod;
	}

	/**
	 * @param string $sFunction
	 * @param array $aArguments
	 * @return array
	 */
	private function executeRequest($sMethod, array $aArguments = array())
	{
		$aArguments = $this->encode($aArguments);

		$sRpcRequest = xmlrpc_encode_request($sMethod, $aArguments);

		$rCurl = curl_init(CONTENTDE_URL_XMLRPC);

		$aHeaders = array(
			'content-type: text/xml'
		);

		curl_setopt_array($rCurl, array(
			CURLOPT_POSTFIELDS => $sRpcRequest,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 50,
			CURLOPT_HTTPHEADER => $aHeaders,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false
		));

		$sResponse = curl_exec($rCurl);
		$aInfo = curl_getinfo($rCurl);

		if($aInfo['http_code'] != 200)
		{
			throw new Exception(sprintf('cUrl request failed: %s', print_r($aInfo, true)));
		}

		$aResponse = xmlrpc_decode($sResponse);

		if(is_array($aResponse) && isset($aResponse['faultCode']) && isset($aResponse['faultString']))
		{
			throw new Exception(sprintf('%s %s', $aResponse['faultString'], print_r($aInfo, true)), (int) $aResponse['faultCode']);
		}

		$aResponse = $this->decode($aResponse);

		return $aResponse;
	}

	/**
	 * @param array $aData
	 * @return array
	 */
	private function decode($mData)
	{
		if(is_array($mData))
		{
			foreach($mData as $mIndex => &$mSubData)
			{
				$mSubData = $this->decode($mSubData);
			}

			return $mData;
		}

		if(is_string($mData))
		{
			$mData = utf8_encode($mData);
			$mData = html_entity_decode($mData, ENT_QUOTES, 'utf-8');
		}

		return $mData;
	}

	/**
	 * @param mixed $mData
	 * @return mixed
	 */
	private function encode($mData)
	{
		if(is_array($mData))
		{
			foreach($mData as $mIndex => $mSubData)
			{
				$mData[$mIndex] = $this->encode($mSubData);
			}
		}
		elseif(is_string($mData))
		{
			$mData = htmlentities(utf8_decode($mData), ENT_QUOTES);
		}

		return $mData;
	}
}

?>