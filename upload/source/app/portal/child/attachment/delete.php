<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowmanagearticle'] && ($_G['uid'] != $attach['uid'] || $aid != $attach['aid'])) {
	showmessage('portal_attachment_nopermission_delete');
}
if(!isset($_GET['formhash']) || formhash() != $_GET['formhash']) {
	showmessage('portal_attachment_nopermission_delete');
}
if($aid) {
	table_portal_article_title::t()->update($aid, ['pic' => '']);
}
table_portal_attachment::t()->delete($id);
pic_delete($attach['attachment'], 'portal', $attach['thumb'], $attach['remote']);
showmessage('portal_image_noexist');
	