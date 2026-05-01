<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('linksubmit')) {

	?>
	<script type="text/JavaScript">
		var rowtypedata = [
			[
				[1, '', 'td25'],
				[1, '<input type="text" class="txt" name="newname[]" size="15">'],
				[1, '<input type="text" name="newurl[]" size="50">'],
				[1, '<input class="checkbox" type="checkbox" value="1" name="newarticle[{n}]">'],
				[1, '<input class="checkbox" type="checkbox" value="1" name="newforum[{n}]">'],
				[1, '<input class="checkbox" type="checkbox" value="1" name="newgroup[{n}]">'],
				[1, '<input class="checkbox" type="checkbox" value="1" name="newblog[{n}]">']
			]
		]
	</script>
	<?php

	shownav('extended', 'misc_relatedlink');
	showsubmenu('nav_misc_relatedlink');
	/*search={"misc_relatedlink":"action=misc&operation=relatedlink"}*/
	showtips('misc_relatedlink_tips');
	/*search*/
	$tdstyle = ['width="80"', 'width="120"', 'width="330"', 'width="75"', 'width="105"', 'width="105"', ''];
	showformheader('misc&operation=relatedlink');
	showtableheader();
	showsetting('misc_relatedlink_status', 'relatedlinkstatus', $_G['setting']['relatedlinkstatus'], 'radio');
	showtablefooter();
	showtableheader('', '', 'id="relatedlink_header"');
	showsubtitle(['', 'misc_relatedlink_edit_name', 'misc_relatedlink_edit_url', 'misc_relatedlink_extent_article', 'misc_relatedlink_extent_forum', 'misc_relatedlink_extent_group', 'misc_relatedlink_extent_blog'], 'header tbm', $tdstyle);
	showtablefooter();
	showtableheader('', '', 'id="relatedlinktable"');
	showsubtitle(['', 'misc_relatedlink_edit_name', 'misc_relatedlink_edit_url', '<label><input class="checkbox" type="checkbox" name="articleall" onclick="checkAll(\'prefix\', this.form, \'article\', \'articleall\')">'.cplang('misc_relatedlink_extent_article').'</label>', '<label><input class="checkbox" type="checkbox" name="forumall" onclick="checkAll(\'prefix\', this.form, \'forum\', \'forumall\')">'.cplang('misc_relatedlink_extent_forum').'</label>', '<label><input class="checkbox" type="checkbox" name="groupall" onclick="checkAll(\'prefix\', this.form, \'group\', \'groupall\')">'.cplang('misc_relatedlink_extent_group').'</label>', '<label><input class="checkbox" type="checkbox" name="blogall" onclick="checkAll(\'prefix\', this.form, \'blog\', \'blogall\')">'.cplang('misc_relatedlink_extent_blog').'</label>'], 'header', $tdstyle);

	$ppp = 50;
	$page = max($_GET['page'], 1);
	$start = ($page - 1) * $ppp;
	$totalcount = table_common_relatedlink::t()->count();
	$query = table_common_relatedlink::t()->range($start, $ppp, 'DESC');
	foreach($query as $link) {
		$extent = sprintf('%04b', $link['extent']);
		showtablerow('', ['class="td25"', '', '', 'class="td26"', 'class="td26"', 'class="td26"', ''], [
			'<input type="checkbox" class="checkbox" name="delete[]" value="'.$link['id'].'" />',
			'<input type="text" class="txt" name="name['.$link['id'].']}" value="'.$link['name'].'" size="15" />',
			'<input type="text" name="url['.$link['id'].']}" value="'.$link['url'].'" size="50" />',
			'<input class="checkbox" type="checkbox" value="1" name="article['.$link['id'].']}" '.($extent[0] ? 'checked' : '').'>',
			'<input class="checkbox" type="checkbox" value="1" name="forum['.$link['id'].']}" '.($extent[1] ? 'checked' : '').'>',
			'<input class="checkbox" type="checkbox" value="1" name="group['.$link['id'].']}" '.($extent[2] ? 'checked' : '').'>',
			'<input class="checkbox" type="checkbox" value="1" name="blog['.$link['id'].']" '.($extent[3] ? 'checked' : '').'>',
		]);
	}

	echo '<tr><td></td><td colspan="6"><div><a href="###" onclick="addrow(this, 0)" class="addtr">'.$lang['misc_relatedlink_add'].'</a></div></td></tr>';
	$multipage = multi($totalcount, $ppp, $page, ADMINSCRIPT.'?action=misc&operation=relatedlink');
	showsubmit('linksubmit', 'submit', 'del', '', $multipage);
	showhiddenfields(['page' => $page]);
	showtablefooter();
	showformfooter();
	echo '<script type="text/javascript">floatbottom(\'relatedlink_header\');$(\'relatedlink_header\').style.width = $(\'relatedlinktable\').offsetWidth + \'px\';</script>';

} else {

	if($_GET['delete']) {
		table_common_relatedlink::t()->delete($_GET['delete']);
	}

	if(is_array($_GET['name'])) {
		foreach($_GET['name'] as $id => $val) {
			$extent_str = intval($_GET['article'][$id]).intval($_GET['forum'][$id]).intval($_GET['group'][$id]).intval($_GET['blog'][$id]);
			$extent_str = intval($extent_str, '2');
			table_common_relatedlink::t()->update($id, [
				'name' => $_GET['name'][$id],
				'url' => $_GET['url'][$id],
				'extent' => $extent_str,
			]);
		}
	}

	if(is_array($_GET['newname'])) {
		foreach($_GET['newname'] as $key => $value) {
			if($value) {
				$extent_str = intval($_GET['newarticle'][$key]).intval($_GET['newforum'][$key]).intval($_GET['newgroup'][$key]).intval($_GET['newblog'][$key]);
				$extent_str = intval($extent_str, '2');
				table_common_relatedlink::t()->insert([
					'name' => $value,
					'url' => $_GET['newurl'][$key],
					'extent' => $extent_str,
				]);
			}
		}
	}
	table_common_setting::t()->update_setting('relatedlinkstatus', $_GET['relatedlinkstatus']);
	updatecache(['relatedlink', 'setting']);
	cpmsg('relatedlink_succeed', 'action=misc&operation=relatedlink'.(!empty($_GET['page']) ? '&page='.$_GET['page'] : ''), 'succeed');

}
	