<?php
/**
 * Copyright 2020  content.de AG  (email: info[YEAR]@content.de (eg: info2012@content.de))
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

class contentdeHelper
{
	/**
	 * @return void
	 */
	static public function clearParams()
	{
		delete_option(CONTENTDE_PARAM_LOGIN_INFO);
		delete_option(CONTENTDE_PARAM_LAST_PROJECT);
		delete_option(CONTENTDE_PARAM_LAST_STATE);
		delete_option(CONTENTDE_PARAM_LAST_ARCHIVE);
		delete_option(CONTENTDE_PARAM_LOGIN);
		delete_option(CONTENTDE_PARAM_PASSWORD);
		delete_option(CONTENTDE_PARAM_SAVED_ORDERS);
	}

	/**
	 * @param string $sPage
	 * @param array $aParams
	 * @return string
	 */
	static public function getPageUrl($sPage = '', array $aParams = array())
	{
		if(!empty($sPage))
		{
			$aParams = array_merge(array('page' => 'contentde-' . $sPage), $aParams);
		}

		$sParams = http_build_query($aParams, '', '&');

		if(!empty($sParams))
		{
			$sParams = '?' . $sParams;
		}

		return admin_url('admin.php' . $sParams);
	}

	/**
	 * @param string $sResource
	 * @return string
	 */
	static public function getPluginUrl($sResource)
	{
		return plugins_url(CONTENTDE_PLUGIN_NAME . '/' . $sResource);
	}

	/**
	 * @param array $aArray
	 * @param string $sKey
	 * @param mixed $mDefault
	 * @return mixed
	 */
	static public function getValue($aArray, $sKey, $mDefault = null)
	{
		return isset($aArray[$sKey]) ? $aArray[$sKey] : $mDefault;
	}

	/**
	 * @param string $sPage
	 * @param mixed $mCallback
	 * @return void
	 */
	static public function registerWpAdminPage($sPage, $mCallback)
	{
		if(current_user_can(CONTENTDE_BASE_CAPABILITY))
		{
			$sHookName = get_plugin_page_hookname('contentde-' . $sPage, '');

			if(!empty($sHookName))
			{
				add_action($sHookName, $mCallback);

				$GLOBALS['_registered_pages'][$sHookName] = true;
			}
		}
	}

	/**
	 * @param array $aData
	 * @param string $sDefault
	 * @param array $aParams
	 * @return string
	 */
	static public function buildSelect($aData, $sDefault = null, array $aParams = array())
	{
		$sSelect = '<select' . self::buildParamList($aParams) . '>';

		foreach($aData as $sKey => $sValue)
		{
			$sSelect .= '<option value="' . $sKey . '"' . ((string) $sKey == (string) $sDefault ? ' selected="selected"' : '') . '>' . $sValue . '</option>';
		}

		return $sSelect . '</select>';
	}

	/**
	 * @param array $aParams
	 * @return string
	 */
	static private function buildParamList($aParams)
	{
		$sParams = '';

		foreach($aParams as $sName => $sValue)
		{
			$sParams .= ' ' . $sName . ' = "' . $sValue . '"';
		}

		return $sParams;
	}

	/**
	 * @param float|int $fNumber
	 * @param string $sSuffix
	 * @return string
	 */
	static public function formatNumber($fNumber, $sSuffix)
	{
		return str_replace('.', ',', sprintf('%.2f', (float) $fNumber)) . ' ' . $sSuffix;
	}

	/**
	 * @param string $sText
	 * @return string
	 */
	static public function replaceBBCode($sText)
	{
		$aBBCodes = array('strong', 'b', 'u', 'i', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'em',  'sup','sub','blockquote', 'br', 'table', 'tr', 'th', 'td', 'thead', 'tbody');

		
		$sText = preg_replace(
			'/\\[(?:\s*(\\/?)\s*(' . implode('|', $aBBCodes) . ')\s*)\\]/i',
			'<\\1\\2>',
			$sText
		);


		$sPattern = '/\\[div([^\\]]*)\\](.+)\\[\\/div\\]/isU';
		$sText = preg_replace($sPattern, '<div \\1>\\2</div>', $sText);

		$sPattern = '/\\[url=(?(?=")"([^"]+)"|([^\\]]+))\\](.+)\\[\\/url\\]/isU';
		$sText = preg_replace($sPattern, '<a href="\\1\\2">\\3</a>', $sText);
		$sPattern = '/\\[url=([^\\]]+)\\](.+)\\[\\/url\\]/isU';
		$sText = preg_replace($sPattern, '<a href=\\1>\\2</a>', $sText);
	return $sText ;
	




	}

	/**
	 * @return array
	 */
	static public function getPostTypes()
	{
		$aForbidden = array(
			'attachment',
			'revision',
			'nav_menu_item'
		);

		$aTypes = array();

		foreach(get_post_types(array(), 'objects') as $sType => $oType)
		{
			if(!in_array($sType, $aForbidden))
			{
				$aTypes[$sType] = $oType->labels->add_new_item;
			}
		}

		return $aTypes;
	}
}

class contentdePager
{
	/**
	 * @var int
	 */
	const DEFAULT_ITEMS_PER_PAGE = 20;

	/**
	 * @var int
	 */
	const PADDING = 3;

	/**
	 * @var string
	 */
	const PAGE_PARAMETER = 'contentdePage';

	/**
	 * @var array
	 */
	private $aData = array();

	/**
	 * @var int
	 */
	private $iItems = 0;

	/**
	 * @var int
	 */
	private $iItemsPerPage = 0;

	/**
	 * @var int
	 */
	private $iPages = 0;

	/**
	 * @var int
	 */
	private $iPrevPage = 0;

	/**
	 * @var int
	 */
	private $iCurrentPage = 0;

	/**
	 * @var int
	 */
	private $iNextPage = 0;

	/**
	 * @param array $aData
	 * @param int $iItemsPerPage
	 * @return void
	 */
	public function __construct(array $aData, contentdeRequest $oRequest, $iItemsPerPage = self::DEFAULT_ITEMS_PER_PAGE)
	{
		$this->iItemsPerPage = $iItemsPerPage;
		$this->iCurrentPage = $oRequest->getParam(self::PAGE_PARAMETER, 1);

		$this->calculatePages($aData);
	}

	/**
	 * @return int
	 */
	public function getCurrentPage()
	{
		return $this->iCurrentPage;
	}

	/**
	 * @param array $aData
	 * @return void
	 */
	private function calculatePages(array &$aData)
	{
		$this->iItems = count($aData);

		$this->iPages = (int) ceil($this->iItems / $this->iItemsPerPage);

		if($this->iCurrentPage < 1)
		{
			$this->iCurrentPage = 1;
		}
		elseif($this->iCurrentPage > $this->iPages)
		{
			$this->iCurrentPage = $this->iPages;
		}

		$this->iPrevPage = $this->iCurrentPage - 1;

		if($this->iPrevPage < 1)
		{
			$this->iPrevPage = 1;
		}

		$this->iNextPage = $this->iCurrentPage + 1;

		if($this->iNextPage > $this->iPages)
		{
			$this->iNextPage = $this->iPages;
		}

		$this->aData = array_slice(
			$aData,
			($this->iItemsPerPage * $this->iCurrentPage) - $this->iItemsPerPage,
			$this->iItemsPerPage,
			true
		);
	}

	/**
	 * @return bool
	 */
	public function hasPages()
	{
		return $this->iPages > 1;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->aData;
	}

	/**
	 * @return array
	 */
	public function getNavigation($sLink, array $aParams = array())
	{
		$iStartPage = $this->iCurrentPage - self::PADDING;
		$iEndPage = $this->iCurrentPage + self::PADDING;

		$iStartDelta = 1 - $iStartPage;
		$iEndDelta = $iEndPage - $this->iPages;

		if($iStartDelta > 0)
		{
			$iEndPage += $iStartDelta;
		}

		if($iEndDelta > 0)
		{
			$iStartPage -= $iEndDelta;
		}

		if($iStartPage < 1)
		{
			$iStartPage = 1;
		}

		if($iEndPage > $this->iPages)
		{
			$iEndPage = $this->iPages;
		}

		$aNavigation = array();

		for($iPage=$iStartPage;$iPage<=$iEndPage;++$iPage)
		{
			$aNavigation[$iPage] = $this->buildNavigationLink(
				$iPage,
				$sLink,
				$aParams
			);
		}

		return $aNavigation;
	}

	/**
	 * @param int $iPage
	 * @param string $sLink
	 * @param array $aParams
	 * @return array
	 */
	private function buildNavigationLink($iPage, $sLink, array $aParams)
	{
		$aParams = array_merge(array(self::PAGE_PARAMETER => $iPage), $aParams);

		$sQuery = http_build_query($aParams, '', '&');

		if(!empty($sQuery))
		{
			$sSeparator = '?';

			if(strpos($sLink, $sSeparator) !== false)
			{
				$sSeparator = '&';
			}

			$sLink .= $sSeparator . $sQuery;
		}

		return $sLink;
	}

	/**
	 * @param string $sLink
	 * @param array $aParams
	 * @return string
	 */
	public function getDisplayNavigation($sLink, array $aParams = array())
	{
		$aNavigation = $this->getNavigation($sLink, $aParams);


		$sHtml .= 'Seiten: ';

		if(!isset($aNavigation[1]))
		{
			$sHtml .= '<a href="' . $this->buildNavigationLink(1, $sLink, $aParams) . '">erste</a> ... ';
		}

		if($this->iPrevPage != $this->iCurrentPage)
		{
			$sHtml .= '<a href="' . $this->buildNavigationLink($this->iPrevPage, $sLink, $aParams) . '">vorherige</a> ... ';
		}

		foreach($aNavigation as $iPage => $sLink)
		{
			$sPage = $iPage;

			if($iPage == $this->iCurrentPage)
			{
				$sPage = '<b>' . $sPage . '</b>';
			}

			$sHtml .= '<a href="' . $sLink . '">' . $sPage . '</a> ';
		}

		if($this->iNextPage != $this->iCurrentPage)
		{
			$sHtml .= ' ... <a href="' . $this->buildNavigationLink($this->iNextPage, $sLink, $aParams) . '">n&auml;chste</a>';
		}

		if(!isset($aNavigation[$this->iPages]))
		{
			$sHtml .= ' ... <a href="' . $this->buildNavigationLink($this->iPages, $sLink, $aParams) . '">letzte</a>';
		}

		return $sHtml;
	}
}

?>