<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('classsubmit')) {
	?>
	<script type="text/JavaScript">
		var rowtypedata = [
			[
				[1, '', 'td25'],
				[1, '<input type="text" class="txt" name="newdisplayorder[]" size="2" value="">', 'td25'],
				[1, '<input type="text" class="txt" name="newtitle[]" size="15">', 'td29'],
			],
		];
	</script>
	<?php
	foreach($classlists as $class) {
		$classlist .= showtablerow('', ['class="td25"', 'class="td25"', 'class="td29"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$class['optionid']}\">",
			"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayordernew[{$class['optionid']}]\" value=\"{$class['displayorder']}\">",
			"<input type=\"text\" class=\"txt\" size=\"15\" name=\"titlenew[{$class['optionid']}]\" value=\"".dhtmlspecialchars($class['title'])."\">",
		], TRUE);
	}

	shownav('forum', 'threadtype_infotypes');
	showsubmenu('threadtype_infotypes', [
		['threadtype_infotypes_type', 'threadtypes', 0],
		['threadtype_infotypes_content', 'threadtypes&operation=content', 0],
		['threadtype_infotypes_class', 'threadtypes&operation=class', 1],
		[['menu' => ($curclassname ? $curclassname : 'threadtype_infotypes_option'), 'submenu' => $classoptionmenu], 0]
	]);
	showformheader('threadtypes&operation=class', 'enctype', 'threadtypeform');
	showtableheader('');
	showsubtitle(['', 'display_order', cplang('name')], 'header');
	echo $classlist;
	echo '<tr><td colspan="5"><div><a href="###" onclick="addrow(this, 0)" class="addtr">'.$lang['threadtype_infotypes_add'].'</a></div></td>';

	showsubmit('classsubmit', 'submit', 'del');
	showtablefooter();
	showformfooter();
} else {
	if(is_array($_GET['delete'])) {
		foreach($_GET['delete'] as $id) {
			$options = table_forum_typeoption::t()->fetch_all_by_classid($id);
			if($options) {
				cpmsg('forums_threadtypes_delete_error', '', 'error');
			}
			table_forum_typeoption::t()->delete($id);
		}
	}
	if(is_array($_GET['titlenew']) && $_GET['titlenew']) {
		foreach($_GET['titlenew'] as $optionid => $val) {
			$data = [
				'title' => trim($_GET['titlenew'][$optionid]),
				'displayorder' => intval($_GET['displayordernew'][$optionid]),
			];
			$affected_rows = table_forum_typeoption::t()->update($optionid, $data);
		}
	}
	if(is_array($_GET['newtitle'])) {
		foreach($_GET['newtitle'] as $key => $value) {
			if($newtitle1 = trim(strip_tags($value))) {
				$data = [
					'title' => $newtitle1,
					'displayorder' => $_GET['newdisplayorder'][$key],
				];
				table_forum_typeoption::t()->insert($data);
			}
		}
	}

	cpmsg('forums_threadtypes_succeed', 'action=threadtypes&operation=class', 'succeed');
}
	