<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$checkperm = checkpermission('runquery', 0);

$runquerys = [];
include_once childfile('db/quickquery');

if(!submitcheck('sqlsubmit')) {

	$runqueryselect = '';
	foreach($simplequeries as $key => $query) {
		if(empty($query['sql'])) {
			$runqueryselect .= "<optgroup label=\"{$query['comment']}\">";
		} else {
			$runqueryselect .= '<option value="'.$key.'">'.$query['comment'].'</option>';
		}
	}
	if($runqueryselect) {
		$runqueryselect = '<select name="queryselect" style="width:500px">'.$runqueryselect.'</select>';
	}
	$queryselect = intval($_GET['queryselect']);
	$queries = $queryselect ? $runquerys[$queryselect] : '';

	shownav('founder', 'nav_db', 'nav_db_runquery');
	showsubmenu('nav_db', [
		['nav_db_export', 'db&operation=export', 0],
		['nav_db_runquery', 'db&operation=runquery', 1],
		['nav_db_optimize', 'db&operation=optimize', 0],
		['nav_db_dbcheck', 'db&operation=dbcheck', 0]
	]);
	/*search={"nav_db":"action=db&operation=export","nav_db_runquery":"action=db&operation=runquery"}*/
	showtips('db_runquery_tips');
	showtableheader();
	showformheader('db&operation=runquery&option=simple');
	showsetting('db_runquery_simply', '', '', $runqueryselect);
	showsetting('', '', '', '<input type="checkbox" class="checkbox" name="createcompatible" value="1" checked="checked" />'.cplang('db_runquery_createcompatible'));
	showsubmit('sqlsubmit');
	showformfooter();

	if($checkperm) {
		showformheader('db&operation=runquery&option=');
		showsetting('db_runquery_sql', '', '', '<textarea cols="85" rows="10" name="queries" style="width:500px;" onkeyup="textareasize(this)" onkeydown="textareakey(this, event)">'.$queries.'</textarea>');
		showsetting('', '', '', '<input type="checkbox" class="checkbox" name="createcompatible" value="1" checked="checked" />'.cplang('db_runquery_createcompatible'));
		showsubmit('sqlsubmit', 'submit', '', cplang('db_runquery_comment'));
		showformfooter();
	}

	showtablefooter();
	/*search*/

} else {
	$queries = $_GET['queries'];
	if($_GET['option'] == 'simple') {
		$queryselect = intval($_GET['queryselect']);
		$queries = isset($simplequeries[$queryselect]) && $simplequeries[$queryselect]['sql'] ? $simplequeries[$queryselect]['sql'] : '';
	} elseif(!$checkperm) {
		cpmsg('database_run_query_denied', '', 'error');
	}
	$sqlquery = str_replace([' cdb_', ' `cdb_', ' pre_', ' `pre_'], [' {tablepre}', ' `{tablepre}', ' {tablepre}', ' `{tablepre}'], $queries);
	$sqlquery = splitsql(str_replace([' {tablepre}', ' `{tablepre}'], [' '.$tablepre, ' `'.$tablepre], $sqlquery));
	$affected_rows = 0;
	foreach($sqlquery as $sql) {
		if(trim($sql) != '') {
			$sql = !empty($_GET['createcompatible']) ? syntablestruct(trim($sql), true, $dbcharset) : $sql;

			DB::query($sql, 'SILENT');
			if($sqlerror = DB::error()) {
				break;
			} else {
				$affected_rows += intval(DB::affected_rows());
			}
		}
	}

	$sqlerror ? cpmsg('database_run_query_invalid', '', 'error', ['sqlerror' => $sqlerror]) : cpmsg('database_run_query_succeed', '', 'succeed', ['affected_rows' => $affected_rows]);

}
	