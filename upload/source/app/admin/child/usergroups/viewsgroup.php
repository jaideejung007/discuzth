<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$sgroupid = $_GET['sgroupid'];
$num = table_common_member::t()->count_by_groupid($sgroupid);
$sgroups = '';
foreach(table_common_member::t()->fetch_all_by_groupid($sgroupid, 0, 80) as $uid => $member) {
	$sgroups .= '<li><a href="home.php?mod=space&uid='.$uid.'" target="_blank">'.$member['username'].'</a></li>';
}
ajaxshowheader();
echo '<ul class="userlist"><li class="unum">'.$lang['usernum'].$num.($num > 80 ? '&nbsp;<a href="'.ADMINSCRIPT.'?action=members&operation=search&submit=yes&groupid='.$sgroupid.'">'.$lang['more'].'&raquo;</a>' : '').'</li>'.$sgroups.'</ul>';
ajaxshowfooter();
	