<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['uid']) {
	showmessage('not_loggedin', NULL, [], ['login' => 1]);
}

$myrepeatsusergroups = (array)dunserialize($_G['cache']['plugin']['myrepeats']['usergroups']);
if(in_array('', $myrepeatsusergroups)) {
	$myrepeatsusergroups = [];
}
$singleprem = FALSE;
$permusers = $permuids = [];
if(!in_array($_G['groupid'], $myrepeatsusergroups)) {
	$singleprem = TRUE;
}

foreach(myrepeats\table_myrepeats::t()->fetch_all_by_username($_G['username']) as $user) {
	$permuids[] = $user['uid'];
}
foreach(table_common_member::t()->fetch_all_by_uid($permuids, ['uid', 'loginname']) as $user) {
	$permusers[$user['uid']] = $user['loginname'];
}
if(!$permusers && $singleprem) {
	showmessage('myrepeats:usergroup_disabled');
}

if($_GET['pluginop'] == 'add' && submitcheck('adduser')) {
	if($singleprem && in_array($_GET['usernamenew'], $permusers) || !$singleprem) {
		$usernamenew = addslashes(strip_tags($_GET['usernamenew']));
		$logindata = addslashes(authcode($_GET['passwordnew']."\t".$_GET['questionidnew']."\t".$_GET['answernew'], 'ENCODE', $_G['config']['security']['authkey']));
		if(myrepeats\table_myrepeats::t()->count_by_uid_username($_G['uid'], $usernamenew)) {
			DB::query('UPDATE ' .DB::table('myrepeats')." SET logindata='$logindata' WHERE uid='{$_G['uid']}' AND username='$usernamenew'");
		} else {
			$_GET['commentnew'] = addslashes($_GET['commentnew']);
			DB::query('INSERT INTO ' .DB::table('myrepeats')." (uid, username, logindata, comment) VALUES ('{$_G['uid']}', '$usernamenew', '$logindata', '".strip_tags($_GET['commentnew'])."')");
		}
		dsetcookie('mrn', '');
		dsetcookie('mrd', '');
		showmessage('myrepeats:adduser_succeed', 'home.php?mod=spacecp&ac=plugin&id=myrepeats:memcp', ['usernamenew' => stripslashes($usernamenew)]);
	}
} elseif($_GET['pluginop'] == 'update' && submitcheck('updateuser')) {
	if(!empty($_GET['delete'])) {
		myrepeats\table_myrepeats::t()->delete_by_uid_usernames($_G['uid'], $_GET['delete']);
	}
	$_GET['comment'] = daddslashes($_GET['comment']);
	foreach($_GET['comment'] as $user => $v) {
		myrepeats\table_myrepeats::t()->update_comment_by_uid_username($_G['uid'], $user, strip_tags($v));
	}
	dsetcookie('mrn', '');
	dsetcookie('mrd', '');
	showmessage('myrepeats:updateuser_succeed', 'home.php?mod=spacecp&ac=plugin&id=myrepeats:memcp');
}

$username = empty($_GET['username']) ? '' : htmlspecialchars($_GET['username']);

$repeatusers = [];
foreach(myrepeats\table_myrepeats::t()->fetch_all_by_uid($_G['uid']) as $myrepeat) {
	$myrepeat['lastswitch'] = $myrepeat['lastswitch'] ? dgmdate($myrepeat['lastswitch']) : '';
	$myrepeat['usernameenc'] = rawurlencode($myrepeat['username']);
	$myrepeat['comment'] = htmlspecialchars($myrepeat['comment']);
	$repeatusers[] = $myrepeat;
}

$_G['basescript'] = 'home';

