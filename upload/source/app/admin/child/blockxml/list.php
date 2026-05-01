<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showsubmenu('blockxml', [
	['list', 'blockxml', 1],
	['add', 'blockxml&operation=add', 0]
]);

showtableheader('blockxml_list');
showsubtitle(['blockxml_name', 'blockxml_xmlurl', 'operation']);
foreach(table_common_block_xml::t()->range() as $row) {
	showtablerow('', ['class=""', 'class=""', 'class="td28"'], [
		$row['name'],
		$row['url'],
		"<a href=\"".ADMINSCRIPT."?action=blockxml&operation=update&id={$row['id']}\">".cplang('blockxml_update').'</a>&nbsp;&nbsp;'.
		"<a href=\"".ADMINSCRIPT."?action=blockxml&operation=edit&id={$row['id']}\">".cplang('edit').'</a>&nbsp;&nbsp;'.
		"<a href=\"".ADMINSCRIPT."?action=blockxml&operation=delete&id={$row['id']}\">".cplang('delete').'</a>&nbsp;&nbsp;'
	]);
}
showtablefooter();
showformfooter();
	