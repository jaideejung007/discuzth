<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class ip_v6system {

	private static $instance = null;

	private $binaryFile = '';
	private $locationIndex = [];
	private $totalCount = 0;
	private $recordSize = 34;
	private $dataLoaded = false;
	private $filePointer = null;

	private $header = 'Discuz! IPDB';
	private $headerLength = 30;
	private $indexCount = 0;
	private $indexSize = 24;
	private $indexOffset = 0;
	private $dataOffset = 0;

	private $indexCache = [];
	private $recordCache = [];
	private $maxRecordCacheSize = 1000;
	private $useMemoryCache = true;

	private $memoryCacheKey = 'ipdb_index_v6';

	private $memoryCacheTTL = 86400;

	
	public function __construct() {
		$sys_lang = getglobal('i18n');
		if(empty($sys_lang) || $sys_lang == 'default') {
			$sys_lang = currentlang();
		}
		$this->loadData(DISCUZ_ROOT.'./source/data/ip/ipv6db.dat', DISCUZ_ROOT.'./source/i18n/'.$sys_lang.'/lang_ipdb.php');
	}

	
	public function __destruct() {
		if($this->filePointer !== null) {
			fclose($this->filePointer);
			$this->filePointer = null;
		}
	}

	public static function getInstance() {
		if(!self::$instance) {
			try {
				self::$instance = new ip_v6system();
			} catch (Exception $e) {
				return null;
			}
		}
		return self::$instance;
	}

	public function loadData($binaryFile, $langFile) {
		if($this->filePointer !== null) {
			fclose($this->filePointer);
			$this->filePointer = null;
		}

		if(file_exists($langFile)) {
			include($langFile);
			if(isset($lang)) {
				$this->locationIndex = $lang;
			} else {
				throw new Exception('IPDB: language not found');
			}
		} else {
			throw new Exception('IPDB: language not found');
		}

		if(!file_exists($binaryFile)) {
			throw new Exception('IPDB: ipv6db.dat not found');
		}

		$this->filePointer = fopen($binaryFile, 'rb');
		if(!$this->filePointer) {
			throw new Exception('IPDB: ipv6db.dat not found');
		}

		$fileHeader = fread($this->filePointer, $this->headerLength);
		if(!str_starts_with($fileHeader, 'Discuz! IPDB')) {
			fclose($this->filePointer);
			$this->filePointer = null;
			throw new Exception('IPDB: ipv6db.dat is not valid');
		}

		$indexCountData = fread($this->filePointer, 4);
		if(strlen($indexCountData) !== 4) {
			fclose($this->filePointer);
			$this->filePointer = null;
			throw new Exception('IPDB: ipv6db.dat index error');
		}
		$this->indexCount = unpack('N', $indexCountData)[1];

		$this->indexOffset = $this->headerLength + 4;
		$this->dataOffset = $this->indexOffset + ($this->indexCount * $this->indexSize) + 4;

		if(fseek($this->filePointer, $this->dataOffset - 4, SEEK_SET) === -1) {
			fclose($this->filePointer);
			$this->filePointer = null;
			throw new Exception('IPDB: ipv6db.dat index error');
		}

		$countData = fread($this->filePointer, 4);
		if(strlen($countData) !== 4) {
			fclose($this->filePointer);
			$this->filePointer = null;
			throw new Exception('IPDB: ipv6db.dat index error');
		}

		$this->totalCount = unpack('N', $countData)[1];
		$this->binaryFile = $binaryFile;
		$this->dataLoaded = true;
		if($this->useMemoryCache && $this->indexCount > 0) {
			if(memory('check') && ($this->indexCache = memory('get', $this->memoryCacheKey))) {
				return;
			}
			if(fseek($this->filePointer, $this->indexOffset, SEEK_SET) === -1) {
				throw new Exception('IPDB: ipv6db.dat index error');
			}

			$allIndexData = fread($this->filePointer, $this->indexCount * $this->indexSize);
			if(strlen($allIndexData) !== $this->indexCount * $this->indexSize) {
				throw new Exception('IPDB: ipv6db.dat index error');
			}

			$this->indexCache = [];
			for($i = 0; $i < $this->indexCount; $i++) {
				$offset = $i * $this->indexSize;
				$startIp = substr($allIndexData, $offset, 16);
				$dataOffset = unpack('P', substr($allIndexData, $offset + 16, 8))[1];
				$this->indexCache[$i] = [
					'startIp' => $startIp,
					'offset' => $dataOffset
				];
			}
			if(memory('check') && $this->indexCache) {
				memory('set', $this->memoryCacheKey, $this->indexCache, $this->memoryCacheTTL);
			}
		}
	}

	public function ipv62bin($ip) {
		$bin = inet_pton($ip);
		return $bin !== false ? $bin : null;
	}

	private function readIndexRecord($index) {
		if($index < 0 || $index >= $this->indexCount || $this->filePointer === null) {
			return null;
		}

		if($this->useMemoryCache && isset($this->indexCache[$index])) {
			return $this->indexCache[$index];
		}

		$offset = $this->indexOffset + $index * $this->indexSize;

		if(fseek($this->filePointer, $offset, SEEK_SET) === -1) {
			throw new Exception('IPDB: ipv6db.dat index error');
		}

		$indexData = fread($this->filePointer, $this->indexSize);
		if(strlen($indexData) !== $this->indexSize) {
			throw new Exception('IPDB: ipv6db.dat index error');
		}

		$startIp = substr($indexData, 0, 16);
		$dataOffset = unpack('P', substr($indexData, 16, 8))[1];

		$result = [
			'startIp' => $startIp,
			'offset' => $dataOffset
		];

		if($this->useMemoryCache) {
			$this->indexCache[$index] = $result;
		}

		return $result;
	}

	private function readRecord($index) {
		if($index < 0 || $index >= $this->totalCount || $this->filePointer === null) {
			return null;
		}

		if($this->useMemoryCache && isset($this->recordCache[$index])) {
			return $this->recordCache[$index];
		}

		$offset = $this->dataOffset + $index * $this->recordSize;

		if(fseek($this->filePointer, $offset, SEEK_SET) === -1) {
			throw new Exception('IPDB: ipv6db.dat index error');
		}

		$recordData = fread($this->filePointer, $this->recordSize);
		if(strlen($recordData) !== $this->recordSize) {
			throw new Exception('IPDB: ipdb.dat index error');
		}

		$startIp = substr($recordData, 0, 16);
		$endIp = substr($recordData, 16, 16);
		$locationIndex = unpack('n', substr($recordData, 32, 2))[1];

		if($locationIndex > 0x7FFF) {
			$locationIndex = $locationIndex - 0x10000;
		}

		$result = [
			'startIp' => $startIp,
			'endIp' => $endIp,
			'locationIndex' => $locationIndex
		];

		if($this->useMemoryCache) {
			if(count($this->recordCache) >= $this->maxRecordCacheSize) {
				reset($this->recordCache);
				unset($this->recordCache[key($this->recordCache)]);
			}
			$this->recordCache[$index] = $result;
		}

		return $result;
	}

	public function query($ip) {
		if(!$this->dataLoaded || $this->filePointer === null) {
			throw new Exception('IPDB: ipv6db.dat load error');
		}

		$ipBin = $this->ipv62bin($ip);
		if($ipBin === null) {
			return null;
		}

		$low = 0;
		$high = $this->totalCount - 1;

		if($this->indexCount > 0) {
			$indexLow = 0;
			$indexHigh = $this->indexCount - 1;
			$blockStartIndex = 0;

			while($indexLow <= $indexHigh) {
				$indexMid = floor(($indexLow + $indexHigh) / 2);
				$indexRecord = $this->readIndexRecord($indexMid);

				if($indexRecord === null) {
					break;
				}

				if(strcmp($ipBin, $indexRecord['startIp']) < 0) {
					$indexHigh = $indexMid - 1;
				} else {
					$blockStartIndex = floor(($indexRecord['offset'] - $this->dataOffset) / $this->recordSize);
					$indexLow = $indexMid + 1;
				}
			}

			$low = $blockStartIndex;

			$indexLow = 0;
			$indexHigh = $this->indexCount - 1;
			$nextBlockStartIndex = $this->totalCount;

			while($indexLow <= $indexHigh) {
				$indexMid = floor(($indexLow + $indexHigh) / 2);
				$indexRecord = $this->readIndexRecord($indexMid);

				if($indexRecord === null) {
					break;
				}

				if(strcmp($ipBin, $indexRecord['startIp']) < 0) {
					$nextBlockStartIndex = floor(($indexRecord['offset'] - $this->dataOffset) / $this->recordSize);
					$indexHigh = $indexMid - 1;
				} else {
					$indexLow = $indexMid + 1;
				}
			}

			$high = min($nextBlockStartIndex - 1, $this->totalCount - 1);
		}

		while($low <= $high) {
			$mid = floor(($low + $high) / 2);

			$midRecord = $this->readRecord($mid);
			if($midRecord === null) {
				return null;
			}

			if(strcmp($ipBin, $midRecord['startIp']) >= 0 && strcmp($ipBin, $midRecord['endIp']) <= 0) {
				return $midRecord['locationIndex'];
			} elseif(strcmp($ipBin, $midRecord['startIp']) < 0) {
				$high = $mid - 1;
			} else {
				$low = $mid + 1;
			}
		}

		return null;
	}

	public function convert($ip) {
		$index = $this->query($ip);
		if($index !== null && isset($this->locationIndex[$index])) {
			return $this->locationIndex[$index];
		}
		return null;
	}

	public function getLocationIndex() {
		return $this->locationIndex;
	}

	public function getTotalCount() {
		return $this->totalCount;
	}

}

