<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!table_common_setting::t()->fetch_all_field()) {
	cpmsg('dbcheck_permissions_invalid', '', 'error');
}
$installSqlPath = DISCUZ_ROOT.'./install/sql/sql_install.php';
$step = max(1, intval($_GET['step']));
if($step == 3) {

	if(!file_exists('source/data/admincp/discuzdb.md5')) {
		cpmsg('dbcheck_nofound_md5file', '', 'error');
	}

	$dbcharset = $discuz->config['db'][1]['dbcharset'];
	unset($dbuser, $dbpw, $dbname);

	$fp = fopen(DISCUZ_ROOT.'./source/data/admincp/discuzdb.md5', 'rb');
	$discuzdb = fread($fp, filesize(DISCUZ_ROOT.'./source/data/admincp/discuzdb.md5'));
	fclose($fp);
	$dbmd5 = substr($discuzdb, 0, 32);
	$discuzdb = dunserialize(substr($discuzdb, 34));
	$settingsdata = $discuzdb[1];
	$discuzdb = $discuzdb[0][0];
	$repair = !empty($_GET['repair']) ? $_GET['repair'] : [];
	$setting = !empty($_GET['setting']) ? $_GET['setting'] : [];
	$missingtable = !empty($_GET['missingtable']) ? $_GET['missingtable'] : [];
	$repairtable = is_array($_GET['repairtable']) && !empty($_GET['repairtable']) ? $_GET['repairtable'] : [];

	$except = ['threads' => ['sgid']];
	foreach(table_common_member_profile_setting::t()->range_setting() as $profilefields) {
		$except['memberfields'][] = 'field_'.$profilefields['fieldid'];
	}

	if(submitcheck('repairsubmit') && (!empty($repair) || !empty($setting) || !empty($repairtable) || !empty($missingtable))) {
		$error = '';
		$errorcount = 0;
		$alter = $fielddefault = [];

		foreach($missingtable as $value) {
			if(!isset($installdata)) {
				$fp = fopen($installSqlPath, 'rb');
				$installdata = fread($fp, filesize($installSqlPath));
				fclose($fp);
				$installdata = substr($installdata, 30);
			}
			preg_match('/CREATE TABLE '.DB::table($value).'\s+\(.+?;/is', $installdata, $a);
			DB::query(createtable($a[0], $dbcharset));
		}

		foreach($repair as $value) {
			list($r_table, $r_field, $option) = explode('|', $value);
			if(!in_array($r_table, $repairtable) || $option != 'modify') {
				if($fieldsquery = DB::query('SHOW FIELDS FROM '.DB::table($r_table), 'SILENT')) {
					while($fields = DB::fetch($fieldsquery)) {
						$fielddefault[$r_table][$fields['Field']] = $fields['Default'];
					}
				}

				$field = $discuzdb[$r_table][$r_field];
				$altersql = '`'.$field['Field'].'` '.$field['Type'];
				$altersql .= $field['Null'] == 'NO' ? ' NOT NULL' : '';
				$altersql .= in_array($fielddefault[$r_table][$field['Field']], ['', '0']) && in_array($field['Default'], ['', '0']) ||
				$field['Null'] == 'NO' && $field['Default'] == '' ||
				preg_match('/text/i', $field['Type']) || preg_match('/auto_increment/i', $field['Extra']) ?
					'' : ' default \''.$field['Default'].'\'';
				$altersql .= $field['Extra'] != '' ? ' '.$field['Extra'] : '';
				$altersql = $option == 'modify' ? 'MODIFY COLUMN '.$altersql : 'ADD COLUMN '.$altersql;
				$alter[$r_table][] = $altersql;
			}
		}

		foreach($alter as $r_table => $sqls) {
			DB::query("ALTER TABLE `$tablepre$r_table` ".implode(',', $sqls), 'SILENT');
			if($sqlerror = DB::error()) {
				$errorcount += count($sqls);
				$error .= $sqlerror.'<br /><br />';
			}
		}
		$alter = [];

		foreach($repairtable as $value) {
			foreach($discuzdb[$value] as $field) {
				if(!isset($fielddefault[$value]) && $fieldsquery = DB::query('SHOW FIELDS FROM '.DB::table($value), 'SILENT')) {
					while($fields = DB::fetch($fieldsquery)) {
						$fielddefault[$value][$fields['Field']] = $fields['Default'];
					}
				}
				$altersql = '`'.$field['Field'].'` '.$field['Type'];
				$altersql .= $field['Null'] == 'NO' ? ' NOT NULL' : '';
				$altersql .= in_array($fielddefault[$value][$field['Field']], ['', '0']) && in_array($field['Default'], ['', '0']) ||
				$field['Null'] == 'NO' && $field['Default'] == '' ||
				preg_match('/text/i', $field['Type']) || preg_match('/auto_increment/i', $field['Extra']) ?
					'' : ' default \''.$field['Default'].'\'';
				$altersql .= $field['Extra'] != '' ? ' '.$field['Extra'] : '';
				$altersql = 'MODIFY COLUMN '.$altersql;
				$alter[$value][] = $altersql;
			}
		}

		foreach($alter as $r_table => $sqls) {
			DB::query('ALTER TABLE `'.DB::table($r_table).'` '.implode(',', $sqls), 'SILENT');
			if($sqlerror = DB::error()) {
				$errorcount += count($sqls);
				$error .= $sqlerror.'<br /><br />';
			}
		}

		if(!empty($setting)) {
			$settingsdatanow = $newsettings = [];
			$allsetting = table_common_setting::t()->fetch_all_setting();
			$settingsdatanew = array_keys($allsetting);
			unset($allsetting);
			$settingsdellist = is_array($settingsdata) ? array_diff($settingsdata, $settingsdatanew) : [];
			if($setting['del'] && is_array($settingsdellist)) {
				foreach($settingsdellist as $variable) {
					$newsettings[$variable] = '';
				}
			}
			if($newsettings) {
				table_common_setting::t()->update_batch($newsettings);
				updatecache('setting');
			}
		}

		if($errorcount) {
			cpmsg('dbcheck_repair_error', '', 'error', ['errorcount' => $errorcount, 'error' => $error]);
		} else {
			cpmsg('dbcheck_repair_completed', 'action=db&operation=dbcheck&step=3', 'succeed');
		}
	}

	$installexists = file_exists($installSqlPath);
	$discuzdbnew = $deltables = $excepttables = $missingtables = $charseterror = [];
	foreach($discuzdb as $dbtable => $fields) {
		if($fieldsquery = DB::query('SHOW FIELDS FROM '.DB::table($dbtable), 'SILENT')) {
			while($fields = DB::fetch($fieldsquery)) {
				$r = '/^'.$tablepre.'/';
				$cuttable = preg_replace($r, '', $dbtable);
				if($cuttable == 'memberfields' && preg_match('/^field\_\d+$/', $fields['Field'])) {
					unset($discuzdbnew[$cuttable][$fields['Field']]);
					continue;
				}
				$discuzdbnew[$cuttable][$fields['Field']]['Field'] = $fields['Field'];
				$discuzdbnew[$cuttable][$fields['Field']]['Type'] = $fields['Type'];
				$discuzdbnew[$cuttable][$fields['Field']]['Null'] = $fields['Null'] == '' ? 'NO' : $fields['Null'];
				$discuzdbnew[$cuttable][$fields['Field']]['Extra'] = $fields['Extra'];
				$discuzdbnew[$cuttable][$fields['Field']]['Default'] = $fields['Default'] == '' || $fields['Default'] == '0' || str_starts_with($fields['Type'], 'varbinary') ? '' : $fields['Default'];
			}
			ksort($discuzdbnew[$cuttable]);
		} else {
			$missingtables[] = '<span style="float:left;width:33%">'.(($installexists ? '<input name="missingtable[]" type="checkbox" class="checkbox" value="'.$dbtable.'">' : '').$tablepre.$dbtable).'</span>';
			$excepttables[] = $dbtable;
		}
	}

	$dbcharset = strtoupper($dbcharset) == 'UTF-8' ? 'UTF8' : strtoupper($dbcharset);
	$query = DB::query("SHOW TABLE STATUS LIKE '$tablepre%'");
	while($tables = DB::fetch($query)) {
		$r = '/^'.$tablepre.'/';
		$cuttable = preg_replace($r, '', $tables['Name']);
		$tabledbcharset = substr($tables['Collation'], 0, strpos($tables['Collation'], '_'));
		if($dbcharset != strtoupper($tabledbcharset)) {
			$charseterror[] = '<span style="float:left;width:33%">'.$tablepre.$cuttable.'('.$tabledbcharset.')</span>';
		}
	}

	$dbmd5new = md5(serialize($discuzdbnew));

	$settingsdatanow = [];
	$allsetting = table_common_setting::t()->fetch_all_setting();
	$settingsdatanew = array_keys($allsetting);
	unset($allsetting);
	$settingsdellist = is_array($settingsdata) ? array_diff($settingsdata, $settingsdatanew) : [];

	if((substr($dbmd5, 0, 16) == substr($dbmd5new, 0, 16) || substr($dbmd5, 16, 16) == substr($dbmd5new, 16, 16)) && empty($charseterror) && empty($settingsdellist)) {
		cpmsg('dbcheck_ok', '', 'succeed');
	}

	$showlist = $addlists = '';
	foreach($discuzdb as $dbtable => $fields) {
		$addlist = $modifylist = $dellist = [];
		if(is_array($excepttables) && in_array($dbtable, $excepttables)) {
			continue;
		}
		if($fields != $discuzdbnew[$dbtable]) {
			foreach($discuzdb[$dbtable] as $key => $value) {
				if(empty($discuzdbnew[$dbtable][$key])) {
					$dellist[] = $value;
				} else {
					$tempvalue = str_replace('mediumtext', 'text', $value);
					$discuzdbnew[$dbtable][$key] = str_replace('mediumtext', 'text', $discuzdbnew[$dbtable][$key]);
					if($tempvalue != $discuzdbnew[$dbtable][$key]) {
						if(str_contains($tempvalue['Type'], 'int') && !empty($discuzdbnew[$dbtable][$key]['Type']) && str_contains($discuzdbnew[$dbtable][$key]['Type'], '(')) {
							$discuzdbnew[$dbtable][$key]['Type'] = preg_replace('/\(\d+\)/', '', $discuzdbnew[$dbtable][$key]['Type']);
							if($tempvalue == $discuzdbnew[$dbtable][$key]) {
								continue;
							}
						}
						if(str_contains($tempvalue['Extra'], 'DEFAULT_GENERATED')) {
							$tempvalue['Extra'] = str_replace('DEFAULT_GENERATED ', '', $tempvalue['Extra']);
							if($tempvalue == $discuzdbnew[$dbtable][$key]) {
								continue;
							}
						}
						$modifylist[] = $value;
					}
				}
			}
			if(is_array($discuzdbnew[$dbtable])) {
				foreach($discuzdbnew[$dbtable] as $key => $value) {
					if(!isset($discuzdb[$dbtable][$key]) && (!is_array($except[$dbtable]) || !in_array($value['Field'], $except[$dbtable]))) {
						$addlist[] = $value;
					}
				}
			}
		}

		if(($modifylist || $dellist) && !in_array($dbtable, $excepttables)) {

			$showlist .= showtablerow('', '', ["<span class=\"diffcolor3\">$tablepre$dbtable</span> {$lang['dbcheck_field']}", $lang['dbcheck_org_field'], $lang['dbcheck_status']], TRUE);

			foreach($modifylist as $value) {
				$dbvfield = empty($discuzdbnew[$dbtable][$value['Field']]) ? ['Type' => '', 'Null' => '', 'Extra' => '', 'Default' => ''] : $discuzdbnew[$dbtable][$value['Field']];
				$slowstatus = slowcheck($dbvfield['Type'], $value['Type']);

				$showlist .= "<tr><td><input name=\"repair[]\" class=\"checkbox\" type=\"checkbox\" value=\"$dbtable|{$value['Field']}|modify\"> <b>".$value['Field'].'</b> '.
					$dbvfield['Type'].
					($dbvfield['Null'] == 'NO' ? ' NOT NULL' : '').
					(!preg_match('/auto_increment/i', $dbvfield['Extra']) && !preg_match('/text/i', $dbvfield['Type']) ? ' default \''.$dbvfield['Default'].'\'' : '').
					' '.$dbvfield['Extra'].
					'</td><td><b>'.$value['Field'].'</b> '.$value['Type'].
					($value['Null'] == 'NO' ? ' NOT NULL' : '').
					(!preg_match('/auto_increment/i', $value['Extra']) && !preg_match('/text/i', $value['Type']) ? ' default \''.$value['Default'].'\'' : '').
					' '.$value['Extra'].'</td><td>'.
					(!$slowstatus ? "<em class=\"edited\">{$lang['dbcheck_modify']}</em></td></tr>" : "<em class=\"unknown\">{$lang['dbcheck_slow']}</em>").'</td></tr>';
			}

			if($modifylist) {
				$showlist .= showtablerow('', 'colspan="3"', "<input onclick=\"setrepaircheck(this, this.form, '$dbtable')\" name=\"repairtable[]\" class=\"checkbox\" type=\"checkbox\" value=\"$dbtable\"> <b>{$lang['dbcheck_repairtable']}</b>", TRUE);
			}

			foreach($dellist as $value) {
				$showlist .= "<tr><td><input name=\"repair[]\" class=\"checkbox\" type=\"checkbox\" value=\"$dbtable|{$value['Field']}|add\"> <strike><b>".$value['Field'].'</b></strike></td><td> <b>'.$value['Field'].'</b> '.$value['Type'].($value['Null'] == 'NO' ? ' NOT NULL' : '').'</td><td>'.
					"<em class=\"del\">{$lang['dbcheck_delete']}</em></td></tr>";
			}
		}

		if($addlist) {
			$addlists .= "<tr><td colspan=\"3\"><b>$tablepre$dbtable</b> {$lang['dbcheck_new_field']}</td></tr>";

			foreach($addlist as $value) {
				$addlists .= "<tr><td colspan=\"3\">&nbsp;&nbsp;&nbsp;&nbsp;<b>".$value['Field'].'</b> '.$discuzdbnew[$dbtable][$value['Field']]['Type'].($discuzdbnew[$dbtable][$value['Field']]['Null'] == 'NO' ? ' NOT NULL' : '').'</td></tr>';
			}
		}

	}

	if($showlist) {
		$showlist = showtablerow('', 'colspan="3" class="partition"', $lang['dbcheck_errorfields_tables'], TRUE).$showlist;
	}

	if($missingtables) {
		$showlist .= showtablerow('', 'colspan="3" class="partition"', $lang['dbcheck_missing_tables'], TRUE);
		$showlist .= showtablerow('', 'colspan="3" class="partition"', implode('', $missingtables), TRUE);
	}

	if($settingsdellist) {
		$showlist .= "<tr class=\"partition\"><td colspan=\"3\">{$lang['dbcheck_setting']}</td></tr>";
		$showlist .= '<tr><td colspan="3">';
		$showlist .= "<input name=\"setting[del]\" class=\"checkbox\" type=\"checkbox\" value=\"1\"> ".implode(', ', $settingsdellist).'<br />';
		$showlist .= '</td></tr>';
	}

	if($showlist) {
		$showlist .= '<tr><td colspan="3"><input class="btn" type="submit" value="'.$lang['dbcheck_repair'].'" name="repairsubmit"></td></tr>';
	}

	if($charseterror) {
		$showlist .= "<tr><td class=\"partition\" colspan=\"3\">{$lang['dbcheck_charseterror_tables']} ({$lang['dbcheck_charseterror_notice']} $dbcharset)</td></tr>";
		$showlist .= '<tr><td colspan="3">'.implode('', $charseterror).'</td></tr>';
	}

	if($addlists) {
		$showlist .= '<tr><td class="partition" colspan="3">'.$lang['dbcheck_userfields'].'</td></tr>'.$addlists;
	}

}

shownav('founder', 'nav_db', 'nav_db_dbcheck');
showsubmenu('nav_db', [
	['nav_db_export', 'db&operation=export', 0],
	['nav_db_runquery', 'db&operation=runquery', 0],
	['nav_db_optimize', 'db&operation=optimize', 0],
	['nav_db_dbcheck', 'db&operation=dbcheck', 1]
]);
showsubmenusteps('', [
	['nav_filecheck_confirm', $step == 1],
	['nav_filecheck_verify', $step == 2],
	['nav_filecheck_completed', $step == 3]
]);

if($step == 1) {
	cpmsg(cplang('dbcheck_tips_step1'), 'action=db&operation=dbcheck&step=2', 'button', '', FALSE);
} elseif($step == 2) {
	cpmsg(cplang('dbcheck_verifying'), 'action=db&operation=dbcheck&step=3', 'loading', '', FALSE);
} elseif($step == 3) {
	showtips('dbcheck_tips');
	echo <<<EOT
<script type="text/JavaScript">
	function setrepaircheck(obj, form, table) {
		eval('var rem = /^' + table + '\\\\|.+?\\\\|modify$/;');
		eval('var rea = /^' + table + '\\\\|.+?\\\\|add$/;');
		for(var i = 0; i < form.elements.length; i++) {
			var e = form.elements[i];
			if(e.type == 'checkbox' && e.name == 'repair[]') {
				if(rem.exec(e.value) != null) {
					if(obj.checked) {
						e.checked = false;
						e.disabled = true;
					} else {
						e.checked = false;
						e.disabled = false;

					}
				}
				if(rea.exec(e.value) != null) {
					if(obj.checked) {
						e.checked = true;
						e.disabled = false;
					} else {
						e.checked = false;
						e.disabled = false;
					}
				}
			}
		}
	}
</script>
EOT;
	showformheader('db&operation=dbcheck&step=3', 'fixpadding');
	showtableheader();
	echo $showlist;
	!$showlist && cpmsg('dbcheck_ok', '', 'succeed');
	showtablefooter();
	showformfooter();

}
	