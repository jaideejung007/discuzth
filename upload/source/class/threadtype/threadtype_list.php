<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}



class threadtype_list {

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
	{"template": {"global": "เทมเพลตสากล", "viewthread": "เทมเพลตหน้าเนื้อหาโพสต์"}}
	...
]
type: รองรับ text (ข้อความ), radio (เลือกหนึ่งเดียว), checkbox (เลือกหลายรายการ), select (รายการดรอปดาวน์), color (สี)
options: มีผลเมื่อประเภทเป็น mradio, checkbox หรือ select
field: ชื่อตัวแปรของฟิลด์
name: ชื่อที่ใช้แสดงของฟิลด์
width: ความกว้างการแสดงผล
maxlen: ความยาวสูงสุด มีผลเมื่อประเภทเป็น text หรือ color
mask: การพรางด้วยดอกจัน รูปแบบ "s,l", s = ตำแหน่งเริ่มต้น, l = ความยาว มีผลเมื่อประเภทเป็น text
template: ในเทมเพลตให้ใช้ {ชื่อฟิลด์} เพื่ออ้างอิงถึงฟิลด์รายการที่เกี่ยวข้อง

【ตัวอย่าง】
[
 {"type": "text", "field": "f1", "name": "ข้อความ", "width": 80},
 {"type": "radio", "field": "f2", "name": "สวิตช์", "options": [{"name":"A","value":"a","default":true},{"name":"B","value":"b"}]},
 {"template": {"global": "{f1} - {f2}", "viewthread": "{f1} - {f2}<br>"}}
]

</pre>
	';

	function show($option, $params) {
		static $header = '';
		if(!$header) {
			$header = '<style type="text/css">
.enhcomp {
	width: auto !important;
}
.enhcomp th {
	width: auto !important;
	text-align: left !important;
	padding: 0 4px 2px 4px !important;
}
.enhcomp td {
	padding-right: 20px !important;
}
.enhcomp label {
	margin-right: 10px;
}
.enhcomp .albumBtn {
	display: inline-block;
	min-width: 26px;
	height: 26px;
	background-image: url('.STATICURL.'image/common/uploadbutton_small_pic.png);
	background-repeat: no-repeat;
	background-position: center top;
	vertical-align: middle;
}
div.enhcomp ul {
	border-bottom: 1px dotted #ccc;
}
div.enhcomp li input{
	border: none;
	padding: 0;
	line-height: 30px;
	font-size: 16px;
}
</style>
<script>
function threadTypeAddRow(id) {
	var tpl = document.getElementById(\'row_\' + id).innerHTML;
	if(document.getElementById(id).tagName == \'TABLE\') {
		var i = document.getElementById(id).rows.length - 1;
		var row = document.getElementById(id).insertRow();
		tpl.replace(/\{_col_(\d+)_\}(.+?)\{\/_col_(\d+)_\}/g, function($1, $2, $3) {
			$3 = $3.replace(\'{_i_}\', i);
			col = row.insertCell($2);
			col.innerHTML = $3;
		});
	} else {
		var i = document.getElementById(id).childNodes.length;
		tpl = tpl.replace(/\{_i_\}/g, i);
		document.getElementById(id).innerHTML += tpl;
	}
}
</script>
';
			$string = $header;
		} else {
			$string = '';
		}

		$conf = is_string($params) ? json_decode($params, true) : $params;
		$this->_id = $_id = random(5);
		$_names = [];
		$_header = [$this->__get_tpl('header_col', $_names[] = lang('template', 'delete'))];
		foreach($conf as $col) {
			if(!isset($col['field'])) {
				continue;
			}
			$_header[] = $this->__get_tpl('header_col', $_names[] = $col['name']);
		}
		$_header[] = $this->__get_tpl('header_col', $_names[] = lang('forum/template', 'displayorder'));
		$_header = $this->__get_tpl('header_row', implode('', $_header));
		$_rows = [];
		if(!empty($option['value'])) {
			$i = 0;
			foreach($option['value'] as $value) {
				$_row = [$this->__get_tpl('list_col',
					'<input type="checkbox" name="typeoption['.$option['identifier'].'][_del_][]" value="'.$i.'">',
					$_names[0]
				)];
				$c = 1;
				foreach($conf as $col) {
					if(!isset($col['field'])) {
						continue;
					}
					$var['variable'] = 'typeoption['.$option['identifier'].']';
					$_row[] = $this->__get_tpl('list_col',
						$this->_get_input($col, $var, $i, $value[$col['field']] ?? null),
						$_names[$c++]
					);
				}
				$_row[] = $this->__get_tpl('list_col',
					'<input type="text" name="typeoption['.$option['identifier'].'][_order_][]" value="'.$i.'" style="width: 50px;">',
					$_names[$c]
				);
				$_rows[] = $this->__get_tpl('list_row', implode('', $_row));
				$i++;
			}
		}
		$_content = $this->__get_tpl('body', $_header.implode('', $_rows));
		$string .= $_content.'<a href="javascript:;" onclick="threadTypeAddRow(\''.$_id.'\')" class="addtr">'.lang('template', 'add').'</a>';

		if(!defined('IN_MOBILE')) {
			$addCols = '';
			$c = 1;
			$addCols .= '{_col_0_}<input type="checkbox" name="typeoption['.$option['identifier'].'][_del_][]" value="{_i_}">{/_col_0_}';
			foreach($conf as $col) {
				if(!isset($col['field'])) {
					continue;
				}
				$var['variable'] = 'typeoption['.$option['identifier'].']';
				$s = str_replace('{i}', '{_i_}', $this->_get_input($col, $var, '{i}', null, true));
				$addCols .= '{_col_'.$c.'_}'.$s.'{/_col_'.$c.'_}';
				$c++;
			}
			$addCols .= '{_col_'.$c.'_}<input type="text" name="typeoption['.$option['identifier'].'][_order_][]" value="{_i_}" style="width: 50px;">{/_col_'.$c.'_}';
			$string .= '<script type="text/html" id="row_'.$_id.'">'.$addCols.'</script>';
		} else {
			$_row = [$this->__get_tpl('list_col',
				'<input type="checkbox" name="typeoption['.$option['identifier'].'][_del_][]" value="{_i_}">',
				$_names[0]
			)];
			$c = 1;
			foreach($conf as $col) {
				if(!isset($col['field'])) {
					continue;
				}
				$var['variable'] = 'typeoption['.$option['identifier'].']';
				$s = str_replace('{i}', '{_i_}', $this->_get_input($col, $var, '{i}', null, true));
				$_row[] = $this->__get_tpl('list_col',
					$s,
					$_names[$c++]
				);
			}
			$_row[] = $this->__get_tpl('list_col',
				'<input type="text" name="typeoption['.$option['identifier'].'][_order_][]" value="{_i_}" style="width: 50px;">',
				$_names[$c]
			);
			$addCols = $this->__get_tpl('list_row', implode('', $_row));
			$string .= '<script type="text/html" id="row_'.$_id.'">'.$addCols.'</script>';
		}
		return $string;
	}

	function __get_tpl($type, $content, $name = '') {
		static $tpl = null;
		if($tpl === null) {
			$tpl = !defined('IN_MOBILE') ? [
				'body' => '<table class="enhcomp" id="'.$this->_id.'">{content}</table>',
				'header_row' => '<tr class="header bbda">{content}</tr>',
				'header_col' => '<th>{content}</th>',
				'list_row' => '<tr>{content}</tr>',
				'list_col' => '<td>{content}</td>',
			] : [
				'body' => '<div class="enhcomp" id="'.$this->_id.'">{content}</div>',
				'header_row' => '',
				'header_col' => '',
				'list_row' => '<ul>{content}</ul>',
				'list_col' => '<li>{name}: {content}</li>',
			];
		}
		return isset($tpl[$type]) ? str_replace([
				'{name}',
				'{content}',
			], [
				$name,
				$content,
			], $tpl[$type]) : '';
	}

	function __get_maskvalue($value, $mask) {
		if(!empty($mask)) {
			list($s, $l) = explode(',', $mask);
			if($s > 0 && $l > 0) {
				$e = substr($value, intval($s) + intval($l));
				$value = component_list.phpsubstr($value, 0, $s).str_repeat('*', $l).$e;
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
				$str = '<select name="'.$var['variable'].'['.$i.']['.$col['field'].']"'.$widthstr.'>';
				foreach($col['options'] as $opt) {
					$selected = $value !== null && $opt['value'] == $value || $default && $opt['default'] ? ' selected' : '';
					$str .= '<option value="'.$opt['value'].'"'.$selected.'>'.$opt['name'].'</option>';
				}
				return $str.'</select>';
			case 'album':
				$paramstr = $value !== null ? ' value="'.dhtmlspecialchars($value).'"' : '';
				$str = '<input name="'.$var['variable'].'['.$i.']['.$col['field'].']" type="text"'.$paramstr.$widthstr.' />';
				return $str.(!defined('IN_MOBILE') ? '<a href="javascript:;" onclick="openAlbumWindow(this.previousElementSibling)" class="albumBtn"></a>' : '');
		}
	}

	function view($viewtype, $option, $params, $value) {
		$params = json_decode($params, true);
		$tpl = '';
		foreach($params as $param) {
			if(isset($param['template'][$viewtype])) {
				$tpl = $param['template'][$viewtype];
				break;
			}
		}
		if(!$tpl) {
			return '';
		}
		$string = '';
		foreach($value as $v) {
			$row = $tpl;
			foreach($params as $param) {
				if(!isset($param['field'])) {
					continue;
				}
				$value = isset($v[$param['field']]) ? (is_array($v[$param['field']]) ? implode(',', $v[$param['field']]) : $v[$param['field']]) : '';
				$row = str_replace('{'.$param['field'].'}', $value, $row);
			}
			$string .= $row;
		}
		return $string;
	}

	function serialize($params, &$value) {
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

	function unserialize($params, &$value) {
		$value = json_decode($value, 1);
	}

}