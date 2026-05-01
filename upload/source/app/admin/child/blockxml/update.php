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

if(!($blockxml = table_common_block_xml::t()->fetch($id))) {
	cpmsg('blockxml_xmlurl_notfound', '', 'error');
}
require_once libfile('function/importdata');
import_block($blockxml['url'], $blockxml['clientid'], $blockxml['key'], $blockxml['signtype'], 1, $id);

require_once libfile('function/block');
blockclass_cache();

cpmsg('blockxml_xmlurl_update_succeed', 'action=blockxml', 'succeed');