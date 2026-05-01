<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('settingsubmit')) {
	if(isset($settingnew['commentitem'])) {
		foreach($settingnew['commentitem'] as $k => $v) {
			if(!is_int($k)) {
				$settingnew['commentitem'][$k] = $k.chr(0).chr(0).chr(0).$v;
			}
		}
		$settingnew['commentitem'] = implode("\t", $settingnew['commentitem']);
	}

	$settingnew['bannedmessages'] = bindec(intval($settingnew['bannedmessages'][3]).intval($settingnew['bannedmessages'][2]).intval($settingnew['bannedmessages'][1]));
	$settingnew['activityextnum'] = intval($settingnew['activityextnum']);
	$settingnew['activitypp'] = intval($settingnew['activitypp']) == 0 ? 8 : intval($settingnew['activitypp']);
	if(!$settingnew['allowpostcomment']) $settingnew['allowpostcomment'] = [];
	if(!$settingnew['activityfield']) $settingnew['activityfield'] = [];
	if(!$settingnew['darkroom']) {
		table_common_nav::t()->update_by_navtype_type_identifier(1, 0, 'darkroom', ['available' => 0]);
	} else {
		table_common_nav::t()->update_by_navtype_type_identifier(1, 0, 'darkroom', ['available' => 1]);
	}

	if(isset($settingnew['heatthread'])) {
		$settingnew['heatthread']['reply'] = intval($settingnew['heatthread']['reply']);
		$settingnew['heatthread']['recommend'] = intval($settingnew['heatthread']['recommend']);
		$settingnew['heatthread']['type'] = 2;
		$settingnew['heatthread']['period'] = intval($settingnew['heatthread']['period']);
		$settingnew['heatthread']['guidelimit'] = $settingnew['heatthread']['guidelimit'] < 3 ? 3 : intval($settingnew['heatthread']['guidelimit']);
	}

	if(isset($settingnew['guide'])) {
		$settingnew['guide']['hotdt'] = intval($settingnew['guide']['hotdt']);
		$settingnew['guide']['digestdt'] = intval($settingnew['guide']['digestdt']);
	}
} else {
	shownav('global', 'setting_'.$operation);

	$_GET['anchor'] = in_array($_GET['anchor'], ['mod', 'heatthread', 'recommend', 'comment', 'activity', 'other', 'threadexp', 'avatar', 'guide', 'login']) ? $_GET['anchor'] : 'login';
	showsubmenu('setting_functions', [
		['setting_functions_login', 'setting&operation=functions&anchor=login', $_GET['anchor'] == 'login'],
		['setting_functions_mod', 'setting&operation=functions&anchor=mod', $_GET['anchor'] == 'mod'],
		['setting_functions_heatthread', 'setting&operation=functions&anchor=heatthread', $_GET['anchor'] == 'heatthread'],
		['setting_functions_recommend', 'setting&operation=functions&anchor=recommend', $_GET['anchor'] == 'recommend'],
		['setting_functions_comment', 'setting&operation=functions&anchor=comment', $_GET['anchor'] == 'comment'],
		['setting_functions_guide', 'setting&operation=functions&anchor=guide', $_GET['anchor'] == 'guide'],
		['setting_functions_activity', 'setting&operation=functions&anchor=activity', $_GET['anchor'] == 'activity'],
		['setting_functions_threadexp', 'setting&operation=functions&anchor=threadexp', $_GET['anchor'] == 'threadexp'],
		['setting_avatar', 'setting&operation=functions&anchor=avatar', $_GET['anchor'] == 'avatar'],
		['setting_functions_other', 'setting&operation=functions&anchor=other', $_GET['anchor'] == 'other'],
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	/*search={"setting_functions":"action=setting&operation=functions","setting_functions_mod":"action=setting&operation=functions&anchor=mod"}*/
	showtips('setting_tips', 'mod_tips', $_GET['anchor'] == 'mod');
	showtableheader('', 'nobottom', 'id="mod"'.($_GET['anchor'] != 'mod' ? ' style="display: none"' : ''));
	showsetting('setting_functions_mod_updatestat', 'settingnew[updatestat]', $setting['updatestat'], 'radio');
	showsetting('setting_functions_mod_status', 'settingnew[modworkstatus]', $setting['modworkstatus'], 'radio');
	showsetting('setting_functions_archiver', 'settingnew[archiver]', $setting['archiver'], 'radio', 0, 1);
	showsetting('setting_functions_archiverredirect', 'settingnew[archiverredirect]', $setting['archiverredirect'], 'radio');
	showtagfooter('tbody');
	showsetting('setting_functions_mod_maxmodworksmonths', 'settingnew[maxmodworksmonths]', $setting['maxmodworksmonths'], 'text');
	showsetting('setting_functions_mod_losslessdel', 'settingnew[losslessdel]', $setting['losslessdel'], 'text');
	showsetting('setting_functions_mod_reasons', 'settingnew[modreasons]', $setting['modreasons'], 'textarea');
	showsetting('setting_functions_mod_reasons_public', 'settingnew[modreasons_public]', $setting['modreasons_public'], 'radio');
	showsetting('setting_functions_mod_user_public', 'settingnew[moduser_public]', $setting['moduser_public'], 'radio');
	showsetting('setting_functions_user_reasons', 'settingnew[userreasons]', $setting['userreasons'], 'textarea');
	showsetting('setting_functions_mod_bannedmessages', ['settingnew[bannedmessages]', [
		$lang['setting_functions_mod_bannedmessages_thread'],
		$lang['setting_functions_mod_bannedmessages_avatar'],
		$lang['setting_functions_mod_bannedmessages_signature']]], $setting['bannedmessages'], 'binmcheckbox');
	showsetting('setting_functions_mod_warninglimit', 'settingnew[warninglimit]', $setting['warninglimit'], 'text');
	showsetting('setting_functions_mod_warningexpiration', 'settingnew[warningexpiration]', $setting['warningexpiration'], 'text');
	showsetting('setting_functions_mod_rewardexpiration', 'settingnew[rewardexpiration]', $setting['rewardexpiration'], 'text');
	showsetting('setting_functions_mod_moddetail', 'settingnew[moddetail]', $setting['moddetail'], 'radio');
	showtablefooter();
	/*search*/

	$setting['heatthread'] = dunserialize($setting['heatthread']);
	$setting['recommendthread'] = dunserialize($setting['recommendthread']);
	$setting['allowpostcomment'] = dunserialize($setting['allowpostcomment']);
	$count = count(explode(',', $setting['heatthread']['iconlevels']));
	$heatthreadicons = '';
	for($i = 0; $i < $count; $i++) {
		$heatthreadicons .= '<img src="static/image/common/hot_'.($i + 1).'.gif" /> ';
	}
	$count = count(explode(',', $setting['recommendthread']['iconlevels']));
	$recommendicons = '';
	for($i = 0; $i < $count; $i++) {
		$recommendicons .= '<img src="static/image/common/recommend_'.($i + 1).'.gif" /> ';
	}

	$setting['commentitem'] = explode("\t", $setting['commentitem']);
	foreach($setting['commentitem'] as $k => $v) {
		$tmp = explode(chr(0).chr(0).chr(0), $v);
		if(count($tmp) > 1) {
			$setting['commentitem'][$tmp[0]] = $tmp[1];
		}
	}

	/*search={"setting_functions":"action=setting&operation=functions","setting_functions_heatthread":"action=setting&operation=functions&anchor=heatthread"}*/
	showtips('setting_functions_heatthread_tips', 'heatthread_tips', $_GET['anchor'] == 'heatthread');
	showtableheader('', 'nobottom', 'id="heatthread"'.($_GET['anchor'] != 'heatthread' ? ' style="display: none"' : ''));
	showsetting('setting_functions_heatthread_period', 'settingnew[heatthread][period]', $setting['heatthread']['period'], 'text');
	showsetting('setting_functions_heatthread_iconlevels', '', '', '<input name="settingnew[heatthread][iconlevels]" class="txt" type="text" value="'.$setting['heatthread']['iconlevels'].'" /><br />'.$heatthreadicons);
	showtablefooter();
	/*search*/

	/*search={"setting_functions":"action=setting&operation=functions","setting_functions_recommend":"action=setting&operation=functions&anchor=recommend"}*/
	showtips('setting_functions_recommend_tips', 'recommend_tips', $_GET['anchor'] == 'recommend');
	showtableheader('', 'nobottom', 'id="recommend"'.($_GET['anchor'] != 'recommend' ? ' style="display: none"' : ''));
	showsetting('setting_functions_recommend_status', 'settingnew[recommendthread][status]', $setting['recommendthread']['status'], 'radio', 0, 1);
	showsetting('setting_functions_recommend_addtext', 'settingnew[recommendthread][addtext]', $setting['recommendthread']['addtext'], 'text');
	showsetting('setting_functions_recommend_subtracttext', 'settingnew[recommendthread][subtracttext]', $setting['recommendthread']['subtracttext'], 'text');
	showsetting('setting_functions_recommend_daycount', 'settingnew[recommendthread][daycount]', intval($setting['recommendthread']['daycount']), 'text');
	showsetting('setting_functions_recommend_ownthread', 'settingnew[recommendthread][ownthread]', $setting['recommendthread']['ownthread'], 'radio');
	showsetting('setting_functions_recommend_iconlevels', '', '', '<input name="settingnew[recommendthread][iconlevels]" class="txt" type="text" value="'.$setting['recommendthread']['iconlevels'].'" /><br />'.$recommendicons);
	showtablefooter();
	/*search*/

	/*search={"setting_functions":"action=setting&operation=functions","setting_functions_comment":"action=setting&operation=functions&anchor=comment"}*/
	showtableheader('', 'nobottom', 'id="comment"'.($_GET['anchor'] != 'comment' ? ' style="display: none"' : ''));
	showsetting('setting_functions_comment_allow', ['settingnew[allowpostcomment]', [
		[1, $lang['setting_functions_comment_allow_1'], 'commentextra'],
		[2, $lang['setting_functions_comment_allow_2']]]], $setting['allowpostcomment'], 'mcheckbox');
	showsetting('setting_functions_comment_number', 'settingnew[commentnumber]', $setting['commentnumber'], 'text');
	showsetting('setting_functions_comment_postself', 'settingnew[commentpostself]', $setting['commentpostself'], 'radio');
	showtagheader('tbody', 'commentextra', is_array($setting['allowpostcomment']) && in_array(1, $setting['allowpostcomment']));
	showsetting('setting_functions_comment_firstpost', 'settingnew[commentfirstpost]', $setting['commentfirstpost'], 'radio');
	showsetting('setting_functions_comment_commentitem_0', 'settingnew[commentitem][0]', $setting['commentitem'][0], 'textarea');
	showsetting('setting_functions_comment_commentitem_1', 'settingnew[commentitem][1]', $setting['commentitem'][1], 'textarea');
	showsetting('setting_functions_comment_commentitem_2', 'settingnew[commentitem][2]', $setting['commentitem'][2], 'textarea');
	showsetting('setting_functions_comment_commentitem_3', 'settingnew[commentitem][3]', $setting['commentitem'][3], 'textarea');
	showsetting('setting_functions_comment_commentitem_4', 'settingnew[commentitem][4]', $setting['commentitem'][4], 'textarea');
	showsetting('setting_functions_comment_commentitem_5', 'settingnew[commentitem][5]', $setting['commentitem'][5], 'textarea');
	showtagfooter('tbody');
	if(is_array($_G['setting']['threadplugins'])) foreach($_G['setting']['threadplugins'] as $tpid => $data) {
		showsetting($data['name'].cplang('setting_functions_comment_commentitem_threadplugin'), 'settingnew[commentitem]['.$tpid.']', $setting['commentitem'][$tpid], 'textarea', '', 0, cplang('setting_functions_comment_commentitem_threadplugin_comment'));
	}
	showtablefooter();
	/*search*/

	/*search={"setting_functions":"action=setting&operation=functions","setting_functions_threadexp":"action=setting&operation=functions&anchor=threadexp"}*/
	showtableheader('', 'nobottom', 'id="threadexp"'.($_GET['anchor'] != 'threadexp' ? ' style="display: none"' : ''));
	showsetting('setting_functions_threadexp_repliesrank', 'settingnew[repliesrank]', $setting['repliesrank'], 'radio');
	showsetting('setting_functions_threadexp_blacklist', 'settingnew[threadblacklist]', $setting['threadblacklist'], 'radio');
	showsetting('setting_functions_threadexp_hotreplies', 'settingnew[threadhotreplies]', $setting['threadhotreplies'], 'text');
	showsetting('setting_functions_threadexp_filter', 'settingnew[threadfilternum]', $setting['threadfilternum'], 'text');
	showsetting('setting_functions_threadexp_nofilteredpost', 'settingnew[nofilteredpost]', $setting['nofilteredpost'], 'radio');
	showsetting('setting_functions_threadexp_hidefilteredpost', 'settingnew[hidefilteredpost]', $setting['hidefilteredpost'], 'radio');
	showsetting('setting_functions_threadexp_filterednovote', 'settingnew[filterednovote]', $setting['filterednovote'], 'radio');
	showtablefooter();
	/*search*/

	/*search={"setting_functions":"action=setting&operation=avatar","setting_avatar":"action=setting&operation=functions&anchor=avatar"}*/
	showtableheader('', 'nobottom', 'id="avatar"'.($_GET['anchor'] != 'avatar' ? ' style="display: none"' : ''));
	showsetting('setting_uc_avatarmethod', ['settingnew[avatarmethod]', [
		[0, $lang['setting_uc_avatarmethod_0']],
		[1, $lang['setting_uc_avatarmethod_1']],
		[2, $lang['setting_uc_avatarmethod_2']],
	]], $setting['avatarmethod'], 'mradio');
	showsetting('setting_uc_dynavt', ['settingnew[dynavt]', [
		[0, $lang['setting_uc_dynavt_0']],
		[1, $lang['setting_uc_dynavt_1']],
		[2, $lang['setting_uc_dynavt_2']],
	]], $setting['dynavt'], 'mradio');
	showtablefooter();
	/*search*/

	/*search={"setting_functions":"action=setting&operation=functions","setting_functions_login":"action=setting&operation=functions&anchor=login"}*/
	showtableheader('', 'nobottom', 'id="login"'.($_GET['anchor'] != 'login' ? ' style="display: none"' : ''));
	showsetting('setting_functions_other_uidlogin', 'settingnew[uidlogin]', $setting['uidlogin'], 'radio');
	showsetting('setting_functions_other_secmobilelogin', 'settingnew[secmobilelogin]', $setting['secmobilelogin'], 'radio');
	showsetting('setting_functions_other_autoidselect', 'settingnew[autoidselect]', $setting['autoidselect'], 'radio');
	showsetting('setting_functions_other_disableipnotice', 'settingnew[disableipnotice]', $setting['disableipnotice'], 'radio');
	showtablefooter();
	/*search*/

	/*search={"setting_functions":"action=setting&operation=functions","setting_functions_other":"action=setting&operation=functions&anchor=other"}*/
	showtips('setting_tips', 'other_tips', $_GET['anchor'] == 'other');
	showtableheader('', 'nobottom', 'id="other"'.($_GET['anchor'] != 'other' ? ' style="display: none"' : ''));
	showsetting('setting_functions_other_submitlock', 'settingnew[submitlock]', $setting['submitlock'], 'radio');
	showsetting('setting_functions_other_rssstatus', 'settingnew[rssstatus]', $setting['rssstatus'], 'radio');
	showsetting('setting_functions_other_rssttl', 'settingnew[rssttl]', $setting['rssttl'], 'text');
	showsetting('setting_functions_other_oltimespan', 'settingnew[oltimespan]', $setting['oltimespan'], 'text');
	showsetting('setting_functions_other_debug', 'settingnew[debug]', $setting['debug'], 'radio');
	showsetting('setting_functions_other_onlyacceptfriendpm', 'settingnew[onlyacceptfriendpm]', $setting['onlyacceptfriendpm'], 'radio');
	showsetting('setting_functions_other_pmreportuser', 'settingnew[pmreportuser]', $setting['pmreportuser'], 'text');
	showsetting('setting_functions_other_at_anyone', 'settingnew[at_anyone]', $setting['at_anyone'], 'radio');
	showsetting('setting_functions_other_chatpmrefreshtime', 'settingnew[chatpmrefreshtime]', $setting['chatpmrefreshtime'], 'text');
	showsetting('setting_functions_other_collectionteamworkernum', 'settingnew[collectionteamworkernum]', $setting['collectionteamworkernum'], 'text');
	showsetting('setting_functions_other_shortcut', 'settingnew[shortcut]', $setting['shortcut'], 'text');
	showsetting('setting_functions_other_closeforumorderby', 'settingnew[closeforumorderby]', $setting['closeforumorderby'], 'radio');
	showsetting('setting_functions_other_darkroom', 'settingnew[darkroom]', $setting['darkroom'], 'radio');
	showsetting('setting_functions_other_global_sign', 'settingnew[globalsightml]', $setting['globalsightml'], 'textarea');
	showtablefooter();
	/*search*/

	/*search={"setting_functions":"action=setting&operation=functions","setting_functions_guide":"action=setting&operation=functions&anchor=guide"}*/
	$setting['guide'] = dunserialize($setting['guide']);
	showtableheader('', 'nobottom', 'id="guide"'.($_GET['anchor'] != 'guide' ? ' style="display: none"' : ''));
	showsetting('setting_functions_heatthread_guidelimit', 'settingnew[heatthread][guidelimit]', $setting['heatthread']['guidelimit'], 'text');
	$indexarray = [
		['index', $lang['setting_functions_guide_index_index']],
		['newthread', $lang['setting_functions_guide_index_newthread']],
		['new', $lang['setting_functions_guide_index_new']],
		['hot', $lang['setting_functions_guide_index_hot']],
		['digest', $lang['setting_functions_guide_index_digest']],
		['sofa', $lang['setting_functions_guide_index_sofa']]
	];
	showsetting('setting_functions_guide_index', ['settingnew[guide][index]', $indexarray], $setting['guide']['index'], 'select');
	$dtarray = [
		[604800, $lang['7_day']],
		[1209600, $lang['14_day']],
		[2592000, $lang['30_day']],
		[7776000, $lang['90_day']],
		[15552000, $lang['180_day']],
		[31536000, $lang['365_day']]
	];
	showsetting('setting_functions_guide_hotdt', ['settingnew[guide][hotdt]', $dtarray], $setting['guide']['hotdt'], 'select');
	showsetting('setting_functions_guide_digestdt', ['settingnew[guide][digestdt]', $dtarray], $setting['guide']['digestdt'], 'select');
	showtablefooter();
	/*search*/

	/*search={"setting_functions":"action=setting&operation=functions","setting_functions_activity":"action=setting&operation=functions&anchor=activity"}*/
	showtableheader('', 'nobottom', 'id="activity"'.($_GET['anchor'] != 'activity' ? ' style="display: none"' : ''));
	showsetting('setting_functions_activity_type', 'settingnew[activitytype]', $setting['activitytype'], 'textarea');
	$varname = ['settingnew[activityfield]', [], 'isfloat'];
	$ignorearray = ['birthyear', 'birthmonth', 'residecountry', 'resideprovince', 'birthcountry', 'birthprovince', 'residedist', 'residecommunity', 'constellation', 'zodiac'];
	foreach(table_common_member_profile_setting::t()->fetch_all_by_available(1) as $row) {
		if(in_array($row['fieldid'], $ignorearray)) continue;
		$varname[1][] = [$row['fieldid'], $row['title'], $row['title']];
	}
	$activityfield = dunserialize($_G['setting']['activityfield']);
	showsetting('setting_functions_activity_field', $varname, $activityfield, 'omcheckbox');
	showsetting('setting_functions_activity_extnum', 'settingnew[activityextnum]', $setting['activityextnum'], 'text');
	$_G['setting']['creditstrans'] = [];
	$setting['extcredits'] = dunserialize($setting['extcredits']);
	for($i = 0; $i <= 8; $i++) {
		$_G['setting']['creditstrans'] .= '<option value="'.$i.'" '.($i == $setting['activitycredit'] ? 'selected' : '').'>'.($i ? 'extcredits'.$i.($setting['extcredits'][$i]['title'] ? '('.$setting['extcredits'][$i]['title'].')' : '') : $lang['none']).'</option>';
	}
	showsetting('setting_functions_activity_credit', '', '', '<select name="settingnew[activitycredit]">'.$_G['setting']['creditstrans'].'</select>');
	showsetting('setting_functions_activity_pp', 'settingnew[activitypp]', $setting['activitypp'], 'text');
	showtablefooter();
	/*search*/

	showtableheader('', 'notop');
	if($_GET['anchor'] != 'curscript') {
		showsubmit('settingsubmit');
	}
	showtablefooter();
	showformfooter();
}
