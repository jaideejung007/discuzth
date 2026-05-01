<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$submitcheck = submitcheck('groupsubmit') || submitcheck('multijssubmit');

$multiset = 0;
if(empty($_GET['multi'])) {
	$gids = $_GET['id'];
} else {
	$multiset = 1;
	if(is_array($_GET['multi'])) {
		$gids = $_GET['multi'];
	} else {
		$_GET['multi'] = explode(',', $_GET['multi']);
		array_walk($_GET['multi'], 'intval');
		$gids = $_GET['multi'];
	}
}
if(!empty($_GET['multi']) && is_array($_GET['multi']) && count($_GET['multi']) == 1) {
	$gids = $_GET['multi'][0];
	$multiset = 0;
}

if(!$submitcheck) {
	if(empty($gids)) {
		$grouplist = "<select name=\"id\" style=\"width: 150px\">\n";
		foreach(table_common_admingroup::t()->fetch_all_merge_usergroup() as $group) {
			$grouplist .= "<option value=\"{$group['groupid']}\">{$group['grouptitle']}</option>\n";
		}
		$grouplist .= '</select>';
		$highlight = getgpc('highlight');
		$highlight = !empty($highlight) ? dhtmlspecialchars($highlight, ENT_QUOTES) : '';
		cpmsg('admingroups_edit_nonexistence', 'action=admingroup&operation=edit'.(!empty($highlight) ? "&highlight=$highlight" : ''), 'form', [], $grouplist);
	}

	$mgroup = table_common_admingroup::t()->fetch_all_merge_usergroup($gids);
	if(!$mgroup) {
		cpmsg('usergroups_nonexistence', '', 'error');
	}/* else {
			while($group = DB::fetch($query)) {
				$mgroup[] = $group;
			}
		}*/

	$grouplist = $gutype = '';
	foreach(table_common_admingroup::t()->fetch_all_order() as $ggroup) {
		$checked = $_GET['id'] == $ggroup['groupid'] || (is_array($_GET['multi']) && in_array($ggroup['groupid'], $_GET['multi']));
		if($gutype != $ggroup['radminid']) {
			$grouplist .= '<em><span class="right"><input name="checkall_'.$ggroup['radminid'].'" onclick="checkAll(\'value\', this.form, \'g'.$ggroup['radminid'].'\', \'checkall_'.$ggroup['radminid'].'\')" type="checkbox" class="vmiddle checkbox" /></span>'.
				($ggroup['radminid'] == 1 ? $lang['usergroups_system_1'] : ($ggroup['radminid'] == 2 ? $lang['usergroups_system_2'] : $lang['usergroups_system_3'])).'</em>';
			$gutype = $ggroup['radminid'];
		}
		$grouplist .= '<input class="left checkbox ck" chkvalue="g'.$ggroup['radminid'].'" name="multi[]" value="'.$ggroup['groupid'].'" type="checkbox" '.($checked ? 'checked="checked" ' : '').'/>'.
			'<a href="###" onclick="location.href=\''.ADMINSCRIPT.'?action=admingroup&operation=edit&switch=yes&id='.$ggroup['groupid'].'&anchor=\'+currentAnchor+\'&scrolltop=\'+document.documentElement.scrollTop"'.($checked ? ' class="current"' : '').'>'.$ggroup['grouptitle'].'</a>';
	}
	$gselect = '<span id="ugselect" class="right popupmenu_dropmenu" onmouseover="showMenu({\'ctrlid\':this.id,\'pos\':\'34\'});$(\'ugselect_menu\').style.top=(parseInt($(\'ugselect_menu\').style.top)-scrollTopBody())+\'px\';$(\'ugselect_menu\').style.left=(parseInt($(\'ugselect_menu\').style.left)-document.documentElement.scrollLeft-20)+\'px\'">'.$lang['usergroups_switch'].'<em>&nbsp;&nbsp;</em></span>'.
		'<div id="ugselect_menu" class="popupmenu_popup" style="display:none">'.
		$grouplist.
		'<br style="clear:both" /><div class="cl"><input type="button" class="btn right" onclick="multiselect(\'menuform\')" value="'.cplang('admingroups_multiedit').'" /></div>'.
		'</div>';

	$_GET['anchor'] = in_array($_GET['anchor'], ['threadperm', 'postperm', 'modcpperm', 'portalperm', 'otherperm', 'spaceperm']) ? $_GET['anchor'] : 'threadperm';
	$anchorarray = [
		['admingroup_edit_threadperm', 'threadperm', $_GET['anchor'] == 'threadperm'],
		['admingroup_edit_postperm', 'postperm', $_GET['anchor'] == 'postperm'],
		['admingroup_edit_modcpperm', 'modcpperm', $_GET['anchor'] == 'modcpperm'],
		['admingroup_edit_spaceperm', 'spaceperm', $_GET['anchor'] == 'spaceperm'],
		['admingroup_edit_portalperm', 'portalperm', $_GET['anchor'] == 'portalperm'],
		['admingroup_edit_otherperm', 'otherperm', $_GET['anchor'] == 'otherperm'],
	];

	showformheader('', '', 'menuform', 'get');
	showhiddenfields(['action' => 'admingroup', 'operation' => 'edit']);
	showchildmenu([['nav_admingroups', 'admingroup']], (count($mgroup) == 1 ? $mgroup[$_GET['id']]['grouptitle'].'(groupid:'.$mgroup[$_GET['id']]['groupid'].')' : cplang('multiedit')),
		$anchorarray, $gselect, true);
	showformfooter();

	if($multiset) {
		showtips('setting_multi_tips');
	}

	if($multiset) {
		$_G['showsetting_multi'] = 0;
		$_G['showsetting_multicount'] = count($mgroup);
		foreach($mgroup as $group) {
			$_G['showtableheader_multi'][] = '<a href="javascript:;" onclick="location.href=\''.ADMINSCRIPT.'?action=admingroup&operation=edit&id='.$group['groupid'].'&anchor=\'+$(\'cpform\').anchor.value;return false">'.$group['grouptitle'].'(groupid:'.$group['groupid'].')</a>';
		}
	}

	showformheader("admingroup&operation=edit&id={$_GET['id']}");
	$mgids = [];
	foreach($mgroup as $group) {
		$_GET['id'] = $gid = $group['groupid'];
		$mgids[] = $gid;

		/*search={"admingroup":"action=admingroup","admingroup_edit_threadperm":"action=admingroup&operation=edit&anchor=threadperm"}*/
		showmultititle();
		showtableheader('', 'nobottom');
		showtagheader('tbody', 'threadperm', $_GET['anchor'] == 'threadperm');
		showtitle('admingroup_edit_threadperm');
		showsetting('admingroup_edit_stick_thread', ['allowstickthreadnew', [
			[0, $lang['admingroup_edit_stick_thread_none']],
			[1, $lang['admingroup_edit_stick_thread_1']],
			[2, $lang['admingroup_edit_stick_thread_2']],
			[3, $lang['admingroup_edit_stick_thread_3']]
		]], $group['allowstickthread'], 'mradio');
		showsetting('admingroup_edit_digest_thread', ['allowdigestthreadnew', [
			[0, $lang['admingroup_edit_digest_thread_none']],
			[1, $lang['admingroup_edit_digest_thread_1']],
			[2, $lang['admingroup_edit_digest_thread_2']],
			[3, $lang['admingroup_edit_digest_thread_3']]
		]], $group['allowdigestthread'], 'mradio');
		showsetting('admingroup_edit_bump_thread', 'allowbumpthreadnew', $group['allowbumpthread'], 'radio');
		showsetting('admingroup_edit_highlight_thread', 'allowhighlightthreadnew', $group['allowhighlightthread'], 'radio');
		showsetting('admingroup_edit_live_thread', 'allowlivethreadnew', $group['allowlivethread'], 'radio');
		showsetting('admingroup_edit_recommend_thread', 'allowrecommendthreadnew', $group['allowrecommendthread'], 'radio');
		showsetting('admingroup_edit_stamp_thread', 'allowstampthreadnew', $group['allowstampthread'], 'radio');
		showsetting('admingroup_edit_stamp_list', 'allowstamplistnew', $group['allowstamplist'], 'radio');
		showsetting('admingroup_edit_close_thread', 'allowclosethreadnew', $group['allowclosethread'], 'radio');
		showsetting('admingroup_edit_move_thread', 'allowmovethreadnew', $group['allowmovethread'], 'radio');
		showsetting('admingroup_edit_edittype_thread', 'allowedittypethreadnew', $group['allowedittypethread'], 'radio');
		showsetting('admingroup_edit_copy_thread', 'allowcopythreadnew', $group['allowcopythread'], 'radio');
		showsetting('admingroup_edit_merge_thread', 'allowmergethreadnew', $group['allowmergethread'], 'radio');
		showsetting('admingroup_edit_split_thread', 'allowsplitthreadnew', $group['allowsplitthread'], 'radio');
		showsetting('admingroup_edit_repair_thread', 'allowrepairthreadnew', $group['allowrepairthread'], 'radio');
		showsetting('admingroup_edit_refund', 'allowrefundnew', $group['allowrefund'], 'radio');
		showsetting('admingroup_edit_edit_poll', 'alloweditpollnew', $group['alloweditpoll'], 'radio');
		showsetting('admingroup_edit_remove_reward', 'allowremoverewardnew', $group['allowremovereward'], 'radio');
		showsetting('admingroup_edit_edit_activity', 'alloweditactivitynew', $group['alloweditactivity'], 'radio');
		showsetting('admingroup_edit_edit_trade', 'allowedittradenew', $group['allowedittrade'], 'radio');
		showsetting('admingroup_edit_usertag', 'alloweditusertagnew', $group['alloweditusertag'], 'radio');
		showtagfooter('tbody');
		/*search*/

		/*search={"admingroup":"action=admingroup","admingroup_edit_postperm":"action=admingroup&operation=edit&anchor=postperm"}*/
		showtagheader('tbody', 'postperm', $_GET['anchor'] == 'postperm');
		showtitle('admingroup_edit_postperm');
		showsetting('admingroup_edit_edit_post', 'alloweditpostnew', $group['alloweditpost'], 'radio');
		showsetting('admingroup_edit_warn_post', 'allowwarnpostnew', $group['allowwarnpost'], 'radio');
		showsetting('admingroup_edit_ban_post', 'allowbanpostnew', $group['allowbanpost'], 'radio');
		showsetting('admingroup_edit_del_post', 'allowdelpostnew', $group['allowdelpost'], 'radio');
		showsetting('admingroup_edit_stick_post', 'allowstickreplynew', $group['allowstickreply'], 'radio');
		showsetting('admingroup_edit_manage_tag', 'allowmanagetagnew', $group['allowmanagetag'], 'radio');
		showtagfooter('tbody');
		/*search*/

		/*search={"admingroup":"action=admingroup","admingroup_edit_modcpperm":"action=admingroup&operation=edit&anchor=modcpperm"}*/
		showtagheader('tbody', 'modcpperm', $_GET['anchor'] == 'modcpperm');
		showtitle('admingroup_edit_modcpperm');
		showsetting('admingroup_edit_mod_post', 'allowmodpostnew', $group['allowmodpost'], 'radio');
		showsetting('admingroup_edit_mod_user', 'allowmodusernew', $group['allowmoduser'], 'radio');
		showsetting('admingroup_edit_ban_user', 'allowbanusernew', $group['allowbanuser'], 'radio');
		showsetting('admingroup_edit_ban_user_visit', 'allowbanvisitusernew', $group['allowbanvisituser'], 'radio');
		showsetting('admingroup_edit_ban_ip', 'allowbanipnew', $group['allowbanip'], 'radio');
		showsetting('admingroup_edit_edit_user', 'alloweditusernew', $group['allowedituser'], 'radio');
		showsetting('admingroup_edit_mass_prune', 'allowmassprunenew', $group['allowmassprune'], 'radio');
		showsetting('admingroup_edit_edit_forum', 'alloweditforumnew', $group['alloweditforum'], 'radio');
		showsetting('admingroup_edit_post_announce', 'allowpostannouncenew', $group['allowpostannounce'], 'radio');
		showsetting('admingroup_edit_clear_recycle', 'allowclearrecyclenew', $group['allowclearrecycle'], 'radio');
		showsetting('admingroup_edit_view_log', 'allowviewlognew', $group['allowviewlog'], 'radio');
		showtagfooter('tbody');
		/*search*/

		/*search={"admingroup":"action=admingroup","admingroup_edit_spaceperm":"action=admingroup&operation=edit&anchor=spaceperm"}*/
		showtagheader('tbody', 'spaceperm', $_GET['anchor'] == 'spaceperm');
		showtitle('admingroup_edit_spaceperm');
		showsetting('admingroup_edit_manage_feed', 'managefeednew', $group['managefeed'], 'radio');
		showsetting('admingroup_edit_manage_doing', 'managedoingnew', $group['managedoing'], 'radio');
		showsetting('admingroup_edit_manage_share', 'managesharenew', $group['manageshare'], 'radio');
		showsetting('admingroup_edit_manage_blog', 'manageblognew', $group['manageblog'], 'radio');
		showsetting('admingroup_edit_manage_album', 'managealbumnew', $group['managealbum'], 'radio');
		showsetting('admingroup_edit_manage_comment', 'managecommentnew', $group['managecomment'], 'radio');
		showsetting('admingroup_edit_manage_magiclog', 'managemagiclognew', $group['managemagiclog'], 'radio');
		showsetting('admingroup_edit_manage_report', 'managereportnew', $group['managereport'], 'radio');
		showsetting('admingroup_edit_manage_hotuser', 'managehotusernew', $group['managehotuser'], 'radio');
		showsetting('admingroup_edit_manage_defaultuser', 'managedefaultusernew', $group['managedefaultuser'], 'radio');
		showsetting('admingroup_edit_manage_magic', 'managemagicnew', $group['managemagic'], 'radio');
		showsetting('admingroup_edit_manage_click', 'manageclicknew', $group['manageclick'], 'radio');
		showtagfooter('tbody');
		/*search*/

		/*search={"admingroup":"action=admingroup","admingroup_edit_otherperm":"action=admingroup&operation=edit&anchor=otherperm"}*/
		showtagheader('tbody', 'otherperm', $_GET['anchor'] == 'otherperm');
		showtitle('admingroup_edit_otherperm');
		showsetting('admingroup_edit_view_ip', 'allowviewipnew', $group['allowviewip'], 'radio');
		showsetting('admingroup_edit_manage_collection', 'allowmanagecollectionnew', $group['allowmanagecollection'], 'radio');
		showsetting('admingroup_edit_allow_make_html', 'allowmakehtmlnew', $group['allowmakehtml'], 'radio');
		showtagfooter('tbody');
		showtablefooter();
		/*search*/

		/*search={"admingroup":"action=admingroup","admingroup_edit_portalperm":"action=admingroup&operation=edit&anchor=portalperm"}*/
		showtagheader('div', 'portalperm', $_GET['anchor'] == 'portalperm');
		showtableheader('', 'nobottom');
		showtagheader('tbody', '', true);
		showtitle('admingroup_edit_portalperm');
		showsetting('admingroup_edit_manage_article', 'allowmanagearticlenew', $group['allowmanagearticle'], 'radio');
		showtagfooter('tbody');
		showtagheader('tbody', '', true);
		showsetting('admingroup_edit_add_topic', 'allowaddtopicnew', $group['allowaddtopic'], 'radio');
		showsetting('admingroup_edit_manage_topic', 'allowmanagetopicnew', $group['allowmanagetopic'], 'radio');
		showsetting('admingroup_edit_diy', 'allowdiynew', $group['allowdiy'], 'radio');
		showtagfooter('tbody');
		showtablefooter();
		showtagfooter('div');
		/*search*/
		showtableheader();
		showsubmit('groupsubmit');
		showtablefooter();
		$_G['showsetting_multi']++;
	}

	if($_G['showsetting_multicount'] > 1) {
		showhiddenfields(['multi' => implode(',', $mgids)]);
		showmulti();
	}
	showformfooter();

} else {

	if(!$multiset) {
		$_GET['multinew'] = [0 => ['single' => 1]];
	}
	foreach($_GET['multinew'] as $k => $row) {
		if(empty($row['single'])) {
			foreach($row as $key => $value) {
				$_GET[''.$key] = $value;
			}
			$_GET['id'] = $_GET['multi'][$k];
		}
		$group = $mgroup[$k];

		$data = [
			'alloweditpost' => $_GET['alloweditpostnew'],
			'alloweditpoll' => $_GET['alloweditpollnew'],
			'allowedittrade' => $_GET['allowedittradenew'],
			'alloweditusertag' => $_GET['alloweditusertagnew'],
			'allowremovereward' => $_GET['allowremoverewardnew'],
			'alloweditactivity' => $_GET['alloweditactivitynew'],
			'allowstickthread' => $_GET['allowstickthreadnew'],
			'allowmodpost' => $_GET['allowmodpostnew'],
			'allowbanpost' => $_GET['allowbanpostnew'],
			'allowdelpost' => $_GET['allowdelpostnew'],
			'allowmassprune' => $_GET['allowmassprunenew'],
			'allowrefund' => $_GET['allowrefundnew'],
			'allowcensorword' => $_GET['allowcensorwordnew'],
			'allowviewip' => $_GET['allowviewipnew'],
			'allowmanagecollection' => $_GET['allowmanagecollectionnew'],
			'allowbanip' => $_GET['allowbanipnew'],
			'allowedituser' => $_GET['alloweditusernew'],
			'allowbanuser' => $_GET['allowbanusernew'],
			'allowbanvisituser' => $_GET['allowbanvisitusernew'],
			'allowmoduser' => $_GET['allowmodusernew'],
			'allowpostannounce' => $_GET['allowpostannouncenew'],
			'allowclearrecycle' => $_GET['allowclearrecyclenew'],
			'allowhighlightthread' => $_GET['allowhighlightthreadnew'],
			'allowlivethread' => $_GET['allowlivethreadnew'],
			'allowdigestthread' => $_GET['allowdigestthreadnew'],
			'allowrecommendthread' => $_GET['allowrecommendthreadnew'],
			'allowbumpthread' => $_GET['allowbumpthreadnew'],
			'allowclosethread' => $_GET['allowclosethreadnew'],
			'allowmovethread' => $_GET['allowmovethreadnew'],
			'allowedittypethread' => $_GET['allowedittypethreadnew'],
			'allowstampthread' => $_GET['allowstampthreadnew'],
			'allowstamplist' => $_GET['allowstamplistnew'],
			'allowcopythread' => $_GET['allowcopythreadnew'],
			'allowmergethread' => $_GET['allowmergethreadnew'],
			'allowsplitthread' => $_GET['allowsplitthreadnew'],
			'allowrepairthread' => $_GET['allowrepairthreadnew'],
			'allowwarnpost' => $_GET['allowwarnpostnew'],
			'alloweditforum' => $_GET['alloweditforumnew'],
			'allowviewlog' => $_GET['allowviewlognew'],
			'allowmanagearticle' => $_GET['allowmanagearticlenew'],
			'allowaddtopic' => $_GET['allowaddtopicnew'],
			'allowmanagetopic' => $_GET['allowmanagetopicnew'],
			'allowdiy' => $_GET['allowdiynew'],
			'allowstickreply' => $_GET['allowstickreplynew'],
			'allowmanagetag' => $_GET['allowmanagetagnew'],
			'managefeed' => $_GET['managefeednew'],
			'managedoing' => $_GET['managedoingnew'],
			'manageshare' => $_GET['managesharenew'],
			'manageblog' => $_GET['manageblognew'],
			'managealbum' => $_GET['managealbumnew'],
			'managecomment' => $_GET['managecommentnew'],
			'managemagiclog' => $_GET['managemagiclognew'],
			'managereport' => $_GET['managereportnew'],
			'managehotuser' => $_GET['managehotusernew'],
			'managedefaultuser' => $_GET['managedefaultusernew'],
			'managemagic' => $_GET['managemagicnew'],
			'manageclick' => $_GET['manageclicknew'],
			'allowmakehtml' => $_GET['allowmakehtmlnew'],
		];
		table_common_admingroup::t()->update($_GET['id'], array_map('intval', $data));
	}

	updatecache(['usergroups', 'groupreadaccess', 'admingroups']);

	cpmsg('admingroups_edit_succeed', 'action=admingroup&operation=edit&'.($multiset ? 'multi='.implode(',', $_GET['multi']) : 'id='.$_GET['id']).'&anchor='.$_GET['anchor'], 'succeed', ['frame' => $multiset]);
}
	