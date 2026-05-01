<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(table_common_member::t()->fetch_by_username(trim($_GET['username'])) || table_common_member_archive::t()->fetch_by_username(trim($_GET['username']))) {
	showmessage('<img src="'.$_G['style']['imgdir'].'/check_right.gif" width="13" height="13">', '', [], ['msgtype' => 3]);
} else {
	showmessage('username_nonexistence', '', [], ['msgtype' => 3]);
}
	