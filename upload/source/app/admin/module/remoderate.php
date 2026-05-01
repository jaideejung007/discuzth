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

if(submitcheck('threadsubmit', 1)) {

	require_once childfile('remoderate/thread');

} elseif(submitcheck('blogsubmit', 1)) {

	require_once childfile('remoderate/blog');

} elseif(submitcheck('picsubmit', 1)) {

	require_once childfile('remoderate/pic');

} elseif(submitcheck('doingsubmit', 1)) {

	require_once childfile('remoderate/doing');

} elseif(submitcheck('sharesubmit', 1)) {

	require_once childfile('remoderate/share');

} elseif(submitcheck('commentsubmit', 1)) {

	require_once childfile('remoderate/comment');

} elseif(submitcheck('articlesubmit', 1)) {

	require_once childfile('remoderate/article');

} elseif(submitcheck('articlecommentsubmit', 1)) {

	require_once childfile('remoderate/articlecomment');

} elseif(submitcheck('topiccommentsubmit', 1)) {

	require_once childfile('remoderate/topiccomment');

} else {

	require_once childfile('remoderate/base');

}