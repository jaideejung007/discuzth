<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowavatarupload']) {
	showmessage('no_privilege_upload_avatar');
}

if(submitcheck('avatarsubmit')) {
	showmessage('do_success', 'cp.php?ac=avatar&quickforward=1');
}

loaducenter();
if(UC_STANDALONE) {
	define('UC_AVTAPI', $_G['siteurl'].'api/avatar');
}
$uc_avatarflash = uc_avatar($_G['uid'], 'virtual', 0);
$uc_avatarflash[] = 'standalone';
$uc_avatarflash[] = UC_STANDALONE;

if(empty($space['avatarstatus']) && uc_check_avatar($_G['uid'], 'middle')) {
	table_common_member::t()->update($_G['uid'], ['avatarstatus' => '1']);

	updatecreditbyaction('setavatar');
}
$reload = intval($_GET['reload']);
$actives = ['avatar' => ' class="a"'];
include template('home/spacecp_avatar');

