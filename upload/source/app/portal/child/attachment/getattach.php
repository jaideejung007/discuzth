<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/attachment');
if($attach['isimage']) {
	require_once libfile('function/home');
	$smallimg = pic_get($attach['attachment'], 'portal', $attach['thumb'], $attach['remote']);
	$bigimg = pic_get($attach['attachment'], 'portal', 0, $attach['remote']);
	$coverstr = addslashes(serialize(['pic' => 'portal/'.$attach['attachment'], 'thumb' => $attach['thumb'], 'remote' => $attach['remote']]));
}
$attach['filetype'] = attachtype($attach['filetype']."\t".$attach['filetype']);
$attach['filesize'] = sizecount($attach['filesize']);
include template('portal/portal_attachment');
exit;
	