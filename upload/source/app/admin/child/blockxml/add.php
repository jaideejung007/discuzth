<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('addsubmit')) {
	require_once libfile('function/importdata');
	import_block($_GET['xmlurl'], $_GET['clientid'], $_GET['key'], $_GET['signtype'], $_GET['ignoreversion']);
	require_once libfile('function/block');
	blockclass_cache();
	cpmsg('blockxml_xmlurl_add_succeed', 'action=blockxml', 'succeed');
} else {
	showsubmenu('blockxml', [
		['list', 'blockxml', 0],
		['add', 'blockxml&operation=add', 1]
	]);

	/*search={"blockxml":"action=blockxml","search":"action=blockxml&operation=add"}*/
	showtips('blockxml_tips');
	showformheader('blockxml&operation=add');
	showtableheader('blockxml_add');
	showsetting('blockxml_xmlurl', 'xmlurl', '', 'text');
	showsetting('blockxml_clientid', 'clientid', $blockxml['clientid'], 'text');
	showsetting('blockxml_signtype', ['signtype', $signtypearr], $blockxml['signtype'], 'select');
	showsetting('blockxml_xmlkey', 'key', $blockxml['key'], 'text');
	echo '<tr><td colspan="2" class="rowform"><input class="checkbox" type="checkbox" name="ignoreversion" id="ignoreversion" value="1" /><label for="ignoreversion"> '.cplang('blockxml_import_ignore_version').'</label></td></tr>';
	showsubmit('addsubmit');
	showtablefooter();
	showformfooter();
	/*search*/
}
	