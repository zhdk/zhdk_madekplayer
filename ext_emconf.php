<?php

########################################################################
# Extension Manager/Repository config file for ext "zhdk_madekplayer".
#
# Auto generated 19-06-2012 13:55
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'ZHDK MAdeK Player',
	'description' => 'Provides a frontend plugin to display a gallery of single set from a MAdeK server (https://github.com/zhdk/madek).',
	'category' => 'plugin',
	'author' => 'Beat Rohrer',
	'author_email' => 'beat.rohrer@zhdk.ch',
	'shy' => '',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'Zürcher Hochschule der Künste, ZHdK',
	'version' => '0.9.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'php' => '5.0.0-5.3.99',
			'typo3' => '4.1.0-4.3.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:24:{s:9:"ChangeLog";s:4:"1462";s:9:"README.md";s:4:"0ffa";s:26:"class.zhdk_madekplayer.php";s:4:"ceee";s:21:"ext_conf_template.txt";s:4:"f7f3";s:12:"ext_icon.gif";s:4:"3f61";s:17:"ext_localconf.php";s:4:"6f63";s:14:"ext_tables.php";s:4:"5896";s:13:"locallang.xml";s:4:"4625";s:19:"doc/wizard_form.dat";s:4:"549b";s:20:"doc/wizard_form.html";s:4:"f7e1";s:14:"pi1/ce_wiz.gif";s:4:"02b6";s:36:"pi1/class.tx_zhdkmadekplayer_pi1.php";s:4:"3815";s:44:"pi1/class.tx_zhdkmadekplayer_pi1_wizicon.php";s:4:"2d5f";s:13:"pi1/clear.gif";s:4:"cc11";s:23:"pi1/flexform_ds_pi1.xml";s:4:"2745";s:17:"pi1/locallang.xml";s:4:"b757";s:27:"res/css/zhdkmadekplayer.css";s:4:"9ed7";s:22:"res/html/template.html";s:4:"8e2b";s:30:"res/html/template_caption.html";s:4:"3305";s:28:"res/js/jquery.galleriffic.js";s:4:"8154";s:20:"static/constants.txt";s:4:"a8f6";s:16:"static/setup.txt";s:4:"74a4";s:34:"wizards/class.madek_set_picker.php";s:4:"f3cc";s:21:"wizards/locallang.xml";s:4:"2fdc";}',
	'suggests' => array(
	),
);

?>