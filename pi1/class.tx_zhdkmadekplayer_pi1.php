<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Beat Rohrer <beat.rohrer@zhdk.ch>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */
require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('zhdk_madekplayer') . '/class.zhdk_madekplayer.php');

/**
 * Plugin 'Madek Gallery' for the 'zhdk_madekplayer' extension.
 *
 * @author	Beat Rohrer <beat.rohrer@zhdk.ch>
 * @package	TYPO3
 * @subpackage	tx_zhdkmadekplayer
 */
class tx_zhdkmadekplayer_pi1 extends tslib_pibase {

	var $prefixId = 'tx_zhdkmadekplayer_pi1';  // Same as class name
	var $scriptRelPath = 'pi1/class.tx_zhdkmadekplayer_pi1.php'; // Path to this script relative to the extension dir.
	var $extKey = 'zhdk_madekplayer'; // The extension key.
	var $pi_checkCHash = true;

	/**
	 * Main method of your PlugIn
	 *
	 * @param	string		$content: The content of the PlugIn
	 * @param	array		$conf: The PlugIn Configuration
	 * @return	The content that should be displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->init();
		return $this->galleryView();
		
//		switch ((string) $conf['CMD']) {
//			case 'singleView':
//				list($t) = explode(':', $this->cObj->currentRecord);
//				$this->internal['currentTable'] = $t;
//				$this->internal['currentRow'] = $this->cObj->data;
//				return $this->pi_wrapInBaseClass($this->singleView($content, $conf));
//				break;
//			default:
//				if (strstr($this->cObj->currentRecord, 'tt_content')) {
//					$conf['pidList'] = $this->cObj->data['pages'];
//					$conf['recursive'] = $this->cObj->data['recursive'];
//				}
//				return $this->pi_wrapInBaseClass($this->listView($content, $conf));
//				break;
//		}
	}

	function init() {
		$this->pi_setPiVarDefaults();
		$this->pi_initPIflexForm(); // Init and get the flexform data of the plugin
		$this->lConf = array(); // Setup our storage array...
		// Assign the flexform data to a local variable for easier access
		$piFlexForm = $this->cObj->data['pi_flexform'];
		// Traverse the entire array based on the language...
		// and assign each configuration option to $this->lConf array...
		foreach ($piFlexForm['data'] as $sheet => $data) {
			foreach ($data as $lang => $value) {
				foreach ($value as $key => $val) {
					$this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
				}
			}
		}
	}
	
	function galleryView() {
		$this->pi_loadLL();
		$madekSetId = $this->lConf['madek_set'];
		$madekServer = rtrim($this->piVars['madekServer'], '/');
		if(empty($madekSetId)) {
			return;
		}
		$imageList = '';
		//get set content
		$json_url = "$madekServer/media_resources.json?ids=$madekSetId&with[media_type]=true&with[children]=true&public=true&with[meta_data][meta_key_names][]=title&with[meta_data][meta_key_names][]=subtitle&with[meta_data][meta_context_names][]=copyright";
		$json = file_get_contents($json_url);
		$data = json_decode($json, TRUE);
		$debug = '';
		$GLOBALS['TSFE']->additionalHeaderData['galleriffic_js'] = '<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath('zhdk_madekplayer') . 'res/js/jquery.galleriffic.js"></script>';
		$GLOBALS['TSFE']->additionalHeaderData['zhdk_madekplayer_css'] = '<link  media="screen" rel="stylesheet" type="text/css"  href="' . t3lib_extMgm::siteRelPath('zhdk_madekplayer') . 'res/css/zhdkmadekplayer.css" />';
		foreach($data['media_resources'][0]['children'] as $item) {
			if($item['type'] != 'media_entry') {
				continue;
			}
			if($item['media_type'] != 'Image') {
				continue;
			}
			$description = '';
			$title = 'Media Entry no. ' . $item['id'];
			if(isset($item['meta_data'])) {
				//set meta data
				$tmpTitle = zhdk_madekplayer::getMetaDataValue('title', $item['meta_data']);
				if(!empty($tmpTitle) && $this->lConf['show_title']) {
					$title = $tmpTitle;
					$description = '<h3>' . $title . '</h3>';
				}
				$tmpSubtitle = zhdk_madekplayer::getMetaDataValue('subtitle', $item['meta_data']);
				if(!empty($tmpSubtitle) && $this->lConf['show_subtitle']) {
					$description .= '<p>' . $tmpSubtitle . '</p>';
				}
				if($this->lConf['show_copyright']) {
					$notice = zhdk_madekplayer::getMetaDataValue('copyright notice', $item['meta_data']);
					$status = zhdk_madekplayer::getMetaDataValue('copyright status', $item['meta_data']);
					$usage = zhdk_madekplayer::getMetaDataValue('copyright usage', $item['meta_data']);
					$url = zhdk_madekplayer::getMetaDataValue('copyright url', $item['meta_data']);
					$description .= '<h4>' . $this->pi_getLL('tx_zhdkmadekplayer_pi1.copyright', 'Copyright') . '</h4>
						<p>' . $notice . '<br>' . 
							(!empty($url) ? '<a target="_blank" href="' . $url . '">' : '') . $status . (!empty($url) ? '</a>' : '');
						'</p>';
				}
				
			}
			$imageList .= '
				<li>
					<a class="thumb" href="' . $madekServer . '/media_resources/' . $item['id'] . '/image?size=large" title="' . $title  . '">
						<img alt="' . $title . '" src="' . $madekServer . '/media_resources/' . $item['id'] . '/image?size=small" />
					</a>
					<div class="caption">
						' . $description  . '
					</div>
				</li>';
		}
		//prevent problems with multiple galleries on the same page
		$randomIndex = rand();
		$html = '
<div class="zhdk_madekplayer-galleriffic">
	<div class="zhdk_madekplayer-controls" id="zhdk_madekplayer-controls-' . $randomIndex . '"></div>
	<!--<div id="zhdk_madekplayer-loading-' . $randomIndex . '"></div>-->
	<div class="zhdk_madekplayer-slideshow" id="zhdk_madekplayer-slideshow-' . $randomIndex . '"></div>
	<div class="zhdk_madekplayer-caption" id="zhdk_madekplayer-caption-' . $randomIndex . '"></div>
	<div class="zhdk_madekplayer-thumbs" id="zhdk_madekplayer-thumbs-' . $randomIndex . '">
		<ul class="thumbs noscript">
			' . $imageList . '
		</ul>
	</div>
</div>
<script type="text/javascript">
' . "
jQuery(document).ready(function($) {
    var gallery = $('#zhdk_madekplayer-thumbs-$randomIndex').galleriffic({
        delay:                     3000, // in milliseconds
        numThumbs:                 6, // The number of thumbnails to show page
        preloadAhead:              24, // Set to -1 to preload all images
        enableTopPager:            false,
        enableBottomPager:         true,
        maxPagesToShow:            7,  // The maximum number of pages to display in either the top or bottom pager
        imageContainerSel:         '#zhdk_madekplayer-slideshow-$randomIndex', // The CSS selector for the element within which the main slideshow image should be rendered
        controlsContainerSel:      '#zhdk_madekplayer-controls-$randomIndex', // The CSS selector for the element within which the slideshow controls should be rendered
        captionContainerSel:       '#zhdk_madekplayer-caption-$randomIndex', // The CSS selector for the element within which the captions should be rendered
        //loadingContainerSel:       '', // The CSS selector for the element within which should be shown when an image is loading
        renderSSControls:          true, // Specifies whether the slideshow's Play and Pause links should be rendered
        renderNavControls:         true, // Specifies whether the slideshow's Next and Previous links should be rendered
        playLinkText:              'Play',
        pauseLinkText:             'Pause',
        prevLinkText:              'Previous',
        nextLinkText:              'Next',
        nextPageLinkText:          'Next &rsaquo;',
        prevPageLinkText:          '&lsaquo; Prev',
        enableHistory:             false, // Specifies whether the url's hash and the browser's history cache should update when the current slideshow image changes
        enableKeyboardNavigation:  true, // Specifies whether keyboard navigation is enabled
        autoStart:                 false, // Specifies whether the slideshow should be playing or paused when the page first loads
        syncTransitions:           false, // Specifies whether the out and in transitions occur simultaneously or distinctly
        defaultTransitionDuration: 500, // If using the default transitions, specifies the duration of the transitions
        /*onSlideChange:             undefined, // accepts a delegate like such: function(prevIndex, nextIndex) { ... }
        onTransitionOut:           undefined, // accepts a delegate like such: function(slide, caption, isSync, callback) { ... }
        onTransitionIn:            undefined, // accepts a delegate like such: function(slide, caption, isSync) { ... }
        onPageTransitionOut:       undefined, // accepts a delegate like such: function(callback) { ... }
        onPageTransitionIn:        undefined, // accepts a delegate like such: function() { ... }
        onImageAdded:              undefined, // accepts a delegate like such: function(imageData, li) { ... }
        onImageRemoved:            undefined  // accepts a delegate like such: function(imageData, li) { ... }*/
    });
	console.log('test1');
});
</script>
";
//		die($debug);
		return $html;
	}
	
	function galleryViewOld() {
		$localImageIds = $this->lConf['gallery_items'];
		if(empty($localImageIds)) {
			return;
		}
		$localImageIds = implode(',', $GLOBALS['TYPO3_DB']->fullQuoteArray(explode(',', $localImageIds)));
		
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('madekid, title', 'tx_zhdkmadekplayer_item', "uid IN ($localImageIds)", '', '', '10');
		$GLOBALS['TSFE']->additionalHeaderData['galleriffic_js'] = '<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath('zhdk_madekplayer') . 'res/js/jquery.galleriffic.js"></script>';
		$GLOBALS['TSFE']->additionalHeaderData['zhdk_madekplayer_css'] = '<link  media="screen" rel="stylesheet" type="text/css"  href="' . t3lib_extMgm::siteRelPath('zhdk_madekplayer') . 'res/zhdk_madekplayer.css" />';
		foreach($result as $row) {
			$imageList .= '
				<li>
					<a class="thumb" href="http://test.madek.zhdk.ch/media_resources/' . $row['madekid'] . '/image?size=large" title="' . htmlentities($row['title'])  . '">
						<img alt="' . htmlentities($row['title']) . '" src="http://test.madek.zhdk.ch/media_resources/' . $row['madekid'] . '/image?size=small" />
					</a>
					<div class="caption">
						' . htmlentities($row['title'])  . '
					</div>
				</li>';
		}
		//prevent problems with multiple galleries on the same page
		$randomIndex = rand();
		$html = '
<div class="zhdk_madekplayer-galleriffic">
	<div id="zhdk_madekplayer-controls-' . $randomIndex . '"></div>
	<!--<div id="zhdk_madekplayer-loading-' . $randomIndex . '"></div>-->
	<div class="zhdk_madekplayer-slideshow" id="zhdk_madekplayer-slideshow-' . $randomIndex . '"></div>
	<div id="zhdk_madekplayer-caption-' . $randomIndex . '"></div>
	<div id="zhdk_madekplayer-thumbs-' . $randomIndex . '">
		<ul class="thumbs noscript">
			' . $imageList . '
		</ul>
	</div>
</div>
<script type="text/javascript">
' . "
jQuery(document).ready(function($) {
    var gallery = $('#zhdk_madekplayer-thumbs-$randomIndex').galleriffic({
        /*delay:                     3000, // in milliseconds
        numThumbs:                 20, // The number of thumbnails to show page
        preloadAhead:              40, // Set to -1 to preload all images
        enableTopPager:            false,
        enableBottomPager:         true,
        maxPagesToShow:            7,  // The maximum number of pages to display in either the top or bottom pager*/
        imageContainerSel:         '#zhdk_madekplayer-slideshow-$randomIndex', // The CSS selector for the element within which the main slideshow image should be rendered
        //controlsContainerSel:      '#zhdk_madekplayer-controls-$randomIndex', // The CSS selector for the element within which the slideshow controls should be rendered
        captionContainerSel:       '#zhdk_madekplayer-caption-$randomIndex', // The CSS selector for the element within which the captions should be rendered
        /*loadingContainerSel:       '', // The CSS selector for the element within which should be shown when an image is loading
        renderSSControls:          true, // Specifies whether the slideshow's Play and Pause links should be rendered
        renderNavControls:         true, // Specifies whether the slideshow's Next and Previous links should be rendered
        playLinkText:              'Play',
        pauseLinkText:             'Pause',
        prevLinkText:              'Previous',
        nextLinkText:              'Next',
        nextPageLinkText:          'Next &rsaquo;',
        prevPageLinkText:          '&lsaquo; Prev',
        enableHistory:             false, // Specifies whether the url's hash and the browser's history cache should update when the current slideshow image changes
        enableKeyboardNavigation:  true, // Specifies whether keyboard navigation is enabled
        autoStart:                 false, // Specifies whether the slideshow should be playing or paused when the page first loads
        syncTransitions:           false, // Specifies whether the out and in transitions occur simultaneously or distinctly
        defaultTransitionDuration: 1000, // If using the default transitions, specifies the duration of the transitions
        onSlideChange:             undefined, // accepts a delegate like such: function(prevIndex, nextIndex) { ... }
        onTransitionOut:           undefined, // accepts a delegate like such: function(slide, caption, isSync, callback) { ... }
        onTransitionIn:            undefined, // accepts a delegate like such: function(slide, caption, isSync) { ... }
        onPageTransitionOut:       undefined, // accepts a delegate like such: function(callback) { ... }
        onPageTransitionIn:        undefined, // accepts a delegate like such: function() { ... }
        onImageAdded:              undefined, // accepts a delegate like such: function(imageData, li) { ... }
        onImageRemoved:            undefined  // accepts a delegate like such: function(imageData, li) { ... }*/
    });
	console.log('test1');
});
</script>
";
		return $html;
//		die(print_r($result));
	}

	/**
	 * Shows a list of database entries
	 *
	 * @param	string		$content: content of the PlugIn
	 * @param	array		$conf: PlugIn Configuration
	 * @return	HTML list of table entries
	 */
	function listView($content, $conf) {
		$this->conf = $conf;  // Setting the TypoScript passed to this function in $this->conf
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();  // Loading the LOCAL_LANG values

		$lConf = $this->conf['listView.']; // Local settings for the listView function

		if ($this->piVars['showUid']) { // If a single element should be displayed:
			$this->internal['currentTable'] = 'tx_zhdkmadekplayer_gallery';
			$this->internal['currentRow'] = $this->pi_getRecord('tx_zhdkmadekplayer_gallery', $this->piVars['showUid']);

			$content = $this->singleView($content, $conf);
			return $content;
		} else {
			$items = array(
				'1' => $this->pi_getLL('list_mode_1', 'Mode 1'),
				'2' => $this->pi_getLL('list_mode_2', 'Mode 2'),
				'3' => $this->pi_getLL('list_mode_3', 'Mode 3'),
			);
			if (!isset($this->piVars['pointer']))
				$this->piVars['pointer'] = 0;
			if (!isset($this->piVars['mode']))
				$this->piVars['mode'] = 1;

			// Initializing the query parameters:
			list($this->internal['orderBy'], $this->internal['descFlag']) = explode(':', $this->piVars['sort']);
			$this->internal['results_at_a_time'] = t3lib_div::intInRange($lConf['results_at_a_time'], 0, 1000, 3);  // Number of results to show in a listing.
			$this->internal['maxPages'] = t3lib_div::intInRange($lConf['maxPages'], 0, 1000, 2);
			;  // The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
			$this->internal['searchFieldList'] = 'title,description';
			$this->internal['orderByList'] = 'uid,title';

			// Get number of records:
			$res = $this->pi_exec_query('tx_zhdkmadekplayer_gallery', 1);
			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);

			// Make listing query, pass query to SQL database:
			$res = $this->pi_exec_query('tx_zhdkmadekplayer_gallery');
			$this->internal['currentTable'] = 'tx_zhdkmadekplayer_gallery';

			// Put the whole list together:
			$fullTable = ''; // Clear var;
			#	$fullTable.=t3lib_div::view_array($this->piVars);	// DEBUG: Output the content of $this->piVars for debug purposes. REMEMBER to comment out the IP-lock in the debug() function in t3lib/config_default.php if nothing happens when you un-comment this line!
			// Adds the mode selector.
			$fullTable.=$this->pi_list_modeSelector($items);

			// Adds the whole list table
			$fullTable.=$this->pi_list_makelist($res);

			// Adds the search box:
			$fullTable.=$this->pi_list_searchBox();

			// Adds the result browser:
			$fullTable.=$this->pi_list_browseresults();

			// Returns the content from the plugin.
			return $fullTable;
		}
	}

	/**
	 * Display a single item from the database
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	HTML of a single database entry
	 */
	function singleView($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();


		// This sets the title of the page for use in indexed search results:
		if ($this->internal['currentRow']['title'])
			$GLOBALS['TSFE']->indexedDocTitle = $this->internal['currentRow']['title'];

		$content = '<div' . $this->pi_classParam('singleView') . '>
			<H2>Record "' . $this->internal['currentRow']['uid'] . '" from table "' . $this->internal['currentTable'] . '":</H2>
			<table>
				<tr>
					<td nowrap="nowrap" valign="top"' . $this->pi_classParam('singleView-HCell') . '><p>' . $this->getFieldHeader('title') . '</p></td>
					<td valign="top"><p>' . $this->getFieldContent('title') . '</p></td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="top"' . $this->pi_classParam('singleView-HCell') . '><p>' . $this->getFieldHeader('description') . '</p></td>
					<td valign="top"><p>' . $this->getFieldContent('description') . '</p></td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="top"' . $this->pi_classParam('singleView-HCell') . '><p>' . $this->getFieldHeader('type') . '</p></td>
					<td valign="top"><p>' . $this->getFieldContent('type') . '</p></td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="top"' . $this->pi_classParam('singleView-HCell') . '><p>' . $this->getFieldHeader('items') . '</p></td>
					<td valign="top"><p>' . $this->getFieldContent('items') . '</p></td>
				</tr>
				<tr>
					<td nowrap' . $this->pi_classParam('singleView-HCell') . '><p>Last updated:</p></td>
					<td valign="top"><p>' . date('d-m-Y H:i', $this->internal['currentRow']['tstamp']) . '</p></td>
				</tr>
				<tr>
					<td nowrap' . $this->pi_classParam('singleView-HCell') . '><p>Created:</p></td>
					<td valign="top"><p>' . date('d-m-Y H:i', $this->internal['currentRow']['crdate']) . '</p></td>
				</tr>
			</table>
		<p>' . $this->pi_list_linkSingle($this->pi_getLL('back', 'Back'), 0) . '</p></div>' .
				$this->pi_getEditPanel();

		return $content;
	}

	/**
	 * Returns a single table row for list view
	 *
	 * @param	integer		$c: Counter for odd / even behavior
	 * @return	A HTML table row
	 */
	function pi_list_row($c) {
		$editPanel = $this->pi_getEditPanel();
		if ($editPanel)
			$editPanel = '<TD>' . $editPanel . '</TD>';

		return '<tr' . ($c % 2 ? $this->pi_classParam('listrow-odd') : '') . '>
				<td><p>' . $this->getFieldContent('uid') . '</p></td>
				<td valign="top"><p>' . $this->getFieldContent('title') . '</p></td>
				<td valign="top"><p>' . $this->getFieldContent('type') . '</p></td>
				<td valign="top"><p>' . $this->getFieldContent('items') . '</p></td>
			</tr>';
	}

	/**
	 * Returns a table row with column names of the table
	 *
	 * @return	A HTML table row
	 */
	function pi_list_header() {
		return '<tr' . $this->pi_classParam('listrow-header') . '>
				<td><p>' . $this->getFieldHeader_sortLink('uid') . '</p></td>
				<td><p>' . $this->getFieldHeader_sortLink('title') . '</p></td>
				<td nowrap><p>' . $this->getFieldHeader('type') . '</p></td>
				<td nowrap><p>' . $this->getFieldHeader('items') . '</p></td>
			</tr>';
	}

	/**
	 * Returns the content of a given field
	 *
	 * @param	string		$fN: name of table field
	 * @return	Value of the field
	 */
	function getFieldContent($fN) {
		switch ($fN) {
			case 'uid':
				return $this->pi_list_linkSingle($this->internal['currentRow'][$fN], $this->internal['currentRow']['uid'], 1); // The "1" means that the display of single items is CACHED! Set to zero to disable caching.
				break;
			case "title":
				// This will wrap the title in a link.
				return $this->pi_list_linkSingle($this->internal['currentRow']['title'], $this->internal['currentRow']['uid'], 1);
				break;
			default:
				return $this->internal['currentRow'][$fN];
				break;
		}
	}

	/**
	 * Returns the label for a fieldname from local language array
	 *
	 * @param	[type]		$fN: ...
	 * @return	[type]		...
	 */
	function getFieldHeader($fN) {
		switch ($fN) {
			case "title":
				return $this->pi_getLL('listFieldHeader_title', '<em>title</em>');
				break;
			default:
				return $this->pi_getLL('listFieldHeader_' . $fN, '[' . $fN . ']');
				break;
		}
	}

	/**
	 * Returns a sorting link for a column header
	 *
	 * @param	string		$fN: Fieldname
	 * @return	The fieldlabel wrapped in link that contains sorting vars
	 */
	function getFieldHeader_sortLink($fN) {
		return $this->pi_linkTP_keepPIvars($this->getFieldHeader($fN), array('sort' => $fN . ':' . ($this->internal['descFlag'] ? 0 : 1)));
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/zhdk_madekplayer/pi1/class.tx_zhdkmadekplayer_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/zhdk_madekplayer/pi1/class.tx_zhdkmadekplayer_pi1.php']);
}
?>