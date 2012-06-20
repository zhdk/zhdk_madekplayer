<?php

// include the interface file
if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
	t3lib_div::requireOnce(t3lib_div::getFileAbsFileName('typo3/sysext/cms/layout/interfaces/interface.tx_cms_layout_tt_content_drawitemhook.php'));
} elseif (!interface_exists('tx_cms_layout_tt_content_drawItemHook')) {
	
	// dummy interface definition to prevent php errors
	interface tx_cms_layout_tt_content_drawItemHook {
		
	}
	
}

class tx_hmtfwperson_pi1_drawitem implements tx_cms_layout_tt_content_drawItemHook {
	
	/**
	 * Preprocesses the preview rendering of a content element.
	 * renders m-tag preview.
	 *
	 * @param	tx_cms_layout		$parentObject: Calling parent object
	 * @param	boolean				$drawItem: Whether to draw the item using the default functionalities
	 * @param	string				$headerContent: Header content
	 * @param	string				$itemContent: Item content
	 * @param	array				$row: Record row of tt_content
	 * @return	void
	 */
	public function preProcess(tx_cms_layout &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row) {
		if ($row['CType'] != 'hmtfwperson_pi1') {
			$drawItem= true;
			return;
		}

		$headerContent= '';
		
		if (strlen(trim($row['header'])) == 0 ) {
			$name= $row['tx_hmtfwperson_name_hidden'];
		} else {
			// name overwrite
			$name= $row['header'];
		}
		
		$id= (int)$row['tx_hmtfwperson_id'];
		$content= '';
		
		if ($id > 0) {
			$img_url= 
				t3lib_div::getIndpEnv('TYPO3_SITE_URL').'?person/foto&id='.$id.
				'&width=50&height=57&fit=1&filters=BWF&compressionlevel=85&sharpen=unsharp(0)&keepmeta=0';
			
			$content.= '<img src="'.$img_url.'"><br />';
		}
		
		$content.= htmlspecialchars($name);

		$itemContent= $parentObject->linkEditContent($content, $row);
		$drawItem= true;
	}

}

?>