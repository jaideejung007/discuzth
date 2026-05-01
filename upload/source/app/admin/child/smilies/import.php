<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('importsubmit')) {

	shownav('style', 'smilies_edit');
	showsubmenu('nav_smilies', [
		['smilies_type', 'smilies', 0],
		['smilies_import', 'smilies&operation=import', 1],
	]);
	/*search={"nav_smilies":"action=smilies","smilies_import":"action=smilies&operation=import"}*/
	showtips('smilies_tips');
	/*search*/
	showformheader('smilies&operation=import', 'enctype');
	showtableheader('smilies_import');
	showimportdata();
	showsubmit('importsubmit');
	showtablefooter();
	showformfooter();

} else {

	require_once libfile('function/importdata');
	$renamed = import_smilies();
	if($renamed) {
		cpmsg('smilies_import_succeed_renamed', 'action=smilies', 'succeed');
	} else {
		cpmsg('smilies_import_succeed', 'action=smilies', 'succeed');
	}

}
	