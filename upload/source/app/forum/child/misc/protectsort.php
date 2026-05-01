<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$tid = $_GET['tid'];
$optionid = $_GET['optionid'];
include template('common/header_ajax');
$typeoptionvarvalue = table_forum_typeoptionvar::t()->fetch_all_by_tid_optionid($tid, $optionid);
$typeoptionvarvalue[0]['expiration'] = $typeoptionvarvalue[0]['expiration'] && $typeoptionvarvalue[0]['expiration'] <= TIMESTAMP ? 1 : 0;
$option = table_forum_typeoption::t()->fetch($optionid);

if(($option['expiration'] && !$typeoptionvarvalue[0]['expiration']) || empty($option['expiration'])) {
	$protect = dunserialize($option['protect']);
	include_once libfile('function/threadsort');
	if(protectguard($protect)) {
		if(empty($option['permprompt'])) {
			echo lang('forum/misc', 'view_noperm');
		} else {
			echo $option['permprompt'];
		}
	} else {
		echo nl2br($typeoptionvarvalue[0]['value']);
	}
} else {
	echo lang('forum/misc', 'has_expired');
}
include template('common/footer_ajax');
	