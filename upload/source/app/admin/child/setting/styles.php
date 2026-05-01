<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$floatwinkeys = ['login', 'sendpm', 'newthread', 'reply', 'viewratings', 'viewwarning', 'viewthreadmod', 'viewvote', 'tradeorder', 'activity', 'debate', 'nav', 'usergroups', 'task'];
$floatwinarray = [];
foreach($floatwinkeys as $k) {
	$floatwinarray[] = [$k, $lang['setting_styles_global_allowfloatwin_'.$k]];
}

if(submitcheck('settingsubmit')) {
	if($_GET['anchor'] == 'threadprofile') {
		foreach($_GET['threadprofile'] as $gid => $tpid) {
			if(!$tpid) {
				table_forum_threadprofile_group::t()->delete($gid);
			} else {
				table_forum_threadprofile_group::t()->insert(['gid' => $gid, 'tpid' => $tpid], false, true);
			}
		}
		table_forum_threadprofile::t()->reset_default($_GET['default']);
		updatecache('setting');
		cpmsg('setting_update_succeed', 'action=setting&operation=styles&anchor=threadprofile', 'succeed');
	}

	if(isset($settingnew['showsignatures']) && isset($settingnew['showavatars']) && isset($settingnew['showimages'])) {
		$settingnew['showsettings'] = bindec($settingnew['showsignatures'].$settingnew['showavatars'].$settingnew['showimages']);
	}

	if(!empty($settingnew['globalstick'])) {
		updatecache('globalstick');
	}

	if(isset($settingnew['targetblank'])) {
		$settingnew['targetblank'] = intval($settingnew['targetblank']);
	}

	if(isset($settingnew['indexhot'])) {
		$settingnew['indexhot']['limit'] = intval($settingnew['indexhot']['limit']) ? $settingnew['indexhot']['limit'] : 10;
		$settingnew['indexhot']['days'] = intval($settingnew['indexhot']['days']) ? $settingnew['indexhot']['days'] : 7;
		$settingnew['indexhot']['expiration'] = intval($settingnew['indexhot']['expiration']) ? $settingnew['indexhot']['expiration'] : 900;
		$settingnew['indexhot']['width'] = intval($settingnew['indexhot']['width']) ? $settingnew['indexhot']['width'] : 100;
		$settingnew['indexhot']['height'] = intval($settingnew['indexhot']['height']) ? $settingnew['indexhot']['height'] : 70;
		$settingnew['indexhot']['messagecut'] = intval($settingnew['indexhot']['messagecut']) ? $settingnew['indexhot']['messagecut'] : 200;
		$_G['setting']['indexhot'] = $settingnew['indexhot'];
		updatecache('heats');
	}

	if(isset($settingnew['anonymoustext'])) {
		if(empty($settingnew['anonymoustext'])) {
			$settingnew['anonymoustext'] = cplang('anonymous');
		} else {
			$settingnew['anonymoustext'] = dhtmlspecialchars($settingnew['anonymoustext']);
		}
	}

	if(isset($settingnew['msgforward'])) {
		if(!empty($settingnew['msgforward']['messages'])) {
			$tempmsg = explode("\n", $settingnew['msgforward']['messages']);
			$settingnew['msgforward']['messages'] = [];
			foreach($tempmsg as $msg) {
				if($msg = strip_tags(trim($msg))) {
					$settingnew['msgforward']['messages'][] = $msg;
				}
			}
		} else {
			$settingnew['msgforward']['messages'] = [];
		}

		$tmparray = [
			'refreshtime' => intval($settingnew['msgforward']['refreshtime']),
			'quick' => $settingnew['msgforward']['quick'] ? 1 : 0,
			'messages' => $settingnew['msgforward']['messages']
		];
		$settingnew['msgforward'] = $tmparray;
	}

	if(isset($settingnew['postno'])) {
		$settingnew['postno'] = trim($settingnew['postno']);
	}
	if(isset($settingnew['postnocustom'])) {
		$settingnew['postnocustom'] = explode("\n", $settingnew['postnocustom']);
	}

	table_common_member_profile_setting::t()->clear_showinthread();
	$showinthreadfields = [];
	if(is_array($settingnew['customauthorinfo']) && array_key_exists('field_birthday', $settingnew['customauthorinfo'])) {
		$settingnew['customauthorinfo']['field_birthyear'] = $settingnew['customauthorinfo']['field_birthmonth'] = $settingnew['customauthorinfo']['field_birthday'];
	}
	foreach($settingnew['customauthorinfo'] as $field => $v) {
		if(str_starts_with($field, 'field_') && ($v['menu'] || $v['left'])) {
			$showinthreadfields[] = substr($field, 6);
		}
	}
	$settingnew['disallowfloat'] = array_diff($floatwinkeys, $settingnew['allowfloatwin'] ?? []);
	$settingnew['customauthorinfo'] = [$settingnew['customauthorinfo']];
	list(, $_G['setting']['imagemaxwidth']) = explode("\t", $setting['zoomstatus']);
	if(!$settingnew['zoomstatus']) {
		$settingnew['showexif'] = 0;
	}
	$settingnew['zoomstatus'] = $settingnew['zoomstatus']."\t".(!empty($settingnew['imagemaxwidth']) ? $settingnew['imagemaxwidth'] : 600);
	if($settingnew['forumpicstyle']) {
		$settingnew['forumpicstyle']['thumbwidth'] = intval($settingnew['forumpicstyle']['thumbwidth']);
		$settingnew['forumpicstyle']['thumbheight'] = intval($settingnew['forumpicstyle']['thumbheight']);
		$settingnew['forumpicstyle']['thumbnum'] = intval($settingnew['forumpicstyle']['thumbnum']);
	}

	$settingnew['guestviewthumb']['flag'] = intval($settingnew['guestviewthumb']['flag']) ? 1 : 0;
	$settingnew['guestviewthumb']['width'] = intval($settingnew['guestviewthumb']['width']);
	$settingnew['guestviewthumb']['height'] = intval($settingnew['guestviewthumb']['height']);
	$settingnew['guestviewthumb']['width'] = $settingnew['guestviewthumb']['width'] ? $settingnew['guestviewthumb']['width'] : 100;
	$settingnew['guestviewthumb']['height'] = $settingnew['guestviewthumb']['height'] ? $settingnew['guestviewthumb']['height'] : 100;

	$settingnew['guesttipsinthread']['flag'] = intval($settingnew['guesttipsinthread']['flag']) ? 1 : 0;


	if($showinthreadfields) {
		table_common_member_profile_setting::t()->update($showinthreadfields, ['showinthread' => 1]);
	}

	unset($settingnew['allowfloatwin']);

	if($_GET['delglobalreplybg']) {
		$valueparse = parse_url($setting['globalreplybg']);
		if(!isset($valueparse['host']) && file_exists($_G['setting']['attachurl'].'common/'.$setting['globalreplybg'])) {
			@unlink($_G['setting']['attachurl'].'common/'.$setting['globalreplybg']);
			ftpcmd('delete', 'common/'.$setting['globalreplybg']);
		}
		$_GET['globalreplybg'] = '';
	}
	if($_FILES['globalreplybg']) {
		$data = ['extid' => 'globalreplybg'];
		$settingnew['globalreplybg'] = upload_icon_banner($data, $_FILES['globalreplybg'], 'globalreplybg');
	} else {
		$settingnew['globalreplybg'] = $_GET['globalreplybg'];
	}
} else {
	shownav('style', 'setting_styles');

	$_GET['anchor'] = in_array($_GET['anchor'], ['global', 'index', 'forumdisplay', 'viewthread', 'threadprofile', 'numbercard', 'refresh', 'sitemessage']) ? $_GET['anchor'] : 'global';
	$current = [$_GET['anchor'] => 1];
	showsubmenu('setting_styles', [
		['setting_styles_global', 'setting&operation=styles&anchor=global', $current['global']],
		['setting_styles_index', 'setting&operation=styles&anchor=index', $current['index']],
		['setting_styles_forumdisplay', 'setting&operation=styles&anchor=forumdisplay', $current['forumdisplay']],
		['setting_styles_viewthread', 'setting&operation=styles&anchor=viewthread', $current['viewthread']],
		['setting_styles_threadprofile', 'setting&operation=styles&anchor=threadprofile', $current['threadprofile']],
		['members_profile_numbercard', 'setting&operation=styles&anchor=numbercard', $current['numbercard']],
		['setting_styles_refresh', 'setting&operation=styles&anchor=refresh', $current['refresh']],
		['setting_styles_sitemessage', 'setting&operation=styles&anchor=sitemessage', $current['sitemessage']]
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	$_G['setting']['showsettings'] = str_pad(decbin($setting['showsettings']), 3, 0, STR_PAD_LEFT);
	$setting['showsignatures'] = $_G['setting']['showsettings'][0];
	$setting['showavatars'] = $_G['setting']['showsettings'][1];
	$setting['showimages'] = $_G['setting']['showsettings'][2];
	$setting['postnocustom'] = implode("\n", (array)dunserialize($setting['postnocustom']));
	$setting['sitemessage'] = dunserialize($setting['sitemessage']);
	$setting['disallowfloat'] = $setting['disallowfloat'] ? dunserialize($setting['disallowfloat']) : [];
	$setting['allowfloatwin'] = array_diff($floatwinkeys, $setting['disallowfloat']);
	$setting['indexhot'] = dunserialize($setting['indexhot']);

	$setting['customauthorinfo'] = dunserialize($setting['customauthorinfo']);
	$setting['customauthorinfo'] = $setting['customauthorinfo'][0];
	list($setting['zoomstatus'], $setting['imagemaxwidth']) = explode("\t", $setting['zoomstatus']);
	$setting['imagemaxwidth'] = !empty($setting['imagemaxwidth']) ? $setting['imagemaxwidth'] : 600;
	$setting['guestviewthumb'] = dunserialize($setting['guestviewthumb']);
	$setting['guesttipsinthread'] = dunserialize($setting['guesttipsinthread']);

	/*search={"setting_styles":"action=setting&operation=styles","setting_styles_global":"action=setting&operation=styles&anchor=global"}*/
	showtips('setting_tips', 'global_tips', $_GET['anchor'] == 'global');
	showtableheader('setting_styles_global', 'nobottom', 'id="global"'.($_GET['anchor'] != 'global' ? ' style="display: none"' : ''));
	showsetting('setting_styles_global_home_style', ['settingnew[homestyle]', [
		[1, $lang['setting_styles_global_home_style_1']],
		[0, $lang['setting_styles_global_home_style_0']],
	]], $setting['homestyle'], 'mradio');
	showsetting('setting_styles_global_homepage_style', ['settingnew[homepagestyle]', [
		[1, $lang['setting_styles_global_homepage_style_1']],
		[0, $lang['setting_styles_global_homepage_style_0']],
	]], $setting['homepagestyle'], 'mradio');

	showsetting('setting_styles_global_navsubhover', ['settingnew[navsubhover]', [
		[0, $lang['setting_styles_global_navsubhover_0']],
		[1, $lang['setting_styles_global_navsubhover_1']],
	]], $setting['navsubhover'], 'mradio');
	showsetting('setting_styles_index_allowwidthauto', ['settingnew[allowwidthauto]', [
		[1, $lang['setting_styles_index_allowwidthauto_1']],
		[0, $lang['setting_styles_index_allowwidthauto_0']],
	], 1], $setting['allowwidthauto'], 'mradio');
	showtagheader('tbody', '', 1, 'sub');
	showsetting('setting_styles_index_switchwidthauto', 'settingnew[switchwidthauto]', $setting['switchwidthauto'], 'radio');
	showtagfooter('tbody');
	showsetting('setting_styles_global_allowfloatwin', ['settingnew[allowfloatwin]', $floatwinarray], $setting['allowfloatwin'], 'mcheckbox');
	showsetting('setting_styles_global_showfjump', 'settingnew[showfjump]', $setting['showfjump'], 'radio');
	showsetting('setting_styles_global_creditnotice', 'settingnew[creditnotice]', $setting['creditnotice'], 'radio');
	showsetting('setting_styles_global_showusercard', 'settingnew[showusercard]', $setting['showusercard'], 'radio');
	showsetting('setting_styles_global_showiplocation', 'settingnew[showiplocation]', $setting['showiplocation'], 'radio');
	showsetting('setting_styles_global_anonymoustext', 'settingnew[anonymoustext]', $setting['anonymoustext'], 'text');
	showtablefooter();
	/*search*/

	/*search={"setting_styles":"action=setting&operation=styles","setting_styles_index":"action=setting&operation=styles&anchor=index"}*/
	showtableheader('setting_styles_index', 'nobottom', 'id="index"'.($_GET['anchor'] != 'index' ? ' style="display: none"' : ''));
	showsetting('setting_styles_index_indexhot_status', 'settingnew[indexhot][status]', $setting['indexhot']['status'], 'radio', 0, 1);
	showsetting('setting_styles_index_indexhot_limit', 'settingnew[indexhot][limit]', $setting['indexhot']['limit'], 'text');
	showsetting('setting_styles_index_indexhot_days', 'settingnew[indexhot][days]', $setting['indexhot']['days'], 'text');
	showsetting('setting_styles_index_indexhot_expiration', 'settingnew[indexhot][expiration]', $setting['indexhot']['expiration'], 'text');
	showsetting('setting_styles_index_indexhot_messagecut', 'settingnew[indexhot][messagecut]', $setting['indexhot']['messagecut'], 'text');
	showtagfooter('tbody');
	showsetting('setting_styles_index_subforumsindex', 'settingnew[subforumsindex]', $setting['subforumsindex'], 'radio');
	showsetting('setting_styles_index_forumlinkstatus', 'settingnew[forumlinkstatus]', $setting['forumlinkstatus'], 'radio');
	showsetting('setting_styles_index_forumallowside', 'settingnew[forumallowside]', $setting['forumallowside'], 'radio');
	showsetting('setting_styles_index_whosonline', ['settingnew[whosonlinestatus]', [
		[0, $lang['setting_styles_index_display_none']],
		[1, $lang['setting_styles_index_whosonline_index']],
		[2, $lang['setting_styles_index_whosonline_forum']],
		[3, $lang['setting_styles_index_whosonline_both']]
	]], $setting['whosonlinestatus'], 'select');
	showsetting('setting_styles_index_whosonline_contract', 'settingnew[whosonline_contract]', $setting['whosonline_contract'], 'radio');
	showsetting('setting_styles_index_online_more_members', 'settingnew[maxonlinelist]', $setting['maxonlinelist'], 'text');
	showsetting('setting_styles_index_hideprivate', 'settingnew[hideprivate]', $setting['hideprivate'], 'radio');
	showsetting('setting_styles_index_showfollowcollection', 'settingnew[showfollowcollection]', $setting['showfollowcollection'], 'text');
	showsetting('setting_styles_index_disfixednv', 'settingnew[disfixednv_forumindex]', !empty($setting['disfixednv_forumindex']), 'radio');
	showtablefooter();
	/*search*/

	/*search={"setting_styles":"action=setting&operation=styles","setting_styles_forumdisplay":"action=setting&operation=styles&anchor=forumdisplay"}*/
	showtips('setting_tips', 'forumdisplay_tips', $_GET['anchor'] == 'forumdisplay');
	showtableheader('setting_styles_forumdisplay', 'nobottom', 'id="forumdisplay"'.($_GET['anchor'] != 'forumdisplay' ? ' style="display: none"' : ''));
	showsetting('setting_styles_forumdisplay_tpp', 'settingnew[topicperpage]', $setting['topicperpage'], 'text');
	showsetting('setting_styles_forumdisplay_threadmaxpages', 'settingnew[threadmaxpages]', $setting['threadmaxpages'], 'text');
	showsetting('setting_styles_forumdisplay_leftsidewidth', 'settingnew[leftsidewidth]', $setting['leftsidewidth'], 'text');
	showsetting('setting_styles_forumdisplay_leftsideopen', 'settingnew[leftsideopen]', $setting['leftsideopen'], 'radio');
	showsetting('setting_styles_forumdisplay_globalstick', 'settingnew[globalstick]', $setting['globalstick'], 'radio');
	showsetting('setting_styles_forumdisplay_targetblank', 'settingnew[targetblank]', $setting['targetblank'], 'radio');
	showsetting('setting_styles_forumdisplay_stick', 'settingnew[threadsticky]', $setting['threadsticky'], 'text');
	showsetting('setting_styles_forumdisplay_part', 'settingnew[forumseparator]', $setting['forumseparator'], 'radio');
	showsetting('setting_styles_forumdisplay_visitedforums', 'settingnew[visitedforums]', $setting['visitedforums'], 'text');
	showsetting('setting_styles_forumdisplay_fastpost', 'settingnew[fastpost]', $setting['fastpost'], 'radio', 0, 1);
	showsetting('setting_styles_forumdisplay_fastsmilies', 'settingnew[fastsmilies]', $setting['fastsmilies'], 'radio');
	showtagfooter('tbody');
	$setting['forumpicstyle'] = dunserialize($setting['forumpicstyle']);
	showsetting('setting_styles_forumdisplay_forumpicstyle_thumbwidth', 'settingnew[forumpicstyle][thumbwidth]', $setting['forumpicstyle']['thumbwidth'], 'text');
	showsetting('setting_styles_forumdisplay_forumpicstyle_thumbheight', 'settingnew[forumpicstyle][thumbheight]', $setting['forumpicstyle']['thumbheight'], 'text');
	showsetting('setting_styles_forumdisplay_forumpicstyle_thumbnum', 'settingnew[forumpicstyle][thumbnum]', $setting['forumpicstyle']['thumbnum'], 'text');

	$stamplist[] = [0, ''];
	foreach(table_common_smiley::t()->fetch_all_by_type('stamplist') as $smiley) {
		$stamplist[] = [$smiley['displayorder'], $smiley['code']];
	}
	showsetting('setting_styles_forumdisplay_newbie', ['settingnew[newbie]', $stamplist], $setting['newbie'], 'select');
	showsetting('setting_styles_forumdisplay_disfixednv_forumdisplay', 'settingnew[disfixednv_forumdisplay]', !empty($setting['disfixednv_forumdisplay']), 'radio');
	showsetting('setting_styles_forumdisplay_threadpreview', 'settingnew[forumdisplaythreadpreview]', !empty($setting['forumdisplaythreadpreview']), 'radio');
	showtablefooter();
	/*search*/

	/*search={"setting_styles":"action=setting&operation=styles","setting_styles_viewthread":"action=setting&operation=styles&anchor=viewthread"}*/
	showtagheader('div', 'viewthread', $_GET['anchor'] == 'viewthread');
	showtableheader('nav_setting_viewthread', 'nobottom');
	showsetting('setting_styles_viewthread_ppp', 'settingnew[postperpage]', $setting['postperpage'], 'text');
	showsetting('setting_styles_viewthread_starthreshold', 'settingnew[starthreshold]', $setting['starthreshold'], 'text');
	showsetting('setting_styles_viewthread_maxsigrows', 'settingnew[maxsigrows]', $setting['maxsigrows'], 'text');
	showsetting('setting_styles_viewthread_sigviewcond', 'settingnew[sigviewcond]', $setting['sigviewcond'], 'text');
	showsetting('setting_styles_viewthread_sigimgclick_on', 'settingnew[sigimgclick]', $setting['sigimgclick'], 'radio');
	showsetting('setting_styles_viewthread_rate_on', 'settingnew[ratelogon]', $setting['ratelogon'], 'radio');
	showsetting('setting_styles_viewthread_rate_number', 'settingnew[ratelogrecord]', $setting['ratelogrecord'], 'text');
	showsetting('setting_styles_viewthread_collection_number', 'settingnew[collectionnum]', $setting['collectionnum'], 'text');
	showsetting('setting_styles_viewthread_relate_number', 'settingnew[relatenum]', $setting['relatenum'], 'text');
	showsetting('setting_styles_viewthread_relate_time', 'settingnew[relatetime]', $setting['relatetime'], 'text');
	showsetting('setting_styles_viewthread_hideattachdown', 'settingnew[hideattachdown]', $setting['hideattachdown'], 'radio');
	showsetting('setting_styles_viewthread_hideattachtips', 'settingnew[hideattachtips]', $setting['hideattachtips'], 'radio');
	showsetting('setting_styles_viewthread_show_signature', 'settingnew[showsignatures]', $setting['showsignatures'], 'radio');
	showsetting('setting_styles_viewthread_show_face', 'settingnew[showavatars]', $setting['showavatars'], 'radio');
	showsetting('setting_styles_viewthread_show_images', 'settingnew[showimages]', $setting['showimages'], 'radio');
	showsetting('setting_styles_viewthread_imagemaxwidth', 'settingnew[imagemaxwidth]', $setting['imagemaxwidth'], 'text');
	showsetting('setting_styles_viewthread_imagelistthumb', 'settingnew[imagelistthumb]', $setting['imagelistthumb'], 'text');
	showsetting('setting_styles_viewthread_zoomstatus', 'settingnew[zoomstatus]', $setting['zoomstatus'], 'radio', 0, 1);
	showsetting('setting_styles_viewthread_showexif', 'settingnew[showexif]', $setting['showexif'], 'radio', !function_exists('exif_read_data'));
	showtagfooter('tbody');
	showsetting('setting_styles_viewthread_vtonlinestatus', ['settingnew[vtonlinestatus]', [
		[0, $lang['setting_styles_viewthread_display_none']],
		[1, $lang['setting_styles_viewthread_online_easy']],
		[2, $lang['setting_styles_viewthread_online_exactitude']]
	]], $setting['vtonlinestatus'], 'select');
	showsetting('setting_styles_viewthread_userstatusby', 'settingnew[userstatusby]', $setting['userstatusby'], 'radio');
	showsetting('setting_styles_viewthread_postno', 'settingnew[postno]', $setting['postno'], 'text');
	showsetting('setting_styles_viewthread_postnocustom', 'settingnew[postnocustom]', $setting['postnocustom'], 'textarea');
	showsetting('setting_styles_viewthread_maxsmilies', 'settingnew[maxsmilies]', $setting['maxsmilies'], 'text');

	showsetting('setting_styles_viewthread_author_onleft', ['settingnew[authoronleft]', [
		[1, cplang('setting_styles_viewthread_author_onleft_yes')],
		[0, cplang('setting_styles_viewthread_author_onleft_no')]]], $setting['authoronleft'], 'mradio');

	showsetting('setting_styles_forumdisplay_disfixedavatar', 'settingnew[disfixedavatar]', !empty($setting['disfixedavatar']), 'radio');
	showsetting('setting_styles_forumdisplay_disfixednv_viewthread', 'settingnew[disfixednv_viewthread]', !empty($setting['disfixednv_viewthread']), 'radio');
	showsetting('setting_styles_forumdisplay_threadguestlite', 'settingnew[threadguestlite]', !empty($setting['threadguestlite']), 'radio');
	showsetting('setting_styles_viewthread_close_leftinfo', 'settingnew[close_leftinfo]', !empty($setting['close_leftinfo']), 'radio');
	showsetting('setting_styles_viewthread_close_leftinfo_userctrl', 'settingnew[close_leftinfo_userctrl]', !empty($setting['close_leftinfo_userctrl']), 'radio');
	showsetting('setting_styles_viewthread_guestviewthumb', 'settingnew[guestviewthumb][flag]', !empty($setting['guestviewthumb']['flag']), 'radio', 0, 1);
	showsetting('setting_styles_viewthread_guestviewthumb_width', 'settingnew[guestviewthumb][width]', $setting['guestviewthumb']['width'], 'text');
	showsetting('setting_styles_viewthread_guestviewthumb_height', 'settingnew[guestviewthumb][height]', $setting['guestviewthumb']['height'], 'text');
	showtagfooter('tbody');
	showsetting('setting_styles_viewthread_guesttipsinthread', 'settingnew[guesttipsinthread][flag]', !empty($setting['guesttipsinthread']['flag']), 'radio', 0, 1);
	showsetting('setting_styles_viewthread_guesttipsinthread_text', 'settingnew[guesttipsinthread][text]', $setting['guesttipsinthread']['text'], 'text');
	showtagfooter('tbody');
	showsetting('setting_styles_viewthread_imgcontent', 'settingnew[imgcontentwidth]', $setting['imgcontentwidth'], 'text');
	showsetting('setting_styles_viewthread_fast_reply', 'settingnew[allowfastreply]', $setting['allowfastreply'], 'radio');
	showsetting('setting_styles_viewthread_allow_replybg', 'settingnew[allowreplybg]', $setting['allowreplybg'], 'radio', 0, 1);
	$replybghtml = '';
	if($setting['globalreplybg']) {
		$replybghtml = '<label><input type="checkbox" class="checkbox" name="delglobalreplybg" value="yes" /> '.$lang['delete'].'</label><br /><img src="'.$_G['setting']['attachurl'].'common/'.$setting['globalreplybg'].'" width="200px" />';
	}
	if($setting['globalreplybg']) {
		$replybgurl = parse_url($setting['globalreplybg']);
	}
	showsetting('setting_styles_viewthread_global_reply_background', 'globalreplybg', (!$replybgurl['host'] ? str_replace($_G['setting']['attachurl'].'common/', '', $setting['globalreplybg']) : $setting['globalreplybg']), 'filetext', '', 0, $replybghtml);
	showtablefooter();
	showtagfooter('div');

	$setting['msgforward'] = !empty($setting['msgforward']) ? dunserialize($setting['msgforward']) : [];
	$setting['msgforward']['messages'] = !empty($setting['msgforward']['messages']) ? implode("\n", $setting['msgforward']['messages']) : '';
	showtablefooter();
	/*search*/

	/*search={"setting_styles":"action=setting&operation=styles","setting_styles_threadprofile":"action=setting&operation=styles&anchor=threadprofile"}*/
	loadcache('usergroups');
	$threadprofiles = table_forum_threadprofile::t()->fetch_all_threadprofile();
	$threadprofile_group = table_forum_threadprofile_group::t()->fetch_all_threadprofile();
	showtagheader('div', 'threadprofile', $_GET['anchor'] == 'threadprofile');

	echo '<style>*, *::before, *::after {box-sizing: inherit}</style>';
	echo '<div class="drow"><div class="dcol d-12">';

	showboxheader('setting_styles_threadprofile_group');
	showtableheader('', 'nobottom');
	showsubtitle(['setting_styles_threadprofile_name', 'setting_styles_threadprofile_plan']);
	foreach($_G['cache']['usergroups'] as $gid => $usergroup) {
		$select = '<select name="threadprofile['.$gid.']"><option value="0">'.$lang['nav_global'].'</option>';
		foreach($threadprofiles as $id => $threadprofile) {
			$select .= '<option value="'.$id.'"'.($threadprofile_group[$gid]['tpid'] == $id ? ' selected' : '').'>'.$threadprofile['name'].'</option>';
		}
		$select .= '</select>';
		showtablerow('', ['', ''], [$usergroup['grouptitle'], $select]);
	}
	if($_G['setting']['verify']['enabled']) {
		foreach($_G['setting']['verify'] as $gid => $verify) {
			if($verify['available']) {
				$select = '<select name="threadprofile[-'.$gid.']"><option value="0">'.$lang['nav_global'].'</option>';
				foreach($threadprofiles as $id => $threadprofile) {
					$select .= '<option value="'.$id.'"'.($threadprofile_group[-$gid]['tpid'] == $id ? ' selected' : '').'>'.$threadprofile['name'].'</option>';
				}
				$select .= '</select>';
				showtablerow('', ['', ''], [$verify['title'], $select]);
			}
		}
	}
	showtablefooter();
	showboxfooter();

	echo '</div><div class="dcol d-12">';

	showboxheader('setting_styles_threadprofile_project');
	showtableheader('', 'nobottom');
	$setting['threadprofile'] = !empty($setting['threadprofile']) ? dunserialize($setting['threadprofile']) : [];
	showsubtitle(['setting_styles_threadprofile_name', 'nav_global', '']);
	foreach($threadprofiles as $id => $threadprofile) {
		showtablerow('', ['style="width:200px"', 'style="width:50px"', ''], [
			$threadprofile['name'],
			'<input name="default" type="radio" value="'.$id.'"'.($threadprofile['global'] ? ' checked' : '').' />',
			'<a href="'.ADMINSCRIPT.'?action=setting&operation=threadprofile&do=edit&id='.$id.'">'.cplang('edit').'</a>'.
			($id > 1 ? '&nbsp;<a href="'.ADMINSCRIPT.'?action=setting&operation=threadprofile&do=delete&id='.$id.'">'.cplang('delete').'</a>' : ''),
		]);
	}
	echo '<tr><td colspan="3"><a href="'.ADMINSCRIPT.'?action=setting&operation=threadprofile&do=add" class="addtr">'.$lang['setting_styles_threadprofile_addplan'].'</td></tr>';
	showtablefooter();
	showboxfooter();

	echo '</div></div>';

	showtagfooter('div');
	/*search*/

	showtips('members_profile_numbercard_tips', 'numbercard_tips', $_GET['anchor'] == 'numbercard');
	showtableheader('members_profile_numbercard', 'nobottom', 'id="numbercard"'.($_GET['anchor'] != 'numbercard' ? ' style="display: none"' : ''));
	$settingsAttribute = [];
	$allowedAttribute = ['threads', 'posts', 'credits', 'digestposts', 'doings', 'blogs', 'albums', 'sharings', 'oltime', 'feeds', 'follower', 'following', 'friends'];
	foreach($allowedAttribute as $attribute) {
		$settingsAttribute[] = [$attribute, $lang['setting_numbercard_type_'.$attribute]];
	}
	$extcredits = dunserialize($setting['extcredits']);
	foreach($extcredits as $creditid => $extcredit) {
		if($extcredit['title']) {
			$settingsAttribute[] = ['extcredits'.$creditid, $extcredit['title']];
		}
	}

	$setting['numbercard'] = dunserialize($setting['numbercard']);

	for($i = 1; $i <= 3; $i++) {
		showsetting(cplang('setting_numbercard_row', ['i' => $i]), ['settingnew[numbercard][row]['.$i.']', $settingsAttribute], $setting['numbercard']['row'][$i], 'select');
	}
	showtablefooter();

	/*search={"setting_styles":"action=setting&operation=styles","setting_styles_refresh":"action=setting&operation=styles&anchor=refresh"}*/
	showtableheader('setting_styles_refresh', 'nobottom', 'id="refresh"'.($_GET['anchor'] != 'refresh' ? ' style="display: none"' : ''));
	showsetting('setting_styles_refresh_refreshtime', 'settingnew[msgforward][refreshtime]', $setting['msgforward']['refreshtime'], 'text');
	showsetting('setting_styles_refresh_quick', 'settingnew[msgforward][quick]', $setting['msgforward']['quick'], 'radio', '', 1);
	showsetting('setting_styles_refresh_messages', 'settingnew[msgforward][messages]', $setting['msgforward']['messages'], 'textarea');
	showtagfooter('tbody');
	showtablefooter();
	/*search*/

	/*search={"setting_styles":"action=setting&operation=styles","setting_styles_sitemessage":"action=setting&operation=styles&anchor=sitemessage"}*/
	showtableheader('setting_styles_sitemessage', 'nobottom', 'id="sitemessage"'.($_GET['anchor'] != 'sitemessage' ? ' style="display: none"' : ''));
	showsetting('setting_styles_sitemessage_time', 'settingnew[sitemessage][time]', $setting['sitemessage']['time'], 'text');
	showsetting('setting_styles_sitemessage_register', 'settingnew[sitemessage][register]', $setting['sitemessage']['register'], 'textarea');
	showsetting('setting_styles_sitemessage_login', 'settingnew[sitemessage][login]', $setting['sitemessage']['login'], 'textarea');
	showsetting('setting_styles_sitemessage_newthread', 'settingnew[sitemessage][newthread]', $setting['sitemessage']['newthread'], 'textarea');
	showsetting('setting_styles_sitemessage_reply', 'settingnew[sitemessage][reply]', $setting['sitemessage']['reply'], 'textarea');
	showtagfooter('tbody');
	showtablefooter();
	/*search*/

	showtableheader('', 'notop');
	showsubmit('settingsubmit');
	showtablefooter();
	showformfooter();
}