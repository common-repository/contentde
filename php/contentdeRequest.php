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

class contentdeRequest
{
	/**
	 * @var bool
	 */
	private $bIsAjax = false;

	/**
	 * @var string
	 */
	private $sMethod = array();

	/**
	 * @var array
	 */
	private $aParams = array();

	/**
	 * @var string
	 */
	private $sPage = '';

	/**
	 * @return void
	 */
	public function __construct()
	{
		$this->bIsAjax = basename($_SERVER['SCRIPT_NAME']) == 'admin-ajax.php';

		$this->setMethod(
			isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get'
		);

		$this->aParams = array_merge($_GET, $_POST);

		$this->setPage(strtolower(
			preg_replace(
				'/^contentde-/',
				'',
				$this->getParam(
					$this->isAjax() ? 'action' : 'page',
					'contentde-main'
				)
			)
		));
	}

	/**
	 * @param string $sPage
	 * @return contentdeRequest
	 */
	public function setPage($sPage)
	{
		$this->sPage = (string) $sPage;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPage()
	{
		return $this->sPage;
	}

	/**
	 * @param string $sMethod
	 * @return bool
	 */
	public function isMethod($sMethod)
	{
		return $this->sMethod == strtolower($sMethod);
	}

	/**
	 * @param string $sMethod
	 * @return contentdeRequest
	 */
	public function setMethod($sMethod)
	{
		$this->sMethod = strtolower($sMethod);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getMethod()
	{
		return $this->sMethod;
	}

	/**
	 * @param string $sParam
	 * @return bool
	 */
	public function hasParam($sParam)
	{
		return isset($this->aParams[$sParam]);
	}

	/**
	 * @param string $sParam
	 * @param mixed $mDefault
	 * @return mixed
	 */
	public function getParam($sParam, $mDefault = null)
	{
		return $this->hasParam($sParam) ? $this->aParams[$sParam] : $mDefault;
	}

	/**
	 * @param string $sParam
	 * @param mixed $mValue
	 * @return contentdeRequest
	 */
	public function setParam($sParam, $mValue)
	{
		$this->aParams[$sParam] = $mValue;

		return $this;
	}

	/**
	 * @param array $aParams
	 * @return ops_context
	 */
	public function setParams(array $aParams)
	{
		foreach($aParams as $sParam => $mValue)
		{
			$this->setParam($sParam, $mValue);
		}

		return $this;
	}

	/**
	 * @return contentdeRequest
	 */
	public function clearParams()
	{
		$this->aParams = array();

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAjax()
	{
		return $this->bIsAjax;
	}
}

?>