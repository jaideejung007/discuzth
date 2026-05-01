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
	$settingnew['alloweditpost'] = bindec(intval($settingnew['alloweditpost'][6]).intval($settingnew['alloweditpost'][5]).intval($settingnew['alloweditpost'][4]).intval($settingnew['alloweditpost'][3]).intval($settingnew['alloweditpost'][2]).intval($settingnew['alloweditpost'][1]));
} else {
	shownav('global', 'setting_'.$operation);

	showsubmenu('setting_'.$operation);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	include_once libfile('function/forumlist');
	$setting['allowviewuserthread'] = dunserialize($setting['allowviewuserthread']);
	$checkallselect = $setting['allowviewuserthread']['fids'] ? '' : ' selected';
	$forumselect = '<select name="settingnew[allowviewuserthread][fids][]" multiple="multiple" size="10"><option value=""'.$checkallselect.'>'.cplang('setting_permissions_allowviewuserthread_forum_group').'</option>'.forumselect(FALSE, 0, 0, TRUE).'</select>';
	if($setting['allowviewuserthread']['fids']) {
		foreach($setting['allowviewuserthread']['fids'] as $v) {
			$forumselect = str_replace('<option value="'.$v.'">', '<option value="'.$v.'" selected>', $forumselect);
		}
	}

	/*search={"setting_permissions":"action=setting&operation=permissions"}*/
	showtableheader();
	showsetting('setting_permissions_allowviewuserthread', 'settingnew[allowviewuserthread][allow]', $setting['allowviewuserthread']['allow'], 'radio', 0, 1);
	showsetting('setting_permissions_allowviewuserthread_fids', '', '', $forumselect);
	showtagfooter('tbody');
	showsetting('setting_permissions_allowmoderatingthread', 'settingnew[allowmoderatingthread]', $setting['allowmoderatingthread'], 'radio');
	showsetting('setting_permissions_memliststatus', 'settingnew[memliststatus]', $setting['memliststatus'], 'radio');
	showsetting('setting_permissions_minsubjectsize', 'settingnew[minsubjectsize]', $setting['minsubjectsize'], 'text');
	showsetting('setting_permissions_maxsubjectsize', 'settingnew[maxsubjectsize]', $setting['maxsubjectsize'], 'text');
	showsetting('setting_permissions_minpostsize', 'settingnew[minpostsize]', $setting['minpostsize'], 'text');
	showsetting('setting_permissions_minpostsize_mobile', 'settingnew[minpostsize_mobile]', $setting['minpostsize_mobile'], 'text');
	showsetting('setting_permissions_maxpostsize', 'settingnew[maxpostsize]', $setting['maxpostsize'], 'text');
	showsetting('setting_permissions_post_append', 'settingnew[postappend]', $setting['postappend'], 'radio');
	showsetting('setting_permissions_hideexpiration', 'settingnew[hideexpiration]', $setting['hideexpiration'], 'text');
	showsetting('setting_permissions_mailinterval', 'settingnew[mailinterval]', $setting['mailinterval'], 'text');
	showsetting('setting_permissions_maxpolloptions', 'settingnew[maxpolloptions]', $setting['maxpolloptions'], 'text');
	showsetting('setting_permissions_profilehistory', 'settingnew[profilehistory]', $setting['profilehistory'], 'radio');
	showsetting('setting_permissions_nsprofiles', 'settingnew[nsprofiles]', $setting['nsprofiles'], 'radio');
	showsetting('setting_permissions_modasban', 'settingnew[modasban]', $setting['modasban'], 'radio');

	showtitle('setting_permissions_editpost');
	showsetting('setting_permissions_alloweditpost', ['settingnew[alloweditpost]', [
		cplang('thread_general'),
		cplang('thread_poll'),
		cplang('thread_trade'),
		cplang('thread_reward'),
		cplang('thread_activity'),
		cplang('thread_debate')
	]], $setting['alloweditpost'], 'binmcheckbox');
	showsetting('setting_permissions_editperdel', 'settingnew[editperdel]', $setting['editperdel'], 'radio');
	showsetting('setting_permissions_editby', 'settingnew[editedby]', $setting['editedby'], 'radio');

	showtitle('nav_setting_rate');
	showsetting('setting_permissions_karmaratelimit', 'settingnew[karmaratelimit]', $setting['karmaratelimit'], 'text');
	showsetting('setting_permissions_modratelimit', 'settingnew[modratelimit]', $setting['modratelimit'], 'radio');
	showsetting('setting_permissions_dupkarmarate', 'settingnew[dupkarmarate]', $setting['dupkarmarate'], 'radio');
	/*search*/

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}