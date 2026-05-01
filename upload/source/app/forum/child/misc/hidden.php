<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_GET['formhash'] != FORMHASH) {
	showmessage('undefined_action', NULL);
}
if(!$_G['uid']) {
	showmessage('group_nopermission', NULL, ['grouptitle' => $_G['group']['grouptitle']], ['login' => 1]);
}
if(in_array($thread['fid'], $_G['setting']['security_forums_white_list']) || $thread['displayorder'] > 0 || $thread['highlight'] || $thread['digest'] || $thread['stamp'] > -1) {
	showmessage('thread_hidden_error', NULL);
}
$member = table_common_member::t()->fetch($thread['authorid']);
if(in_array($member['groupid'], $_G['setting']['security_usergroups_white_list'])) {
	showmessage('thread_hidden_error', NULL);
}
if(table_forum_forumrecommend::t()->fetch($thread['tid'])) {
	showmessage('thread_hidden_error', NULL);
}
table_forum_threadhidelog::t()->insert_hidelog($_GET['tid'], $_G['uid']);
if($thread['hidden'] + 1 == $_G['setting']['threadhidethreshold']) {
	notification_add($thread['authorid'], 'post', 'thread_hidden', ['tid' => $thread['tid'], 'subject' => $thread['subject']], 1);
}
$thide = explode('|', $_G['cookie']['thide']);
$thide = array_slice($thide, -20);
if(!in_array($_GET['tid'], $thide)) {
	$thide[] = $_GET['tid'];
}
dsetcookie('thide', implode('|', $thide), 2592000);
showmessage('thread_hidden_success', dreferer(), [], ['showdialog' => true, 'closetime' => true, 'extrajs' => '<script type="text/javascript" reload="1">$(\'normalthread_'.$_GET['tid'].'\').style.display = \'none\'</script>']);
	