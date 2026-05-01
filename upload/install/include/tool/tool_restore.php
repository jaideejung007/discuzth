<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('RUN_MODE') || RUN_MODE != 'tool') {
	show_msg('method_undefined', $method, 0);
}

if(!submitcheck('importsubmit', 1)) {
	$exportlog = $exportsize = [];
	check_exportfile($exportlog, $exportsize);
	if(empty($exportlog)) {
		restore_msg('backup_file_unexist');
	}

	show_header();
	echo '</div><div class="main">';
	echo '<div style="font-size: 12px">'.lang('db_import_tips').'</div>';
	echo '<div class="box">';
	show_importfile_list($exportlog, $exportsize);
	echo '</div>
		<div class="btnbox">
		<em>'.lang('tool_tips').'</em>
		<div class="inputbox">
			<input type="button" name="oldbtn" value="'.lang('old_step').'" class="btn oldbtn" onclick="location.href=\'?method=select\'">
			<input type="button" value="'.lang('done').'" class="btn" onclick="location.href=\'?method=done\'">
	        </div></div>';
	show_footer();
} else {
	$readerror = 0;
	$datafile_vol1 = trim(getgpc('datafile_vol1'));
	if($datafile_vol1) {
		$datafile = $datafile_vol1;
	} else {
		$datafile = getgpc('datafile_server', 'G');
	}
	if(!preg_match("#^\.\./data/backup_\w+/[\w\-]+\.sql$#i", $datafile)) {
		touch($lock_file);
		restore_msg('database_import_format_illegal');
	}
	if(file_exists($datafile) && @$fp = fopen($datafile, 'rb')) {
		$confirm = trim(getgpc('confirm', 'G'));
		$start = trim(getgpc('start', 'G'));
		$start = $start ? 1 : 0;
		if(!$start) {
			restore_msg('database_import_multivol_start',
				$_SERVER['PHP_SELF']."?method=restore&operation=import&datafile_server=$datafile&autoimport=yes&importsubmit=yes&start=yes".(!empty($confirm) ? '&confirm=yes' : ''),
				'redirect');
		}
		$confirm = $confirm ? 1 : 0;
		$sqldump = fgets($fp, 256);
		$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", $sqldump)));
		$dumpinfo = array('method' => $identify[3], 'volume' => intval($identify[4]), 'tablepre' => $identify[5], 'dbcharset' => strtolower($identify[6]));
		if(!$confirm) {
			$showmsg = '';
			if($dumpinfo['tablepre'] != $_config['db']['1']['tablepre'] && !getgpc('ignore_tablepre', 'G')) {
				$showmsg .= lang('tableprediff');
			}
			if($dumpinfo['dbcharset'] != strtolower($_config['db']['1']['dbcharset'])) {
				$showmsg .= lang('dbcharsetdiff');
			}
			if($showmsg) {
				$_lang = str_replace('{diff}', $showmsg, lang('different_dbcharset_tablepre'));
				restore_msg($_lang, $_SERVER['PHP_SELF']."?method=restore&operation=import&datafile_server=$datafile&autoimport=yes&importsubmit=yes&start=yes&confirm=yes",
					'confirm');
			}
		}

		if($dumpinfo['method'] == 'multivol') {
			$sqldump .= fread($fp, filesize($datafile));
		}
		fclose($fp);
	} else {
		if(getgpc('autoimport', 'G')) {
			touch($lock_file);
			restore_msg('database_import_succeed', '', 'success');
		} else {
			restore_msg('database_import_file_illegal');
		}
	}

	$db = new dbstuff;
	$db->connect($_config['db'][1]['dbhost'], $_config['db'][1]['dbuser'], $_config['db'][1]['dbpw'], $_config['db'][1]['dbname'], DBCHARSET);

	if($dumpinfo['method'] == 'multivol') {
		$sqlquery = splitsql($sqldump);
		unset($sqldump);

		foreach($sqlquery as $sql) {

			$sql = syntablestruct(trim($sql), true, DBCHARSET);

			if($sql != '') {
				$db->query($sql, 'SILENT');
				if(($sqlerror = $db->error()) && $db->errno() != 1062) {
					restore_msg('MySQL Query Error: '.$sqlerror. '<br />On '.$sql);
				}
			}
		}

		$datafile_next = preg_replace("/-({$dumpinfo['volume']})(\..+)$/", "-".($dumpinfo['volume'] + 1)."\\2", $datafile);
		$datafile_next = urlencode($datafile_next);
		$_lang = str_replace('{volume}', $dumpinfo['volume'], lang('database_import_multivol_redirect'));
		if($dumpinfo['volume'] == 1) {
			restore_msg($_lang, $_SERVER['PHP_SELF']."?method=restore&operation=import&datafile_server=$datafile_next&autoimport=yes&importsubmit=yes&start=yes&confirm=yes", 'redirect');
		} elseif(getgpc('autoimport', 'G')) {
			restore_msg($_lang, $_SERVER['PHP_SELF']."?method=restore&operation=import&datafile_server=$datafile_next&autoimport=yes&importsubmit=yes&start=yes&confirm=yes", 'redirect');
		} else {
			restore_msg('database_import_succeed', '', 'success');
		}
	} else {
		restore_msg('database_import_format_illegal');
	}
}

function check_exportfile(&$exportlog, &$exportsize) {
	$backupdirs = get_backup_dir();
	if(empty($backupdirs)) {
		return;
	}
	$exportfiletime = [];
	foreach($backupdirs as $backupdir) {
		$dir = dir(ROOT_PATH.'./data/'.$backupdir);
		while($entry = $dir->read()) {
			$entry = '../data/'.$backupdir.'/'.$entry;
			if(is_file($entry)) {
				if(preg_match("/\.sql$/i", $entry)) {
					$filesize = filesize($entry);
					$filemtime = filemtime($entry);
					$fp = fopen($entry, 'rb');
					$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", fgets($fp, 256))));
					fclose($fp);
					$key = preg_replace('/^(.+?)(\-\d+)\.sql$/i', '\\1', basename($entry));
					$exportlog[$key][$identify[4]] = array(
						'version' => $identify[1],
						'type' => $identify[2],
						'method' => $identify[3],
						'volume' => $identify[4],
						'tablepre' => $identify[5],
						'dbcharset' => $identify[6],
						'filename' => $entry,
						'dateline' => $filemtime,
						'size' => $filesize
					);
					$exportsize[$key] += $filesize;
					$exportfiletime[$key] = $filemtime;
				}
			}
		}
		$dir->close();
		if(!empty($exportlog)) {
			array_multisort($exportfiletime, SORT_DESC, SORT_STRING, $exportlog);
		}
	}
}

function get_backup_dir() {
	$backupdirs = array();
	$dir = dir(ROOT_PATH.'./data');
	while(($file = $dir->read()) !== FALSE) {
		if(filetype(ROOT_PATH.'./data/'.$file) == 'dir' && preg_match('/^backup_\w+/', $file)) {
			$backupdirs[] = $file;
		}
	}
	$dir->close();
	return $backupdirs;
}

function show_importfile_list($exportlog = array(), $exportsize = array()) {
	$title = array('filename', 'version', 'time', 'type', 'size', 'db_volume', '');
	echo '<style type="text/css">
		table {
			font-size: 12px;text-align: left
		}
		tr {
			line-height: 24px;
		}
		table a {
			color: #93a3bb;text-decoration: none
		}
	</style>';
	echo "\n<table width='100%'>\n<tr>";
	foreach($title as $col) {
		echo "<th>".lang($col)."</th>";
	}
	echo "</tr>\n";

	foreach($exportlog as $key => $val) {
		$info = $val[1];
		$info['dateline'] = is_int($info['dateline']) ? gmdate('Y-m-d H:i:s', $info['dateline'] + 3600 * 8) : lang('unknown');
		$info['size'] = sizecount($exportsize[$key]);
		$info['volume'] = count($val);
		echo "<tr style=''>";
		echo
			"<td>".basename(dirname($info['filename']))."/".basename($info['filename'])."</td>",
			'<td width="60">'.$info['version'].'</td>',
			'<td width="140">'.$info['dateline'].'</td>',
			'<td width="170">'.lang('db_export_'.$info['type']).'</td>',
			'<td width="80">'.$info['size'].'</td>',
			'<td width="30">'.$info['volume'].'</td>',
			'<td width="40">'."<a href=\"".$_SERVER['HTTP_SELF']."?method=restore&operation=import&datafile_server={$info['filename']}&importsubmit=yes\" ".(($info['version'] != DISCUZ_VERSION) ? "onclick=\"return confirm('".lang('database_import_confirm')."');\"" : "onclick=\"return confirm('".lang('database_import_confirm_sql')."');\"").">".lang('import')."</a></td>";
		echo "</tr>\n";
	}

	echo "</table>\n";

}

function sizecount($size) {
	if($size >= 1073741824) {
		$size = round($size / 1073741824 * 100) / 100 .' GB';
	} elseif($size >= 1048576) {
		$size = round($size / 1048576 * 100) / 100 .' MB';
	} elseif($size >= 1024) {
		$size = round($size / 1024 * 100) / 100 .' KB';
	} else {
		$size = $size.' Bytes';
	}
	return $size;
}

function submitcheck($var, $allowget = 0, $seccodecheck = 0, $secqaacheck = 0) {
	if(!getgpc($var)) {
		return FALSE;
	} else {
		if($allowget || ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_SERVER['HTTP_X_FLASH_VERSION']) && (empty($_SERVER['HTTP_REFERER']) ||
					preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}

function splitsql($sql) {
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query) {
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= $query[0] == "#" ? NULL : $query;
		}
		$num++;
	}
	return ($ret);
}

function syntablestruct($sql, $version, $dbcharset) {

	if(strpos(trim(substr($sql, 0, 18)), 'CREATE TABLE') === FALSE) {
		return $sql;
	}

	$sqlversion = strpos($sql, 'ENGINE=') === FALSE ? FALSE : TRUE;

	if($sqlversion === $version) {

		return $sqlversion && $dbcharset ? preg_replace(array('/ character set \w+/i', '/ collate \w+/i', "/DEFAULT CHARSET=\w+/is"), array('', '', "DEFAULT CHARSET=$dbcharset"), $sql) : $sql;
	}

	if($version) {
		return preg_replace(array('/TYPE=HEAP/i', '/TYPE=(\w+)/is'), array("ENGINE=MEMORY DEFAULT CHARSET=$dbcharset", "ENGINE=\\1 DEFAULT CHARSET=$dbcharset"), $sql);

	} else {
		return preg_replace(array('/character set \w+/i', '/collate \w+/i', '/ENGINE=MEMORY/i', '/\s*DEFAULT CHARSET=\w+/is', '/\s*COLLATE=\w+/is', '/ENGINE=(\w+)(.*)/is'), array('', '', 'ENGINE=HEAP', '', '', 'TYPE=\\1\\2'), $sql);
	}
}

function restore_msg($message, $url_forward = '', $type = 'message', $success = 0) {
	show_header();

	$message = lang($message);

	echo "</div><div class=\"main\"><div class=\"box\" style='word-wrap: break-word'>";

	if($type == 'message') {
		echo '<span'.($success ? '' : ' style="color: #F00"').'>'.$message.'</span>';
	} elseif($type == 'redirect') {
		echo "$message ...";
		echo "<br /><br /><br /><a href=\"$url_forward\" style=\"color: #333;font-size: 12px;text-decoration: none\">".lang('database_waiting_link')."</a>";
		echo "<script>setTimeout(\"window.location = '$url_forward'\", 1250);</script>";
	} elseif($type == 'confirm') {
		echo "$message";
		echo "<br /><br /><br /><button id=\"confirmbtn\" onclick=\"window.location = '$url_forward'\">".lang('database_confirm')."</button>&nbsp;<button id=\"cancelbtn\" onclick=\"window.location = '?'\">".lang('database_cancel')."</button>";
	} elseif($type == 'success') {
		echo $message;
		echo '</div>
		<div class="btnbox">
			<div class="inputbox">
			<input type="button" name="oldbtn" value="'.lang('old_step').'" class="btn oldbtn" onclick="location.href=\'?\'">
			<input type="button" value="'.lang('done').'" class="btn" onclick="location.href=\'?method=done\'">
	      	</div></div>';
	}

	echo '</div>';

	show_footer();
}