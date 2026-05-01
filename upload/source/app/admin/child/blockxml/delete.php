<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$id = intval($_GET['id']);
if(empty($id)) {
	cpmsg('undefined_action');
}

if(!empty($_GET['confirm'])) {
	table_common_block_xml::t()->delete($id);

	require_once libfile('function/block');
	blockclass_cache();
	cpmsg('blockxml_xmlurl_delete_succeed', 'action=blockxml', 'succeed');
} else {
	cpmsg('blockxml_xmlurl_delete_confirm', 'action=blockxml&operation=delete&id='.$id.'&confirm=yes', 'form');
}
	