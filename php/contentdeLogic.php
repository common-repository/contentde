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

class contentdeLogic
{
	/**
	 * @var array
	 */
	static public $aErrors = array();

	/**
	 * @var array
	 */
	static public $aSucesses = array();

	/**
	 * @var array
	 */
	static private $aErrorMapping = array(
		'an error has occured' => 'Es ist ein Fehler aufgetreten. Bitte &uuml;berpr&uuml;fen Sie nochmal alle Eingaben.',
		'not enough balance' => 'Sie haben nicht genug Guthaben. Bitte laden Sie ihr Konto auf.',
		'order title too short' => 'Auftragstitel ist zu kurz.',
		'order description too short' => 'Autorenbriefing ist zu kurz.',
		'project id incorrect' => 'Bitte w&auml;hlen sie ein richtiges Projekt.',
		'order id incorrect' => 'Ung&uuml;ltige Auftrags-ID.',
		'level incorrect' => 'Bitte geben Sie eine korrekte Einstufung an.',
		'wrong category id' => 'Bitte geben Sie eine korrekte Kategorie an.',
		'Session data outdated or wrong' => 'Ihre content.de Session ist abgelaufen.',
		'no user found' => 'Es konnte kein User gefunden werden.',
		'no categories found' => 'Es konnten keine Kategorien gefunden werden.',
		'no categories/user found' => 'Es konnten keine Kategorien gefunden werden.',
		'values for content rating must be between -2 and 2' => 'Werte f�r die Bewertung m&uuml;ssen zwischen -2 und 2 liegen.',
		'values for form rating must be between -2 and 2' => 'Werte f�r die Bewertung m�ssen zwischen -2 und 2 liegen.',
		'values for readability rating must be between -2 and 2' => 'Werte f�r die Bewertung m&uuml;ssen zwischen -2 und 2 liegen.',
		'values for communication rating must be between -2 and 2' => 'Werte f�r die Bewertung m&uuml;ssen zwischen -2 und 2 liegen.',
		'error writing data, please contact support' => 'Daten konnten nicht geschrieben werden.',
		'no orders found to be rated' => 'Es konnte kein Auftrag gefunden werden, der bewertet werden k&ouml;nnte',
		'invalid order-id' => 'Ung&uuml;ltige Auftrags-ID.',
		'no text to revise found' => 'Es konnte kein Text gefunden werden, der in Revision gegeben werden k&ouml;nnte.',
		'no text to reject found' => 'Es konnte kein Text gefunden werden, f�r den eine Ablehnung beantragt werden k&ouml;nnte.',
		'Please add a  comment' => 'Bitte geben Sie einen Kommentar ein.',
		'page not found' => 'Seite konnte nicht gefunden werden',
		'invalid order type' => 'ung&uuml;tiger Auftragstyp',
		'User Data invalid' => 'Ihre content.de Zugangsdaten sind nicht korrekt',
		'there is no possible rpc module' => 'Ihre PHP-Installation unterst&uuml;tzt weder <strong>SOAP</strong> noch <strong>xmlrpc</strong>. Bitte installieren oder aktivieren Sie eines der genannten PHP-Module, da das Plugin sonst nicht verwendet werden kann.'
	);

	/**
	 * @return void
	 */
	static public function register()
	{
		contentdeController::registerPage('main', array(__CLASS__, 'execMain'));
		contentdeController::registerPage('settings', array(__CLASS__, 'execSettings'));
		contentdeController::registerPage('neworder', array(__CLASS__, 'execNewOrder'));
		contentdeController::registerPage('calcneworder', array(__CLASS__, 'execCalcNewOrder'));
		contentdeController::registerPage('getprojectsettings', array(__CLASS__, 'execGetProjectSettings'));
		contentdeController::registerPage('rateorder', array(__CLASS__, 'execRateOrder'));
		contentdeController::registerPage('loadordermessages', array(__CLASS__, 'execLoadOrderMessages'));
		contentdeController::registerPage('writeordermessage', array(__CLASS__, 'execWriteOrderMessage'));
	}

	/**
	 * @return void
	 */
	static public function showErrors()
	{
		if(($iErrorCount = count(self::$aErrors)) > 0)
		{
			echo '<div class="error">';

			$bMultiple = $iErrorCount > 1;

			if($bMultiple)
			{
				echo '<b>Folgende Fehler sind aufgetreten:</b><br />';
			}
			else
			{
				echo '<b>Folgender Fehler ist aufgetreten:</b><br />';
			}

			foreach(self::$aErrors as $sError)
			{
				if(isset(self::$aErrorMapping[$sError]))
				{
					$sError = self::$aErrorMapping[$sError];
				}

				echo ($bMultiple ? ' - ' : '') . $sError . '<br />';
			}

			echo '</div>';
		}
	}

	/**
	 * @return void
	 */
	static public function showSuccesses()
	{
		if(count(self::$aSucesses) > 0)
		{
			echo '<div class="updated">';

			foreach(self::$aSucesses as $sSuccess)
			{
				echo $sSuccess . '<br />';
			}

			echo '</div>';
		}
	}

	/**
	 * @param contentdeRequest $oRequest
	 * @return array
	 */
	static public function execMain(contentdeRequest $oRequest)
	{
		if(CONTENTDE_HAS_LOGIN_DATA)
		{
			if($oRequest->hasParam('doArchive') || $oRequest->hasParam('doUnarchive'))
			{
				$sOrder = '';
				$iArchive = -1;

				if($oRequest->hasParam('doArchive'))
				{
					$sOrder = $oRequest->getParam('doArchive');
					$iArchive = 1;
				}
				elseif($oRequest->hasParam('doUnarchive'))
				{
					$sOrder = $oRequest->getParam('doUnarchive');
					$iArchive = 0;
				}

				contentdeController::getApiHandler()->archiveOrder($sOrder, $iArchive);

				if(((int) $oRequest->getParam('noheader', 0)) == 1)
				{
					wp_redirect(contentdeHelper::getPageUrl('main', array(contentdePager::PAGE_PARAMETER => $oRequest->getParam(contentdePager::PAGE_PARAMETER, 1))));
					die();
				}
			}

			$sSelectedProject = get_option(CONTENTDE_PARAM_LAST_PROJECT, 'ALL');
			$sSelectedStatus = get_option(CONTENTDE_PARAM_LAST_STATE, 'ALL');
			$sSelectedArchive = get_option(CONTENTDE_PARAM_LAST_ARCHIVE, 0);

			if($oRequest->hasParam('project'))
			{
				$sSelectedProject = $oRequest->getParam('project');
				update_option(CONTENTDE_PARAM_LAST_PROJECT, $sSelectedProject);
			}

			if($oRequest->hasParam('status'))
			{
				$sSelectedStatus = $oRequest->getParam('status');
				update_option(CONTENTDE_PARAM_LAST_STATE, $sSelectedStatus);
			}

			if($oRequest->hasParam('archive'))
			{
				$sSelectedArchive = $oRequest->getParam('archive');
				update_option(CONTENTDE_PARAM_LAST_ARCHIVE, $sSelectedArchive);
			}

			$oApiHandler = contentdeController::getApiHandler();

			$aOrderList = $oApiHandler->getOrderList(
				$sSelectedProject,
				$sSelectedStatus,
				$sSelectedArchive
			);

			$oPager = new contentdePager($aOrderList, $oRequest, CONTENTDE_PAGER_PER_PAGE);

			if($oRequest->hasParam('contentdeOrderCreated'))
			{
				self::$aSucesses[] = 'Der Auftrag wurde erfolgreich erstellt.';
			}

			if($oRequest->hasParam('contentdeOrderSaved'))
			{
				self::$aSucesses[] = 'Der Auftrag wurde erfolgreich gespeichert.';
			}

			if($oRequest->hasParam('contentdeActivated'))
			{
				self::$aSucesses[] = 'Ihre Zugangsdaten wurden erfolgreich gespeichert.';
			}

			return array(
				'orderList' => $oPager->getData(),
				'projects' => $oApiHandler->getProjectsSimple(),
				'selectedProject' => $sSelectedProject,
				'selectedStatus' => $sSelectedStatus,
				'selectedArchive' => $sSelectedArchive,
				'pager' => $oPager
			);
		}
		else
		{
			self::$aErrors[] = 'Sie haben noch keine Zugangsdaten eingegeben';
		}
	}

	/**
	 * @param contentdeRequest $oRequest
	 * @return void
	 */
	static public function execSettings(contentdeRequest $oRequest)
	{
		if($oRequest->isMethod('post'))
		{
			if($oRequest->hasParam('save_login_data'))
			{
				$sLogin = (string) $oRequest->getParam('clogin', '');
				$sPassword = (string) $oRequest->getParam('cpassword', '');

				if(strlen($sLogin) == 0)
				{
					self::$aErrors[] = 'Bitte geben Sie eine E-Mail Adresse / einen API-Key ein';
				}

				if(strlen($sPassword) == 0)
				{
					self::$aErrors[] = 'Bitte geben Sie ein Passwort ein';
				}

				if(count(self::$aErrors) == 0)
				{
					if(!contentdeApiHandler::testLogin($sLogin, $sPassword))
					{
						self::$aErrors[] = 'Die Zugangsdaten sind falsch';
					}

					if(count(self::$aErrors) == 0)
					{
						update_option(CONTENTDE_PARAM_LAST_PROJECT, 'all');
						update_option(CONTENTDE_PARAM_LAST_STATE, 'all');

						update_option(CONTENTDE_PARAM_LOGIN, $sLogin);
						update_option(CONTENTDE_PARAM_PASSWORD, $sPassword);

						contentdeController::getApiHandler()->setLoginData($sLogin, $sPassword);

						update_option(
							CONTENTDE_PARAM_LOGIN_INFO,
							contentdeController::getApiHandler()->getLoginInfo()
						);

						if(CONTENTDE_HAS_LOGIN_DATA)
						{
							self::$aSucesses[] = 'Einstellungen erfolgreich gespeichert';
						}
						else
						{
							if($oRequest->hasParam('noheader'))
							{
								wp_redirect(contentdeHelper::getPageUrl(
									'main',
									array('contentdeActivated' => 1)
								));

								die();
							}
						}
					}
				}
			}
			elseif($oRequest->hasParam('clear_login_data'))
			{
				contentdeHelper::clearParams();

				if($oRequest->hasParam('noheader'))
				{
					wp_redirect(contentdeHelper::getPageUrl('settings'));

					die();
				}
			}
            elseif($oRequest->hasParam('save_per_page'))
            {

                $iPerPage = (int) $oRequest->getParam('perPage', '');
                update_option(CONTENTDE_PARAM_PAGER_PER_PAGE, $iPerPage);

                if($oRequest->hasParam('noheader'))
                {
                    wp_redirect(contentdeHelper::getPageUrl('settings'));

                    die();
                }
            }
            elseif($oRequest->hasParam('save_post_and_archive'))
            {

                $bPostAndArchive = (int) $oRequest->getParam('param_post_and_archive', 0);
                update_option(CONTENTDE_PARAM_POST_AND_ARCHIVE, $bPostAndArchive);

                if($oRequest->hasParam('noheader'))
                {
                    wp_redirect(contentdeHelper::getPageUrl('settings'));

                    die();
                }
            }

			if($oRequest->hasParam('noheader'))
			{
				require_once(ABSPATH . 'wp-admin/admin-header.php');
			}
		}
	}

	/**
	 * @param contentdeRequest $oRequest
	 * @return array
	 */
	static public function execNewOrder(contentdeRequest $oRequest)
	{
		$oApiHandler = contentdeController::getApiHandler();

		$aFormData = array();

		if($oRequest->hasParam('remove_saved_order'))
		{
			self::removeSavedOrder((int) $oRequest->getParam('remove_saved_order'));

			self::$aSucesses[] = 'Auftrag wurde erfolgreich gel&ouml;scht.';
		}

		if($oRequest->isMethod('post') && $oRequest->hasParam('new_order'))
		{
			$aFormData = (array) $oRequest->getParam('new_order', array());

			try
			{
				$aParams = array();

				if($oRequest->hasParam('create_new_order'))
				{
					$oApiHandler->createOrder($aFormData);

					$aParams = array('contentdeOrderCreated' => 1);
				}
				elseif($oRequest->hasParam('save_new_order'))
				{
					self::saveOrder($aFormData);

					$aParams = array('contentdeOrderSaved' => 1);
				}

				if($oRequest->hasParam('noheader'))
				{
					wp_redirect(contentdeHelper::getPageUrl('main', $aParams));
					die();
				}
			}
			catch(Exception $oError)
			{
				self::$aErrors[] = $oError->getMessage();

				if($oRequest->hasParam('noheader'))
				{
					require_once(ABSPATH . 'wp-admin/admin-header.php');
				}
			}
		}

		$iSavedOrderId = null;

		if($oRequest->hasParam('load_order'))
		{
			$iSavedOrderId = (int) $oRequest->getParam('load_order');

			$aFormData = self::getSavedOrder($iSavedOrderId);
		}

		return array(
			'savedOrders' => self::getSavedOrders(),
			'savedOrderId' => $iSavedOrderId,

			'levels' => $oApiHandler->getLevelsSimple(),
			'groups' => $oApiHandler->getGroups(),
			'contractors' => $oApiHandler->getContractorsSimple(),
			'projects' => $oApiHandler->getProjectsSimple(),
			'categories' => $oApiHandler->getCategories(),
			'briefings' => $oApiHandler->getTemplates(),

			'formData' => $aFormData
		);
	}

	/**
	 * @param contentdeRequest $oRequest
	 * @return void
	 */
	static public function execCalcNewOrder(contentdeRequest $oRequest)
	{
		$oApiHandler = contentdeController::getApiHandler();

		$fAvailableBalance = 0.0;
		$fAvailableBudget = 0.0;
		$fCostsPerWord = 0.0;
		$fCosts = 0.0;

		try
		{
			$aBalance = $oApiHandler->getBalance();

			$fAvailableBalance = (float) $aBalance['available'];
			$fAvailableBudget = (float) $aBalance['budget_available'];
		}
		catch(Exception $oError)
		{
		}

		try
		{
			$aOrderCosts = $oApiHandler->calculateOrderPrice(
				$oRequest->getParam('type'),
				$oRequest->getParam('level'),
				$oRequest->getParam('wordCount'),
				$oRequest->getParam('project')
			);

			$fCostsPerWord = $aOrderCosts['order_costs_per_word'];
			$fCosts = $aOrderCosts['order_costs'];
			$sProjectSettingsId = $aOrderCosts['project_settings_id'];
		}
		catch(Exception $oError)
		{
		}

		return array(
			'avail_balance' => contentdeHelper::formatNumber($fAvailableBalance, 'Eur'),
			'avail_budget' => $fAvailableBudget > 0 ? contentdeHelper::formatNumber($fAvailableBudget, 'Eur') : '',
			'order_costs_per_word' => contentdeHelper::formatNumber($fCostsPerWord * 100, 'Eur Ct'),
			'order_costs' => contentdeHelper::formatNumber($fCosts, 'Eur')
		);
	}

	/**
	 * @param contentdeRequest $oRequest
	 * @return array
	 */
	static public function execGetProjectSettings(contentdeRequest $oRequest)
	{
		static $aFields = array(
			'category',
			'duration',
			'min_words',
			'max_words',
			'min_density',
			'max_density',
			'template'
		);

		$oApiHandler = contentdeController::getApiHandler();

		try
		{
			$aSettings = $oApiHandler->getProjectSettings(
				$oRequest->getParam('project')
			);

			$aUsedSettings = array();

			foreach($aFields as $sField)
			{
				$aUsedSettings[$sField] = $aSettings[$sField];
			}

			return $aUsedSettings;
		}
		catch(Exception $oError)
		{
		}

		return null;
	}

	/**
	 * @param contentdeRequest $oRequest
	 * @return array
	 */
	static public function execRateOrder(contentdeRequest $oRequest)
	{
		$oApiHandler = contentdeController::getApiHandler();

		$aOrder = $oApiHandler->getOrder($oRequest->getParam('contentdeOrder'));

		if($aOrder['status'] == 'waiting')
		{
			if($oRequest->isMethod('post'))
			{
				try
				{
					if($oRequest->hasParam('revise_order_button'))
					{
						$oApiHandler->reviseOrder(
							$aOrder['order_id'],
							$oRequest->getParam('reviseOrderReview', '')
						);

						if($oRequest->hasParam('noheader'))
						{
							wp_redirect(contentdeHelper::getPageUrl(
								'main',
								array('contentdeOrderRevised' => 1)
							));

							die();
						}
					}
					elseif($oRequest->hasParam('accept_order_button'))
					{
						$oApiHandler->acceptOrder(
							$aOrder['order_id'],
							$oRequest->getParam('acceptOrderReview', ''),
							$oRequest->getParam('ratingContent', 0),
							$oRequest->getParam('ratingForm', 0),
							$oRequest->getParam('ratingReadability', 0),
							$oRequest->getParam('ratingCommunication', 0)
						);

						if($oRequest->hasParam('noheader'))
						{
							wp_redirect(contentdeHelper::getPageUrl(
								'main',
								array('contentdeOrderAccepted' => 1)
							));

							die();
						}
					}
				}
				catch(Exception $oError)
				{
					self::$aErrors[] = $oError->getMessage();
				}

				if($oRequest->hasParam('noheader'))
				{
					require_once(ABSPATH . 'wp-admin/admin-header.php');
				}
			}
		}
		else
		{
			self::$aErrors[] = 'Sie k&ouml;nnen diesen Auftrag nicht bewerten';
		}

		return array(
			'order' => $aOrder
		);
	}

	/**
	 * @param contentdeRequest $oRequest
	 * @return array
	 */
	static public function execLoadOrderMessages(contentdeRequest $oRequest)
	{
		$aMessages = array();

		try
		{
			$aMessages = contentdeController::getApiHandler()->getOrderMessages(
				$oRequest->getParam('contentdeOrder')
			);
		}
		catch(Exception $oError)
		{
		}

		return array(
			'messages' => $aMessages
		);
	}

	/**
	 * @param contentdeRequest $oRequest
	 * @return void
	 */
	static public function execWriteOrderMessage(contentdeRequest $oRequest)
	{
		try
		{
			contentdeController::getApiHandler()->writeOrderMessage(
				$oRequest->getParam('contentdeOrder'),
				$oRequest->getParam('contentdeMessage')
			);
		}
		catch(Exception $oError)
		{
		}
	}

	static public function addPostExtra()
	{
		$aOrder = self::getRequestOrder();

		if(isset($aOrder['keywords']) && count($aOrder['keywords']) > 0)
		{
			echo '<script type="text/javascript">var __contentde_post_keywords = ' . json_encode($aOrder['keywords']) . '</script>';

			wp_enqueue_script(
				'contentde-addPostExtra-js',
				contentdeHelper::getPluginUrl('js/contentdeAddPostExtra.js')
			);
		}
	}

	/**
	 * @param string $sTitle
	 * @return string
	 */
	static public function filterNewPostTitle($sTitle)
	{
		$aOrder = self::getRequestOrder();

		return $aOrder['title'];
	}

	/**
	 * @param string $sContent
	 * @return string
	 */
	static public function filterNewPostContent($sContent)
	{
		$aOrder = self::getRequestOrder();

		return $aOrder['content'];
	}

	/**
	 * @return array
	 */
	static private function getRequestOrder()
	{
		static $aOrder;

		if(!is_array($aOrder))
		{
			$aOrder = array(
				'title' => '',
				'content' => '',
				'keywords' => array()
			);

			if(CONTENTDE_HAS_LOGIN_DATA)
			{
				try
				{
					if(contentdeController::getRequest()->hasParam('contentdeOrder'))
					{
						$aRealOrder = contentdeController::getApiHandler()->getOrder(
							contentdeController::getRequest()->getParam('contentdeOrder')
						);


                        if(CONTENTDE_POST_AND_ARCHIVE == 1 && is_array($aRealOrder) && isset($aRealOrder['order_id']) )
                        {
                            contentdeController::getApiHandler()->archiveOrder($aRealOrder['order_id'], true);
                        }

						$aKeywords = array();

						foreach($aRealOrder['keywords'] as $aKeyword)
						{
							$aKeywords[] = $aKeyword['title'];
						}

						$aOrder['title'] = $aRealOrder['title'];
						$aOrder['content'] = contentdeHelper::replaceBBCode($aRealOrder['text']);
						$aOrder['keywords'] = $aKeywords;
					}
				}
				catch(Exception $oError)
				{
				}
			}
		}

		return $aOrder;
	}

	/**
	 * @return array
	 */
	static private function getSavedOrdersRaw()
	{
		return json_decode(get_option(CONTENTDE_PARAM_SAVED_ORDERS, '[]'), true);
	}

	/**
	 * @param array $aOrders
	 * @return void
	 */
	static private function setSavedOrdersRaw($aOrders)
	{
		update_option(CONTENTDE_PARAM_SAVED_ORDERS, json_encode($aOrders));
	}

	/**
	 * @param array $aData
	 * @return void
	 */
	static private function saveOrder($aData)
	{
		$aOrders = self::getSavedOrdersRaw();

		$aOrders[] = $aData;

		self::setSavedOrdersRaw($aOrders);
	}

	/**
	 * @param int $iId
	 * @return void
	 */
	static private function removeSavedOrder($iId)
	{
		$aOrders = self::getSavedOrdersRaw();

		if(isset($aOrders[$iId]))
		{
			unset($aOrders[$iId]);

			self::setSavedOrdersRaw($aOrders);
		}
	}

	/**
	 * @param int $iId
	 * @return array
	 */
	static private function getSavedOrder($iId)
	{
		$aOrders = self::getSavedOrdersRaw();

		if(isset($aOrders[$iId]))
		{
			return $aOrders[$iId];
		}

		return array();
	}

	/**
	 * @return array
	 */
	static private function getSavedOrders()
	{
		return self::getSavedOrdersRaw();
	}
}

?>