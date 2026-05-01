<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$navtitle = lang('core', 'search');
if(submitcheck('searchsubmit')) {
	if(($keyword = $_GET['keyword'])) {
		$sqlsrch = '';
		$searchtype = in_array($_GET['searchtype'], ['all', 'title', 'message']) ? $_GET['searchtype'] : 'all';
		$faqlist = [];
		foreach(table_forum_faq::t()->fetch_all_by_fpid('', $keyword) as $faq) {
			if(!empty($faq['fpid'])) {
				$faq['title'] = preg_replace("/(?<=[\s\"\]>()]|[\x7f-\xff]|^)(".preg_quote($keyword, '/').")(([.,:;-?!()\s\"<\[]|[\x7f-\xff]|$))/siU", "<u><b><font color=\"#FF0000\">\\1</font></b></u>\\2", $faq['title']);
				$faq['message'] = preg_replace("/(?<=[\s\"\]>()]|[\x7f-\xff]|^)(".preg_quote($keyword, '/').")(([.,:;-?!()\s\"<\[]|[\x7f-\xff]|$))/siU", "<u><b><font color=\"#FF0000\">\\1</font></b></u>\\2", $faq['message']);
				$faqlist[] = $faq;
			}
		}
	} else {
		showmessage('faq_keywords_empty', 'misc.php?mod=faq');
	}
	$keyword = dhtmlspecialchars($keyword);
}
	