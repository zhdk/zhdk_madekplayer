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
		// die(print_r($this->conf));
	}

	function fetchData() {
		$json_url = $this->madekServer . '/media_resources.json?'.
			'ids=' . $this->madekSetId . '&'.
			'with[media_type]=true&'.
			'with[children]=true&'.
			'public=true&'.
			'with[meta_data][meta_context_names][]=copyright&'.
			'with[meta_data][meta_key_names][]=title&'.
			'with[meta_data][meta_key_names][]=subtitle&'.
			'with[meta_data][meta_key_names][]=author&'.
			'with[meta_data][meta_key_names][]=portrayed%20object%20dates';
		$json = file_get_contents($json_url);
		$this->data = json_decode($json, TRUE);
	}
	
	function galleryView() {
		$this->pi_loadLL();
		$this->madekSetId = $this->lConf['madek_set'];
		if(empty($this->madekSetId)) {
			return;
		}
		$imageList = '';
		//get set content
		$this->fetchData();
		$GLOBALS['TSFE']->additionalHeaderData['galleriffic_js'] = '<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath('zhdk_madekplayer') . 'res/js/jquery.galleriffic.js"></script>';
		
		//get template
		$this->templateFile = $this->cObj->fileResource($this->conf['templateFile']);
		$subparts['template'] = $this->cObj->getSubpart($this->templateFile, '###TEMPLATE1###');
		$subparts['row'] = $this->cObj->getSubpart($subparts['template'], '###ROW###');
		$contentItem = '';
		foreach($this->data['media_resources'][0]['children'] as $item) {
			if($item['type'] != 'media_entry') {
				continue;
			}
			if($item['media_type'] != 'Image') {
				continue;
			}
			$markerArray = array (
					'###COPYRIGHT_NOTICE###' => '',
					'###COPYRIGHT_STATUS###' => '',
					'###COPYRIGHT_USAGE###' => '',
					'###COPYRIGHT_URL###' => '',
					'###TITLE###' => '',
					'###DATE###' => '',
					'###SUBTITLE###' => '',
					'###AUTHOR###' => '',
				);
			$description = '';
			$title = 'Media Entry no. ' . $item['id'];
			if(isset($item['meta_data'])) {
				//set meta data
				$tmpTitle = zhdk_madekplayer::getMetaDataValue('title', $item['meta_data']);
				if(!empty($tmpTitle) && $this->lConf['show_title']) {
					$tmpDate = zhdk_madekplayer::getMetaDataValue('portrayed object dates', $item['meta_data']);
					$title = $tmpTitle;
					$markerArray['###TITLE###'] = $tmpDate;
					$markerArray['###DATE###'] = $title;
					$description = '<h3>' . $title . ' ('. $tmpDate . ')</h3>';

				}
				$tmpSubtitle = zhdk_madekplayer::getMetaDataValue('subtitle', $item['meta_data']);
				if(!empty($tmpSubtitle) && $this->lConf['show_subtitle']) {
					$markerArray['###SUBTITLE###'] = $tmpSubtitle;
					$description .= '<p>' . $tmpSubtitle . '</p>';
				}
				$tmpAuthor = zhdk_madekplayer::getMetaDataValue('author', $item['meta_data']);
				if(!empty($tmpAuthor) && $this->lConf['show_author']) {
					$markerArray['###AUTHOR###'] = $tmpAuthor;
					$description .= '<p>' . $tmpAuthor . '</p>';
				}
				if($this->lConf['show_copyright']) {
					$notice = zhdk_madekplayer::getMetaDataValue('copyright notice', $item['meta_data']);
					$status = zhdk_madekplayer::getMetaDataValue('copyright status', $item['meta_data']);
					$usage = zhdk_madekplayer::getMetaDataValue('copyright usage', $item['meta_data']);
					$url = zhdk_madekplayer::getMetaDataValue('copyright url', $item['meta_data']);
					$markerArray['###COPYRIGHT_NOTICE###'] = $notice;
					$markerArray['###COPYRIGHT_STATUS###'] = $status;
					$markerArray['###COPYRIGHT_USAGE###'] = $usage;
					$markerArray['###COPYRIGHT_URL###'] = $url;
					$description .= '<h4>' . $this->pi_getLL('tx_zhdkmadekplayer_pi1.copyright', 'Copyright') . '</h4>
						<p>' . $notice . '<br>' . 
							(!empty($url) ? '<a target="_blank" href="' . $url . '">' : '') . $status . (!empty($url) ? '</a>' : '');
						'</p>';
				}
				
			}
			$markerArray['###IMAGE_URL###'] = $this->madekServer . '/media_resources/' . $item['id'] . '/image?size=large';
			$markerArray['###THUMBNAIL_URL###'] = $this->madekServer . '/media_resources/' . $item['id'] . '/image?size=small';
			$contentItem .= $this->cObj->substituteMarkerArrayCached($subparts['row'], $markerArray);
		}
		$subpartArray['###CONTENT###'] = $contentItem;
		$markerArray['###RANDOM_INDEX###'] = $randomIndex = rand();
		$content = $this->cObj->substituteMarkerArrayCached($subparts['template'], $markerArray, $subpartArray);
		return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/zhdk_madekplayer/pi1/class.tx_zhdkmadekplayer_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/zhdk_madekplayer/pi1/class.tx_zhdkmadekplayer_pi1.php']);
}
?>