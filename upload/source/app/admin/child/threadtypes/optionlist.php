<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$classid = $_GET['classid'];
if(!$classid) {
	$classid = table_forum_typeoption::t()->fetch_all_by_classid(0, 0, 1);
	$classid = $classid[0]['optionid'];
}
$option = $options = [];
foreach(table_forum_typevar::t()->fetch_all_by_sortid($_GET['typeid']) as $option) {
	$options[] = $option['optionid'];
}

$optionlist = '';
foreach(table_forum_typeoption::t()->fetch_all_by_classid($classid) as $option) {
	$optionlist .= '<input '.(in_array($option['optionid'], $options) ? ' checked="checked" ' : '')."class=\"checkbox\" type=\"checkbox\" name=\"typeselect[]\" id=\"typeselect_{$option['optionid']}\" value=\"{$option['optionid']}\" onclick=\"insertoption(this.value);\" /><label for=\"typeselect_{$option['optionid']}\">".dhtmlspecialchars($option['title']).'</label>&nbsp;&nbsp;';
}
include template('common/header');
echo $optionlist;
include template('common/footer');
exit;
	