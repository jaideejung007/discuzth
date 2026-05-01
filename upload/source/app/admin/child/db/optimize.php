<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$optimizetable = '';
$totalsize = 0;
$tablearray = [0 => $tablepre];

shownav('founder', 'nav_db', 'nav_db_optimize');
showsubmenu('nav_db', [
	['nav_db_export', 'db&operation=export', 0],
	['nav_db_runquery', 'db&operation=runquery', 0],
	['nav_db_optimize', 'db&operation=optimize', 1],
	['nav_db_dbcheck', 'db&operation=dbcheck', 0]
]);
/*search={"nav_db":"action=db&operation=export","nav_db_optimize":"action=db&operation=optimize"}*/
showtips('db_optimize_tips');
/*search*/
showformheader('db&operation=optimize');
showtableheader('db_optimize_tables');
showsubtitle(['', 'db_optimize_table_name', 'type', 'db_optimize_rows', 'db_optimize_data', 'db_optimize_index', 'db_optimize_frag']);

if(!submitcheck('optimizesubmit')) {

	foreach($tablearray as $tp) {
		$query = DB::query("SHOW TABLE STATUS LIKE '$tp%'", 'SILENT');
		while($table = DB::fetch($query)) {
			if($table['Data_free'] && $table[$tabletype] == 'MyISAM') {
				$checked = $table[$tabletype] == 'MyISAM' ? 'checked' : 'disabled';
				showtablerow('', '', [
					"<input class=\"checkbox\" type=\"checkbox\" name=\"optimizetables[]\" value=\"{$table['Name']}\" $checked>",
					$table['Name'],
					$table[$tabletype],
					$table['Rows'],
					$table['Data_length'],
					$table['Index_length'],
					$table['Data_free'],
				]);
				$totalsize += $table['Data_length'] + $table['Index_length'];
			}
		}
	}
	if(empty($totalsize)) {
		showtablerow('', 'colspan="6"', $lang['db_optimize_done']);
	} else {
		showtablerow('', 'colspan="6"', $lang['db_optimize_used'].' '.sizecount($totalsize));
		showsubmit('optimizesubmit', 'submit', '<input name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form)" checked="checked" type="checkbox" /><label for="chkall">'.$lang['db_optimize_opt'].'</label>');
	}

} else {


	foreach($tablearray as $tp) {
		$query = DB::query("SHOW TABLE STATUS LIKE '$tp%'", 'SILENT');
		while($table = DB::fetch($query)) {
			if($table['Data_free'] && $table[$tabletype] == 'MyISAM') {
				$optimizeinput = "<input class=\"checkbox\" type=\"checkbox\" name=\"optimizetables[]\" value=\"{$table['Name']}\" $checked>";
				if(is_array($_GET['optimizetables']) && in_array($table['Name'], $_GET['optimizetables'])) {
					DB::query("OPTIMIZE TABLE {$table['Name']}");
					$table['Data_free'] = 0;
					$optimizeinput = '';
				}
				showtablerow('', '', [
					$optimizeinput,
					$table['Name'],
					$table['Engine'],
					$table['Rows'],
					$table['Data_length'],
					$table['Index_length'],
					$table['Data_free']
				]);
				$totalsize += $table['Data_length'] + $table['Index_length'];
			}
		}
	}
	showtablerow('', 'colspan="6"', $lang['db_optimize_used'].' '.sizecount($totalsize));
}

showtablefooter();
showformfooter();
	