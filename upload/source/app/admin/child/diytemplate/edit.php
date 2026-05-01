<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('diytemplatename');
$targettplname = $_GET['targettplname'];
$tpldirectory = $_GET['tpldirectory'];
$diydata = table_common_diy_data::t()->fetch_diy($targettplname, $tpldirectory);
if(empty($diydata)) {
	cpmsg_error('diytemplate_targettplname_error', dreferer());
}
if(!submitcheck('editsubmit')) {
	if(empty($diydata['name'])) $diydata['name'] = $_G['cache']['diytemplatename'][$diydata['targettplname']];
	shownav('portal', 'diytemplate', $diydata['name']);
	showchildmenu([['diytemplate', 'diytemplate'], [$diydata['name'].' ', '']], cplang('diytemplate_edit'));

	showformheader("diytemplate&operation=edit&targettplname=$targettplname&tpldirectory=$tpldirectory");
	showtableheader();
	showtitle('edit');

	showsetting('diytemplate_name', 'name', $diydata['name'], 'text');
	showsetting('diytemplate_targettplname', '', '', cplang('diytemplate_path').'./data/diy/'.$diydata['targettplname'].'.htm');
	showsetting('diytemplate_primaltplname', '', '', cplang('diytemplate_path').$_G['style']['tpldir'].'/'.$diydata['primaltplname'].'.htm');
	showsetting('diytemplate_username', '', '', $diydata['username']);
	showsetting('diytemplate_dateline', '', '', $diydata['dateline'] ? dgmdate($diydata['dateline']) : '');

	showsubmit('editsubmit');
	showtablefooter();
	showformfooter();

} else {

	$editdiydata = ['name' => $_GET['name']];
	table_common_diy_data::t()->update_diy($targettplname, $tpldirectory, $editdiydata);

	include_once libfile('function/cache');
	updatecache('diytemplatename');

	cpmsg('diytemplate_edit_succeed', 'action=diytemplate', 'succeed');
}
	