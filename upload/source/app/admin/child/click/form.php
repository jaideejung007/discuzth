<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}


shownav('style', 'click_edit');
showsubmenu('nav_click', [
	['click_edit_blogid', 'click&idtype=blogid', $idtype == 'blogid' ? 1 : 0],
	['click_edit_picid', 'click&idtype=picid', $idtype == 'picid' ? 1 : 0],
	['click_edit_aid', 'click&idtype=aid', $idtype == 'aid' ? 1 : 0],
]);
/*search={"nav_click":"action=click"}*/
showtips('click_edit_tips');
/*search*/
showformheader('click&idtype='.$idtype);
showtableheader();
showsubtitle(['', 'display_order', '', 'available', 'name', 'click_edit_image', 'click_edit_type']);
print <<<EOF
<script type="text/JavaScript">
	var rowtypedata = [
		[
			[1,'', 'td25'],
			[1,'<input type="text" class="txt" name="newdisplayorder[]" size="3">', 'td28'],
			[1,'', 'td25'],
			[1,'<input type="checkbox" name="newavailable[]" value="1">', 'td25'],
			[1,'<input type="text" class="txt" name="newname[]" size="10">'],
			[1,'<input type="text" class="txt" name="newicon[]" size="20">'],
			[1,'', 'td23']
		]
	];
</script>
EOF;
foreach(table_home_click::t()->fetch_all_by_idtype($idtype) as $click) {
	$checkavailable = $click['available'] ? 'checked' : '';
	$click['idtype'] = cplang('click_edit_'.$click['idtype']);
	$iconurl = preg_match('/^https?:\/\//is', $click['icon']) ? $click['icon'] : STATICURL.'image/click/'.$click['icon'];
	showtablerow('', ['class="td25"', 'class="td28"', 'class="td25"', 'class="td25"', '', '', '', 'class="td23"', 'class="td25"'], [
		"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$click['clickid']}\">",
		"<input type=\"text\" class=\"txt\" size=\"3\" name=\"displayorder[{$click['clickid']}]\" value=\"{$click['displayorder']}\">",
		"<img src=\"$iconurl\">",
		"<input class=\"checkbox\" type=\"checkbox\" name=\"available[{$click['clickid']}]\" value=\"1\" $checkavailable>",
		"<input type=\"text\" class=\"txt\" size=\"10\" name=\"name[{$click['clickid']}]\" value=\"{$click['name']}\">",
		"<input type=\"text\" class=\"txt\" size=\"20\" name=\"icon[{$click['clickid']}]\" value=\"{$click['icon']}\">",
		$click['idtype']
	]);
}
echo '<tr><td></td><td colspan="8"><div><a href="###" onclick="addrow(this, 0)" class="addtr">'.$lang['click_edit_addnew'].'</a></div></td></tr>';
showsubmit('clicksubmit', 'submit', 'del');
showtablefooter();
showformfooter();

		