<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$pageadd = '';
if(!getstatus($_G['thread'], 4)) {
	$page = ceil(($_G['thread']['special'] ? $_G['thread']['replies'] : $_G['thread']['replies'] + 1) / $_G['ppp']);
	$pageadd = $page > 1 ? '&page='.$page : '';
}

dheader('Location: forum.php?mod=viewthread&tid='.$_G['tid'].$pageadd.'#lastpost');
	