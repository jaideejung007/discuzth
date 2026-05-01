<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('cardsubmit')) {
	if(is_array($_POST['delete'])) {
		table_common_card_type::t()->delete($_POST['delete']);
		table_common_card::t()->update_by_typeid($_POST['delete'], ['typeid' => 1]);
	}
	if(is_array($_POST['newtype'])) {
		$_POST['newtype'] = dhtmlspecialchars(daddslashes($_POST['newtype']));
		foreach($_POST['newtype'] as $key => $val) {
			if(trim($val)) {
				table_common_card_type::t()->insert(['typename' => trim($val)]);
			}
		}
	}
}
/*search={"card_type_tips":"action=card&operation=type"}*/
showtips('card_type_tips');
/*search*/
showformheader('card&operation=type&');
showtableheader();
showtablerow('class="header"', ['', ''], [
	cplang('delete'),
	cplang('card_type'),
]);

showtablerow('', '', [
	'<input class="checkbox" type="checkbox" value ="" disabled="disabled" >',
	cplang('card_type_default'),
]);
foreach(table_common_card_type::t()->range(0, 0, 'ASC') as $result) {
	showtablerow('', '', [
		'<input class="checkbox" type="checkbox" name ="delete[]" value ="'.$result['id'].'" >',
		$result['typename'],
	]);
}
echo <<<EOT
<script type="text/JavaScript">
	var rowtypedata = [
		[[1,''], [1,'<input type="text" class="txt" size="30" name="newtype[]">']],
	];
	</script>
EOT;
echo '<tr><td></td><td colspan="2"><div><a href="###" onclick="addrow(this, 0)" class="addtr">'.$lang['add_new'].'</a></div></td></tr>';
showsubmit('cardsubmit', 'submit', 'select_all');
showtablefooter();
showformfooter();
