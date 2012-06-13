<?php

class zhdk_madekplayer {
	
	/**
	 * Loops through 1st level of array an checks each item if its subarray contains an item with ['name'] == $metadataName.
	 * If yes, it returns the wanted corresponding value
	 * @param type $needle the name of the key to check, array of arrays
	 * @param array $haystack the array to search in
	 * @param type $targetKey name of the Key you want. Default is 'value'
	 * @return type 
	 */
	static function getMetaDataValue($needle, array $haystack, $targetKey = 'value', $htmlEscape = true) {
		$found = null;
		foreach($haystack as $item) {
			if(isset($item['name']) && $item['name'] == $needle) {
				$found = $item[$targetKey];
				break;
			}
		}
		if($found && $htmlEscape) {
			$found = htmlspecialchars($found, ENT_QUOTES | ENT_XHTML, 'UTF-8');
		}
		return $found;
	}
}
?>
