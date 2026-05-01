<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('forums');

$source = intval($_GET['source']);
$sourceforum = $_G['cache']['forums'][$source];

if(empty($sourceforum) || $sourceforum['type'] == 'group') {
	cpmsg('forums_copy_source_invalid', '', 'error');
}

$delfields = [
	'forums' => ['fid', 'fup', 'type', 'name', 'status', 'displayorder', 'threads', 'posts', 'todayposts', 'lastpost', 'modworks', 'icon', 'level', 'commoncredits', 'archive', 'recommend'],
	'forumfields' => ['description', 'password', 'redirect', 'moderators', 'rules', 'threadtypes', 'threadsorts', 'threadplugin', 'jointype', 'gviewperm', 'membernum', 'dateline', 'lastupdate', 'founderuid', 'foundername', 'banner', 'groupnum', 'activity'],
];
$fields = [
	'forums' => table_forum_forum::t()->fetch_table_struct('forum_forum'),
	'forumfields' => table_forum_forum::t()->fetch_table_struct('forum_forumfield'),
];

if(!submitcheck('copysubmit')) {

	$vfidstr = !empty($_GET['vfid']) ? '&vfid='.$_GET['vfid'] : '';

	require_once libfile('function/forumlist');

	$forumselect = '<select name="target[]" size="10" multiple="multiple">'.forumselect(FALSE, 0, 0, TRUE).'</select>';
	$optselect = '<select name="options[]" size="10" multiple="multiple">';
	$fieldarray = array_merge($fields['forums'], $fields['forumfields']);
	$listfields = array_diff($fieldarray, array_merge($delfields['forums'], $delfields['forumfields']));
	foreach($listfields as $field) {
		if(isset($lang['project_option_forum_'.$field])) {
			$optselect .= '<option value="'.$field.'">'.$lang['project_option_forum_'.$field].'</option>';
		}
	}
	$optselect .= '</select>';
	shownav('forum', 'forums_copy');
	showchildmenu([['nav_forums', 'forums']], cplang('forums_copy'));
	showtips('forums_copy_tips');
	showformheader('forums&operation=copy'.$vfidstr);
	showhiddenfields(['source' => $source]);
	showtableheader();
	showtitle('forums_copy');
	showsetting(cplang('forums_copy_source').':', '', '', $sourceforum['name']);
	showsetting('forums_copy_target', '', '', $forumselect);
	showsetting('forums_copy_options', '', '', $optselect);
	showsubmit('copysubmit');
	showtablefooter();
	showformfooter();

} else {

	$vfidstr = !empty($_GET['vfid']) ? '&fid='.$_GET['vfid'] : '';

	$fids = [];
	if(!empty($_GET['target']) && is_array($_GET['target']) && count($_GET['target'])) {
		foreach($_GET['target'] as $fid) {
			if(($fid = intval($fid)) && $fid != $source) {
				$fids[] = $fid;
			}
		}
	}
	if(empty($fids)) {
		cpmsg('forums_copy_target_invalid', '', 'error');
	}

	$forumoptions = [];
	if(is_array($_GET['options']) && !empty($_GET['options'])) {
		foreach($_GET['options'] as $option) {
			if($option = trim($option)) {
				if(in_array($option, $fields['forums'])) {
					$forumoptions['forum_forum'][] = $option;
				} elseif(in_array($option, $fields['forumfields'])) {
					$forumoptions['forum_forumfield'][] = $option;
				}
			}
		}
	}

	if(empty($forumoptions)) {
		cpmsg('forums_copy_options_invalid', '', 'error');
	}
	foreach(['forum_forum', 'forum_forumfield'] as $table) {
		if(is_array($forumoptions[$table]) && !empty($forumoptions[$table])) {
			$sourceforum = C::t($table)->fetch($source);
			foreach($sourceforum as $key => $value) {
				if(!in_array($key, $forumoptions[$table])) {
					unset($sourceforum[$key]);
				}
			}
			if(!$sourceforum) {
				cpmsg('forums_copy_source_invalid', '', 'error');
			}
			C::t($table)->update($fids, $sourceforum);
		}
	}

	updatecache('forums');
	cpmsg('forums_copy_succeed', 'action=forums'.$vfidstr, 'succeed');

}
	