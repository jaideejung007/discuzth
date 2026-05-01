<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$id = $_GET['id'];
if(!submitcheck('detailsubmit')) {

	$faq = table_forum_faq::t()->fetch($id);
	if(!$faq) {
		cpmsg('faq_nonexistence', '', 'error');
	}

	foreach(table_forum_faq::t()->fetch_all_by_fpid(0) as $parent) {
		$faqselect .= "<option value=\"{$parent['id']}\" ".($faq['fpid'] == $parent['id'] ? 'selected' : '').">{$parent['title']}</option>";
	}

	shownav('extended', 'faq');
	showchildmenu([['faq', 'faq']], $faq['title']);
	showformheader("faq&operation=detail&id=$id");
	showtableheader();
	showtitle('faq_edit');
	showsetting('faq_title', 'titlenew', $faq['title'], 'text');
	if(!empty($faq['fpid'])) {
		showsetting('faq_sortup', '', '', '<select name="fpidnew"><option value=\"\">'.$lang['none'].'</option>'.$faqselect.'</select>');
		showsetting('faq_identifier', 'identifiernew', $faq['identifier'], 'text');
		showsetting('faq_keywords', 'keywordnew', $faq['keyword'], 'text');
		showsetting('faq_content', 'messagenew', $faq['message'], 'textarea');
	}
	showsubmit('detailsubmit');
	showtablefooter();
	showformfooter();

} else {

	if(!$_GET['titlenew']) {
		cpmsg('faq_no_title', '', 'error');
	}

	if(!empty($_GET['identifiernew'])) {
		if(table_forum_faq::t()->check_identifier($_GET['identifiernew'], $id)) {
			cpmsg('faq_identifier_invalid', '', 'error');
		}
	}

	if(strlen($_GET['keywordnew']) > 50) {
		cpmsg('faq_keyword_toolong', '', 'error');
	}

	$fpidnew = $_GET['fpidnew'] ? intval($_GET['fpidnew']) : 0;
	$titlenew = trim($_GET['titlenew']);
	$messagenew = trim($_GET['messagenew']);
	$identifiernew = trim($_GET['identifiernew']);
	$keywordnew = trim($_GET['keywordnew']);

	table_forum_faq::t()->update($id, [
		'fpid' => $fpidnew,
		'identifier' => $identifiernew,
		'keyword' => $keywordnew,
		'title' => $titlenew,
		'message' => $messagenew,
	]);

	cpmsg('faq_list_update', 'action=faq&operation=list', 'succeed');

}
	