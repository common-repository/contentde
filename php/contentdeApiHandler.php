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

class contentdeApiHandler
{
	/**
	 * @var string
	 */
	private $sLogin = '';

	/**
	 * @var string
	 */
	private $sPassword = '';

	/**
	 * @var contentdeApi
	 */
	private $oApi = null;

	/**
	 * @return void
	 */
	public function __construct()
	{
		$this->oApi = new contentdeApi;
	}

	/**
	 * @return void
	 */
	public function __destruct()
	{
		if($this->oApi->isLoggedIn())
		{
			$this->oApi->logout();
		}
	}

	/**
	 * @param string $sLogin
	 * @param string $sPassword
	 * @return void
	 */
	public function setLoginData($sLogin, $sPassword)
	{
		$this->sLogin = $sLogin;
		$this->sPassword = $sPassword;
	}

	/**
	 * @return void
	 */
	private function checkLogin()
	{
		if(!$this->oApi->isLoggedIn())
		{
			$this->oApi->login($this->sLogin, $this->sPassword);
		}
	}

	/**
	 * @param string $sLogin
	 * @param string $sPassword
	 * @return bool
	 */
	static public function testLogin($sLogin, $sPassword)
	{
		$oApi = new contentdeApi;

		try
		{
			$oApi->login($sLogin, $sPassword);
			$oApi->logout();

			$bLoginResult = true;
		}
		catch(Exception $oError)
		{
			$bLoginResult = false;
		}

		return $bLoginResult;
	}

	/**
	 * @return array
	 */
	public function getBalance()
	{
		$this->checkLogin();

		return $this->oApi->getBalance();
	}

	/**
	 * @return array
	 */
	public function getCategories()
	{
		$this->checkLogin();

		$aCategories = array();

		foreach($this->oApi->getCategories() as $aCategory)
		{
			$aCategories[$aCategory['id']] = $aCategory['titel'];
		}

		return $aCategories;
	}

	/**
	 * @return array
	 */
	public function getLevels()
	{
		$this->checkLogin();

		return $this->oApi->getLevels();
	}

	/**
	 * @return array
	 */
	public function getLevelsSimple()
	{
		$aLevels = array();

		foreach($this->getLevels() as $aLevel)
		{
			$aLevels[$aLevel['id']] = $aLevel['title'];
		}

		return $aLevels;
	}

	/**
	 * @return array
	 */
	public function getGroups()
	{
		$this->checkLogin();

		return $this->oApi->getGroups();
	}

	/**
	 * @return array
	 */
	public function getContractors()
	{
		$this->checkLogin();

		return $this->oApi->getContractors();
	}

	public function getContractorsSimple()
	{
		$aContractors = array();

		foreach($this->getContractors() as $aContractor)
		{
			$aContractors[$aContractor['id']] = $aContractor['name'];
		}

		return $aContractors;
	}

	/**
	 * @return array
	 */
	public function getProjects()
	{
		$this->checkLogin();

		return $this->oApi->getProjects();
	}

	/**
	 * @return array
	 */
	public function getProjectsSimple()
	{
		$aProjects = array();

		foreach($this->getProjects() as $aProject)
		{
			$aProjects[$aProject['id']] = $aProject['titel'];
		}

		return $aProjects;
	}

	/**
	 * @param string $sProject
	 * @return array
	 */
	public function getProjectSettings($sProject)
	{
		$this->checkLogin();

		return $this->oApi->getProjectSettings($sProject);
	}

	/**
	 * @return array
	 */
	public function getTemplates()
	{
		$this->checkLogin();

		return $this->oApi->getTemplates();
	}

	/**
	 * @return array
	 */
	public function getOrderList($sProject = 'ALL', $sStatus = 'ALL', $sArchive = '0')
	{
		$this->checkLogin();

		return $this->oApi->getOrders($sProject, $sStatus, $sArchive);
	}

	/**
	 * @param string $sOrder
	 * @return array
	 */
	public function getOrder($sOrder)
	{
		$this->checkLogin();

		return $this->oApi->getOrder($sOrder);
	}

	/**
	 * @param string $sType
	 * @param string $sLevel
	 * @param int $iWordCount
	 * @return array
	 */
	public function calculateOrderPrice($sType, $sLevel, $iWordCount, $sProject)
	{
		$this->checkLogin();

		return $this->oApi->calculateOrderPrice($sType, $sLevel, $iWordCount, $sProject);
	}

	/**
	 * @param string $sOrder
	 * @return array
	 */
	public function getOrderMessages($sOrder)
	{
		$this->checkLogin();

		return $this->oApi->getOrderMessages($sOrder);
	}

	/**
	 * @param string $sOrder
	 * @param string $sMessage
	 * @return bool
	 */
	public function writeOrderMessage($sOrder, $sMessage)
	{
		$this->checkLogin();

		return $this->oApi->writeOrderMessage($sOrder, $sMessage);
	}

	/**
	 * @param array $aOrderData
	 * @return void
	 */
	public function createOrder(array $aOrderData)
	{
		$this->checkLogin();

		$sExternalId = 'wpp_' . contentdeHelper::getValue(
			$_SERVER,
			'HTTP_HOST',
			contentdeHelper::getValue(
				$_SERVER,
				'SERVER_NAME',
				contentdeHelper::getValue(
					$_SERVER,
					'SERVER_ADDR',
					''
				)
			)
		);

		switch(contentdeHelper::getValue($aOrderData, 'type'))
		{
			case 'oo':

				$this->oApi->createOpenOrder(
					contentdeHelper::getValue($aOrderData, 'title'),
					contentdeHelper::getValue($aOrderData, 'keywords', array()),
					contentdeHelper::getValue($aOrderData, 'description'),
					contentdeHelper::getValue($aOrderData, 'project'),
					contentdeHelper::getValue($aOrderData, 'category'),
					contentdeHelper::getValue($aOrderData, 'duration'),
					contentdeHelper::getValue($aOrderData, 'oo_level'),
					contentdeHelper::getValue($aOrderData, 'min_words'),
					contentdeHelper::getValue($aOrderData, 'max_words'),
					contentdeHelper::getValue($aOrderData, 'min_keyword_density', 0.1),
					contentdeHelper::getValue($aOrderData, 'max_keyword_density', 1.0),
					$sExternalId
				);

				break;

			case 'go':

				$this->oApi->createGroupOrder(
					contentdeHelper::getValue($aOrderData, 'title'),
					contentdeHelper::getValue($aOrderData, 'keywords', array()),
					contentdeHelper::getValue($aOrderData, 'description'),
					contentdeHelper::getValue($aOrderData, 'project'),
					contentdeHelper::getValue($aOrderData, 'category'),
					contentdeHelper::getValue($aOrderData, 'go_group'),
					contentdeHelper::getValue($aOrderData, 'duration'),
					contentdeHelper::getValue($aOrderData, 'go_level'),
					contentdeHelper::getValue($aOrderData, 'min_words'),
					contentdeHelper::getValue($aOrderData, 'max_words'),
					contentdeHelper::getValue($aOrderData, 'min_keyword_density', 0.1),
					contentdeHelper::getValue($aOrderData, 'max_keyword_density', 1.0),
					$sExternalId
				);

				break;

			case 'do':

				$this->oApi->createDirectOrder(
					contentdeHelper::getValue($aOrderData, 'title'),
					contentdeHelper::getValue($aOrderData, 'keywords', array()),
					contentdeHelper::getValue($aOrderData, 'description'),
					contentdeHelper::getValue($aOrderData, 'project'),
					contentdeHelper::getValue($aOrderData, 'do_contractor'),
					contentdeHelper::getValue($aOrderData, 'category'),
					contentdeHelper::getValue($aOrderData, 'duration'),
					contentdeHelper::getValue($aOrderData, 'min_words'),
					contentdeHelper::getValue($aOrderData, 'max_words'),
					contentdeHelper::getValue($aOrderData, 'min_keyword_density', 0.1),
					contentdeHelper::getValue($aOrderData, 'max_keyword_density', 1.0),
					$sExternalId
				);

				break;

			default:
				throw new Exception('invalid order type');
				break;
		}
	}

	/**
	 * @param string $sOrder
	 * @param string $sReview
	 * @return void
	 */
	public function reviseOrder($sOrder, $sReview)
	{
		$this->checkLogin();

		$this->oApi->reviseOrder($sOrder, $sReview);
	}

	/**
	 * @param string $sOrder
	 * @param string $sReview
	 * @param int $iContentRating
	 * @param int $iFormRating
	 * @param int $iReadabilityRating
	 * @param int$iCommunicationRating
	 * @return void
	 */
	public function acceptOrder(
		$sOrder,
		$sReview,
		$iContentRating = 0,
		$iFormRating = 0,
		$iReadabilityRating = 0,
		$iCommunicationRating = 0
	)
	{
		$this->checkLogin();

		$this->oApi->acceptOrder(
			$sOrder,
			$sReview,
			(int) $iContentRating,
			(int) $iFormRating,
			(int) $iReadabilityRating,
			(int) $iCommunicationRating
		);
	}

	/**
	 * @param string $sOrderId
	 * @param bool $bArchive
	 * @return bool
	 */
	public function archiveOrder($sOrderId, $bArchive)
	{
		$this->checkLogin();

		return $this->oApi->archivOrder($sOrderId, (bool) $bArchive);
	}

	/**
	 * @return array
	 */
	public function getLoginInfo()
	{
		$this->checkLogin();

		return $this->oApi->getLoginInfo();
	}
}

?>