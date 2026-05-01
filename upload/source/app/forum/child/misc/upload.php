<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$type = !empty($_GET['type']) && in_array($_GET['type'], ['image', 'file']) ? $_GET['type'] : 'image';
$attachexts = $imgexts = '';
$_G['group']['allowpostattach'] = $_G['forum']['allowpostattach'] != -1 && ($_G['forum']['allowpostattach'] == 1 || (!$_G['forum']['postattachperm'] && $_G['group']['allowpostattach']) || ($_G['forum']['postattachperm'] && forumperm($_G['forum']['postattachperm'])));
$_G['group']['allowpostimage'] = $_G['forum']['allowpostimage'] != -1 && ($_G['forum']['allowpostimage'] == 1 || (!$_G['forum']['postimageperm'] && $_G['group']['allowpostimage']) || ($_G['forum']['postimageperm'] && forumperm($_G['forum']['postimageperm'])));
$_G['group']['attachextensions'] = $_G['forum']['attachextensions'] ? $_G['forum']['attachextensions'] : $_G['group']['attachextensions'];
if($_G['group']['attachextensions']) {
	$imgexts = explode(',', str_replace(' ', '', $_G['group']['attachextensions']));
	$imgexts = array_intersect(['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp'], $imgexts);
	$imgexts = implode(', ', $imgexts);
} else {
	$imgexts = 'jpg, jpeg, gif, png, bmp, webp';
}
if($type == 'image' && (!$_G['group']['allowpostimage'] || !$imgexts)) {
	showmessage('no_privilege_postimage');
}
if($type == 'file' && !$_G['group']['allowpostattach']) {
	showmessage('no_privilege_postattach');
}
include template('forum/upload');
	