<?php
/**
 * Copyright 2020  content.de AG  (email: info[YEAR]@content.de (eg: info2020@content.de))
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

/*
Plugin Name: content.de Wordpress-Plugin
Plugin URI: http://www.content.de/wordpress-plugin
Description: Verwalten Sie Ihre content.de Auftr&auml;ge direkt aus Wordpress heraus.
Version: 1.0.18
Author: content.de AG
Author URI: http://www.content.de/
License: GPLv2
*/

//#relocate last

class contentde
{
	/**
	 * @return void
	 */
	static public function runPlugin()
	{
		static $bExecuted;

		if(!$bExecuted)
		{
			$bExecuted = true;

	//		try
	//		{
				define('CONTENTDE_BASE_FILE', __FILE__);
				define('CONTENTDE_PLUGIN_NAME', basename(dirname(CONTENTDE_BASE_FILE)));

				define('CONTENTDE_DIR_BASE', dirname(CONTENTDE_BASE_FILE) . DIRECTORY_SEPARATOR);
				define('CONTENTDE_DIR_PHP', CONTENTDE_DIR_BASE . 'php' . DIRECTORY_SEPARATOR);
				define('CONTENTDE_DIR_TPL', CONTENTDE_DIR_BASE . 'tpl' . DIRECTORY_SEPARATOR);
				define('CONTENTDE_DIR_CSS', CONTENTDE_DIR_BASE . 'css' . DIRECTORY_SEPARATOR);
				define('CONTENTDE_DIR_JS', CONTENTDE_DIR_BASE . 'js' . DIRECTORY_SEPARATOR);

				//#if false

				require_once CONTENTDE_DIR_PHP . 'contentdeConfig.php';

				//#endif

				define('CONTENTDE_LOGIN', get_option(CONTENTDE_PARAM_LOGIN, ''));
				define('CONTENTDE_PASSWORD', get_option(CONTENTDE_PARAM_PASSWORD, ''));
				define('CONTENTDE_PAGER_PER_PAGE', get_option(CONTENTDE_PARAM_PAGER_PER_PAGE, 20));
				define('CONTENTDE_POST_AND_ARCHIVE', get_option(CONTENTDE_PARAM_POST_AND_ARCHIVE, 0));

				define(
					'CONTENTDE_HAS_LOGIN_DATA',
					strlen(CONTENTDE_LOGIN) > 0 && strlen(CONTENTDE_PASSWORD) > 0
				);

				//#if false

				require_once CONTENTDE_DIR_PHP . 'contentdeRpcModule.php';
				require_once CONTENTDE_DIR_PHP . 'contentdeApi.php';
				require_once CONTENTDE_DIR_PHP . 'contentdeApiHandler.php';
				require_once CONTENTDE_DIR_PHP . 'contentdeController.php';
				require_once CONTENTDE_DIR_PHP . 'contentdeRequest.php';
				require_once CONTENTDE_DIR_PHP . 'contentdeLogic.php';
				require_once CONTENTDE_DIR_PHP . 'contentdeUtil.php';

				//#endif

				contentdeController::init();
	//		}
	//		catch(Exception $oError)
	//		{
	//		}
		}
	}
}
contentde::runPlugin();


?>