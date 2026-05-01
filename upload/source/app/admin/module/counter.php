<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();

$pertask = isset($_GET['pertask']) ? intval($_GET['pertask']) : 100;
$current = isset($_GET['current']) && $_GET['current'] > 0 ? intval($_GET['current']) : 0;
$next = $current + $pertask;

if(submitcheck('forumsubmit', 1)) {
	require_once childfile('counter/forum');

} elseif(submitcheck('digestsubmit', 1)) {

	require_once childfile('counter/digest');

} elseif(submitcheck('membersubmit', 1)) {

	require_once childfile('counter/member');

} elseif(submitcheck('threadsubmit', 1)) {

	require_once childfile('counter/thread');

} elseif(submitcheck('movedthreadsubmit', 1)) {

	require_once childfile('counter/movedthread');

} elseif(submitcheck('specialarrange', 1)) {
	require_once childfile('counter/specialarrange');

} elseif(submitcheck('groupmembernum', 1)) {

	require_once childfile('counter/groupmembernum');

} elseif(submitcheck('groupmemberpost', 1)) {

	require_once childfile('counter/groupmemberpost');

} elseif(submitcheck('groupnum', 1)) {

	require_once childfile('counter/groupnum');

} elseif(submitcheck('blogreplynum', 1)) {

	require_once childfile('counter/blogreplynum');

} elseif(submitcheck('friendnum', 1)) {

	require_once childfile('counter/friendnum');

} elseif(submitcheck('albumpicnum', 1)) {

	require_once childfile('counter/albumpicnum');

} elseif(submitcheck('tagitemnum', 1)) {

	require_once childfile('counter/tagitemnum');

} elseif(submitcheck('setthreadcover', 1)) {

	require_once childfile('counter/setthreadcover');
} else {

	require_once childfile('counter/list');

}
