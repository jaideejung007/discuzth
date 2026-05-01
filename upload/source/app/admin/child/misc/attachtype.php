<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('typesubmit')) {

	$attachtypes = '';
	$query = DB::query('SELECT * FROM '.DB::table('forum_attachtype')." WHERE fid='0'");
	while($type = DB::fetch($query)) {
		$type['maxsize'] = round($type['maxsize'] / 1024);
		$attachtypes .= showtablerow('', ['class="td25"', 'class="td24"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$type['id']}\" />",
			"<input type=\"text\" class=\"txt\" size=\"10\" name=\"extension[{$type['id']}]\" value=\"{$type['extension']}\" />",
			"<input type=\"text\" class=\"txt\" size=\"15\" name=\"maxsize[{$type['id']}]\" value=\"{$type['maxsize']}\" />"
		], TRUE);
	}

	?>
	<script type="text/JavaScript">
		var rowtypedata = [
			[
				[1, '', 'td25'],
				[1, '<input name="newextension[]" type="text" class="txt" size="10">', 'td24'],
				[1, '<input name="newmaxsize[]" type="text" class="txt" size="15">']
			]
		];
	</script>
	<?php

	shownav('global', 'nav_posting_attachtype');
	showsubmenu('nav_posting_attachtype');
	/*search={"nav_posting_attachtype":"action=misc&operation=attachtype"}*/
	showtips('misc_attachtype_tips');
	/*search*/
	showformheader('misc&operation=attachtype');
	showtableheader('', 'nomargin');
	showtablerow('class="partition"', ['class="td25"', 'class="td24"'], ['', cplang('misc_attachtype_ext'), cplang('misc_attachtype_maxsize')]);
	echo $attachtypes;
	echo '<tr><td></td><td colspan="2"><div><a href="###" onclick="addrow(this, 0)" class="addtr">'.$lang['misc_attachtype_add'].'</a></div></tr>';
	showsubmit('typesubmit', 'submit', 'del');
	showtablefooter();
	showformfooter();

} else {

	if($ids = dimplode($_GET['delete'])) {
		DB::delete('forum_attachtype', "id IN ($ids) AND fid='0'");
	}

	if(is_array($_GET['extension'])) {
		foreach($_GET['extension'] as $id => $val) {
			DB::update('forum_attachtype', [
				'extension' => $_GET['extension'][$id],
				'maxsize' => $_GET['maxsize'][$id] * 1024,
			], DB::field('id', $id));
		}
	}

	if(is_array($_GET['newextension'])) {
		foreach($_GET['newextension'] as $key => $value) {
			if($newextension1 = trim($value)) {
				if(table_forum_attachtype::t()->count_by_extension_fid($newextension1, 0)) {
					cpmsg('attachtypes_duplicate', '', 'error');
				}
				table_forum_attachtype::t()->insert([
					'extension' => $newextension1,
					'maxsize' => $_GET['newmaxsize'][$key] * 1024,
					'fid' => 0
				]);
			}
		}
	}

	updatecache('attachtype');
	cpmsg('attachtypes_succeed', 'action=misc&operation=attachtype', 'succeed');

}
	