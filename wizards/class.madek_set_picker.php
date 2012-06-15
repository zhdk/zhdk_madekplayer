<?php
/**
 * Madekpicker wizard
 *
 *
 * @author	Beat Rohrer <beat.rohrer@zhdk.ch>
 */

define('TYPO3_MOD_PATH', '../typo3conf/ext/zhdk_madekplayer/wizards/');
$BACK_PATH='../../../../typo3/';

require ('../../../../typo3/init.php');
require ('../../../../typo3/template.php');
require_once('../class.zhdk_madekplayer.php');

// $BACK_PATH='';
$LANG->includeLLFile('EXT:zhdk_madekplayer/wizards/locallang.xml');

/**
 * Script Class for colorpicker wizard
 *
 * @author	Mathias Schreiber <schreiber@wmdb.de>
 * @author	Peter Kühn <peter@kuehn.com>
 * @author	Kasper Skårhøj <typo3@typo3.com>
 * @package TYPO3
 * @subpackage core
 */
class Madek_set_picker {

		// GET vars:
	var $P;				// Wizard parameters, coming from TCEforms linking to the wizard.

	/**
	 * document template object
	 *
	 * @var fullDoc
	 */
	var $doc;
	var $content;				// Accumulated content.




	/**
	 * Initialises the Class
	 *
	 * @return	void
	 */
	function init()	{
		global $BACK_PATH, $LANG;
		$this->extKey = 'zhdk_madekplayer';
		$this->LANG = $LANG;

		// Setting GET vars (used in frameset script):
		$this->P = t3lib_div::_GP('P',0);
		$this->additionalGetParameter = t3lib_div::implodeArrayForUrl('P', $this->P);
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		$this->madekServer = rtrim($this->extConf['madekServer'], '/');
		$this->pageURL = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . t3lib_extMgm::siteRelPath($this->extKey) . 'wizards/class.madek_set_picker.php';

		$this->uid = $this->P['uid'];
		$this->pid = $this->P['pid'];

			// Initialize document object:
		$this->doc = t3lib_div::makeInstance('mediumDoc');
		$this->doc->backPath = $BACK_PATH;
		$this->doc->JScode .= '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>';
		$this->doc->JScode .= $this->doc->wrapScriptTags('
			function zhdk_madekplayer_picker_writeback(id, title) {
				window.opener.document.editform["data[tt_content][' . $this->uid . '][pi_flexform][data][sDEF][lDEF][madek_set][vDEF]"].value=id;
				window.opener.document.editform["data[tt_content][' . $this->uid . '][pi_flexform][data][sDEF][lDEF][madek_set][vDEF]_hr"].value=id;
				window.close();
			}
			$(document).ready(function() {
				$(\'#search-field\').focus();
			});
		');
		$this->doc->inDocStyles .= "
body {
	padding: 10px;
}

p {
	margin: 0 0 10px;
}

.result-list td,
.result-list th {
	padding: 5px;
}

.result-list td {
	height: 103px;
}

.result-list td:first-child {
	width: 115px:
}

div#search-results h2 {
	margin-top: 20px;
}

div.pagination {
	margin: 0 0 10px;
}

div.pagination p {
	margin: 0;
}

div.pagination a {
	text-decoration: underline;
}

tr.result-row {
	cursor: pointer;
}

/*tr.result-row:nth-child(even) {
	background-color: #cdcdcd;
}*/

tr.result-row td {
	border-top: 1px solid #cdcdcd;
	padding-top: 10px;
	/*border-top: 1px solid #000000;*/
}

table {
	border-collapse:collapse;
}
		";
		$this->currentPage = (int)t3lib_div::_GP('pageOffset');
		$this->currentPage = ((empty($this->currentPage)) ? 1 : $this->currentPage);
		$this->searchQuery = t3lib_div::_GP('search-query');
		if(!empty($this->searchQuery)) {
			$this->fetchResults();
		} else {
			$this->searchQuery = '';
		}
		$this->searchPerformed = !empty($this->searchQuery);
		$this->hasResults = $this->searchPerformed && (count($this->data['media_resources']) > 0);

			// Start page:
		$this->content.=$this->doc->startPage($this->LANG->getLL('tx_zhdkmadekplayer.set_wizard.title'));
	}

	/**
	 * Fetch data for $searchQuery
	 * @param type $searchQuery string of search terms
	 * @return type array of Results
	 */
	protected function fetchResults() {
		$json_url = $this->madekServer . "/media_resources.json?type=media_sets&query=" . urlencode($this->searchQuery) . "&with[media_type]=true&with[meta_data][meta_key_names][]=title&with[image][as]=base64&with[image][size]=small&with[meta_data][meta_key_names][]=author&page=" . urlencode($this->currentPage);
		$json = file_get_contents($json_url);
		if($json === FALSE) {
			t3lib_div::sysLog('Could not connect to madek server "' . $this->madekServer . '".', $this->extKey, 3);
			die('Could not connect to madek server "' . $this->madekServer . '". Please contact your Typo3 admin.');
		}
		$this->data = json_decode($json, TRUE);
	}

	/**
	 * Main Method, rendering either colorpicker or frameset depending on ->showPicker
	 *
	 * @return	void
	 */
	function main()	{
		$content = '<h1>'. $this->LANG->getLL('tx_zhdkmadekplayer.set_wizard.title') . '</h1>';
		$content .= '<p>'. $this->LANG->getLL('tx_zhdkmadekplayer.set_wizard.intro') . '</p>';
		$searchForm = '<form action="' . $this->pageURL . '?' . $this->additionalGetParameter . '" method="post">
			<table>
				<tr>
					<td>
						<input id="search-field" type="text" name="search-query" value="' . $this->searchQuery . '"/>
					</td>
					<td>
						<input type="submit"/>
					</td>
				</tr>
			</table>
		</form>';
		$resultPane = '';
		if($this->searchPerformed) {
			$resultList = '';
			if($this->hasResults) {
				foreach ($this->data['media_resources'] as $item) {
					if(empty($item['image'])) {
						continue;
					}
					$resultList .= '<tr class="result-row" onclick="javascript:zhdk_madekplayer_picker_writeback(' . (int)$item['id'] . ',\'' .  zhdk_madekplayer::getMetaDataValue('title', $item['meta_data']) . '\')">
									<td>
										' . (!empty($item['image']) ? '<img src="' . $item['image'] . '" />' : '') . '
									</td>
									<td>
										<strong>' . zhdk_madekplayer::getMetaDataValue('title', $item['meta_data']) . '</strong><br/>
										' . zhdk_madekplayer::getMetaDataValue('author', $item['meta_data']) . '
									</td>
									<!--<td>
										' . zhdk_madekplayer::getMetaDataValue('author', $item['meta_data']) . '
									</td>-->
								</tr>';
				}
				$resultList = '
				<table class="result-list">
					<!--<thead>
						<th>&nbsp;</th>
						<th>' . $this->LANG->getLL('tx_zhdkmadekplayer.set_wizard.results-list.header.set-title') . '</th>
						<th>' . $this->LANG->getLL('tx_zhdkmadekplayer.set_wizard.results-list.header.set-author') . '</th>
					</thead>-->
					<tbody>' . $resultList . '</tbody>
				</table>';
			} else {
				$resultList = '<em>' . $this->LANG->getLL('tx_zhdkmadekplayer.set_wizard.results-list.no-results') . '</em>';
			}
			$resultPane = '
				<div id="search-results">
					<h2>' . $this->LANG->getLL('tx_zhdkmadekplayer.set_wizard.results-list.results') . '</h2>
					' . $this->getPagination() . '
					' . $resultList . '
				</div>';

		}
		$content .= $searchForm;
		$content .= $resultPane;
		$content .= $this->getPagination();

				// Output:
		$this->content.=$this->doc->section($this->LANG->getLL('colorpicker_title'), $content, 0,1);
	}

	function getPagination() {
		if(!$this->searchPerformed) {
			// we don't have to do anz pagination if there was no search
			return '';
		}
		// array returned from madek API:
		// [pagination] => Array
		//       (
		//           [total] => 144
		//           [page] => 1
		//           [per_page] => 36
		//           [total_pages] => 4
		//       )
		$pagination = $this->data['pagination'];
		if($pagination['total_pages'] <= 1) {
			// we don't need pagination if we don't have more than 1 page
			return '';	
		}
		$paginationHtml = '<div class="pagination">';
		$paginationHtml .= '<p>' . sprintf($this->LANG->getLL('tx_zhdkmadekplayer.set_wizard.results-list.resultsPagination'), $pagination['page'], $pagination['total_pages']) . '</p>';
		for($page = 1; $page <= $pagination['total_pages']; $page++) {
			if($this->currentPage == $page) {
				$paginationHtml .= $page . ' ';
			} else {
				$paginationHtml .= '<a href="' . $this->pageURL . '?' . $this->additionalGetParameter . '&pageOffset='. $page . '&search-query='. urlencode($this->searchQuery) . '">' . $page . '</a> ';
			}
		}
		$paginationHtml .= '</div>';
		return $paginationHtml;
	}

	/**
	 * Returnes the sourcecode to the browser
	 *
	 * @return	void
	 */
	function printContent()	{
		$this->content .= $this->doc->endPage();
		$this->content = $this->doc->insertStylesAndJS($this->content);
		echo $this->content;
	}
}


if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['typo3/wizard_colorpicker.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['typo3/wizard_colorpicker.php']);
}



// Make instance:
$SOBE = t3lib_div::makeInstance('Madek_set_picker');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();

?>
