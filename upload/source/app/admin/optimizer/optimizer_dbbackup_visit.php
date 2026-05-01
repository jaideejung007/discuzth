<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace admin;
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class optimizer_dbbackup_visit {

	public function __construct() {

	}

	public function check() {
		global $_G;
		$exportlog = $exportsize = $exportziplog = [];
		$filecount = 0;
		$this->check_exportfile($exportlog, $exportziplog, $exportsize, $filecount);
		if(!$filecount) {
			$return = ['status' => 0, 'type' => 'none', 'lang' => lang('optimizer', 'optimizer_dbbackup_visit_safe')];
		} else {
			$return = ['status' => 1, 'type' => 'notice', 'lang' => lang('optimizer', 'optimizer_dbbackup_visit_delete', ['filecount' => $filecount])];
		}
		return $return;
	}

	function get_backup_dir() {

		$backupdirs = [];
		$dir = dir(DISCUZ_ROOT.'./data');
		while(($file = $dir->read()) !== FALSE) {
			if(filetype(DISCUZ_DATA.'./'.$file) == 'dir' && preg_match('/^backup_\w+/', $file)) {
				$backupdirs[] = $file;
			}
		}
		$dir->close();
		return $backupdirs;
	}

	function check_exportfile(&$exportlog, &$exportziplog, &$exportsize, &$filecount) {

		$backupdirs = $this->get_backup_dir();
		if(empty($backupdirs)) {
			return;
		}

		$filecount = 0;
		foreach($backupdirs as $backupdir) {
			$dir = dir(DISCUZ_DATA.'./'.$backupdir);
			while($entry = $dir->read()) {
				$entry = DISCUZ_DATA.'./'.$backupdir.'/'.$entry;
				if(is_file($entry)) {
					if(preg_match('/\.sql$/i', $entry)) {
						$filesize = filesize($entry);
						$fp = fopen($entry, 'rb');
						$identify = explode(',', base64_decode(preg_replace('/^# Identify:\s*(\w+).*/s', "\\1", fgets($fp, 256))));
						fclose($fp);
						$key = preg_replace('/^(.+?)(\-\d+)\.sql$/i', '\\1', basename($entry));
						$exportlog[$key][$identify[4]] = [
							'version' => $identify[1],
							'type' => $identify[2],
							'method' => $identify[3],
							'volume' => $identify[4],
							'tablepre' => $identify[5],
							'dbcharset' => $identify[6],
							'filename' => str_replace(DISCUZ_ROOT, '', $entry),
							'dateline' => filemtime($entry),
							'size' => $filesize
						];
						$testurl = str_replace(DISCUZ_DATA.'./', getglobal('siteurl').'data/', $entry);
						$content = dfsockopen($testurl);
						if(!empty($content)) {
							$filecount++;
							$exportsize[$key] += $filesize;
						}
					} elseif(preg_match('/\.zip$/i', $entry)) {
						$key = preg_replace('/^(.+?)(\-\d+)\.zip$/i', '\\1', basename($entry));
						$filesize = filesize($entry);
						$exportziplog[$key][] = [
							'type' => 'zip',
							'filename' => str_replace(DISCUZ_ROOT, '', $entry),
							'size' => $filesize,
							'dateline' => filemtime($entry)
						];
						$testurl = str_replace(DISCUZ_DATA.'./', getglobal('siteurl').'data/', $entry);
						$content = dfsockopen($testurl);
						if(!empty($content)) {
							$filecount++;
						}
					}
				}
			}
			$dir->close();
		}
	}
}