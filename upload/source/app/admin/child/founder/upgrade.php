<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!class_exists('ZipArchive')) {
	cpmsg('extension_missing_ZipArchive', '', 'error');
}

$u = new discuzUpgrade();

if($_GET['downPatch'] && $_GET['formhash'] == formhash()) {
	$patchFile = $u->getPatchFile();
	if(!file_exists($patchFile)) {
		cpmsg('upgrade_patch_not_found', '', 'error');
	}
	ob_end_clean();
	header('Content-Type: application/zip');
	header('Content-Disposition: attachment; filename="'.basename($patchFile).'"');
	readfile($patchFile);
	$u->clearEvent();
	exit;
}

$step = max(1, intval($_GET['step']));
showsubmenusteps('menu_upgrade', [
	['upgrade_step1', $step == 1],
	['upgrade_step2', $step == 2],
	['upgrade_step3', $step == 3],
]);

if($step == 1) {
	[$current, $remote] = $u->getVersion();
	$tips = cplang('upgrade_tips');
	if($u->readmeUrl) {
		$tips .= cplang('upgrade_tips_readme', ['URL' => $u->readmeUrl]);
	}
	showtips($tips);
	if($current >= $remote) {
		cpmsg('<h4 class="infotitle2">'.cplang('upgrade_latest').'</h4>', '', 'succeed');
	} else {
		cpmsg('<h4 class="infotitle2">'.cplang('upgrade_info').'</h4><br />'.$current.' -> '.$remote, 'action=founder&operation=upgrade&step=2', 'form');
	}
} elseif($step == 2) {
	cpmsg('upgrade_waiting', 'action=founder&operation=upgrade&step=3', 'loading');
} elseif($step == 3) {
	$u->clearEvent();
	$u->check();
	$u->createPatch();
	$diff = $u->getCurrentDiff();
	if($diff) {
		$s = '<h4 class="infotitle3">'.cplang('upgrade_diff_notice').'</h4><br /><div style="text-align:left;max-height:400px;overflow-y:auto;">';
		$s .= implode('<br>', $diff);
		$s .= '</div>';
	}
	cpmsg('<h4 class="infotitle2">'.cplang('upgrade_patch_download').'</h4>', 'action=founder&operation=upgrade&downPatch=yes', 'form', extra: $s);
}

class discuzUpgrade {

	const ApiUrl = 'https://addon.dismall.com/api/discuzupgrade/?ver=X5';

	const RemoteBasePath = 'upload/';

	const RemoteMd5 = '/source/data/admincp/discuzfiles.md5';
	const RemoteVer = '/source/discuz_version.php';

	public $readmeUrl = '';

	public function getVersion() {
		$current = $this->getCurrentData(true);
		$remote = $this->getRemoteData(true);
		return [
			$current['ver'].' Release '.$current['release'],
			$remote['ver'].' Release '.$remote['release']
		];
	}

	public function clearEvent() {
		$this->_deltree($this->getExtractDir());
		$this->_deltree($this->getPatchDir());
		@unlink($this->getDownFile());
		@unlink($this->getPatchFile());
	}

	public function check() {
		$this->currentData = $this->getCurrentData();
		$this->remoteData = $this->getRemoteData();
		$this->currentFile = array_keys($this->currentData);
		$this->remoteFile = array_keys($this->remoteData);
	}

	public function getDownFile() {
		return sys_get_temp_dir().'/DZNew.zip';
	}

	public function getExtractDir() {
		return sys_get_temp_dir().'/DZExtract/';
	}

	public function getPatchDir() {
		return sys_get_temp_dir().'/DZPatch/';
	}

	public function getPatchFile() {
		return sys_get_temp_dir().'/DZPatch.zip';
	}

	public function createPatch() {
		$newFiles = array_diff($this->remoteFile, $this->currentFile);
		$modFiles = [];
		foreach(array_intersect($this->currentFile, $this->remoteFile) as $file) {
			if($this->currentData[$file] != $this->remoteData[$file]) {
				$modFiles[] = $file;
			}
		}
		$this->pPath = $this->getPatchDir();
		$this->ePath .= '/';
		$this->_deltree($this->pPath);
		foreach($newFiles as $file) {
			if(str_starts_with($file, './')) {
				$file = substr($file, 2);
			}
			$from = $this->ePath.self::RemoteBasePath.$file;
			if(!file_exists($from) || !filesize($from)) {
				continue;
			}
			mkdir(dirname($this->pPath.$file), 0777, true);
			copy($from, $this->pPath.$file);
		}
		foreach($modFiles as $file) {
			$from = $this->ePath.self::RemoteBasePath.$file;
			if(!file_exists($from) || !filesize($from)) {
				continue;
			}
			mkdir(dirname($this->pPath.$file), 0777, true);
			copy($from, $this->pPath.$file);
		}
		if(!is_dir($this->pPath)) {
			$this->clearEvent();
			cpmsg('upgrade_latest', '', 'succeed');
		}
		$newMd5file = substr(self::RemoteMd5, 1);
		mkdir(dirname($this->pPath.$newMd5file), 0777, true);
		copy($this->ePath.self::RemoteBasePath.$newMd5file, $this->pPath.$newMd5file);

		$patchFile = $this->getPatchFile();
		$zip = new ZipArchive;
		@unlink($patchFile);
		if($zip->open($patchFile, ZipArchive::CREATE) !== TRUE) {
			$this->clearEvent();
			cpmsg('upgrade_patch_create_failed', '', 'error');
		}
		$this->_zip($zip, $this->pPath, strlen($this->pPath.'/'));
		$zip->close();
	}

	private function _getApiData() {
		$c = new filesock_curl();
		$c->unsafe = true;
		$c->returnbody = true;
		$c->conntimeout = 10;
		$c->timeout = 10;
		$c->request(['url' => self::ApiUrl]);
		if($c->curlstatus['http_code'] != 200) {
			cpmsg('upgrade_remote_get_failed', '', 'error');
		}
		$data = json_decode($c->filesockbody, true);
		return !empty($data['ver']) && !empty($data['release']) && !empty($data['url']) && !empty($data['md5']) ? $data : false;
	}

	private function _zip(&$zip, $path, $basePathLen) {
		$handler = opendir($path);
		while(($filename = readdir($handler)) !== false) {
			if($filename == '.' || $filename == '..') {
				continue;
			}
			$f = $path.'/'.$filename;
			$lpath = substr($f, $basePathLen);
			if(is_dir($f)) {
				$this->_zip($zip, $f, $basePathLen);
			} elseif(!$zip->addFile($f, $lpath)) {
				cpmsg('upgrade_patch_create_failed', '', 'error');
			}
		}
		@closedir($handler);
	}

	private function _splitMd5Data($data) {
		$md5Data = [];
		foreach($data as $line) {
			if(!$line) {
				continue;
			}
			$file = trim(substr($line, 34));
			$md5Data[$file] = substr($line, 0, 32);
		}
		ksort($md5Data);
		return $md5Data;
	}

	public function getCurrentData($verOnly = false) {
		require_once DISCUZ_ROOT.'./source/discuz_version.php';
		if($verOnly) {
			return ['ver' => DISCUZ_VERSION, 'release' => DISCUZ_RELEASE];
		} else {
			if(!$data = @file('./source/data/admincp/discuzfiles.md5')) {
				cpmsg('filecheck_nofound_md5file', '', 'error');
			}

			return $this->_splitMd5Data($data);
		}
	}

	public function getCurrentDiff() {
		$diffData = [];
		foreach($this->currentData as $file => $md5) {
			if(is_dir(DISCUZ_ROOT.$file)) {
				continue;
			}
			if(md5_file(DISCUZ_ROOT.$file) != $md5) {
				$diffData[] = $file;
			}
		}
		return $diffData;
	}

	public function getRemoteData($verOnly = false) {
		set_time_limit(0);
		if(!$apiData = $this->_getApiData()) {
			cpmsg('upgrade_remote_get_failed', extra: 'api error');
		}
		if(!empty($apiData['readmeUrl'])) {
			$this->readmeUrl = $apiData['readmeUrl'];
		}
		if($verOnly) {
			return $apiData;
		}
		$file = $this->getDownFile();
		$this->ePath = $this->getExtractDir();
		if(!file_exists($file)) {
			$c = new filesock_curl();
			$c->unsafe = true;
			$c->returnbody = true;
			$c->conntimeout = 10;
			$c->timeout = 60;
			$c->request(['url' => $apiData['url']]);
			if(!empty($c->curlstatus['redirect_url'])) {
				$c->request(['url' => $c->curlstatus['redirect_url']]);
			}
			if($c->curlstatus['http_code'] != 200) {
				cpmsg('upgrade_remote_get_failed', extra: 'http_code: '.$c->curlstatus['http_code']);
			}
			if(empty($c->filesockbody)) {
				cpmsg('upgrade_remote_get_failed', extra: 'response is empty');
			}
			file_put_contents($file, $c->filesockbody);
			if(strtolower(md5_file($file)) != strtolower($apiData['md5'])) {
				@unlink($file);
				cpmsg('upgrade_remote_get_failed', extra: 'md5 check error');
			}
			$this->_deltree($this->ePath);
			$zip = new ZipArchive;
			if($zip->open($file) !== true) {
				@unlink($file);
				cpmsg('upgrade_remote_get_failed', extra: 'zip open error');
			}
			if(!$zip->extractTo($this->ePath)) {
				@unlink($file);
				cpmsg('upgrade_remote_get_failed', extra: 'zip extract error');
			}
			$zip->close();
		}

		if(!$data = @file($this->ePath.self::RemoteBasePath.self::RemoteMd5)) {
			cpmsg('filecheck_nofound_md5file', '', 'error');
		}

		$verData = file_get_contents($this->ePath.self::RemoteBasePath.self::RemoteVer);
		if(!$verData) {
			cpmsg('upgrade_remote_get_failed', extra: 'discuz_version.php not exists');
		}
		preg_match('/define\s*\(\s*[\'"]DISCUZ_VERSION[\'"]\s*,\s*[\'"]?([^\'"]+)[\'"]?\s*\)/i', $verData, $m);
		if(empty($m[1])) {
			cpmsg('upgrade_remote_get_failed', extra: 'DISCUZ_VERSION parse error');
		}
		$ver = $m[1];
		preg_match('/define\s*\(\s*[\'"]DISCUZ_RELEASE[\'"]\s*,\s*[\'"]?([^\'"]+)[\'"]?\s*\)/i', $verData, $m);
		if(empty($m[1])) {
			cpmsg('upgrade_remote_get_failed', extra: 'DISCUZ_RELEASE parse error');
		}
		if($apiData['ver'] != $ver || $apiData['release'] != $m[1]) {
			cpmsg('upgrade_remote_get_failed', extra: 'version is error');
		}
		return $this->_splitMd5Data($data);
	}

	private function _deltree($dir) {
		if($directory = @dir($dir)) {
			while($entry = $directory->read()) {
				if($entry == '.' || $entry == '..') {
					continue;
				}
				$filename = $dir.'/'.$entry;
				if(is_file($filename)) {
					@unlink($filename);
				} else {
					$this->_deltree($filename);
				}
			}
			$directory->close();
			@rmdir($dir);
		}
	}

}