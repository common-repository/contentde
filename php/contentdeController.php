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

class contentdeController
{
	/**
	 * @var contentdeRequest
	 */
	static private $oRequest = null;

	/**
	 * @var contentdeApiHandler
	 */
	static private $oApiHandler = null;

	/**
	 * @var array
	 */
	static private $aPages = array();
  /**
	 * @return void
	 */
	static public function enqueue_scripts()
	{
		wp_enqueue_style('contentde-css', contentdeHelper::getPluginUrl('css/contentde.css'));
	}
	/**
	 * @return void
	 */
	static public function init()
	{
		register_activation_hook(CONTENTDE_BASE_FILE, array(__CLASS__, 'onActivate'));
		register_deactivation_hook(CONTENTDE_BASE_FILE, array(__CLASS__, 'onDeactivate'));

		self::$oRequest = new contentdeRequest;

		self::$oApiHandler = new contentdeApiHandler();
		self::$oApiHandler->setLoginData(CONTENTDE_LOGIN, CONTENTDE_PASSWORD);

		contentdeLogic::register();

		if(is_admin())
		{
			add_action( 'admin_enqueue_scripts', array('contentdeController', 'enqueue_scripts'));
		}

		add_action('admin_menu', array(__CLASS__, 'buildMenu'));

		if(CONTENTDE_HAS_LOGIN_DATA)
		{
			add_action('admin_bar_menu', array(__CLASS__, 'buildBarMenu'), 100);

			if(is_admin())
			{
				add_action('admin_head', array('contentdeLogic', 'addPostExtra'));

				add_filter('default_title', array('contentdeLogic', 'filterNewPostTitle'));
				add_filter('default_content', array('contentdeLogic', 'filterNewPostContent'));

				add_action('wp_ajax_contentde-calcNewOrder', array(__CLASS__, 'execute'));
				add_action('wp_ajax_contentde-getProjectSettings', array(__CLASS__, 'execute'));
				add_action('wp_ajax_contentde-loadOrderMessages', array(__CLASS__, 'execute'));
				add_action('wp_ajax_contentde-writeOrderMessage', array(__CLASS__, 'execute'));
			}
		}
	}

	/**
	 * @return contentdeRequest
	 */
	static public function getRequest()
	{
		return self::$oRequest;
	}

	/**
	 * @return contentdeApiHandler
	 */
	static public function getApiHandler()
	{
		return self::$oApiHandler;
	}

	/**
	 * @return void
	 */
	static public function buildMenu()
	{
		if(CONTENTDE_HAS_LOGIN_DATA)
		{
			add_menu_page(
				'content.de',
				'content.de',
				CONTENTDE_BASE_CAPABILITY,
				'contentde-main',
				array(__CLASS__, 'execute'),
				contentdeHelper::getPluginUrl('img/logo.gif'),
				'6.53'
			);

			add_submenu_page(
				'contentde-main',
				'&Uuml;bersicht',
				'&Uuml;bersicht',
				CONTENTDE_BASE_CAPABILITY,
				'contentde-main',
				array(__CLASS__, 'execute')
			);

			add_submenu_page(
				'contentde-main',
				'Auftrag einstellen',
				'Auftrag einstellen',
				CONTENTDE_BASE_CAPABILITY,
				'contentde-newOrder',
				array(__CLASS__, 'execute')
			);

			add_submenu_page(
				'contentde-main',
				'Einstellungen',
				'Einstellungen',
				CONTENTDE_SETTINGS_CAPABILITY,
				'contentde-settings',
				array(__CLASS__, 'execute')
			);

			contentdeHelper::registerWpAdminPage('rateOrder', array(__CLASS__, 'execute'));
		}
		else
		{
			add_menu_page(
				'content.de',
				'content.de',
				CONTENTDE_SETTINGS_CAPABILITY,
				'contentde-settings',
				array(__CLASS__, 'execute'),
				contentdeHelper::getPluginUrl('img/logo.gif'),
				'6.53'
			);

			add_submenu_page(
				'contentde-settings',
				'Einstellungen',
				'Einstellungen',
				CONTENTDE_SETTINGS_CAPABILITY,
				'contentde-settings',
				array(__CLASS__, 'execute')
			);
		}
	}

	/**
	 * @param WP_Admin_Bar $oMenu
	 * @return void
	 */
	static public function buildBarMenu($oMenu)
	{
		if($oMenu instanceof WP_Admin_Bar && current_user_can(CONTENTDE_BASE_CAPABILITY))
		{
			$aLoginInfo = get_option(CONTENTDE_PARAM_LOGIN_INFO);

			$sLoggedInAs = $aLoginInfo['name'] . ' (' . $aLoginInfo['mail'] . ')';

			$oMenu->add_menu(array(
				'id' => 'contentde',
				'title' => '<img src="' . contentdeHelper::getPluginUrl('img/logo.gif') . '" style="vertical-align: text-bottom;" /> ' . $sLoggedInAs,
				'href' => contentdeHelper::getPageUrl('main')
			));
		}
	}

	/**
	 * @return void
	 */
	static public function onActivate()
	{
		try
		{
			contentdeApi::testConnection();
		}
		catch(Exception $oError)
		{
			$sError  = '<div>Es kann keine Verbindung zum content.de Server hergestellt werden.</div>';
			$sError .= '<div>Bitte stellen Sie sicher, dass <strong>PHP</strong> und die Module <strong>SOAP</strong> bzw. <strong>xmlrpc</strong> installiert, aktiviert und richtig konfiguriert sind.</div>';
			$sError .= '<div><strong>Folgender Fehler ist aufgetreten:</strong> ' . $oError->getMessage() . '</div>';

			if(isset($_GET['action']) && $_GET['action'] == 'error_scrape')
			{
				echo $sError;
				exit;
			}
			else
			{
				trigger_error($sError, E_USER_ERROR);
			}
		}
	}

	/**
	 * @return void
	 */
	static public function onDeactivate()
	{
		contentdeHelper::clearParams();
	}

	/**
	 * @param string $sPage
	 * @param $mCallback
	 * @return void
	 */
	static public function registerPage($sPage, $mCallback)
	{
		if(is_callable($mCallback))
		{
			self::$aPages[$sPage] = $mCallback;
		}
	}

	/**
	 * @return string
	 */
	static public function execute()
	{
		try
		{
			$aParams = self::executeLogic(self::$oRequest);

			$sResult = self::renderTemplate(
				self::$oRequest->getPage(),
				is_array($aParams)
					? $aParams
					: ($aParams === null ? array() : array($aParams))
			);
		}
		catch(Exception $oError)
		{
			contentdeLogic::$aErrors = array($oError->getMessage());
			contentdeLogic::$aSucesses = array();

			$sResult = self::renderTemplate('error');
		}

		echo $sResult;

		if(self::$oRequest->isAjax())
		{
			die();
		}
	}

	/**
	 * @param string $sPage
	 * @param contentdeRequest $oRequest
	 * @return array
	 */
	static private function executeLogic(contentdeRequest $oRequest)
	{
		$sPage = $oRequest->getPage();

		if(isset(self::$aPages[$sPage]) && is_callable(self::$aPages[$sPage]))
		{
			return call_user_func(self::$aPages[$sPage], $oRequest);
		}

		throw new Exception('page not found');
	}

	/**
	 * @param string $sPage
	 * @param array $aParams
	 * @return string
	 */
	static private function renderTemplate($sPage, array $aParams = array())
	{
		//#if false

		$sTemplateFile = CONTENTDE_DIR_TPL . $sPage . '.php';

		if(is_readable($sTemplateFile))
		{
			ob_start();

			require $sTemplateFile;

			ob_clean();

		//#endif

			$sFunctionName = 'cont' . 'entdeT' . 'emplate' . ucfirst($sPage);

			if(function_exists($sFunctionName))
			{
				ob_start();

				$sFunctionName($aParams);

				return ob_get_clean();
			}

		//#if false

		}

		//#endif

		return '';
	}
}

?>