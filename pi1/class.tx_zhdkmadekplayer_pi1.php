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
	/**
	 * Init the class and get all the config data
	 * @return void;
	 */
	function init() {
		$this->pi_setPiVarDefaults();
		$this->pi_initPIflexForm(); // Init and get the flexform data of the plugin
		$this->lConf = array(); // Setup our storage array...
		// Assign the flexform data to a local variable for easier access
		$piFlexForm = $this->cObj->data['pi_flexform'];
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);

		$this->madekServer = rtrim($this->extConf['madekServer'], '/');
		if(empty($this->madekServer)) {
			$this->madekServer = 'http://medienarchiv.zhdk.ch/';
		}

		$this->maxImagesPerGallery = $this->extConf['maxImagesForGallery'];
		if(empty($this->maxImagesPerGallery)) {
			$this->maxImagesPerGallery = 100;
		}
		// set random number for this content element
		// this prohibits problems problems with multiple plugins on the same page
		$this->random = rand();
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

	/**
	 * Get media_resources from MAdeK. Because of pagination we have to do recusrive calls.
	 * Be aware that we don't take into account that we fetch all medias buyt only display images.
	 * @param type $madekServer the madek server url
	 * @param type $madekSetId the set id to fetch the children from
	 * @param int $limit how many medias do we fetch at max
	 * @param int $page = 1: pagination
	 * @return array media_resources
	 */
	static function fetchData($madekServer, $madekSetId, $limit, $page = 1) {
		$json_url = $madekServer . '/media_resources.json?'.
			'ids=' . $madekSetId . '&'.
			'with[children][public]=true&'.
			'with[children][with][media_type]=true&'.
			'with[children][with][meta_data][meta_context_names][]=copyright&'.
			'with[children][with][meta_data][meta_key_ids][]=title&'.
			'with[children][with][meta_data][meta_key_ids][]=subtitle&'.
			'with[children][with][meta_data][meta_key_ids][]=public%20caption&'.
			'with[children][with][meta_data][meta_key_ids][]=author&'.
			'with[children][with][meta_data][meta_key_ids][]=portrayed%20object%20dates&'.
			'with[children][pagination][page]=' . $page;
		$json = file_get_contents($json_url);
		$data = json_decode($json, TRUE);
		$media_resources = $data['media_resources'][0]['children']['media_resources'];
		// check if we do recursive call
		if(
			// we get pagination for children
			!empty($data['media_resources'][0]['children']['pagination'])
			// we haven't reache limit yet
			&& $data['media_resources'][0]['children']['pagination']['per_page'] * $data['media_resources'][0]['children']['pagination']['page'] < $limit 
			// we haven't seen all media yet
			&& $data['media_resources'][0]['children']['pagination']['per_page'] * $data['media_resources'][0]['children']['pagination']['page'] < $data['media_resources'][0]['children']['pagination']['total']) {
			// do the recursive call
			$recursiveMediaResources = tx_zhdkmadekplayer_pi1::fetchData(
				$madekServer,
				$madekSetId,
				$limit - $data['media_resources'][0]['children']['pagination']['per_page'],
				++$page);
			// merge the arrays
			$media_resources = array_merge($media_resources, $recursiveMediaResources);
		}
		return $media_resources;
	}
	
	/**
	 * Display normal gallery view
	 * @return void;
	 */
	function galleryView() {
		$this->pi_loadLL();
		$this->madekSetId = $this->lConf['madek_set'];
		if(empty($this->madekSetId)) {
			return;
		}
		$imageList = '';
		//get madek set content
		$this->media_resources = $this->fetchData($this->madekServer, $this->madekSetId, $this->maxImagesPerGallery);
		$GLOBALS['TSFE']->additionalHeaderData['galleriffic_js'] = '<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath('zhdk_madekplayer') . 'res/js/jquery.galleriffic.js"></script>';
		if(empty($this->lConf['thumbnails_per_page'])) {
			$GLOBALS['TSFE']->setCSS('hide thumbnails', 'div#zhdk_madekplayer-'. $this->random . ' .zhdk_madekplayer-thumbs * {display: none;}');
		}
		

		//get template
		$this->templateFile = $this->cObj->fileResource($this->conf['templateFile']);
		$subparts['template'] = $this->cObj->getSubpart($this->templateFile, '###TEMPLATE1###');
		$subparts['row'] = $this->cObj->getSubpart($subparts['template'], '###ROW###');
		
		//insert the separate caption template file
		$this->templateFileCaption = $this->cObj->fileResource($this->conf['templateFileCaption']);
		$caption = $this->cObj->getSubpart($this->templateFileCaption, '###CAPTION_TEMPLATE###');
		$subparts['row'] = $this->cObj->substituteMarkerArrayCached(
						$subparts['row'],
						array(
							'###CAPTION###' => $caption,
						)
					);

		$subparts['title'] = $this->cObj->getSubpart($subparts['row'], '###PART_TITLE_AND_DATE###');
		$subparts['subtitle'] = $this->cObj->getSubpart($subparts['row'], '###PART_SUBTITLE###');
		$subparts['public caption'] = $this->cObj->getSubpart($subparts['row'], '###PART_PUBLIC_CAPTION###');
		$subparts['author'] = $this->cObj->getSubpart($subparts['row'], '###PART_AUTHOR###');
		$subparts['copyright'] = $this->cObj->getSubpart($subparts['row'], '###PART_COPYRIGHT###');
		$contentItem = '';
		// set data for each image
		if (is_array($this->media_resources)) {
			foreach($this->media_resources as $item) {
				if(strtolower($item['type']) != 'media_entry') {
					continue;
				}
				if(strtolower($item['media_type']) != 'image') {
					continue;
				}
				$row_subparts = array(
						'###PART_TITLE_AND_DATE###' => '',
						'###PART_SUBTITLE###' => '',
						'###PART_PUBLIC_CAPTION###' => '',
						'###PART_AUTHOR###' => '',
						'###PART_COPYRIGHT###' => '',
					);
				$description = '';
				$title = 'Media Entry no. ' . $item['id'];
				if(isset($item['meta_data'])) {
					$title = zhdk_madekplayer::getMetaDataValue('title', $item['meta_data']);
					if(!empty($title) && $this->lConf['show_title']) {
						$date = zhdk_madekplayer::getMetaDataValue('portrayed object dates', $item['meta_data']);
						$row_subparts['###PART_TITLE_AND_DATE###'] = $this->cObj->substituteMarkerArrayCached(
							$subparts['title'],
							array(
								'###TITLE###' => $title,
								'###DATE###' => (!empty($date) ? '(' . $date . ')' : '')
							)
						);
	
					}
					$subtitle = zhdk_madekplayer::getMetaDataValue('subtitle', $item['meta_data']);
					if(!empty($subtitle) && $this->lConf['show_subtitle']) {
						$row_subparts['###PART_SUBTITLE###'] = $this->cObj->substituteMarkerArrayCached(
							$subparts['subtitle'],
							array(
								'###SUBTITLE###' => $subtitle,
							)
						);
					}
					$publicCaption = zhdk_madekplayer::getMetaDataValue('public caption', $item['meta_data']);
					if(!empty($publicCaption) && $this->lConf['show_public_caption']) {
						$row_subparts['###PART_PUBLIC_CAPTION###'] = $this->cObj->substituteMarkerArrayCached(
							$subparts['public caption'],
							array(
								'###PUBLIC_CAPTION###' => $publicCaption,
							)
						);
					}
					$author = zhdk_madekplayer::getMetaDataValue('author', $item['meta_data']);
					if(!empty($author) && $this->lConf['show_author']) {
						$row_subparts['###PART_AUTHOR###'] = $this->cObj->substituteMarkerArrayCached(
							$subparts['author'],
							array(
								'###AUTHOR###' => $author,
							)
						);
					}
					if($this->lConf['show_copyright']) {
						$notice = zhdk_madekplayer::getMetaDataValue('copyright notice', $item['meta_data']);
						$status = zhdk_madekplayer::getMetaDataValue('copyright status', $item['meta_data']);
						$usage = zhdk_madekplayer::getMetaDataValue('copyright usage', $item['meta_data']);
						$url = zhdk_madekplayer::getMetaDataValue('copyright url', $item['meta_data']);
						$row_subparts['###PART_COPYRIGHT###'] = $this->cObj->substituteMarkerArrayCached(
							$subparts['copyright'],
							array(
								'###COPYRIGHT_NOTICE###' => $notice,
								'###COPYRIGHT_STATUS###' => $status,
								'###COPYRIGHT_USAGE###' => $usage,
								'###COPYRIGHT_URL###' => $url,
							)
						);
					}
					
				}
				$markerArray['###TITLE###'] = $title;
				$image_size = 'large';
				if(($this->lConf['max_image_width'] > 620 && $this->lConf['player_width'] > 620) 
					|| $this->lConf['max_image_height'] > 500) {
					$image_size = 'x_large';
				} else if (($this->lConf['max_image_width'] > 300 && $this->lConf['player_width'] > 300) 
					|| $this->lConf['max_image_height'] > 300) {
					$image_size = 'large';
				} else if (($this->lConf['max_image_width'] > 125 && $this->lConf['player_width'] > 125) 
					|| $this->lConf['max_image_height'] > 125) {
					$image_size = 'medium';
				} else if (($this->lConf['max_image_width'] > 100 && $this->lConf['player_width'] > 100) 
					|| $this->lConf['max_image_height'] > 100) {
					$image_size = 'small_125';
				} else {
					$image_size = 'small';
				}
				$markerArray['###IMAGE_URL###'] = $this->madekServer . '/media_resources/' . $item['id'] . '/image?size=' . $image_size;
				$markerArray['###THUMBNAIL_URL###'] = $this->madekServer . '/media_resources/' . $item['id'] . '/image?size=small';
				$contentItem .= $this->cObj->substituteMarkerArrayCached($subparts['row'], $markerArray, $row_subparts);
			}
		}
		$subpartArray['###CONTENT###'] = $contentItem;
		$markerArray['###RANDOM_INDEX###'] = $this->random;
		$markerArray['###PLAY###'] = $this->pi_getLL('tx_zhdkmadekplayer_pi1.play');
		$markerArray['###PAUSE###'] = $this->pi_getLL('tx_zhdkmadekplayer_pi1.pause');
		$markerArray['###PREV###'] = $this->pi_getLL('tx_zhdkmadekplayer_pi1.prev');
		$markerArray['###NEXT###'] = $this->pi_getLL('tx_zhdkmadekplayer_pi1.next');
		$markerArray['###PREV_LINK###'] = '&lsaquo; ' . $this->pi_getLL('tx_zhdkmadekplayer_pi1.prev');
		$markerArray['###NEXT_LINK###'] = $this->pi_getLL('tx_zhdkmadekplayer_pi1.next') . ' &rsaquo;';
		$markerArray['###THUMBNAIL_COUNT###'] = $this->lConf['thumbnails_per_page'];
		$markerArray['###MAX_IMAGE_WIDTH###'] = $this->lConf['max_image_width'];
		$markerArray['###MAX_IMAGE_HEIGHT###'] = $this->lConf['max_image_height'];
		$markerArray['###PLAYER_WIDTH###'] = $this->lConf['player_width'];
		$markerArray['###MADEK_BACKGROUND###'] = $this->lConf['madek_background'];
		$markerArray['###MADEK_BORDER###'] = $this->lConf['madek_border'];
		$content = $this->cObj->substituteMarkerArrayCached($subparts['template'], $markerArray, $subpartArray);
		return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/zhdk_madekplayer/pi1/class.tx_zhdkmadekplayer_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/zhdk_madekplayer/pi1/class.tx_zhdkmadekplayer_pi1.php']);
}
?>