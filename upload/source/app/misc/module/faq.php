<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$keyword = isset($_GET['keyword']) ? dhtmlspecialchars($_GET['keyword']) : '';

$faqparent = $faqsub = [];
foreach(table_forum_faq::t()->fetch_all_by_fpid() as $faq) {
	if(empty($faq['fpid'])) {
		$faqparent[$faq['id']] = $faq;
		if($_GET['id'] == $faq['id']) {
			$ctitle = $faq['title'];
		}
	} else {
		$faqsub[$faq['fpid']][] = $faq;
	}
}

if($_GET['action'] == 'faq') {

	require_once childfile('faq');

} elseif($_GET['action'] == 'search') {

	require_once childfile('search');

} elseif($_GET['action'] == 'plugin' && !empty($_GET['id'])) {

	require_once childfile('plugin');

} else {
	$navtitle = lang('core', 'faq');
}

include template('common/faq');

