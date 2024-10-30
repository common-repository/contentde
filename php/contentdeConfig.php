<?php
/**
 * Copyright 2015  content.de AG  (email: info[YEAR]@content.de (eg: info2015@content.de))
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

//#relocate first

define('CONTENTDE_URL_BASE', 'https://www.content.de/');
define('CONTENTDE_URL_WSDL', CONTENTDE_URL_BASE . 'api/clientservices.php?wsdl');
define('CONTENTDE_URL_XMLRPC', CONTENTDE_URL_BASE . 'api/xmlrpc.php');

define('CONTENTDE_PARAM_PREFIX', '__contentde_');
define('CONTENTDE_PARAM_LOGIN', CONTENTDE_PARAM_PREFIX . 'log' . 'in');
define('CONTENTDE_PARAM_PASSWORD', CONTENTDE_PARAM_PREFIX . 'pass' . 'word');
define('CONTENTDE_PARAM_LAST_PROJECT', CONTENTDE_PARAM_PREFIX . 'last' . 'Project');
define('CONTENTDE_PARAM_LAST_STATE', CONTENTDE_PARAM_PREFIX . 'last' . 'State');
define('CONTENTDE_PARAM_LAST_ARCHIVE', CONTENTDE_PARAM_PREFIX . 'last' . 'Archive');
define('CONTENTDE_PARAM_LOGIN_INFO', CONTENTDE_PARAM_PREFIX . 'login' . 'Info');
define('CONTENTDE_PARAM_SAVED_ORDERS', CONTENTDE_PARAM_PREFIX . 'saved' . 'Orders');
define('CONTENTDE_PARAM_PAGER_PER_PAGE', CONTENTDE_PARAM_PREFIX . 'pager' . 'PerPage');
define('CONTENTDE_PARAM_POST_AND_ARCHIVE', CONTENTDE_PARAM_PREFIX . 'post' . 'AndArchive');

define('CONTENTDE_BASE_CAPABILITY', 'edit_posts');
define('CONTENTDE_SETTINGS_CAPABILITY', 'manage_options');

?>