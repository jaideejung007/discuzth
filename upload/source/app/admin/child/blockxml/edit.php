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
if(!($blockxml = table_common_block_xml::t()->fetch($id))) {
	cpmsg('blockxml_xmlurl_notfound', '', 'error');
}
if(submitcheck('editsubmit')) {
	require_once libfile('function/importdata');
	import_block($_GET['xmlurl'], $_GET['clientid'], $_GET['key'], $_GET['signtype'], 1, $id);

	require_once libfile('function/block');
	blockclass_cache();
	cpmsg('blockxml_xmlurl_update_succeed', 'action=blockxml', 'succeed');
} else {
	showchildmenu([['blockxml', 'blockxml']], $blockxml['name']);

	showformheader('blockxml&operation=edit&id='.$id);
	showtableheader(cplang('blockxml_edit').' - '.$blockxml['name']);
	showsetting('blockxml_xmlurl', 'xmlurl', $blockxml['url'], 'text');
	showsetting('blockxml_clientid', 'clientid', $blockxml['clientid'], 'text');
	showsetting('blockxml_signtype', array('signtype', $signtypearr), $blockxml['signtype'], 'select');
	showsetting('blockxml_xmlkey', 'key', $blockxml['key'], 'text');
	showtablerow('', '', '<input class="checkbox" type="checkbox" name="ignoreversion" id="ignoreversion" value="1" /><label for="ignoreversion"> '.cplang('blockxml_import_ignore_version').'</label>');
	showsubmit('editsubmit');
	showtablefooter();
	showformfooter();
}
	