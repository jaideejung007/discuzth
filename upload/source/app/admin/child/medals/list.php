<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('medalsubmit')) {
	shownav('extended', 'nav_medals', 'admin');
	showsubmenu('nav_medals', [
		['admin', 'medals', 1],
		['nav_medals_confer', 'members&operation=confermedal', 0],
		['nav_medals_mod', 'medals&operation=mod', 0]
	]);
	/*search={"nav_medals":"action=medals"}*/
	showtips('medals_tips');
	/*search*/
	showformheader('medals');
	showtableheader('medals_list', 'fixpadding');
	showsubtitle(['', 'display_order', 'available', 'name', 'description', 'medals_image', 'medals_type', '']);

	?>
	<script type="text/JavaScript">
		var rowtypedata = [
			[
				[1, '', 'td25'],
				[1, '<input type="text" class="txt" name="newdisplayorder[]" size="3">', 'td28'],
				[1, '', 'td25'],
				[1, '<input type="text" class="txt" name="newname[]" size="10">'],
				[1, '<input type="text" class="txt" name="newdescription[]" size="30">'],
				[1, '<input type="text" class="txt" name="newimage[]" size="20">'],
				[1, '', 'td23'],
				[1, '', 'td25']
			]
		];
	</script>
	<?php
	$perpage = 50;
	$start = ($_G['page'] - 1) * $perpage;
	$count = table_forum_medal::t()->count_by_available(false);
	$multi = multi($count, $perpage, $page, ADMINSCRIPT.'?action=medals');
	foreach(table_forum_medal::t()->fetch_all_data(false, $start, $perpage) as $medal) {
		$checkavailable = $medal['available'] ? 'checked' : '';
		switch($medal['type']) {
			case 0:
				$medal['type'] = cplang('medals_adminadd');
				break;
			case 1:
				$medal['type'] = $medal['price'] ? cplang('medals_buy') : cplang('medals_register');
				break;
			case 2:
				$medal['type'] = cplang('modals_moderate');
				break;
		}
		$image = preg_match('/^https?:\/\//is', $medal['image']) ? $medal['image'] : STATICURL.'image/common/'.$medal['image'];
		showtablerow('', ['class="td25"', 'class="td25"', 'class="td25"', '', '', '', 'class="td23"', 'class="td25"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$medal['medalid']}\">",
			"<input type=\"text\" class=\"txt\" size=\"3\" name=\"displayorder[{$medal['medalid']}]\" value=\"{$medal['displayorder']}\">",
			"<input class=\"checkbox\" type=\"checkbox\" name=\"available[{$medal['medalid']}]\" value=\"1\" $checkavailable>",
			"<input type=\"text\" class=\"txt\" size=\"10\" name=\"name[{$medal['medalid']}]\" value=\"{$medal['name']}\">",
			"<input type=\"text\" class=\"txt\" size=\"30\" name=\"description[{$medal['medalid']}]\" value=\"{$medal['description']}\">",
			"<input type=\"text\" class=\"txt\" size=\"20\" name=\"image[{$medal['medalid']}]\" value=\"{$medal['image']}\"><img style=\"vertical-align:middle;max-height:35px;\" src=\"$image\">",
			$medal['type'],
			"<a href=\"".ADMINSCRIPT."?action=medals&operation=edit&medalid={$medal['medalid']}\" class=\"act\">{$lang['detail']}</a>"
		]);
	}

	echo '<tr><td></td><td colspan="8"><div><a href="###" onclick="addrow(this, 0)" class="addtr">'.$lang['medals_addnew'].'</a></div></td></tr>';
	showsubmit('medalsubmit', 'submit', 'del', '', $multi);
	showtablefooter();
	showformfooter();

} else {

	if(is_array($_GET['delete']) && $_GET['delete']) {
		$ids = [];
		foreach($_GET['delete'] as $id) {
			$ids[] = $id;
		}
		table_forum_medal::t()->delete($_GET['delete']);
	}

	if(is_array($_GET['name'])) {
		foreach($_GET['name'] as $id => $val) {
			$update = [
				'available' => $_GET['available'][$id],
				'displayorder' => intval($_GET['displayorder'][$id])
			];
			if($_GET['name'][$id]) {
				$update['name'] = dhtmlspecialchars($_GET['name'][$id]);
			}
			if($_GET['description'][$id]) {
				$update['description'] = dhtmlspecialchars($_GET['description'][$id]);
			}
			if($_GET['image'][$id]) {
				$update['image'] = dhtmlspecialchars($_GET['image'][$id]);
			}
			table_forum_medal::t()->update($id, $update);

		}
	}

	if(is_array($_GET['newname'])) {
		foreach($_GET['newname'] as $key => $value) {
			if($value != '' && $_GET['newimage'][$key] != '') {
				$data = ['name' => dhtmlspecialchars($value),
					'available' => $_GET['newavailable'][$key],
					'image' => $_GET['newimage'][$key],
					'displayorder' => intval($_GET['newdisplayorder'][$key]),
					'description' => dhtmlspecialchars($_GET['newdescription'][$key]),
				];
				table_forum_medal::t()->insert($data);
			}
		}
	}

	updatecache('setting');
	updatecache('medals');
	cpmsg('medals_succeed', 'action=medals', 'succeed');

}
	