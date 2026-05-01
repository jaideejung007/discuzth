<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace home;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class class_tree {

	public $data = [];
	public $child = [-1 => []];
	public $layer = [-1 => -1];
	public $parent = [];
	public $countid = 0;

	public function __construct() {
	}

	public function setNode($id, $parent, $value) {

		$parent = $parent ? $parent : 0;

		$this->data[$id] = $value;
		$this->child[$parent][] = $id;
		$this->parent[$id] = $parent;

		if(!isset($this->layer[$parent])) {
			$this->layer[$id] = 0;
		} else {
			$this->layer[$id] = $this->layer[$parent] + 1;
		}
	}

	public function getList(&$tree, $root = 0) {
		foreach($this->child[$root] as $key => $id) {
			$tree[] = $id;
			if($this->child[$id])
				$this->getList($tree, $id);
		}
	}

	public function getValue($id) {
		return $this->data[$id];
	}

	public function reSetLayer($id) {
		if($this->parent[$id]) {
			$this->layer[$this->countid] = $this->layer[$this->countid] + 1;
			$this->reSetLayer($this->parent[$id]);
		}
	}

	public function getLayer($id, $space = false) {
		$this->layer[$id] = 0;
		$this->countid = $id;
		$this->reSetLayer($id);
		return $space ? str_repeat($space, $this->layer[$id]) : $this->layer[$id];
	}

	public function getParent($id) {
		return $this->parent[$id];
	}

	public function getParents($id) {
		while($this->parent[$id] != -1) {
			$id = $parent[$this->layer[$id]] = $this->parent[$id];
		}

		ksort($parent);
		reset($parent);

		return $parent;
	}

	public function getChild($id) {
		return $this->child[$id];
	}

	public function getChilds($id = 0) {
		$child = [];
		$this->getList($child, $id);

		return $child;
	}

}

