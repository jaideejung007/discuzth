<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$itemtype = !empty($_GET['itemtype']) ? $_GET['itemtype'] : '';
[$itemtype, $addVariable] = explode('/', $itemtype);
$kw = !empty($_GET['kw']) ? trim($_GET['kw']) : '';
$addVariable = !empty($_GET['kwid']) ? $addVariable : '';

if(!$itemtype) {
	exit;
}

$multi = 0;
$s = [];
if($itemtype == 'tag') {
	$title = cplang('forums_edit_perm_usertag_select');
	$tags = table_common_tag::t()->fetch_all_by_status(3, $kw);
	foreach($tags as $tag) {
		$s[] = [$tag['tagid'], $tag['tagname'], 't'];
	}
}
$s = '<div id="'.$itemtype.'_show" class="permitem"></div><script reload="1">perm_show_item('.json_encode([
		'type' => $itemtype, 'data' => $s, 'multi' => $multi, 'addVariable' => $addVariable, 'kwid' => !empty($_GET['kwid']) ? $_GET['kwid'] : '',
	]).');</script>';
$id = 'si'.random(4);
$s = '<input id="'.$id.'" onkeydown="perm_enter(event, this)" value="'.dhtmlspecialchars($kw).'" /> <a href="javascript:;" style="vertical-align: middle" onclick="perm_search(\''.$_GET['itemtype'].'\', \''.$id.'\')">'.cplang('search').'</a>'.$s;
$closeid = 'permitem_menu';
require_once template('admin/window');
exit;