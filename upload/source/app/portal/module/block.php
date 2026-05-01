<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$bid = max(0, intval($_GET['bid']));
if(empty($bid)) {
	showmessage('block_choose_bid', dreferer());
}
block_get($bid);
if(!isset($_G['block'][$bid])) {
	showmessage('block_noexist', dreferer());
}
$block = &$_G['block'][$bid];
$blockmoreurl = $block['param']['moreurl'] = $block['param']['moreurl'] ?? ['perpage' => 20, 'seotitle' => $block['name'], 'keywords' => '', 'description' => ''];
$blocktype = $block['blockclass'];
if(!in_array($blocktype, ['forum_thread', 'portal_article', 'group_thread'], true)) {
	showmessage('block_nomore', dreferer());
}

$perpage = max(1, intval($blockmoreurl['perpage']));
$curpage = max(1, intval($_GET['page']));
$start = ($curpage - 1) * $perpage;
$count = table_common_block_item_data::t()->count_by_bid($bid);
$list = $count ? table_common_block_item_data::t()->fetch_all_by_bid($bid, 1, $start, $perpage) : [];
$multipage = $count ? multi($count, $perpage, $curpage, 'portal.php?mod=block&bid='.$bid) : '';

$navtitle = $blockmoreurl['seotitle'];
$metakeywords = $blockmoreurl['seokeywords'];
$metadescription = $blockmoreurl['seodescription'];

$file = 'portal/block_more_'.$blocktype;
include template('diy:'.$file);

