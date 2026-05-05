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



class component_list {

	var $name = 'รายการที่กำหนดเอง';

	var $desc = '
<pre>	
[
	{
		"type": "ประเภท", "field": "ชื่อฟิลด์", "name": "ชื่อที่ใช้แสดง", "options": [
			{"name":"ชื่อที่ใช้แสดง","value":"ค่า","default":"เริ่มต้น (bool)"}
			...
		]
	},
	...
]
type: รองรับ text (ข้อความ), radio (สวิตช์), mradio (เลือกหนึ่งเดียว), checkbox (เลือกหลายรายการ), select (รายการดรอปดาวน์), color (สี)
options: มีผลเมื่อประเภทเป็น mradio, checkbox หรือ select
optionstype: มีผลเมื่อประเภทเป็น select, ค่าที่เลือกได้: groups (กลุ่มผู้ใช้), forums (บอร์ด), portalcat (หมวดหมู่พอร์ทัล), extcredit (เครดิต)
field: ชื่อตัวแปรฟิลด์
name: ชื่อที่ใช้แสดงของฟิลด์
width: ความกว้างการแสดงผล
maxlen: ความยาวสูงสุด มีผลเมื่อประเภทเป็น text หรือ color
mask: การพรางด้วยดอกจัน รูปแบบ "s,l", s = ตำแหน่งเริ่มต้น, l = ความยาว มีผลเมื่อประเภทเป็น text
</pre>
	';

	function show(&$var, &$extra) {
		static $header = '';
		if(!$header) {
			$header = '<style type="text/css">
.enhcomp th {
    min-width: 50px;
}
.enhcomp td {
    padding-right: 20px !important;
}
</style><script>
function addRow(id) {
    var i = $(id).rows.length - 1;
    var row = $(id).insertRow();
    var tpl = $(\'row_\' + id).innerHTML;
    tpl.replace(/\{_col_(\d+)_\}(.+?)\{\/_col_(\d+)_\}/g, function($1, $2, $3) {
        $3 = $3.replace(\'{_i_}\', i);
        col = row.insertCell($2);
        col.innerHTML = $3;
    });
}
</script>';
			$var['type'] = $header;
		} else {
			$var['type'] = '';
		}

		$conf = is_string($var['extra']) ? json_decode($var['extra'], true) : $var['extra'];
		$_id = random(5);
		$var['type'] .= '<table class="enhcomp" id="'.$_id.'">';
		$var['type'] .= '<tr class="header"><th width="50">'.cplang('delete').'</th>';
		foreach($conf as $col) {
			$var['type'] .= '<th>'.$col['name'].'</th>';
		}
		$var['type'] .= '<th width="50">'.cplang('order').'</th></tr>';
		$addCols = '';
		if(!empty($var['value'])) {
			$i = 0;
			foreach($var['value'] as $value) {
				$var['type'] .= '<tr><td><input type="checkbox" name="'.$var['variable'].'[_del_][]" value="'.$i.'"></td>';
				foreach($conf as $col) {
					$var['type'] .= '<td>';
					$var['type'] .= $this->_get_input($col, $var, $i, $value[$col['field']] ?? null);
					$var['type'] .= '</td>';
				}
				$var['type'] .= '<td><input type="text" name="'.$var['variable'].'[_order_][]" value="'.$i.'" style="width: 50px;"></td></tr>';
				$i++;
			}
		}

		$c = 1;
		$addCols .= '{_col_0_}<input type="checkbox" name="'.$var['variable'].'[_del_][]" value="{_i_}">{/_col_0_}';
		foreach($conf as $col) {
			$s = str_replace('{i}', '{_i_}', $this->_get_input($col, $var, '{i}', null, true));
			$addCols .= '{_col_'.$c.'_}'.$s.'{/_col_'.$c.'_}';
			$c++;
		}
		$addCols .= '{_col_'.$c.'_}<input type="text" name="'.$var['variable'].'[_order_][]" value="{_i_}" style="width: 50px;">{/_col_'.$c.'_}';
		$var['type'] .= '</table><a href="javascript:;" onclick="addRow(\''.$_id.'\')" class="addtr">เพิ่ม</a>';
		$var['type'] .= '<script type="text/html" id="row_'.$_id.'">'.$addCols.'</script>';
		$var['widemode'] = true;
	}

	function _get_optionsdata($type) {
		global $_G;

		static $optionsdata = null;
		if(empty($optionsdata[$type])) {
			switch($type) {
				case 'groups':
					$query = \table_common_usergroup::t()->range_orderby_credit();
					$optionsdata[$type][] = ['value' => '', 'name' => ''];
					foreach($query as $group) {
						$optionsdata[$type][] = ['value' => $group['groupid'], 'name' => $group['grouptitle']];
					}
					break;
				case 'forums':
					require_once libfile('function/forumlist');
					$data = forumselect(false, true, 0, true);
					$optionsdata[$type][] = ['value' => '', 'name' => ''];
					foreach($data as $fid => $forum) {
						$optionsdata[$type][] = ['value' => $fid, 'name' => $forum['name']];
						foreach($forum['sub'] as $fid => $name) {
							$optionsdata[$type][] = ['value' => $fid, 'name' => '&nbsp;&nbsp;-&nbsp;'.$name];
							foreach($forum['child'][$fid] as $fid => $name) {
								$optionsdata[$type][] = ['value' => $fid, 'name' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;'.$name];
							}
						}
					}
					break;
				case 'portalcat':
					loadcache('portalcategory');
					$category = $_G['cache']['portalcategory'];
					$optionsdata[$type][] = ['value' => '', 'name' => ''];
					foreach($category as $value) {
						if($value['level'] == 0) {
							$optionsdata[$type][] = ['value' => $value['catid'], 'name' => $value['catname']];
							if(!$value['children']) {
								continue;
							}
							foreach($value['children'] as $catid) {
								$optionsdata[$type][] = ['value' => $category[$catid]['catid'], 'name' => '&nbsp;&nbsp;-&nbsp;'.$category[$catid]['catname']];
								if($category[$catid]['children']) {
									foreach($category[$catid]['children'] as $catid2) {
										$optionsdata[$type][] = ['value' => $category[$catid2]['catid'], 'name' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;'.$category[$catid2]['catname']];
									}
								}
							}
						}
					}
					break;
				case 'extcredit':
					$optionsdata[$type][] = ['value' => '', 'name' => ''];
					foreach($_G['setting']['extcredits'] as $id => $credit) {
						$optionsdata[$type][] = ['value' => $id, 'name' => $credit['title']];
					}
			}
		}
		return $optionsdata[$type];
	}

	function __get_maskvalue($value, $mask) {
		if(!empty($mask)) {
			list($s, $l) = explode(',', $mask);
			if($s > 0 && $l > 0) {
				$e = substr($value, intval($s) + intval($l));
				$value = component_list . phpsubstr($value, 0, $s) . str_repeat('*', $l) .$e;
			}
		}
		return $value;
	}

	function _get_input($col, $var, $i, $value = null, $default = false) {
		$widthstr = $col['width'] ? ' style="width:'.$col['width'].'px !important;"' : '';
		switch($col['type']) {
			case 'text':
				$append = '';
				if($value !== null && !empty($col['mask'])) {
					$mvalue = dhtmlspecialchars(authcode(json_encode([$value, $col['mask']]), 'ENCODE'));
					$value = $this->__get_maskvalue($value, $col['mask']);
					$append = '<input type="hidden" name="'.$var['variable'].'['.$i.']['.$col['field'].'._mask_]" value="'.$mvalue.'" />';
				}
				$paramstr = $value !== null ? ' value="'.dhtmlspecialchars($value).'"' : '';
				$paramstr .= $col['maxlen'] ? ' maxlength="'.$col['maxlen'].'"' : '';
				return '<input name="'.$var['variable'].'['.$i.']['.$col['field'].']" type="text"'.$paramstr.$widthstr.' />'.$append;
			case 'radio':
				if(!empty($value)) {
					$checked = ' checked';
				}
				return '<input name="'.$var['variable'].'['.$i.']['.$col['field'].']" type="checkbox" value="1"'.$checked.' />';
			case 'mradio':
				$str = '';
				foreach($col['options'] as $opt) {
					$checked = $value !== null && $opt['value'] == $value || $default && $opt['default'] ? ' checked' : '';
					$str .= '<label><input name="'.$var['variable'].'['.$i.']['.$col['field'].']" type="radio" value="'.$opt['value'].'"'.$checked.' />'.$opt['name'].'</label>';
				}
				return $str;
			case 'checkbox':
				$str = '';
				foreach($col['options'] as $opt) {
					$checked = $value !== null && in_array($opt['value'], $value) || $default && $opt['default'] ? ' checked' : '';
					$str .= '<label><input name="'.$var['variable'].'['.$i.']['.$col['field'].'][]" type="checkbox" value="'.$opt['value'].'"'.$checked.' />'.$opt['name'].'</label>';
				}
				return $str;
			case 'select':
				if(!empty($col['optionstype'])) {
					$col['options'] = $this->_get_optionsdata($col['optionstype']);
				}
				$str = '<select name="'.$var['variable'].'['.$i.']['.$col['field'].']"'.$widthstr.'>';
				foreach($col['options'] as $opt) {
					$selected = $value !== null && $opt['value'] == $value || $default && $opt['default'] ? ' selected' : '';
					$str .= '<option value="'.$opt['value'].'"'.$selected.'>'.$opt['name'].'</option>';
				}
				return $str.'</select>';
			case 'color':
				$colorid = ++$GLOBALS['coloridcount'];
				$valstr = $value !== null ? ' value="'.dhtmlspecialchars($value).'"' : '';
				$maxstr = $col['maxlen'] ? ' maxlength="'.$col['maxlen'].'"' : '';
				$s .= '<input id="c'.$colorid.'_v"  name="'.$var['variable'].'['.$i.']['.$col['field'].']" type="text" '.$valstr.$widthstr.$maxstr.'   value="'.$value.'" onchange="updatecolorpreview(\'c'.$colorid.'\')"/>';
				$s .= "<input id=\"c$colorid\" onclick=\"c{$colorid}_frame.location='static/image/admincp/getcolor.htm?c{$colorid}|c{$colorid}_v';showMenu({'ctrlid':'c$colorid'})\" type=\"button\" class=\"colorwd\" value=\"\" style=\"background: ".dhtmlspecialchars($value)."\"><span id=\"c{$colorid}_menu\" style=\"display: none\"><iframe name=\"c{$colorid}_frame\" src=\"\" frameborder=\"0\" width=\"210\" height=\"148\" scrolling=\"no\"></iframe></span>";
				return $s;
		}
	}

	function serialize(&$value) {
		if(!empty($value['_del_'])) {
			foreach($value['_del_'] as $i) {
				unset($value[$i]);
			}
			unset($value['_del_']);
		}
		$valuenew = [];
		if(!empty($value['_order_'])) {
			$value['_order_'] = array_flip($value['_order_']);
			ksort($value['_order_']);
			foreach($value['_order_'] as $k) {
				if(!isset($value[$k])) {
					continue;
				}
				foreach($value[$k] as $_k => $_v) {
					if(str_ends_with($_k, '._mask_')) {
						$mvalue = json_decode(authcode($_v, 'DECODE'));
						if($mvalue) {
							$_ik = substr($_k, 0, -7);
							if($this->__get_maskvalue($mvalue[0], $mvalue[1]) == $value[$k][$_ik]) {
								$value[$k][$_ik] = $mvalue[0];
							}
							unset($value[$k][$_k]);
						}
					}
				}
				$valuenew[] = $value[$k];
			}
			$value = $valuenew;
		}

		$value = array_values($valuenew);
		$value = json_encode($value);
	}

	function unserialize(&$value) {
		$value = json_decode($value, 1);
	}

}