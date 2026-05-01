<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace admin;

class widget_edit {

	public static function output($type) {
		$widgets = [];
		$setting = \admin\widget_setting::get();
		if($setting) {
			$widgets[$type] = $setting['data']['data'][$type];
		} else {
			$setting = \admin\widget_data::get($type);
			$widgets[$type] = $setting[$type];
		}

		foreach($widgets[$type] as $widget) {
			$widgets = explode(',', $widget);
			$isplugin = count($widgets) > 1;
			$text = $isplugin ? lang('plugin/'.$widgets[0], $widgets[1]) : cplang('widget_'.$widget);
			$hide = !empty($setting['data']['hide'][$type]) && in_array($widget, $setting['data']['hide'][$type]) ? ' checked' : '';
			echo '<div class="dragObj" draggable="true" type="'.$type.'">'.$text;
			echo '<label style="float:right"><input type="checkbox" name="hide['.$type.'][]" value="'.$widget.'"'.$hide.' />'.cplang('hidden').'</label>';
			echo '<input type="hidden" name="pos['.$type.'][]" readonly value="'.$widget.'" />';
			echo '</div>';
		}
	}

}

