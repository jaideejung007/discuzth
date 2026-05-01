<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_portal_attachment extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'portal_attachment';
		$this->_pk = 'attachid';

		parent::__construct();
	}

	public function fetch_all_by_aid($aid) {
		return ($aid = dintval($aid, true)) ? DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('aid', $aid).' ORDER BY attachid DESC', [$this->_table], $this->_pk) : [];
	}

	public function fetch_by_aid_image($aid) {
		return $aid ? DB::fetch_first('SELECT * FROM %t WHERE aid=%d AND isimage=1', [$this->_table, $aid]) : [];
	}

	public function update_to_used($newaids, $aid) {
		$aid = dintval($aid);
		return ($newaids = dintval($newaids, true)) ? DB::update($this->_table, ['aid' => $aid], DB::field('attachid', $newaids).' AND aid=0') : false;
	}

	public function fetch_all_for_manage($catid, $authorid = 0, $filename = '', $keyword = '', $sizeless = 0, $sizemore = 0, $daysold = 0, $count = 0, $start = 0, $limit = 0) {
		$sql = '1';
		if($authorid) {
			$sql .= ' AND a.uid='.DB::quote($authorid);
		}
		if($filename) {
			$sql .= ' AND a.filename LIKE '.DB::quote('%'.$filename.'%');
		}
		if($keyword) {
			$sql .= ' AND t.title LIKE '.DB::quote('%'.$keyword.'%');
		}
		if($catid) {
			$sql .= ' AND t.catid='.DB::quote($catid);
		}
		$sql .= $sizeless ? " AND a.filesize>'$sizeless'" : '';
		$sql .= $sizemore ? " AND a.filesize<'$sizemore' " : '';
		$sql .= $daysold ? " AND a.dateline<'".(TIMESTAMP - intval($daysold) * 86400)."'" : '';
		if($count) {
			return DB::result_first('SELECT COUNT(*)
				FROM '.DB::table('portal_attachment').' a
				INNER JOIN '.DB::table('portal_article_title').' t USING(aid)
				WHERE '.$sql);
		}
		return DB::fetch_all('SELECT a.*, t.title, t.catid
				FROM '.DB::table('portal_attachment').' a
				INNER JOIN '.DB::table('portal_article_title').' t USING(aid)
				WHERE '.$sql.' ORDER BY a.aid DESC '.DB::limit($start, $limit));
	}

}

