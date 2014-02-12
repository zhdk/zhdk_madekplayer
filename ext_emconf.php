<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "zhdk_madekplayer".
 *
 * Auto generated 12-02-2014 10:57
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'ZHdK MAdeK Player',
	'description' => 'Provides a frontend plugin to display a gallery of single set from a MAdeK server (https://github.com/zhdk/madek).',
	'category' => 'plugin',
	'author' => 'Beat Rohrer, Manuel Farina',
	'author_email' => 'service.itz@zhdk.ch',
	'author_company' => 'Zurich University of the Arts (ZHdK)',
	'shy' => 0,
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '1.0.3',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'php' => '5.0.0-5.4.99',
			'typo3' => '4.5.0-4.7.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:21:{s:26:"class.zhdk_madekplayer.php";s:4:"ceee";s:21:"ext_conf_template.txt";s:4:"fddd";s:12:"ext_icon.gif";s:4:"3f61";s:17:"ext_localconf.php";s:4:"6f63";s:14:"ext_tables.php";s:4:"e2cb";s:7:"LICENSE";s:4:"d433";s:13:"locallang.xml";s:4:"4625";s:9:"README.md";s:4:"ae96";s:14:"pi1/ce_wiz.gif";s:4:"3f61";s:36:"pi1/class.tx_zhdkmadekplayer_pi1.php";s:4:"6aa0";s:44:"pi1/class.tx_zhdkmadekplayer_pi1_wizicon.php";s:4:"d81c";s:23:"pi1/flexform_ds_pi1.xml";s:4:"835c";s:17:"pi1/locallang.xml";s:4:"15b1";s:27:"res/css/zhdkmadekplayer.css";s:4:"8738";s:22:"res/html/template.html";s:4:"5b36";s:30:"res/html/template_caption.html";s:4:"cb2d";s:28:"res/js/jquery.galleriffic.js";s:4:"d968";s:20:"static/constants.txt";s:4:"a8f6";s:16:"static/setup.txt";s:4:"444a";s:34:"wizards/class.madek_set_picker.php";s:4:"f856";s:21:"wizards/locallang.xml";s:4:"2fdc";}',
	'suggests' => array(
	),
);

?>