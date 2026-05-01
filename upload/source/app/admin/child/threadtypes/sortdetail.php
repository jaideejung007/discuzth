<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('sortdetailsubmit')) {
	$threadtype = table_forum_threadtype::t()->fetch($_GET['sortid']);
	$threadtype['modelid'] = isset($_GET['modelid']) ? intval($_GET['modelid']) : $threadtype['modelid'];
	$threadtype['super'] = dunserialize($threadtype['super']);

	$sortoptions = $jsoptionids = '';
	$showoption = [];
	$typevararr = table_forum_typevar::t()->fetch_all_by_sortid($_GET['sortid'], 'ASC');
	$typeoptionarr = table_forum_typeoption::t()->fetch_all(array_keys($typevararr));
	foreach($typevararr as $option) {
		$option['title'] = $typeoptionarr[$option['optionid']]['title'];
		$option['type'] = $typeoptionarr[$option['optionid']]['type'];
		$option['identifier'] = $typeoptionarr[$option['optionid']]['identifier'];
		$jsoptionids .= "optionids.push({$option['optionid']});\r\n";
		$optiontitle[$option['identifier']] = $option['title'];
		$showoption[$option['optionid']]['optionid'] = $option['optionid'];
		$showoption[$option['optionid']]['title'] = $option['title'];
		$showoption[$option['optionid']]['type'] = $lang['threadtype_edit_vars_type_'.$option['type']];
		$showoption[$option['optionid']]['identifier'] = $option['identifier'];
		$showoption[$option['optionid']]['displayorder'] = $option['displayorder'];
		$showoption[$option['optionid']]['available'] = $option['available'];
		$showoption[$option['optionid']]['required'] = $option['required'];
		$showoption[$option['optionid']]['unchangeable'] = $option['unchangeable'];
		$showoption[$option['optionid']]['search'] = $option['search'];
		$showoption[$option['optionid']]['subjectshow'] = $option['subjectshow'];
	}
	unset($typevararr, $typeoptionarr);

	if($existoption && is_array($existoption)) {
		$optionids = [];
		foreach($existoption as $optionid => $val) {
			$optionids[] = $optionid;
		}
		foreach(table_forum_typeoption::t()->fetch_all($optionids) as $option) {
			$showoption[$option['optionid']]['optionid'] = $option['optionid'];
			$showoption[$option['optionid']]['title'] = $option['title'];
			$showoption[$option['optionid']]['type'] = $lang['threadtype_edit_vars_type_'.$option['type']];
			$showoption[$option['optionid']]['identifier'] = $option['identifier'];
			$showoption[$option['optionid']]['required'] = $existoption[$option['optionid']];
			$showoption[$option['optionid']]['available'] = 1;
			$showoption[$option['optionid']]['unchangeable'] = 0;
			$showoption[$option['optionid']]['model'] = 1;
		}
	}

	$searchtitle = $searchvalue = $searchunit = [];
	foreach($showoption as $optionid => $option) {
		$sortoptions .= showtablerow('id="optionid'.$optionid.'"', ['class="td25"', 'class="td28 td23"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$option['optionid']}\">",
			"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayorder[{$option['optionid']}]\" value=\"{$option['displayorder']}\">",
			"<input class=\"checkbox\" type=\"checkbox\" name=\"available[{$option['optionid']}]\" value=\"1\" ".($option['available'] ? 'checked' : '').' '.($option['model'] ? 'disabled' : '').'>',
			dhtmlspecialchars($option['title']),
			$option['type'],
			"<input class=\"checkbox\" type=\"checkbox\" name=\"required[$option[optionid]]\" value=\"1\" ".($option['required'] ? 'checked' : '').' '.($option['model'] ? 'disabled' : '').'>',
			"<input class=\"checkbox\" type=\"checkbox\" name=\"unchangeable[{$option['optionid']}]\" value=\"1\" ".($option['unchangeable'] ? 'checked' : '').'>',
			"<input class=\"checkbox\" type=\"checkbox\" name=\"search[{$option['optionid']}][form]\" value=\"1\" ".(getstatus($option['search'], 1) == 1 ? 'checked' : '').'>',
			"<input class=\"checkbox\" type=\"checkbox\" name=\"search[{$option['optionid']}][font]\" value=\"1\" ".(getstatus($option['search'], 2) == 1 ? 'checked' : '').'>',
			"<input class=\"checkbox\" type=\"checkbox\" name=\"subjectshow[{$option['optionid']}]\" value=\"1\" ".($option['subjectshow'] ? 'checked' : '').'>',
			"<a href=\"".ADMINSCRIPT."?action=threadtypes&operation=optiondetail&optionid={$option['optionid']}\" class=\"act\" target=\"_blank\">".$lang['edit'].'</a>'
		], TRUE);
		$searchtitle[] = '/{('.$option['identifier'].')}/e';
		$searchvalue[] = '/\[('.$option['identifier'].')value\]/e';
		$searchunit[] = '/\[('.$option['identifier'].')unit\]/e';
	}

	shownav('forum', 'threadtype_infotypes');
	require_once libfile('function/discuzcode');
	$name = discuzcode($threadtype['name'], 0, 0, 0, 1, 1, 0, 0, 0, 0, 0);
	showchildmenu([['threadtype_infotypes', 'threadtypes']], $name, [
		['config', 'threadtypes&operation=sortdetail&sortid='.$_GET['sortid'], 1],
		['threadtype_template', 'threadtypes&operation=sorttemplate&sortid='.$_GET['sortid'], 0],
	]);
	showtips('forums_edit_threadsorts_tips');

	showformheader("threadtypes&operation=sortdetail&sortid={$_GET['sortid']}");
	showtableheader('threadtype_infotypes_validity', 'nobottom');
	showsetting('threadtype_infotypes_validity', 'typeexpiration', $threadtype['expiration'], 'radio');
	showtablefooter();

	showtableheader('threadtype_super', 'nobottom');
	showsetting('threadtype_supertpl_forumdisplay', 'super[forumdisplay]', $threadtype['super']['forumdisplay'], 'text');
	showsetting('threadtype_supertpl_viewthread', 'super[viewthread]', $threadtype['super']['viewthread'], 'text');
	showtablefooter();

	showtableheader("{$threadtype['name']} - {$lang['threadtype_infotypes_add_option']}", 'noborder fixpadding');
	showtablerow('', 'id="classlist"', '');
	showtablerow('', 'id="optionlist"', '');
	showtablefooter();

	showtableheader("{$threadtype['name']} - {$lang['threadtype_infotypes_exist_option']}", 'noborder fixpadding', 'id="sortlist"');
	showsubtitle(['<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form,\'delete\')" /><label for="chkall">'.cplang('del').'</label>', 'display_order', 'available', 'name', 'type', 'required', 'unchangeable', 'threadtype_infotypes_formsearch', 'threadtype_infotypes_fontsearch', 'threadtype_infotypes_show', '']);
	echo $sortoptions;
	showsubmit('sortdetailsubmit');
	showtablefooter();

	?>

	</form>
	<script type="text/JavaScript">
		var optionids = new Array();
		<?php echo $jsoptionids;?>

		function checkedbox() {
			var tags = $('optionlist').getElementsByTagName('input');
			for (var i = 0; i < tags.length; i++) {
				if (in_array(tags[i].value, optionids)) {
					tags[i].checked = true;
				}
			}
		}

		function insertoption(optionid) {
			var x = new Ajax();
			x.optionid = optionid;
			x.get('<?php echo ADMINSCRIPT;?>?action=threadtypes&operation=sortlist&inajax=1&optionid=' + optionid, function (s, x) {
				if (!in_array(x.optionid, optionids)) {
					var div = document.createElement('div');
					div.style.display = 'none';
					$('append_parent').appendChild(div);
					div.innerHTML = '<table>' + s + '</table>';
					var tr = div.getElementsByTagName('tr');
					var trs = $('sortlist').getElementsByTagName('tr');
					tr[0].id = 'optionid' + optionid;
					trs[trs.length - 1].parentNode.appendChild(tr[0]);
					$('append_parent').removeChild(div);
					optionids.push(x.optionid);
				} else {
					$('optionid' + x.optionid).parentNode.removeChild($('optionid' + x.optionid));
					for (var i = 0; i < optionids.length; i++) {
						if (optionids[i] == x.optionid) {
							optionids[i] = 0;
						}
					}
				}
			});
		}
	</script>
	<script type="text/JavaScript">ajaxget('<?php echo ADMINSCRIPT;?>?action=threadtypes&operation=classlist', 'classlist');</script>
	<script type="text/JavaScript">ajaxget('<?php echo ADMINSCRIPT;?>?action=threadtypes&operation=optionlist&sortid=<?php echo $_GET['sortid'];?>', 'optionlist', '', '', '', checkedbox);</script>
	<?php

} else {
	$threadtype = table_forum_threadtype::t()->fetch($_GET['sortid']);
	if($_GET['typeexpiration'] != $threadtype['expiration']) {
		$query = table_forum_forum::t()->fetch_all_for_threadsorts();
		$fidsarray = [];
		foreach($query as $forum) {
			$forum['threadsorts'] = dunserialize($forum['threadsorts']);
			if(is_array($forum['threadsorts']['types'])) {
				foreach($forum['threadsorts']['types'] as $typeid => $name) {
					$typeid == $_GET['sortid'] && $fidsarray[$forum['fid']] = $forum['threadsorts'];
				}
			}
		}
		if($fidsarray) {
			foreach($fidsarray as $changefid => $forumthreadsorts) {
				$forumthreadsorts['expiration'][$_GET['sortid']] = $_GET['typeexpiration'];
				table_forum_forumfield::t()->update($changefid, ['threadsorts' => serialize($forumthreadsorts)]);
			}
		}
	}
	table_forum_threadtype::t()->update($_GET['sortid'], ['special' => 1, 'modelid' => $_GET['modelid'], 'expiration' => $_GET['typeexpiration'], 'super' => serialize($_GET['super'])]);

	if(submitcheck('sortdetailsubmit')) {

		$orgoption = $orgoptions = $addoption = [];
		foreach(table_forum_typevar::t()->fetch_all_by_sortid($_GET['sortid']) as $orgoption) {
			$orgoptions[] = $orgoption['optionid'];
		}

		$addoption = $addoption ? (array)$addoption + (array)$_GET['displayorder'] : (array)$_GET['displayorder'];

		@$newoptions = array_keys($addoption);

		if(empty($addoption)) {
			cpmsg('threadtype_infotypes_invalid', '', 'error');
		}

		@$delete = array_merge((array)$_GET['delete'], array_diff($orgoptions, $newoptions));

		if($delete) {
			if($ids = dimplode($delete)) {
				table_forum_typevar::t()->delete_typevar($_GET['sortid'], $delete);
			}
			foreach($delete as $id) {
				unset($addoption[$id]);
			}
		}

		$insertoptionid = $indexoption = [];
		$create_table_sql = $separator = $create_tableoption_sql = '';

		if(is_array($addoption) && !empty($addoption)) {
			foreach(table_forum_typeoption::t()->fetch_all(array_keys($addoption)) as $option) {
				$insertoptionid[$option['optionid']]['type'] = $option['type'];
				$insertoptionid[$option['optionid']]['identifier'] = $option['identifier'];
			}

			if(!table_forum_optionvalue::t()->showcolumns($_GET['sortid'])) {
				$fields = '';
				foreach($addoption as $optionid => $option) {
					$identifier = $insertoptionid[$optionid]['identifier'];
					if($identifier) {
						if($insertoptionid[$optionid]['type'] == 'radio') {
							$create_tableoption_sql .= "$separator$identifier smallint(6) UNSIGNED NOT NULL DEFAULT '0'";
						} elseif(in_array($insertoptionid[$optionid]['type'], ['number', 'range'])) {
							$create_tableoption_sql .= "$separator$identifier int(10) UNSIGNED NOT NULL DEFAULT '0'";
						} elseif($insertoptionid[$optionid]['type'] == 'select') {
							$create_tableoption_sql .= "$separator$identifier varchar(50) NOT NULL";
						} else {
							$create_tableoption_sql .= "$separator$identifier mediumtext NOT NULL";
						}
						$separator = ' ,';
						if(in_array($insertoptionid[$optionid]['type'], ['radio', 'select', 'number'])) {
							$indexoption[] = $identifier;
						}
					}
				}
				$fields .= ($create_tableoption_sql ? $create_tableoption_sql.',' : '')."tid mediumint(8) UNSIGNED NOT NULL DEFAULT '0',fid smallint(6) UNSIGNED NOT NULL DEFAULT '0',dateline int(10) UNSIGNED NOT NULL DEFAULT '0',expiration int(10) UNSIGNED NOT NULL DEFAULT '0',";
				$fields .= 'KEY (fid), KEY(dateline)';
				if($indexoption) {
					foreach($indexoption as $index) {
						$fields .= "$separator KEY $index ($index)";
						$separator = ' ,';
					}
				}
				$dbcharset = $_G['config']['db'][1]['dbcharset'];
				$dbcharset = empty($dbcharset) ? str_replace('-', '', CHARSET) : $dbcharset;

				table_forum_optionvalue::t()->create($_GET['sortid'], $fields, $dbcharset);
			} else {
				$tables = table_forum_optionvalue::t()->showcolumns($_GET['sortid']);

				foreach($addoption as $optionid => $option) {
					$identifier = $insertoptionid[$optionid]['identifier'];
					if(!$tables[$identifier]) {
						$fieldname = $identifier;
						if($insertoptionid[$optionid]['type'] == 'radio') {
							$fieldtype = 'smallint(6) UNSIGNED NOT NULL DEFAULT \'0\'';
						} elseif(in_array($insertoptionid[$optionid]['type'], ['number', 'range'])) {
							$fieldtype = 'int(10) UNSIGNED NOT NULL DEFAULT \'0\'';
						} elseif($insertoptionid[$optionid]['type'] == 'select') {
							$fieldtype = 'varchar(50) NOT NULL';
						} else {
							$fieldtype = 'mediumtext NOT NULL';
						}
						table_forum_optionvalue::t()->alter($_GET['sortid'], "ADD $fieldname $fieldtype");

						if(in_array($insertoptionid[$optionid]['type'], ['radio', 'select', 'number'])) {
							table_forum_optionvalue::t()->alter($_GET['sortid'], "ADD INDEX ($fieldname)");
						}
					}
				}
			}
			foreach($addoption as $id => $val) {
				$optionid = table_forum_typeoption::t()->fetch($id);
				if($optionid) {
					$data = [
						'sortid' => $_GET['sortid'],
						'optionid' => $id,
						'available' => 1,
						'required' => intval($val),
					];
					table_forum_typevar::t()->insert($data, 0, 0, 1);
					$search_bit = 0;
					foreach($_GET['search'][$id] as $key => $val) {
						if($val == 1) {
							if($key == 'font') {
								$search_bit = setstatus(2, 1, $search_bit);
							} elseif($key == 'form') {
								$search_bit = setstatus(1, 1, $search_bit);
							}
						}
					}

					table_forum_typevar::t()->update_typevar($_GET['sortid'], $id, [
						'displayorder' => $_GET['displayorder'][$id],
						'available' => $_GET['available'][$id],
						'required' => $_GET['required'][$id],
						'unchangeable' => $_GET['unchangeable'][$id],
						'search' => $search_bit,
						'subjectshow' => $_GET['subjectshow'][$id],
					]);
				} else {
					table_forum_typevar::t()->delete_typevar($_GET['sortid'], $id);
				}
			}
		}

		updatecache('threadsorts');
		cpmsg('threadtype_infotypes_succeed', 'action=threadtypes&operation=sortdetail&sortid='.$_GET['sortid'], 'succeed');

	}

}
	