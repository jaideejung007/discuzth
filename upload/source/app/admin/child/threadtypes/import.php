<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$sortid = 0;
$newthreadtype = getimportdata('Discuz! Threadtypes');

if($newthreadtype) {
	$idcmp = $searcharr = $replacearr = $indexoption = [];
	$create_tableoption_sql = $separator = '';
	$i = 0;

	$typeoption = [
		'classid' => 0,
		'expiration' => 0,
		'protect' => '',
		'title' => $newthreadtype[0]['name'] ?? cplang('import').'_'.dgmdate(TIMESTAMP,'Ymd'),
		'description' => '',
		'type' => '',
		'unit' => '',
		'rules' => '',
		'permprompt' => '',
	];
	$newclassid = table_forum_typeoption::t()->insert($typeoption, true);

	foreach($newthreadtype as $key => $value) {
		if(!$i) {
			if($newname1 = trim(strip_tags($value['name']))) {
				$findname = 0;
				$tmpnewname1 = $newname1;
				$decline = '_';
				while(!$findname) {
					if(table_forum_threadtype::t()->checkname($tmpnewname1)) {
						$tmpnewname1 = $newname1.$decline.random(6);
						$decline .= '_';
					} else {
						$findname = 1;
					}
				}
				$newname1 = $tmpnewname1;
				$data = [
					'name' => $newname1,
					'description' => dhtmlspecialchars(trim($value['ttdescription'])),
					'special' => 1,
				];
				$sortid = table_forum_threadtype::t()->insert($data, 1);
			}
			$i = 1;

			if(empty($value['identifier'])) {
				cpmsg('threadtype_import_succeed', 'action=threadtypes', 'succeed');
			}
		}

		$typeoption = [
			'classid' => $newclassid,
			'expiration' => $value['tpexpiration'],
			'protect' => $value['protect'],
			'title' => $value['title'],
			'description' => $value['tpdescription'],
			'type' => $value['type'],
			'unit' => $value['unit'],
			'rules' => $value['rules'],
			'permprompt' => $value['permprompt'],
		];

		$findidentifier = 0;
		$tmpidentifier = trim($value['identifier']);
		if(strlen($tmpidentifier) > 40 || !ispluginkey($tmpidentifier)) {
			cpmsg('threadtype_infotypes_optionvariable_invalid', 'action=threadtypes', 'error');
		}
		$decline = '_';
		while(!$findidentifier) {
			if(table_forum_typeoption::t()->fetch_all_by_identifier($tmpidentifier, 0, 1) || in_array(strtoupper($tmpidentifier), $mysql_keywords)) {
				$tmpidentifier = $value['identifier'].$decline.$sortid;
				if(strlen($tmpidentifier) > 40) {
					cpmsg('threadtype_infotypes_optionvariable_invalid', 'action=threadtypes', 'error');
				}
				$decline .= '_';
			} else {
				$findidentifier = 1;
			}
		}
		$typeoption['identifier'] = $tmpidentifier;
		$idcmp[$value['identifier']] = $tmpidentifier;

		$newoptionid = table_forum_typeoption::t()->insert($typeoption, true);

		$typevar = [
			'sortid' => $sortid,
			'optionid' => $newoptionid,
			'available' => $value['available'],
			'required' => $value['required'],
			'unchangeable' => $value['unchangeable'],
			'search' => $value['search'],
			'displayorder' => $value['displayorder'],
			'subjectshow' => $value['subjectshow'],
		];
		table_forum_typevar::t()->insert($typevar);

		if($tmpidentifier) {
			if($value['type'] == 'radio') {
				$create_tableoption_sql .= "$separator$tmpidentifier smallint(6) UNSIGNED NOT NULL DEFAULT '0'";
			} elseif(in_array($value['type'], ['number', 'range'])) {
				$create_tableoption_sql .= "$separator$tmpidentifier int(10) UNSIGNED NOT NULL DEFAULT '0'";
			} elseif($value['type'] == 'select') {
				$create_tableoption_sql .= "$separator$tmpidentifier varchar(50) NOT NULL";
			} else {
				$create_tableoption_sql .= "$separator$tmpidentifier mediumtext NOT NULL";
			}
			$separator = ' ,';
			if(in_array($value['type'], ['radio', 'select', 'number'])) {
				$indexoption[] = $tmpidentifier;
			}
		}
	}

	foreach($idcmp as $k => $v) {
		if($k != $v) {
			$searcharr[] = '{'.$k;
			$searcharr[] = '['.$k;
			$replacearr[] = '{'.$v;
			$replacearr[] = '['.$v;
		}
	}

	$threadtype = [
		'icon' => $value['icon'],
		'special' => $value['special'],
		'modelid' => $value['modelid'],
		'expiration' => $value['ttexpiration'],
		'super' => $value['super'],
		'template' => str_replace($searcharr, $replacearr, $value['template']),
		'stemplate' => str_replace($searcharr, $replacearr, $value['stemplate']),
		'ptemplate' => str_replace($searcharr, $replacearr, $value['ptemplate']),
		'btemplate' => str_replace($searcharr, $replacearr, $value['btemplate']),
	];
	DB::update('forum_threadtype', $threadtype, ['typeid' => $sortid]);

	$fields = ($create_tableoption_sql ? $create_tableoption_sql.',' : '')."tid mediumint(8) UNSIGNED NOT NULL DEFAULT '0',fid smallint(6) UNSIGNED NOT NULL DEFAULT '0',dateline int(10) UNSIGNED NOT NULL DEFAULT '0',expiration int(10) UNSIGNED NOT NULL DEFAULT '0',";
	$fields .= 'KEY (fid), KEY(dateline)';
	if($indexoption) {
		foreach($indexoption as $index) {
			$fields .= "$separator KEY $index ($index)";
			$separator = ' ,';
		}
	}
	$dbcharset = $_G['config']['db'][1]['dbcharset'];
	$dbcharset = empty($dbcharset) ? str_replace('-', '', CHARSET) : $dbcharset;
	table_forum_optionvalue::t()->create($sortid, $fields, $dbcharset);

	updatecache('threadsorts');
}
cpmsg('threadtype_import_succeed', 'action=threadtypes', 'succeed');
	