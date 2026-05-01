<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$id = intval($_GET['id']);
$faq = table_forum_faq::t()->fetch_all_by_fpid($id);
if($faq) {
	$ffaq = $faq[$id];

	$navtitle = $ctitle;
	$navigation = "<em>&rsaquo;</em> $ctitle";
	$faqlist = [];
	$messageid = empty($_GET['messageid']) ? 0 : $_GET['messageid'];
	foreach(table_forum_faq::t()->fetch_all_by_fpid($id) as $faq) {
		if(!$messageid) {
			$messageid = $faq['id'];
		}
		$faqlist[] = $faq;
	}
} else {
	showmessage('faq_content_empty', 'misc.php?mod=faq');
}
	