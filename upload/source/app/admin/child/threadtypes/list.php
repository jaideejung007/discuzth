<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$navlang = 'threadtype_infotypes';
$operation = 'type';
$changetype = 'threadsorts';

if(!submitcheck('typesubmit')) {

	$forumsarray = $fidsarray = [];
	$query = table_forum_forum::t()->fetch_all_for_threadsorts();
	foreach($query as $forum) {
		$forum[$changetype] = dunserialize($forum[$changetype]);
		if(is_array($forum[$changetype]['types'])) {
			foreach($forum[$changetype]['types'] as $typeid => $name) {
				$forumsarray[$typeid][] = '<a href="'.ADMINSCRIPT.'?action=forums&operation=edit&fid='.$forum['fid'].'&anchor=threadtypes">'.$forum['name'].'</a>';
				$fidsarray[$typeid][] = $forum['fid'];
			}
		}
	}

	$threadtypes = '';
	$query = table_forum_threadtype::t()->fetch_all_for_order();
	foreach($query as $type) {
		$threadtypes .= showtablerow('', ['class="td25"', 'class="td28"', 'class="td29"', 'class="td29"', '', ''], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$type['typeid']}\">",
			"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayordernew[{$type['typeid']}]\" value=\"{$type['displayorder']}\">",
			"<input type=\"text\" class=\"txt\" size=\"15\" name=\"namenew[{$type['typeid']}]\" value=\"".dhtmlspecialchars($type['name'])."\">",
			"<input type=\"text\" class=\"txt\" size=\"30\" name=\"descriptionnew[{$type['typeid']}]\" value=\"{$type['description']}\">",
			is_array($forumsarray[$type['typeid']]) ? '<ul class="lineheight"><li class="left">'.implode(',&nbsp;</li><li class="left"> ', $forumsarray[$type['typeid']])."</li></ul><input type=\"hidden\" name=\"fids[{$type['typeid']}]\" value=\"".implode(', ', $fidsarray[$type['typeid']])."\">" : '',
			"<a href=\"".ADMINSCRIPT."?action=threadtypes&operation=sortdetail&sortid={$type['typeid']}\" class=\"act nowrap\">{$lang['edit']}</a>".
				"<a href=\"".ADMINSCRIPT."?action=threadtypes&operation=export&sortid={$type['typeid']}\" class=\"act nowrap\">{$lang['export']}</a>",
		], TRUE);
	}

	?>
	<script type="text/JavaScript">
		var rowtypedata = [
			[
				[1, '', 'td25'],
				[1, '<input type="text" class="txt" name="newdisplayorder[]" size="2" value="">', 'td28'],
				[1, '<input type="text" class="txt" name="newname[]" size="15">', 'td29'],
				[1, '<input type="text" class="txt" name="newdescription[]" size="30" value="">', 'td29'],
				[2, '']
			],
		];
	</script>
	<?php
	shownav('forum', 'threadtype_infotypes');
	showsubmenu('threadtype_infotypes', [
		['threadtype_infotypes_type', 'threadtypes', 1],
		['threadtype_infotypes_content', 'threadtypes&operation=content', 0],
		['threadtype_infotypes_class', 'threadtypes&operation=class', 0],
		[['menu' => ($curclassname ? $curclassname : 'threadtype_infotypes_option'), 'submenu' => $classoptionmenu], '', 0]
	]);

	showformheader('threadtypes&', 'enctype', 'threadtypeform');
	showtableheader('');
	showsubtitle(['', 'display_order', cplang('name').' '.cplang('tiny_bbcode_support'), 'description', 'forums_relation', ''], 'header', ['', 'width="100"', 'width="200"', 'width="300"', '', 'width="100"', 'width="60"']);
	echo $threadtypes;
	echo '<tr><td class="td25"></td><td colspan="5"><div>'.'<span class="filebtn"><input type="hidden" name="importtype" value="file" /><input type="file" name="importfile" class="pf" size="1" onchange="uploadthreadtypexml($(\'threadtypeform\'), \''.ADMINSCRIPT.'?action=threadtypes&operation=import\');" /><a class="addtr" href="JavaScript:;">'.$lang['import'].'</a></span>'.'<a href="###" onclick="addrow(this, 0)" class="addtr">'.$lang['threadtype_infotypes_add'].'</a></div></td>';

	showsubmit('typesubmit', 'submit', 'del');
	showtablefooter();
	showformfooter();

} else {

	$updatefids = $modifiedtypes = [];

	if(is_array($_GET['delete'])) {

		if($_GET['delete']) {
			table_forum_typeoptionvar::t()->delete_by_sortid($_GET['delete']);
			table_forum_typevar::t()->delete_typevar($_GET['delete']);
			$affected_rows = table_forum_threadtype::t()->delete($_GET['delete']);
		}

		foreach($_GET['delete'] as $_GET['sortid']) {
			table_forum_optionvalue::t()->drop($_GET['sortid']);
		}

		if($_GET['delete'] && $affected_rows) {
			table_forum_thread::t()->update_sortid_by_sortid(0, $_GET['delete']);
			foreach($_GET['delete'] as $id) {
				if(is_array($_GET['namenew']) && isset($_GET['namenew'][$id])) {
					unset($_GET['namenew'][$id]);
				}
				if(!empty($_GET['fids'][$id])) {
					foreach(explode(',', $_GET['fids'][$id]) as $fid) {
						if($fid = intval($fid)) {
							$updatefids[$fid]['deletedids'][] = intval($id);
						}
					}
				}
			}
		}
	}

	if(is_array($_GET['namenew']) && $_GET['namenew']) {
		foreach($_GET['namenew'] as $typeid => $val) {
			$_GET['descriptionnew'] = is_array($_GET['descriptionnew']) ? $_GET['descriptionnew'] : [];
			$data = [
				'name' => trim($_GET['namenew'][$typeid]),
				'description' => dhtmlspecialchars(trim($_GET['descriptionnew'][$typeid])),
				'displayorder' => intval($_GET['displayordernew'][$typeid]),
				'special' => 1,
			];
			$affected_rows = table_forum_threadtype::t()->update($typeid, $data);
			if($affected_rows) {
				$modifiedtypes[] = $typeid;
			}
		}

		if($modifiedtypes = array_unique($modifiedtypes)) {
			foreach($modifiedtypes as $id) {
				if(!empty($_GET['fids'][$id])) {
					foreach(explode(',', $_GET['fids'][$id]) as $fid) {
						if($fid = intval($fid)) {
							$updatefids[$fid]['modifiedids'][] = $id;
						}
					}
				}
			}
		}
	}

	if($updatefids) {
		$query = table_forum_forum::t()->fetch_all_info_by_fids(array_keys($updatefids));
		foreach($query as $forum) {
			if($forum[$changetype] == '') continue;
			$fid = $forum['fid'];
			$forum[$changetype] = dunserialize($forum[$changetype]);
			if($updatefids[$fid]['deletedids']) {
				foreach($updatefids[$fid]['deletedids'] as $id) {
					unset($forum[$changetype]['types'][$id], $forum[$changetype]['flat'][$id], $forum[$changetype]['selectbox'][$id]);
				}
			}
			if($updatefids[$fid]['modifiedids']) {
				foreach($updatefids[$fid]['modifiedids'] as $id) {
					if(isset($forum[$changetype]['types'][$id])) {
						$_GET['namenew'][$id] = trim(strip_tags($_GET['namenew'][$id]));
						$forum[$changetype]['types'][$id] = $_GET['namenew'][$id];
						if(isset($forum[$changetype]['selectbox'][$id])) {
							$forum[$changetype]['selectbox'][$id] = $_GET['namenew'][$id];
						} else {
							$forum[$changetype]['flat'][$id] = $_GET['namenew'][$id];
						}
					}
				}
			}
			table_forum_forumfield::t()->update($fid, [$changetype => serialize($forum[$changetype])]);
		}
	}

	if(is_array($_GET['newname'])) {
		foreach($_GET['newname'] as $key => $value) {
			if($newname1 = trim(strip_tags($value))) {
				if(table_forum_threadtype::t()->checkname($newname1)) {
					cpmsg('forums_threadtypes_duplicate', '', 'error');
				}
				$data = [
					'name' => $newname1,
					'description' => dhtmlspecialchars(trim($_GET['newdescription'][$key])),
					'displayorder' => $_GET['newdisplayorder'][$key],
					'special' => 1,
				];
				table_forum_threadtype::t()->insert($data);
			}
		}
	}

	cpmsg('forums_threadtypes_succeed', 'action=threadtypes', 'succeed');

}
	