<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$cursort = empty($_GET['cursort']) ? 0 : intval($_GET['cursort']);
$changesort = isset($_GET['changesort']) && empty($_GET['changesort']) ? 0 : 1;
$processed = 0;

$fieldtypes = ['number' => 'bigint(20)', 'text' => 'mediumtext', 'radio' => 'smallint(6)', 'checkbox' => 'mediumtext', 'textarea' => 'mediumtext', 'select' => 'smallint(6)', 'calendar' => 'mediumtext', 'email' => 'mediumtext', 'url' => 'mediumtext', 'image' => 'mediumtext'];

$optionvalues = [];

$optionvalues = $sortids = [];
foreach(table_forum_typevar::t()->fetch_all_by_search_optiontype(1, ['checkbox', 'radio', 'select', 'number']) as $row) {
	$optionvalues[$row['sortid']][$row['identifier']] = $row['type'];
	$optionids[$row['sortid']][$row['optionid']] = $row['identifier'];
	$searchs[$row['sortid']][$row['optionid']] = $row['search'];
	$sortids[] = $row['sortid'];
}
$sortids = array_unique($sortids);
sort($sortids);
if($sortids[$cursort] && $optionvalues[$sortids[$cursort]]) {
	$processed = 1;
	$sortid = $sortids[$cursort];
	$options = $optionvalues[$sortid];
	$search = $searchs[$sortid];
	$dbcharset = $_G['config']['db'][1]['dbcharset'];
	$dbcharset = empty($dbcharset) ? str_replace('-', '', CHARSET) : $dbcharset;
	$fields = "tid mediumint(8) UNSIGNED NOT NULL DEFAULT '0',fid smallint(6) UNSIGNED NOT NULL DEFAULT '0',KEY (fid)";
	table_forum_optionvalue::t()->create($sortid, $fields, $dbcharset);
	if($changesort) {
		table_forum_optionvalue::t()->truncate_by_sortid($sortid);
	}
	$opids = array_keys($optionids[$sortid]);

	$tables = table_forum_optionvalue::t()->showcolumns($sortid);
	foreach($optionids[$sortid] as $optionid => $identifier) {
		if(!$tables[$identifier] && (in_array($options[$identifier], ['checkbox', 'radio', 'select', 'number']) || $search[$optionid])) {
			$fieldname = $identifier;
			if($options[$identifier] == 'radio') {
				$fieldtype = 'smallint(6) UNSIGNED NOT NULL DEFAULT \'0\'';
			} elseif(in_array($options[$identifier], ['number', 'range'])) {
				$fieldtype = 'int(10) UNSIGNED NOT NULL DEFAULT \'0\'';
			} elseif($options[$identifier] == 'select') {
				$fieldtype = 'varchar(50) NOT NULL';
			} else {
				$fieldtype = 'mediumtext NOT NULL';
			}
			table_forum_optionvalue::t()->alter($sortid, "ADD $fieldname $fieldtype");

			if(in_array($options[$identifier], ['radio', 'select', 'number'])) {
				table_forum_optionvalue::t()->alter($sortid, "ADD INDEX ($fieldname)");
			}
		}
	}

	$inserts = [];
	$typeoptionvararr = table_forum_typeoptionvar::t()->fetch_all_by_search($sortid, null, null, $opids);
	if($typeoptionvararr) {
		$tids = [];
		foreach($typeoptionvararr as $value) {
			$tids[$value['tid']] = $value['tid'];
		}
		$tids = table_forum_thread::t()->fetch_all($tids);
		foreach($typeoptionvararr as $row) {
			$row['fid'] = $tids[$row['tid']]['fid'];
			$opname = $optionids[$sortid][$row['optionid']];
			if(empty($inserts[$row['tid']])) {
				$inserts[$row['tid']]['tid'] = $row['tid'];
				$inserts[$row['tid']]['fid'] = $row['fid'];
			}
			$inserts[$row['tid']][$opname] = addslashes($row['value']);
		}
		unset($tids, $typeoptionvararr);
	}
	if($inserts) {
		foreach($inserts as $tid => $fieldval) {
			$rfields = [];
			$ikey = $ival = '';
			foreach($fieldval as $ikey => $ival) {
				$rfields[] = "`$ikey`='$ival'";
			}
			table_forum_optionvalue::t()->insert_optionvalue($sortid, 'SET '.implode(',', $rfields), true);
		}
	}
	$cursort++;
	$changesort = 1;
}

$nextlink = "action=counter&changesort=$changesort&cursort=$cursort&specialarrange=yes";
if($processed) {
	cpmsg('counter_special_arrange', $nextlink, 'loading', ['cursort' => $cursort, 'sortids' => count($sortids)]);
} else {
	cpmsg('counter_special_arrange_succeed', 'action=counter', 'succeed');
}


$nextlink = "action=counter&current=$next&pertask=$pertask&membersubmit=yes";
$processed = 0;

$queryt = table_common_member::t()->range($current, $pertask);
foreach($queryt as $mem) {
	$processed = 1;
	$postcount = 0;
	loadcache('posttable_info');
	if(!empty($_G['cache']['posttable_info']) && is_array($_G['cache']['posttable_info'])) {
		foreach($_G['cache']['posttable_info'] as $key => $value) {
			$postcount += table_forum_post::t()->count_by_authorid($key, $mem['uid']);
		}
	} else {
		$postcount += table_forum_post::t()->count_by_authorid(0, $mem['uid']);
	}
	$postcount += table_forum_postcomment::t()->count_by_authorid($mem['uid']);
	$threadcount = table_forum_thread::t()->count_by_authorid($mem['uid']);
	table_common_member_count::t()->update($mem['uid'], ['posts' => $postcount, 'threads' => $threadcount]);
}

if($processed) {
	cpmsg("{$lang['counter_member']}: ".cplang('counter_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('counter_member_succeed', 'action=counter', 'succeed');
}
	