<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$db = DB::object();

$tabletype = 'Engine';
$tablepre = $_G['config']['db'][1]['tablepre'];
$dbcharset = $_G['config']['db'][1]['dbcharset'];

require_once libfile('function/attachment');
cpheader();

if(!isfounder()) cpmsg('noaccess_isfounder', '', 'error');


$excepttables = [$tablepre.'common_admincp_session', $tablepre.'common_failedlogin', $tablepre.'forum_rsscache', $tablepre.'common_searchindex', $tablepre.'forum_spacecache', $tablepre.'common_session'];

$backupdir = table_common_setting::t()->fetch_setting('backupdir');

if(!$backupdir) {
	$backupdir = random(6);
	@mkdir('./data/backup_'.$backupdir, 0777);
	table_common_setting::t()->update_setting('backupdir', $backupdir);
}
$backupdir = 'backup_'.$backupdir;
if(!is_dir('./data/'.$backupdir)) {
	mkdir('./data/'.$backupdir, 0777);
}

$file = childfile('db/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;

function createtable($sql, $dbcharset) {
	$type = strtoupper(preg_replace('/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU', "\\2", $sql));
	$defaultengine = strtolower(getglobal('config/db/common/engine')) !== 'innodb' ? 'MyISAM' : 'InnoDB';
	$type = in_array($type, ['INNODB', 'MYISAM', 'HEAP', 'MEMORY']) ? $type : $defaultengine;
	return preg_replace('/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU', "\\1", $sql)." ENGINE=$type DEFAULT CHARSET=".getglobal('config/db/1/dbcharset').(getglobal('config/db/1/dbcharset') === 'utf8mb4' ? ' COLLATE=utf8mb4_unicode_ci' : '');
}

function fetchtablelist($tablepre = '') {
	global $db;
	$arr = explode('.', $tablepre);
	$dbname = $arr[1] ? $arr[0] : '';
	$tablepre = str_replace('_', '\_', $tablepre);
	$sqladd = $dbname ? " FROM $dbname LIKE '$arr[1]%'" : "LIKE '$tablepre%'";
	$tables = $table = [];
	$query = DB::query("SHOW TABLE STATUS $sqladd");
	while($table = DB::fetch($query)) {
		$table['Name'] = ($dbname ? "$dbname." : '').$table['Name'];
		$tables[] = $table;
	}
	return $tables;
}

function arraykeys2($array, $key2) {
	$return = [];
	foreach($array as $val) {
		$return[] = $val[$key2];
	}
	return $return;
}


function syntablestruct($sql, $version, $dbcharset) {

	if(!str_contains(trim(substr($sql, 0, 18)), 'CREATE TABLE')) {
		return $sql;
	}

	$sqlversion = !(strpos($sql, 'ENGINE=') === FALSE);

	if($sqlversion === $version) {

		return $sqlversion && $dbcharset ? preg_replace(['/ character set \w+/i', '/ collate \w+/i', '/DEFAULT CHARSET=\w+/is'], ['', '', "DEFAULT CHARSET=$dbcharset"], $sql) : $sql;
	}

	if($version) {
		return preg_replace(['/TYPE=HEAP/i', '/TYPE=(\w+)/is'], ["ENGINE=MEMORY DEFAULT CHARSET=$dbcharset", "ENGINE=\\1 DEFAULT CHARSET=$dbcharset"], $sql);

	} else {
		return preg_replace(['/character set \w+/i', '/collate \w+/i', '/ENGINE=MEMORY/i', '/\s*DEFAULT CHARSET=\w+/is', '/\s*COLLATE=\w+/is', '/ENGINE=(\w+)(.*)/is'], ['', '', 'ENGINE=HEAP', '', '', 'TYPE=\\1\\2'], $sql);
	}
}

function sqldumptablestruct($table) {
	global $_G, $db, $dumpcharset;

	$createtable = DB::query("SHOW CREATE TABLE $table", 'SILENT');

	if(!DB::error()) {
		$tabledump = "DROP TABLE IF EXISTS $table;\n";
	} else {
		return '';
	}

	$create = $db->fetch_row($createtable);

	if(str_contains($table, '.')) {
		$tablename = substr($table, strpos($table, '.') + 1);
		$create[1] = str_replace("CREATE TABLE $tablename", 'CREATE TABLE '.$table, $create[1]);
	}
	$tabledump .= $create[1];

	if($_GET['sqlcharset']) {
		$tabledump = preg_replace('/(DEFAULT)*\s*CHARSET=.+/', 'DEFAULT CHARSET='.$_GET['sqlcharset'], $tabledump);
	}

	$tablestatus = DB::fetch_first("SHOW TABLE STATUS LIKE '$table'");
	$tabledump .= ($tablestatus['Auto_increment'] ? " AUTO_INCREMENT={$tablestatus['Auto_increment']}" : '').";\n\n";
	if($_GET['sqlcompat'] == 'MYSQL40') {
		if($tablestatus['Auto_increment'] <> '') {
			$temppos = strpos($tabledump, ',');
			$tabledump = substr($tabledump, 0, $temppos).' auto_increment'.substr($tabledump, $temppos);
		}
		if($tablestatus['Engine'] == 'MEMORY') {
			$tabledump = str_replace('TYPE=MEMORY', 'TYPE=HEAP', $tabledump);
		}
	}
	return $tabledump;
}

function sqldumptable($table, $startfrom = 0, $currsize = 0) {
	global $_G, $db, $startrow, $dumpcharset, $complete, $excepttables;

	$offset = 300;
	$tabledump = '';
	$tablefields = [];

	$query = DB::query("SHOW FULL COLUMNS FROM $table", 'SILENT');
	if(strexists($table, 'adminsessions')) {
		return;
	} elseif(!$query && DB::errno() == 1146) {
		return;
	} elseif(!$query) {
		$_GET['usehex'] = FALSE;
	} else {
		while($fieldrow = DB::fetch($query)) {
			$tablefields[] = $fieldrow;
		}
	}

	if(!in_array($table, $excepttables)) {
		$tabledumped = 0;
		$numrows = $offset;
		$firstfield = $tablefields[0];
		$unique = false;
		if($firstfield['Extra'] == 'auto_increment' || preg_match('/^'.DB::table('forum_post')."(_(\\d+))?$/i", $table)) {
			$unique = true;
		}

		if($_GET['extendins'] == '0') {
			while($currsize + strlen($tabledump) + 500 < $_GET['sizelimit'] * 1000 && $numrows == $offset) {
				if($unique) {
					$selectsql = "SELECT * FROM $table WHERE {$firstfield['Field']} > $startfrom ORDER BY {$firstfield['Field']} LIMIT $offset";
				} else {
					$selectsql = "SELECT * FROM $table LIMIT $startfrom, $offset";
				}
				$tabledumped = 1;
				$rows = DB::query($selectsql);
				$numfields = $db->num_fields($rows);

				$numrows = DB::num_rows($rows);
				while($row = $db->fetch_row($rows)) {
					$comma = $t = '';
					for($i = 0; $i < $numfields; $i++) {
						if($tablefields[$i]['Type'] == 'json' && empty($row[$i])) {
							$value = 'NULL';
						} elseif($_GET['usehex'] && !empty($row[$i]) && (strexists($tablefields[$i]['Type'], 'char') || strexists($tablefields[$i]['Type'], 'text'))) {
							$value = '0x'.bin2hex($row[$i]);
						} else {
							$value = '\''.$db->escape_string($row[$i]).'\'';
						}
						$t .= $comma.$value;
						$comma = ',';
					}
					if(strlen($t) + $currsize + strlen($tabledump) + 500 < $_GET['sizelimit'] * 1000) {
						if($unique) {
							$startfrom = $row[0];
						} else {
							$startfrom++;
						}
						$tabledump .= "INSERT INTO $table VALUES ($t);\n";
					} else {
						$complete = FALSE;
						break 2;
					}
				}
			}
		} else {
			while($currsize + strlen($tabledump) + 500 < $_GET['sizelimit'] * 1000 && $numrows == $offset) {
				if($unique) {
					$selectsql = "SELECT * FROM $table WHERE {$firstfield['Field']} > $startfrom LIMIT $offset";
				} else {
					$selectsql = "SELECT * FROM $table LIMIT $startfrom, $offset";
				}
				$tabledumped = 1;
				$rows = DB::query($selectsql);
				$numfields = $db->num_fields($rows);

				if($numrows = DB::num_rows($rows)) {
					$t1 = $comma1 = '';
					while($row = $db->fetch_row($rows)) {
						$t2 = $comma2 = '';
						for($i = 0; $i < $numfields; $i++) {
							$t2 .= $comma2.($_GET['usehex'] && !empty($row[$i]) && (strexists($tablefields[$i]['Type'], 'char') || strexists($tablefields[$i]['Type'], 'text')) ? '0x'.bin2hex($row[$i]) : '\''.$db->escape_string($row[$i]).'\'');
							$comma2 = ',';
						}
						if(strlen($t1) + $currsize + strlen($tabledump) + 500 < $_GET['sizelimit'] * 1000) {
							if($unique) {
								$startfrom = $row[0];
							} else {
								$startfrom++;
							}
							$t1 .= "$comma1 ($t2)";
							$comma1 = ',';
						} else {
							$tabledump .= "INSERT INTO $table VALUES $t1;\n";
							$complete = FALSE;
							break 2;
						}
					}
					$tabledump .= "INSERT INTO $table VALUES $t1;\n";
				}
			}
		}

		$startrow = $startfrom;
		$tabledump .= "\n";
	}

	return $tabledump;
}

function splitsql($sql) {
	$sql = str_replace("\r", "\n", $sql);
	$ret = [];
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query) {
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= $query[0] == '#' ? NULL : $query;
		}
		$num++;
	}
	return ($ret);
}

function slowcheck($type1, $type2) {
	$t1 = explode(' ', $type1.' ');
	$t1 = $t1[0];
	$t2 = explode(' ', $type2.' ');
	$t2 = $t2[0];
	$arr = [$t1, $t2];
	sort($arr);
	if($arr == ['mediumtext', 'text']) {
		return TRUE;
	} elseif(str_starts_with($arr[0], 'char') && str_starts_with($arr[1], 'varchar')) {
		return TRUE;
	}
	return FALSE;
}

