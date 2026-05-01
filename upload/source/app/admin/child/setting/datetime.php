<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('settingsubmit')) {
	if(isset($settingnew['timeformat'])) {
		$settingnew['timeformat'] = $settingnew['timeformat'] == '24' ? 'H:i' : 'h:i A';
	}
	if(isset($settingnew['dateformat'])) {
		$settingnew['dateformat'] = dateformat($settingnew['dateformat'], 'format');
	}
} else {
	shownav('global', 'setting_'.$operation);

	showsubmenu('setting_'.$operation);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	$checktimeformat = [$setting['timeformat'] == 'H:i' ? 24 : 12 => 'checked'];

	$setting['userdateformat'] = dateformat($setting['userdateformat']);
	$setting['dateformat'] = dateformat($setting['dateformat']);

	/*search={"setting_datetime":"action=setting&operation=datetime"}*/
	showtableheader();
	showtitle('setting_datetime_format');
	showsetting('setting_datetime_dateformat', 'settingnew[dateformat]', $setting['dateformat'], 'text');
	showsetting('setting_datetime_timeformat', '', '', '<input class="radio" type="radio" name="settingnew[timeformat]" value="24" '.$checktimeformat[24].'> 24 '.$lang['hour'].' <input class="radio" type="radio" name="settingnew[timeformat]" value="12" '.$checktimeformat[12].'> 12 '.$lang['hour'].'');
	showsetting('setting_datetime_dateconvert', 'settingnew[dateconvert]', $setting['dateconvert'], 'radio');

	$timezone_lang = cplang('setting_datetime_timezone');
	$timezone_select = "<select name='global_timeoffset' onchange=\"if(this.value !== '')$('settingnew[timeoffset]').value=this.value;\">";
	foreach($timezone_lang as $key => $val) {
		$timezone_select .= "<option value='$key' ".($setting['timeoffset'] == $key ? 'selected="selected"' : '').'>'.cutstr($val, 34, '..').'</option>';
	}
	$timezone_select .= '</select>';
	$timezone_select .= "<br><br><input id=\"settingnew[timeoffset]\" type=\"text\" class=\"txt\" value=\"".$setting['timeoffset']."\" name=\"settingnew[timeoffset]\">";
	showsetting('setting_datetime_timeoffset', '', '', $timezone_select);

	showtitle('setting_datetime_periods');
	showsetting('setting_datetime_visitbanperiods', 'settingnew[visitbanperiods]', $setting['visitbanperiods'], 'textarea');
	showsetting('setting_datetime_ban_downtime', 'settingnew[attachbanperiods]', $setting['attachbanperiods'], 'textarea');
	showsetting('setting_datetime_searchbanperiods', 'settingnew[searchbanperiods]', $setting['searchbanperiods'], 'textarea');
	/*search*/

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}