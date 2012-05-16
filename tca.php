<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_zhdkmadekintegration_gallery'] = array (
	'ctrl' => $TCA['tx_zhdkmadekintegration_gallery']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,fe_group,title,description,type'
	),
	'feInterface' => $TCA['tx_zhdkmadekintegration_gallery']['feInterface'],
	'columns' => array (
		't3ver_label' => array (		
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'max'  => '30',
			)
		),
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l10n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_zhdkmadekintegration_gallery',
				'foreign_table_where' => 'AND tx_zhdkmadekintegration_gallery.pid=###CURRENT_PID### AND tx_zhdkmadekintegration_gallery.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(3, 14, 7, 1, 19, 2038),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'title' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_gallery.title',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
			)
		),
		'description' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_gallery.description',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'type' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_gallery.type',		
			'config' => array (
				'type' => 'select',
				'items' => array (
					array('LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_gallery.type.I.0', '0'),
					array('LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_gallery.type.I.1', '1'),
					array('LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_gallery.type.I.2', '2'),
				),
				'size' => 1,	
				'maxitems' => 1,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title;;;;2-2-2, description;;;richtext[]:rte_transform[mode=ts];3-3-3, type')
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime, endtime, fe_group')
	)
);



$TCA['tx_zhdkmadekintegration_item'] = array (
	'ctrl' => $TCA['tx_zhdkmadekintegration_item']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,fe_group,madekid,type,mediatype,caption,gallery'
	),
	'feInterface' => $TCA['tx_zhdkmadekintegration_item']['feInterface'],
	'columns' => array (
		't3ver_label' => array (		
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'max'  => '30',
			)
		),
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l10n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_zhdkmadekintegration_item',
				'foreign_table_where' => 'AND tx_zhdkmadekintegration_item.pid=###CURRENT_PID### AND tx_zhdkmadekintegration_item.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(3, 14, 7, 1, 19, 2038),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'madekid' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_item.madekid',		
			'config' => array (
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'range'    => array (
					'upper' => '1000',
					'lower' => '10'
				),
				'default' => 0
			)
		),
		'type' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_item.type',		
			'config' => array (
				'type' => 'select',
				'items' => array (
					array('LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_item.type.I.0', '0'),
					array('LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_item.type.I.1', '1'),
				),
				'size' => 1,	
				'maxitems' => 1,
			)
		),
		'mediatype' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_item.mediatype',		
			'config' => array (
				'type' => 'select',
				'items' => array (
					array('LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_item.mediatype.I.0', '0'),
					array('LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_item.mediatype.I.1', '1'),
					array('LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_item.mediatype.I.2', '2'),
				),
				'size' => 1,	
				'maxitems' => 1,
			)
		),
		'caption' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_item.caption',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'gallery' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:zhdk_madekintegration/locallang_db.xml:tx_zhdkmadekintegration_item.gallery',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'tx_zhdkmadekintegration_gallery',	
				'foreign_table_where' => 'AND tx_zhdkmadekintegration_gallery.pid=###CURRENT_PID### ORDER BY tx_zhdkmadekintegration_gallery.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, madekid, type, mediatype, caption, gallery')
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime, endtime, fe_group')
	)
);
?>