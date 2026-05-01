<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$class_tag = new tag();
$tagidarray = [];
$operate_type = $newtag = $thread = '';
$tagidarray = $_GET['tagidarray'];
$operate_type = $_GET['operate_type'];
if($operate_type == 'delete') {
	$class_tag->delete_tag($tagidarray);
} elseif($operate_type == 'open') {
	table_common_tag::t()->update($tagidarray, ['status' => 0]);
} elseif($operate_type == 'close') {
	table_common_tag::t()->update($tagidarray, ['status' => 1]);
} elseif($operate_type == 'merge') {
	$data = $class_tag->merge_tag($tagidarray, $_GET['newtag']);
	if($data != 'succeed') {
		cpmsg($data);
	}
}
cpmsg('tag_admin_updated', 'action=tag&operation=admin&searchsubmit=yes&tagname='.$_GET['tagname'].'&perpage='.$_GET['perpage'].'&status='.$_GET['status'].'&page='.$_GET['page'], 'succeed');
		