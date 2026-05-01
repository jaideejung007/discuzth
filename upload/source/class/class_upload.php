<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


class upload {

	public function __construct($target, $filekey = [], $base64 = '') {
		if(!preg_match('/^[a-z]+[a-z0-9_]*$/i', $target)) {
			return null;
		}
		$this->target = $target;

		$this->files = [];
		$this->simple = false;
		if(!empty($base64)) {
			$base64 = base64_decode($base64);
			$f = tempnam(sys_get_temp_dir(), 'du');
			file_put_contents($f, $base64);
			$name = basename($f);
			$i = getimagesize($f);
			if($i) {
				$name .= image_type_to_extension($i[2], true);
			}
			$this->simple = true;
			$_ENV['DFILES'][$f] = [
				'name' => $name,
				'full_path' => $name,
				'tmp_name' => $f,
				'type' => mime_content_type($f),
				'error' => 0,
				'size' => filesize($f),
			];
			$this->files = [$_ENV['DFILES'][$f]];
		} elseif(empty($filekey)) {
			$this->files = $_FILES;
		} elseif(!empty($_FILES[$filekey])) {
			$this->simple = true;
			$this->files = [$_FILES[$filekey]];
		}
		$this->fileInfo = null;
		return null;
	}

	public function getInfo() {
		if(empty($this->files)) {
			return [];
		}
		$this->fileInfo = [];
		foreach($this->files as $fname => $file) {
			if(empty($file['name'])) {
				continue;
			}
			if(is_array($file['name'])) {
				foreach($file['name'] as $k => $name) {
					$this->fileInfo[$fname][$k] = [
						'name' => $name,
						'full_path' => $file['full_path'][$k],
						'type' => $file['type'][$k],
						'tmp_name' => $file['tmp_name'][$k],
						'error' => $file['error'][$k],
						'size' => $file['size'][$k],
					];
				}
			} else {
				$this->fileInfo[$fname] = $file;
			}
		}
		return $this->simple ? $this->fileInfo[0] : $this->fileInfo;
	}

	public function upload() {
		if($this->fileInfo === null) {
			$this->getInfo();
			if(!$this->fileInfo) {
				return false;
			}
		}
		$this->uploaded = [];
		foreach($this->fileInfo as $fname => $fileInfo) {
			if(!empty($fileInfo['name']) && !empty($fileInfo['tmp_name'])) {
				$this->uploaded[$fname] = $this->_upload($fileInfo);
			} else {
				foreach($fileInfo as $k => $row) {
					$this->uploaded[$fname][$k] = $this->_upload($row);
				}
			}
		}

		return $this->simple ? $this->uploaded[0] : $this->uploaded;
	}

	private function _upload($fileInfo) {
		$upload = new discuz_upload();
		$upload->init($fileInfo, $this->target);
		if(!$upload->error()) {
			$upload->save();
			$fileInfo['attachment'] = $upload->attach['attachment'];
			$fileInfo['ext'] = $upload->attach['ext'];
			$fileInfo['isimage'] = $upload->attach['isimage'];
			$fileInfo['remote'] = $upload->remote;
			$upload->attach['imageinfo'] && $fileInfo['imageinfo'] = $upload->attach['imageinfo'];
		}
		$fileInfo['error'] = $upload->errorcode;
		return $fileInfo;
	}

	public function delete($attachment) {
		global $_G;
		@unlink($_G['setting']['attachdir'].'/'.$this->target.'/'.$attachment);
		ftpcmd('delete', $this->target.'/'.$attachment);
	}

	public function getUrl($attachment = '') {
		global $_G;
		return $_G['setting']['attachurl'].$this->target.'/'.$attachment;
	}

}