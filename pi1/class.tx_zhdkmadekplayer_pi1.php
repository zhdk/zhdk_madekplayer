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
	}

	function init() {
		$this->pi_setPiVarDefaults();
		$this->pi_initPIflexForm(); // Init and get the flexform data of the plugin
		$this->lConf = array(); // Setup our storage array...
		// Assign the flexform data to a local variable for easier access
		$piFlexForm = $this->cObj->data['pi_flexform'];
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		$this->madekServer = rtrim($this->extConf['madekServer'], '/');
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
		if(empty($madekSetId)) {
			return;
		}
		$imageList = '';
		//get set content
		$json_url = "$this->madekServer/media_resources.json?ids=$madekSetId&with[media_type]=true&with[children]=true&public=true&with[meta_data][meta_key_names][]=title&with[meta_data][meta_key_names][]=subtitle&with[meta_data][meta_context_names][]=copyright";
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
				$tmpAuthor = zhdk_madekplayer::getMetaDataValue('description author', $item['meta_data']);
				if(!empty($tmpAuthor) && $this->lConf['show_author']) {
					$description .= '<p>' . $tmpAuthor . '</p>';
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
					<a class="thumb" href="' . $this->madekServer . '/media_resources/' . $item['id'] . '/image?size=large" title="' . $title  . '">
						<img alt="' . $title . '" src="' . $this->madekServer . '/media_resources/' . $item['id'] . '/image?size=small" />
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
		return $html;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/zhdk_madekplayer/pi1/class.tx_zhdkmadekplayer_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/zhdk_madekplayer/pi1/class.tx_zhdkmadekplayer_pi1.php']);
}
?>