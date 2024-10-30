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

class contentdeApi
{
	/**
	 * @var string
	 */
	const RPC_MODULE_SOAP = 'soap';

	/**
	 * @var string
	 */
	const RPC_MODULE_XMLRPC = 'xmlrpc';

	/**
	 * @var array
	 */
	static private $aModules = array(
		self::RPC_MODULE_XMLRPC => 'contentdeRpcModuleXmlrpc',	
		self::RPC_MODULE_SOAP => 'contentdeRpcModuleSoap'
		
	);

	/**
	 * @var contentdeRpcModule
	 */
	private $oRpcModule = null;

	/**
	 * @var string
	 */
	private $sSessionHash = '';

	/**
	 * @var bool
	 */
	private $bLoggedIn = false;

	/**
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * @return void
	 */
	static public function testConnection()
	{
		$oApi = new self;

		$sExpectedValue = 'test';
		$sRealValue = $oApi->test();

		if($sRealValue != $sExpectedValue)
		{
			throw new Exception(sprintf(
				'test function did not return correct value; expected="%s"; got="%s"',
				$sExpectedValue,
				$sRealValue
			));
		}
	}

	/**
	 * @return void
	 */
	private function determineRpcModule()
	{
		if(!($this->oRpcModule instanceof contentdeRpcModule))
		{
			$aErrors = array();

			foreach(self::$aModules as $sModule => $sClass)
			{
				if(extension_loaded($sModule))
				{
					try
					{
						$this->oRpcModule = new $sClass;

						if($this->oRpcModule instanceof contentdeRpcModule)
						{
							return;
						}
					}
					catch(Exception $oError)
					{
						$aErrors[] = $oError->getMessage();
					}
				}
				else
				{
					$aErrors[] = sprintf('php module "%s" is not installed or activated', $sModule);
				}
			}

			$sErrorAddition = count($aErrors) > 0 ? '; ' . implode('; ', $aErrors) : '';

			throw new LogicException('there is no possible rpc module' . $sErrorAddition);
		}
	}

	/**
	 * @return contentdeRpcModule
	 */
	private function getRpcModule()
	{
		$this->determineRpcModule();

		return $this->oRpcModule;
	}

	/**
	 * @return bool
	 */
	public function login($sLogin, $sPassword)
	{
		if(!$this->isLoggedIn())
		{
			$aResult = $this->getRpcModule()->doRequest(
				'l' . 'ogin_result',
				$sLogin,
				$sPassword
			);

			$this->sSessionHash = $aResult['Records']['hash'];
			$this->bLoggedIn = true;
		}
	}

	/**
	 * @return bool
	 */
	public function isLoggedIn()
	{
		return $this->bLoggedIn;
	}

	/**
	 * @return bool
	 */
	public function logout()
	{
		try
		{
			if($this->isLoggedIn())
			{
				$this->getRpcModule()->doRequest(
					'l' . 'ogout',
					$this->sSessionHash
				);

				return true;
			}
		}
		catch(Exception $oError)
		{
		}

		return false;
	}

	/**
	 * @return void
	 */
	public function setSessionHash($sSessionHash)
	{
		$this->sSessionHash = $sSessionHash;

		$this->bLoggedIn = true;
	}

	/**
	 * @return string
	 */
	public function getSessionHash()
	{
		return $this->sSessionHash;
	}

	/**
	 * @param string $sOrderId
	 * @return array
	 */
	public function retrieveOrder($sOrderId)
	{
		$aOrderData = $this->getRpcModule()->doRequest(
			'g' . 'etOrderById',
			$this->sSessionHash,
			$sOrderId
		);

		return $aOrderData['Records'];
	}

	/**
	 * @param string $sTitle
	 * @param array $aKeywords
	 * @param string $sDesciption
	 * @param string $sProject
	 * @param int $iCategory
	 * @param int $iDuration
	 * @param int $iLevel
	 * @param int $iMin
	 * @param int $iMax
	 * @param int $iMinDensity
	 * @param int $iMaxDensity
	 * @param string $sExternalId
	 * @return string
	 */
	public function createOpenOrder(
		$sTitle,
		$aKeywords,
		$sDesciption,
		$sProject,
		$iCategory,
		$iDuration = 3,
		$iLevel = 4,
		$iMin = 200,
		$iMax = 500,
		$iMinDensity = 2,
		$iMaxDensity = 5,
		$sExternalId = ''
	)
	{
		$aResult = $this->getRpcModule()->doRequest(
			'c' . 'reateOpenOrder',
			$this->sSessionHash,
			$sTitle,
			$aKeywords,
			$sDesciption,
			$sProject,
			$iCategory,
			$iDuration,
			$iLevel,
			$iMin,
			$iMax,
			$iMinDensity,
			$iMaxDensity,
			$sExternalId
		);

		return $aResult['Records'];
	}

	/**
	 * @param string $sTitle
	 * @param array $aKeywords
	 * @param string $sDesciption
	 * @param string $sProject
	 * @param int $iCategory
	 * @param int $iGroup
	 * @param int $iDuration
	 * @param int $iLevel
	 * @param int $iMin
	 * @param int $iMax
	 * @param int $iMinDensity
	 * @param int $iMaxDensity
	 * @param string $sExternalId
	 * @return string
	 */
	public function createGroupOrder(
		$sTitle,
		$aKeywords,
		$sDesciption,
		$sProject,
		$iCategory,
		$iGroup,
		$iDuration = 3,
		$iLevel = 10,
		$iMin = 200,
		$iMax = 500,
		$iMinDensity = 1,
		$iMaxDensity = 3,
		$sExternalId = ''
	)
	{
		$aResult = $this->getRpcModule()->doRequest(
			'c' . 'reateGroupOrder',
			$this->sSessionHash,
			$sTitle,
			$aKeywords,
			$sDesciption,
			$sProject,
			$iCategory,
			$iGroup,
			$iDuration,
			$iLevel,
			$iMin,
			$iMax,
			$iMinDensity,
			$iMaxDensity,
			$sExternalId
		);

		return $aResult['Records'];
	}

	/**
	 * @param string $sTitle
	 * @param array $aKeywords
	 * @param string $sDesciption
	 * @param string $sProject
	 * @param string $sContractor
	 * @param int $iCategory
	 * @param int $iDuration
	 * @param int $iMin
	 * @param int $iMax
	 * @param int $iMinDensity
	 * @param int $iMaxDensity
	 * @param string $sExternalId
	 * @return string
	 */
	public function createDirectOrder(
		$sTitle,
		$aKeywords,
		$sDesciption,
		$sProject,
		$sContractor,
		$iCategory,
		$iDuration = 3,
		$iMin = 200,
		$iMax = 500,
		$iMinDensity = 1,
		$iMaxDensity = 3,
		$sExternalId = ''
	)
	{
		$aResult = $this->getRpcModule()->doRequest(
			'c' . 'reateDirectOrder',
			$this->sSessionHash,
			$sTitle,
			$aKeywords,
			$sDesciption,
			$sProject,
			$sContractor,
			$iCategory,
			$iDuration,
			$iMin,
			$iMax,
			$iMinDensity,
			$iMaxDensity,
			$sExternalId
		);

		return $aResult['Records'];
	}

	/**
	 * @param string $sOrderId
	 * @param string $sReview
	 * @param int $iContentRating
	 * @param int $iFormRating
	 * @param int $iReadabilityRating
	 * @param int $iCommunicationRating
	 * @return bool
	 */
	public function acceptOrder(
		$sOrderId,
		$sReview,
		$iContentRating,
		$iFormRating,
		$iReadabilityRating,
		$iCommunicationRating
	)
	{
		$this->getRpcModule()->doRequest(
			'r' . 'ateAndAcceptOrder',
			$this->sSessionHash,
			$sOrderId,
			$sReview,
			(int) $iContentRating,
			(int) $iFormRating,
			(int) $iReadabilityRating,
			(int) $iCommunicationRating
		);
	}

	/**
	 * @param string $sOrderId
	 * @param string $sReview
	 * @return void
	 */
	public function reviseOrder($sOrderId, $sReview)
	{
		$this->getRpcModule()->doRequest(
			'r' . 'equestRevision',
			$this->sSessionHash,
			$sOrderId,
			$sReview
		);
	}

	/**
	 * @param string $sOrderId
	 * @param string $sReview
	 * @return bool
	 */
	public function rejectOrder($sOrderId, $sReview)
	{
		$this->getRpcModule()->doRequest(
			'r' . 'equestRejection',
			$this->sSessionHash,
			$sOrderId,
			$sReview
		);
	}

	/**
	 * @param string $sOrderId
	 * @param bool $bArchive
	 * @return bool
	 */
	public function archivOrder($sOrderId, $bArchive)
	{
		$aResult = $this->getRpcModule()->doRequest(
			'a' . 'rchiveOrder',
			$this->sSessionHash,
			$sOrderId,
			(int) $bArchive
		);

		return $aResult['Records'];
	}

	/**
	 * @return array
	 */
	public function getOrders($sProject = 'ALL', $sStatus = 'ALL', $bArchived = 0)
	{
		$aResult = $this->getRpcModule()->doRequest(
			'g' . 'etOrdersByFilter',
			$this->sSessionHash,
			array(
				'project' => $sProject,
				'status' => $sStatus,
				'archived' => $bArchived != 'ALL' ? (int) $bArchived : $bArchived
			)
		);

		return $aResult['Records'];
	}

	/**
	 * @param string $sOrder
	 */
	public function getOrder($sOrder)
	{
		$aResult = $this->getRpcModule()->doRequest(
			'g' . 'etOrderById',
			$this->sSessionHash,
			$sOrder
		);

		return $aResult['Records'];
	}

	/**
	 * @param string $sType
	 * @param string $sLevel
	 * @param int $iWordCount
	 * @param string $sProjectId
	 * @return array
	 */
	public function calculateOrderPrice($sType, $sLevel, $iWordCount, $sProjectId = "")
	{
		$aResult = $this->getRpcModule()->doRequest(
			'c' . 'alculateOrderPrice',
			$this->sSessionHash,
			$sType,
			$sLevel,
			$iWordCount,
			$sProjectId
		);

		return $aResult['Records'];
	}

	/**
	 * @param string $sOrder
	 * @return array
	 */
	public function getOrderMessages($sOrder)
	{
		$aResult = $this->getRpcModule()->doRequest(
			'g' . 'etOrderMessages',
			$this->sSessionHash,
			$sOrder
		);

		return $aResult['Records'];
	}

	/**
	 * @param string $sOrder
	 * @param string $sMessage
	 * @return bool
	 */
	public function writeOrderMessage($sOrder, $sMessage)
	{
		$aResult = $this->getRpcModule()->doRequest(
			'w' . 'riteOrderMessage',
			$this->sSessionHash,
			$sOrder,
			$sMessage
		);

		return (bool) $aResult['Records'];
	}

	/**
	 * @return float
	 */
	public function getBalance()
	{
		$aBalance = $this->getRpcModule()->doRequest(
			'g' . 'etBalance',
			$this->sSessionHash
		);

		return $aBalance['Records'];
	}

	/**
	 * @return array
	 */
	public function getCategories()
	{
		$aResult = $this->getRpcModule()->doRequest(
			'g' . 'etCategories',
			$this->sSessionHash
		);

		return $aResult['Records'];
	}

	/**
	 * @return array
	 */
	public function getProjects()
	{
		$aResult = $this->getRpcModule()->doRequest(
			'g' . 'etProjects',
			$this->sSessionHash
		);

		return $aResult['Records'];
	}

	/**
	 * @param string $sProject
	 * @return array
	 */
	public function getProjectSettings($sProject)
	{
		$aResult = $this->getRpcModule()->doRequest(
			'getProjectSettings',
			$this->sSessionHash,
			$sProject
		);

		return $aResult['Records'];
	}

	/**
	 * @return array
	 */
	public function getLevels()
	{
		$aResult = $this->getRpcModule()->doRequest(
			'g' . 'etLevels',
			$this->sSessionHash
		);

		return $aResult['Records'];
	}

	/**
	 * @return array
	 */
	public function getGroups()
	{
		$aResult = $this->getRpcModule()->doRequest(
			'g' . 'etGroups',
			$this->sSessionHash
		);

		$aGroups = array();

		foreach($aResult['Records'] as $aGroup)
		{
			$aGroups[$aGroup['id']] = $aGroup['name'];
		}

		return $aGroups;
	}

	/**
	 * @return array
	 */
	public function getTemplates()
	{
		$aResult = $this->getRpcModule()->doRequest(
			'g' . 'etTemplates',
			$this->sSessionHash
		);

		$aTemplates = array();

		foreach($aResult['Records'] as &$aTemplate)
		{
			$aTemplates[$aTemplate['id']] = &$aTemplate;

			unset($aTemplate['id']);
		}

		return $aTemplates;
	}

	/**
	 * @return array
	 */
	public function getContractors()
	{
		$aResult = $this->getRpcModule()->doRequest(
			'g' . 'etContractors',
			$this->sSessionHash
		);

		return $aResult['Records'];
	}

	/**
	 * @return array
	 */
	public function getLoginInfo()
	{
		$aResult = $this->getRpcModule()->doRequest(
			'g' . 'etLoginInfo',
			$this->sSessionHash
		);

		return $aResult['Records'];
	}

	/**
	 * @return string
	 */
	public function test()
	{
		$aResult = $this->getRpcModule()->doRequest('t' . 'est');

		return $aResult['Records'];
	}
}

?>