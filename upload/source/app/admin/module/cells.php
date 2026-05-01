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

echo <<<EOF
<style>
.itemtitle ul li>a { padding: 5px; margin-bottom: 2px; }
</style>
EOF;

shownav('style', 'cells');

$id = $_GET['id'] ?? $_G['style']['styleid'];
$cellId = $_GET['cellId'] ?? '';
if(!preg_match('/^[\/\w_-]+$/', $cellId)) {
	$cellId = '';
}

$style = table_common_style::t()->fetch_by_styleid($id);
if(!$style) {
	cpmsg('style_not_found', '', 'error');
}

if(!$cellId) {
	require_once childfile('cells/list');
} else {
	require_once childfile('cells/cell');
}