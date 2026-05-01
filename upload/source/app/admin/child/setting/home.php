<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('settingsubmit')) {
	shownav('global', 'setting_'.$operation);

	$_GET['anchor'] = in_array($_GET['anchor'], ['base', 'privacy']) ? $_GET['anchor'] : 'base';
	showsubmenuanchors('setting_home', [
		['setting_home_base', 'base', $_GET['anchor'] == 'base'],
		['setting_home_privacy', 'privacy', $_GET['anchor'] == 'privacy']
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	require_once libfile('function/forumlist');

	/*search={"setting_home":"action=setting&operation=home","setting_home_base":"action=setting&operation=home&anchor=base"}*/
	showtableheader('', 'nobottom', 'id="base"'.($_GET['anchor'] != 'base' ? ' style="display: none"' : ''));
	showsetting('setting_home_base_feedday', 'settingnew[feedday]', $setting['feedday'], 'text');
	showsetting('setting_home_base_feedmaxnum', 'settingnew[feedmaxnum]', $setting['feedmaxnum'], 'text');
	showsetting('setting_home_base_feedhotday', 'settingnew[feedhotday]', $setting['feedhotday'], 'text');
	showsetting('setting_home_base_feedhotmin', 'settingnew[feedhotmin]', $setting['feedhotmin'], 'text');
	showsetting('setting_home_base_feedtargetblank', 'settingnew[feedtargetblank]', $setting['feedtargetblank'], 'radio');
	showsetting('setting_home_base_showallfriendnum', 'settingnew[showallfriendnum]', $setting['showallfriendnum'], 'text');
	showsetting('setting_home_base_feedhotnum', 'settingnew[feedhotnum]', $setting['feedhotnum'], 'text');
	showsetting('setting_home_base_maxpage', 'settingnew[maxpage]', $setting['maxpage'], 'text');
	showsetting('setting_home_base_sendmailday', 'settingnew[sendmailday]', $setting['sendmailday'], 'text');
	showsetting('setting_home_base_recycle_bin', 'settingnew[blogrecyclebin]', $setting['blogrecyclebin'], 'radio');
	loadcache('forums');
	showsetting('setting_home_base_groupnum', 'settingnew[friendgroupnum]', $setting['friendgroupnum'], 'text');
	$threadtype = ['1' => 'poll', '2' => 'trade', '3' => 'reward', '4' => 'activity', '5' => 'debate'];
	$oldforums = $_G['cache']['forums'];
	foreach($threadtype as $special => $key) {
		if($special == 0) {
			$fields = table_forum_forumfield::t()->fetch_all_by_fid(array_keys($_G['cache']['forums']));
			foreach($fields as $fid => $field) {
				if(!empty($field['threadsorts'])) {
					unset($_G['cache']['forums'][$fid]);
				}
			}
		} else {
			$_G['cache']['forums'] = $oldforums;
		}
		$forumselect = "<select name=\"%s\">\n<option value=\"\">&nbsp;&nbsp;> ".cplang('select').'</option>'.str_replace('%', '%%', forumselect(FALSE, 0, $setting[$key.'forumid'], TRUE, FALSE, $special)).'</select>';
		showsetting('setting_home_base_default_'.$key.'_forum', "settingnew[{$key}forumid]", $setting[$key.'forumid'], sprintf($forumselect, "settingnew[{$key}forumid]"));
	}

	showsetting('setting_home_base_default_doing', 'settingnew[defaultdoing]', $setting['defaultdoing'], 'textarea');
	showtablefooter();
	/*search*/

	if(isset($setting['privacy'])) {
		$setting['privacy'] = dunserialize($setting['privacy']);
	}
	/*search={"setting_home":"action=setting&operation=home","setting_home_privacy":"action=setting&operation=home&anchor=privacy"}*/
	showtableheader('', 'nobottom', 'id="privacy"'.($_GET['anchor'] != 'privacy' ? ' style="display: none"' : ''));
	showtitle('setting_home_privacy_new_user');
	showsetting('setting_home_privacy_view_index', ['settingnew[privacy][view][index]', [
		[0, $lang['setting_home_privacy_alluser']],
		[1, $lang['setting_home_privacy_friend']],
		[2, $lang['setting_home_privacy_self']],
		[3, $lang['setting_home_privacy_register']]
	]], $setting['privacy']['view']['index'], 'select');
	showsetting('setting_home_privacy_view_profile', ['settingnew[privacy][view][profile]', [
		[0, $lang['setting_home_privacy_alluser']],
		[1, $lang['setting_home_privacy_friend']],
		[2, $lang['setting_home_privacy_self']],
		[3, $lang['setting_home_privacy_register']]
	]], $setting['privacy']['view']['profile'], 'select');
	showsetting('setting_home_privacy_view_friend', ['settingnew[privacy][view][friend]', [
		[0, $lang['setting_home_privacy_alluser']],
		[1, $lang['setting_home_privacy_friend']],
		[2, $lang['setting_home_privacy_self']],
		[3, $lang['setting_home_privacy_register']]
	]], $setting['privacy']['view']['friend'], 'select');
	showsetting('setting_home_privacy_view_wall', ['settingnew[privacy][view][wall]', [
		[0, $lang['setting_home_privacy_alluser']],
		[1, $lang['setting_home_privacy_friend']],
		[2, $lang['setting_home_privacy_self']],
		[3, $lang['setting_home_privacy_register']]
	]], $setting['privacy']['view']['wall'], 'select');
	showsetting('setting_home_privacy_view_feed', ['settingnew[privacy][view][home]', [
		[0, $lang['setting_home_privacy_alluser']],
		[1, $lang['setting_home_privacy_friend']],
		[3, $lang['setting_home_privacy_register']]
	]], $setting['privacy']['view']['home'], 'select');
	showsetting('setting_home_privacy_view_doing', ['settingnew[privacy][view][doing]', [
		[0, $lang['setting_home_privacy_alluser']],
		[1, $lang['setting_home_privacy_friend']],
		[3, $lang['setting_home_privacy_register']]
	]], $setting['privacy']['view']['doing'], 'select');
	showsetting('setting_home_privacy_view_blog', ['settingnew[privacy][view][blog]', [
		[0, $lang['setting_home_privacy_alluser']],
		[1, $lang['setting_home_privacy_friend']],
		[3, $lang['setting_home_privacy_register']]
	]], $setting['privacy']['view']['blog'], 'select');
	showsetting('setting_home_privacy_view_album', ['settingnew[privacy][view][album]', [
		[0, $lang['setting_home_privacy_alluser']],
		[1, $lang['setting_home_privacy_friend']],
		[3, $lang['setting_home_privacy_register']]
	]], $setting['privacy']['view']['album'], 'select');
	showsetting('setting_home_privacy_view_share', ['settingnew[privacy][view][share]', [
		[0, $lang['setting_home_privacy_alluser']],
		[1, $lang['setting_home_privacy_friend']],
		[3, $lang['setting_home_privacy_register']]
	]], $setting['privacy']['view']['share'], 'select');

	showsetting('setting_home_privacy_default_feed', ['settingnew[privacy][feed]', [
		['doing', $lang['setting_home_privacy_default_feed_doing'], '1'],
		['blog', $lang['setting_home_privacy_default_feed_blog'], '1'],
		['upload', $lang['setting_home_privacy_default_feed_upload'], '1'],
		['share', $lang['setting_home_privacy_default_feed_share'], '1'],
		['poll', $lang['setting_home_privacy_default_feed_poll'], '1'],
		['joinpoll', $lang['setting_home_privacy_default_feed_joinpoll'], '1'],
		['friend', $lang['setting_home_privacy_default_feed_friend'], '1'],
		['comment', $lang['setting_home_privacy_default_feed_comments'], '1'],
		['show', $lang['setting_home_privacy_default_feed_show'], '1'],
		['credit', $lang['setting_home_privacy_default_feed_credit'], '1'],
		['spaceopen', $lang['setting_home_privacy_default_feed_spaceopen'], '1'],
		['invite', $lang['setting_home_privacy_default_feed_invite'], '1'],
		['task', $lang['setting_home_privacy_default_feed_task'], '1'],
		['profile', $lang['setting_home_privacy_default_feed_profile'], '1'],
		['click', $lang['setting_home_privacy_default_feed_click'], '1'],
		['newthread', $lang['setting_home_privacy_default_feed_newthread'], '1'],
		['newreply', $lang['setting_home_privacy_default_feed_newreply'], '1'],
	]], $setting['privacy']['feed'], 'omcheckbox');
	showtablefooter();
	/*search*/
	showtableheader();

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}