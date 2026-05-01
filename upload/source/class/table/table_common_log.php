<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_log extends discuz_table {

	var $_class = null;

	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$logtype = getglobal('config/log/type') ?? 'mysql';
			if($logtype == 'script') {
				$script = getglobal('config/log/script');
				if(!file_exists($script)) {
					$logtype = 'mysql';
				}
				require_once $script;
			}
			$c = class_exists('table_common_log_'.$logtype) ? 'table_common_log_'.$logtype : 'table_common_log_mysql';
			$_instance = new $c();
		}
		return $_instance;
	}
}

class table_common_log_mysql extends table_common_log {

	public function __construct() {
		$this->_table = 'common_log';
		parent::__construct();
	}

	public function fetch_all_by_conditions($conditions = [], $startlimit = 0, $count = 0, $returncount = 0, $order = ['id' => 'DESC']) {
		$wheresql = ' 1=1 ';
		if(!empty($conditions)) {
			foreach($conditions as $ckey => $cvalue) {
				$wheresql .= ' AND '.$cvalue[0].' '.$cvalue[1].' '.$cvalue[2].' ';
			}
		}
		$ordersql = '';
		if($order) {
			foreach($order as $okey => $ovalue) {
				$ordersql .= (!empty($ordersql) ? ',' : '').$okey.' '.$ovalue;
			}
		}
		if($returncount) {
			return DB::result_first("SELECT count(*) FROM %t WHERE $wheresql", [$this->_table]);
		}
		return DB::fetch_all("SELECT * FROM %t WHERE $wheresql ORDER BY ".$ordersql.' '.DB::limit($startlimit, $count), [$this->_table]);
	}

	public function delete_by_removetime($removetime, $types = []) {
		return DB::query('DELETE FROM %t WHERE dateline < %d AND type IN('.dimplode($types).')', [$this->_table, $removetime]);
	}

}

class table_common_log_file {

	var $files = [];
	var $indexs = [];

	private function _get_name($type, $date) {
		$type = str_replace(':', '_', $type);
		if(!preg_match('/^\w+$/', $type)) {
			$type = 'system';
		}
		if(!preg_match('/^[0-9]{6}$/', $date)) {
			$date = date('Ym', TIMESTAMP);
		}
		return DISCUZ_DATA.'log/'.$date.'_log_'.$type;
	}

	private function _get_files($type) {
		$type = str_replace(':', '_', $type);
		if(!preg_match('/^\w+$/', $type)) {
			$type = 'system';
		}
		$this->files = glob(DISCUZ_DATA.'log/*_log_'.$type.'.index.php');
		rsort($this->files);
	}

	private function _get_index($fileindex, $file) {
		$datafile = str_replace('.index.php', '.php', $file);
		if(!file_exists($file) || !file_exists($datafile)) {
			return [];
		}
		$indexdata = explode('|', substr(file_get_contents($file), 13));
		if(!$indexdata) {
			return [];
		}
		rsort($indexdata);
		$last = intval(current($indexdata));
		foreach($indexdata as $v) {
			$v = intval($v);
			$len = $last - $v;
			if($len > 0) {
				$this->indexs[] = [$fileindex, $v, $len];
			}
			$last = $v;
		}
	}

	private function _get_data($data) {
		$fps = $return = [];
		$i = 0;
		foreach($data as $row) {
			[$fileindex, $seek, $len] = $row;
			if(!isset($fps[$fileindex])) {
				if(empty($this->files[$fileindex])) {
					continue;
				}
				$file = str_replace('.index.php', '.php', $this->files[$fileindex]);
				if(!file_exists($file)) {
					continue;
				}
				$fps[$fileindex] = fopen($file, 'r');
			}

			fseek($fps[$fileindex], $seek);
			$row = json_decode(substr(fread($fps[$fileindex], $len), 36), true);
			$row['id'] = $i++;
			$return[] = $row;
		}
		foreach($fps as $fp) {
			fclose($fp);
		}
		return $return;
	}

	public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		$time = date('Y-m-d H:i:s', $data['dateline']);
		$date = date('Ym', $data['dateline']);

		$str = '['.$time.'] ';
		$str .= json_encode($data);
		$filename = $this->_get_name($data['type'], $date);
		$fp = fopen($filename.'.php', 'a');
		if(!$fp) {
			fclose($fp);
			return;
		}
		fwrite($fp, "<?PHP exit;?>\t".str_replace(['<?', '?>'], '', $str)."\n");
		fflush($fp);
		fclose($fp);
		$str = file_exists($filename.'.index.php') ? '' : '<?PHP exit;?>';
		$str .= '|'.filesize($filename.'.php');
		$fp = fopen($filename.'.index.php', 'a');
		if(!$fp) {
			fclose($fp);
		}
		fwrite($fp, $str);
		fflush($fp);
		fclose($fp);
	}

	public function fetch_all_by_conditions($conditions = [], $startlimit = 0, $count = 0, $returncount = 0, $order = ['id' => 'DESC']) {
		$this->indexs = [];
		if($conditions[0][0] != 'type') {
			return [];
		}
		$type = substr($conditions[0][2], 1, -1);
		$this->_get_files($type);
		foreach($this->files as $fileindex => $file) {
			$this->_get_index($fileindex, $file);
		}
		if($returncount) {
			return count($this->indexs);
		}
		return $this->_get_data(array_slice($this->indexs, $startlimit, $count));
	}

	public function delete_by_removetime($removetime, $types = []) {
	}

}