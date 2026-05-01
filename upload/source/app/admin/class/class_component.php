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

class class_component {

	const components = [
		'number',
		'text',
		'radio',
		'textarea',
		'select',
		'selects',
		'uploadimage',
		'color',
		'date',
		'datetime',
		'forum',
		'forums',
		'group',
		'groups',
		'portalcat',
		'portalcats',
		'groupfids',
		'extcredit',
		'forum_text',
		'forum_textarea',
		'forum_radio',
		'forum_select',
		'group_text',
		'group_textarea',
		'group_radio',
		'group_select'
	];

	const styleComponents = [
		'styleTitle',
		'stylePage'
	];

	public static function get_optgroup($value = 'text') {
		$typeselect = '';
		foreach(self::components as $type) {
			$typeselect .= '<option value="'.$type.'" '.($value == $type ? 'selected' : '').'>'.cplang('plugins_edit_vars_type_'.$type).'</option>';
		}

		$components = getglobal('cache/admin/component');
		foreach($components as $_value => $v) {
			$typeselect .= '<option value="'.$_value.'" '.($value == $_value ? 'selected' : '').'>'.$v['name'].'('.$_value.')'.'</option>';
		}

		$styletypeselect = '';
		foreach(self::styleComponents as $type) {
			$styletypeselect .= '<option value="'.$type.'" '.($value == $type ? 'selected' : '').'>'.cplang('plugins_edit_vars_type_'.$type).'</option>';
		}
		return '<optgroup label="'.cplang('plugins_edit_vars_optgroup_var').'">'.$typeselect.'</optgroup><optgroup label="'.cplang('plugins_edit_vars_optgroup_style').'">'.$styletypeselect.'</optgroup>';
	}

	public static function get_name($value) {
		if(in_array($value, self::components) || in_array($value, self::styleComponents)) {
			return cplang('plugins_edit_vars_type_'.$value);
		}

		$components = getglobal('cache/admin/component');
		if(isset($components[$value])) {
			return $components[$value]['name'].'('.$value.')';
		}

		return '';
	}

	public static function get_desc($type) {
		$n = self::_plugin_class($type);

		if(!$n || !property_exists($n, 'desc')) {
			return '';
		}

		return $n->desc;
	}

	private static function _plugin_class($type) {
		$components = getglobal('cache/admin/component');

		if(!isset($components[$type])) {
			return null;
		}

		if(!class_exists($components[$type]['class'])) {
			return null;
		}

		return new $components[$type]['class']();
	}

	public static function plugin_serialize($type, &$value) {
		$n = self::_plugin_class($type);

		if(!$n || !method_exists($n, 'serialize')) {
			return;
		}

		$n->serialize($value);
	}

	public static function assign_get($key, $v = null) {
		preg_match_all('/\[([^\]]+)\]/', $key, $matches);
		$firstKey = str_contains($key, '[') ? substr($key, 0, strpos($key, '[')) : $key;
		$keys = array_merge([$firstKey], $matches[1]);

		$current = &$_GET;
		foreach($keys as $k) {
			if(!is_array($current)) {
				$current = [];
			}
			if(!isset($current[$k])) {
				$current[$k] = [];
			}
			$current = &$current[$k];
		}
		if($v !== null) {
			$current = $v;
		}
		return $current;
	}

	public static function plugin_unserialize($type, &$value) {
		$n = self::_plugin_class($type);

		if(!$n || !method_exists($n, 'unserialize')) {
			return;
		}

		$n->unserialize($value);
	}

	public static function type_plugin(&$var, &$extra) {
		$n = self::_plugin_class($var['type']);

		if(!$n || !method_exists($n, 'show')) {
			return;
		}

		if(method_exists($n, 'unserialize')) {
			$n->unserialize($var['value']);
		}

		$n->show($var, $extra);
	}

	public static function type_number(&$var, &$extra) {
		$var['type'] = 'text';
	}

	public static function type_select(&$var, &$extra) {
		$var['type'] = "<select name=\"{$var['variable']}\">\n";
		foreach(explode("\n", $var['extra']) as $key => $option) {
			$option = trim($option);
			if(!str_contains($option, '=')) {
				$key = $option;
			} else {
				$item = explode('=', $option);
				$key = trim($item[0]);
				$option = trim($item[1]);
			}
			$var['type'] .= "<option value=\"".dhtmlspecialchars($key)."\" ".($var['value'] == $key ? 'selected' : '').">$option</option>\n";
		}
		$var['type'] .= "</select>\n";
		$var['variable'] = $var['value'] = '';
	}

	public static function type_selects(&$var, &$extra) {
		$var['value'] = dunserialize($var['value']);
		$var['value'] = is_array($var['value']) ? $var['value'] : [$var['value']];
		$var['type'] = "<select name=\"{$var['variable']}[]\" multiple=\"multiple\" size=\"10\">\n";
		foreach(explode("\n", $var['extra']) as $key => $option) {
			$option = trim($option);
			if(!str_contains($option, '=')) {
				$key = $option;
			} else {
				$item = explode('=', $option);
				$key = trim($item[0]);
				$option = trim($item[1]);
			}
			$var['type'] .= "<option value=\"".dhtmlspecialchars($key)."\" ".(in_array($key, $var['value']) ? 'selected' : '').">$option</option>\n";
		}
		$var['type'] .= "</select>\n";
		$var['variable'] = $var['value'] = '';
	}

	public static function type_uploadimage(&$var, &$extra) {
		$var['type'] = 'filetext';
		$var['extra'] = 'accept=\'image/*\'';
		if($var['value']) {
			$url = \admin\class_attach::getUrl($var['value']);
			$GLOBALS['lang']['__t'] = ($var['description'] ? $var['description'].'<br />' : '').'<label><input type="checkbox" class="checkbox" name="deleteUploadimage[]" value="'.$var['var'].'" /> '.cplang('delete').'</label>'.
				'<br /><img src="'.$url.'" />';
			$var['description'] = '__t';
		}
	}

	public static function type_date(&$var, &$extra) {
		$var['type'] = 'calendar';
		$extra['date'] = '<script type="text/javascript" src="'.STATICURL.'js/calendar.js"></script>';
	}

	public static function type_datetime(&$var, &$extra) {
		$var['type'] = 'calendar';
		$var['extra'] = 1;
		$extra['date'] = '<script type="text/javascript" src="'.STATICURL.'js/calendar.js"></script>';
	}

	public static function type_forum(&$var, &$extra) {
		require_once libfile('function/forumlist');
		$var['type'] = '<select name="'.$var['variable'].'"><option value="">'.cplang('plugins_empty').'</option>'.forumselect(FALSE, 0, $var['value'], TRUE).'</select>';
		$var['variable'] = $var['value'] = '';
	}

	public static function type_forums(&$var, &$extra) {
		$var['description'] = ($var['description'] ? (cplang($var['description']) ?? $var['description'])."\n" : '').cplang('plugins_edit_vars_multiselect_comment')."\n".$var['comment'];
		$var['value'] = dunserialize($var['value']);
		$var['value'] = is_array($var['value']) ? $var['value'] : [];
		require_once libfile('function/forumlist');
		$var['type'] = '<select name="'.$var['variable'].'[]" size="10" multiple="multiple"><option value="">'.cplang('plugins_empty').'</option>'.forumselect(FALSE, 0, 0, TRUE).'</select>';
		foreach($var['value'] as $v) {
			$var['type'] = str_replace('<option value="'.$v.'">', '<option value="'.$v.'" selected>', $var['type']);
		}
		$var['variable'] = $var['value'] = '';
	}

	public static function type_groupfids(&$var, &$extra) {
		$var['type'] = 'text';
		$var['description'] = $var['description']."\n".cplang('members_search_group_fid');
	}

	public static function type_group(&$var, &$extra) {
		if($var['type'] == 'groups') {
			$var['description'] = ($var['description'] ? (cplang($var['description']) ?? $var['description'])."\n" : '').cplang('plugins_edit_vars_multiselect_comment')."\n".$var['comment'];
			$var['value'] = dunserialize($var['value']);
			$var['type'] = '<select name="'.$var['variable'].'[]" size="10" multiple="multiple"><option value=""'.(is_array($var['value']) && in_array('', $var['value']) ? ' selected' : '').'>'.cplang('plugins_empty').'</option>';
		} else {
			$var['type'] = '<select name="'.$var['variable'].'"><option value="">'.cplang('plugins_empty').'</option>';
		}
		$var['value'] = is_array($var['value']) ? $var['value'] : [$var['value']];

		$query = \table_common_usergroup::t()->range_orderby_credit();
		$groupselect = [];
		foreach($query as $group) {
			$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
			$groupselect[$group['type']] .= '<option value="'.$group['groupid'].'"'.(is_array($var['value']) && in_array($group['groupid'], $var['value']) ? ' selected' : '').'>'.$group['grouptitle'].'</option>';
		}
		$var['type'] .= '<optgroup label="'.cplang('usergroups_member').'">'.$groupselect['member'].'</optgroup>'.
			($groupselect['special'] ? '<optgroup label="'.cplang('usergroups_special').'">'.$groupselect['special'].'</optgroup>' : '').
			($groupselect['specialadmin'] ? '<optgroup label="'.cplang('usergroups_specialadmin').'">'.$groupselect['specialadmin'].'</optgroup>' : '').
			'<optgroup label="'.cplang('usergroups_system').'">'.$groupselect['system'].'</optgroup></select>';
		$var['variable'] = $var['value'] = '';
	}

	public static function type_groups(&$var, &$extra) {
		self::type_group($var, $extra);
	}

	public static function type_portalcat(&$var, &$extra) {
		global $_G;

		if($var['type'] == 'portalcats') {
			$var['description'] = ($var['description'] ? (cplang($var['description']) ?? $var['description'])."\n" : '').cplang('plugins_edit_vars_multiselect_comment')."\n".$var['comment'];
			$var['value'] = dunserialize($var['value']);
			$var['type'] = '<select name="'.$var['variable'].'[]" size="10" multiple="multiple"><option value=""'.(is_array($var['value']) && in_array('', $var['value']) ? ' selected' : '').'>'.cplang('plugins_empty').'</option>';
		} else {
			$var['type'] = '<select name="'.$var['variable'].'"><option value="">'.cplang('plugins_empty').'</option>';
		}
		$var['value'] = is_array($var['value']) ? $var['value'] : [$var['value']];

		loadcache('portalcategory');
		$category = $_G['cache']['portalcategory'];

		foreach($category as $value) {
			if($value['level'] == 0) {
				$selected = in_array($value['catid'], $var['value']) ? 'selected="selected"' : '';
				$var['type'] .= "<option value=\"{$value['catid']}\"$selected>{$value['catname']}</option>";
				if(!$value['children']) {
					continue;
				}
				foreach($value['children'] as $catid) {
					$selected = in_array($catid, $var['value']) ? 'selected="selected"' : '';
					$var['type'] .= "<option value=\"{$category[$catid]['catid']}\"$selected>-- {$category[$catid]['catname']}</option>";
					if($category[$catid]['children']) {
						foreach($category[$catid]['children'] as $catid2) {
							$selected = in_array($catid2, $var['value']) ? 'selected="selected"' : '';
							$select .= "<option value=\"{$category[$catid2]['catid']}\"$selected>---- {$category[$catid2]['catname']}</option>";
						}
					}
				}
			}
		}
		$var['type'] .= '</select>';

		$var['variable'] = $var['value'] = '';
	}

	public static function type_portalcats(&$var, &$extra) {
		self::type_portalcat($var, $extra);
	}

	public static function type_extcredit(&$var, &$extra) {
		global $_G;

		$var['type'] = '<select name="'.$var['variable'].'"><option value="">'.cplang('plugins_empty').'</option>';
		foreach($_G['setting']['extcredits'] as $id => $credit) {
			$var['type'] .= '<option value="'.$id.'"'.($var['value'] == $id ? ' selected' : '').'>'.$credit['title'].'</option>';
		}
		$var['type'] .= '</select>';
		$var['variable'] = $var['value'] = '';
	}

}