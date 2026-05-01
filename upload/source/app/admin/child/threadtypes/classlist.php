<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$classoptions = '';
$classidarray = [];
$classid = $_GET['classid'] ? $_GET['classid'] : 0;
foreach(table_forum_typeoption::t()->fetch_all_by_classid($classid) as $option) {
	$classidarray[] = $option['optionid'];
	$classoptions .= "<a href=\"#ol\" onclick=\"ajaxget('".ADMINSCRIPT."?action=threadtypes&operation=optionlist&typeid={$_GET['typeid']}&classid={$option['optionid']}', 'optionlist', 'optionlist', 'Loading...', '', checkedbox)\">{$option['title']}</a> &nbsp; ";
}

include template('common/header');
echo $classoptions;
include template('common/footer');
exit;
	