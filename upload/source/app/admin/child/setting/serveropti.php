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
	if(isset($settingnew['maxonlines'])) {
		if($settingnew['maxonlines'] > 65535 || !is_numeric($settingnew['maxonlines'])) {
			cpmsg('setting_maxonlines_invalid', '', 'error');
		}

		C::app()->session->update_max_rows($settingnew['maxonlines']);
		if($settingnew['maxonlines'] < $setting['maxonlines']) {
			C::app()->session->clear();
		}
	}

	if(isset($settingnew['jspath'])) {
		if(!$settingnew['jspath']) {
			$settingnew['jspath'] = $settingnew['jspathcustom'];
		}
	}

	if(isset($settingnew['csspathv'])) {
		if(!$settingnew['csspathv']) {
			$settingnew['csspathv'] = $settingnew['csspathcustom'];
		}
	}

	if(isset($settingnew['blockmaxaggregationitem'])) {
		$settingnew['blockmaxaggregationitem'] = intval($settingnew['blockmaxaggregationitem']);
	}

	if(isset($settingnew['blockcachetimerange'])) {
		$settingnew['blockcachetimerange'] = $settingnew['blockcachetimerange'][0] == 0 && $settingnew['blockcachetimerange'][1] == 23 ? '' : $settingnew['blockcachetimerange'][0].','.$settingnew['blockcachetimerange'][1];
	}

	if(isset($settingnew['sessionclose'])) {
		$settingnew['sessionclose'] = (bool)$settingnew['sessionclose'];
	}

	if(isset($settingnew['onlineguestsmultiple'])) {
		$settingnew['onlineguestsmultiple'] = floatval($settingnew['onlineguestsmultiple']);
	}
} else {
	shownav('global', 'setting_'.$operation);

	$current = [$operation => 1];
	$memorydata = memory('check') ? ['setting_memorydata', 'setting&operation=memorydata', $current['memorydata']] : '';
	showsubmenu('setting_optimize', [
		['setting_cachethread', 'setting&operation=cachethread', $current['cachethread']],
		['setting_serveropti', 'setting&operation=serveropti', $current['serveropti']],
		['setting_memory', 'setting&operation=memory', $current['memory']],
		$memorydata
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	$checkgzipfunc = !function_exists('ob_gzhandler') ? 1 : 0;
	if($setting['jspath'] == 'static/js/') {
		$tjspath['default'] = 'checked="checked"';
		$setting['jspath'] = '';
	} elseif($setting['jspath'] == 'data/cache/') {
		$tjspath['cache'] = 'checked="checked"';
		$setting['jspath'] = '';
	} else {
		$tjspath['custom'] = 'checked="checked"';
	}

	if(!$setting['csspathv'] || $setting['csspathv'] == 'data/cache/') {
		$tcsspath['cache'] = 'checked="checked"';
		$setting['csspathv'] = '';
	} else {
		$tcsspath['custom'] = 'checked="checked"';
	}

	/*search={"setting_optimize":"action=setting&operation=seo","setting_serveropti":"action=setting&operation=serveropti"}*/
	showtips('setting_tips');
	showtableheader();
	showtitle('setting_serveropti');
	showsetting('setting_serveropti_optimize_thread_view', 'settingnew[optimizeviews]', $setting['optimizeviews'], 'radio');
	showsetting('setting_serveropti_preventrefresh', 'settingnew[preventrefresh]', $setting['preventrefresh'], 'radio');
	showsetting('setting_serveropti_delayviewcount', 'settingnew[delayviewcount]', $setting['delayviewcount'], 'radio');
	showsetting('setting_serveropti_nocacheheaders', 'settingnew[nocacheheaders]', $setting['nocacheheaders'], 'radio');
	showsetting('setting_serveropti_maxonlines', 'settingnew[maxonlines]', $setting['maxonlines'], 'text');
	showsetting('setting_serveropti_onlinehold', 'settingnew[onlinehold]', $setting['onlinehold'], 'text');
	showsetting('setting_serveropti_jspath', '', '', '<ul class="nofloat" onmouseover="altStyle(this);">'.
		(!empty($_G['config']['plugindeveloper']) ? '<li'.($tjspath['default'] ? ' class="checked"' : '').'><input class="radio" type="radio" name="settingnew[jspath]" value="static/js/" '.$tjspath['default'].'> '.$lang['setting_serveropti_jspath_default'].'</li>' : '').
		'<li'.($tjspath['cache'] ? ' class="checked"' : '').'><input class="radio" type="radio" name="settingnew[jspath]" value="data/cache/" '.$tjspath['cache'].'> '.$lang['setting_serveropti_jspath_cache'].'</li>'.
		'<li'.($tjspath['custom'] ? ' class="checked"' : '').'><input class="radio" type="radio" name="settingnew[jspath]" value="" '.$tjspath['custom'].'> '.$lang['setting_serveropti_jspath_custom'].' <input type="text" class="txt" style="width: 150px" name="settingnew[jspathcustom]" value="'.$setting['jspath'].'" size="6"></li></ul>'
	);
	showsetting('setting_serveropti_csspath', '', '', '<ul class="nofloat" onmouseover="altStyle(this);">
				<li'.($tcsspath['cache'] ? ' class="checked"' : '').'><input class="radio" type="radio" name="settingnew[csspathv]" value="data/cache/" '.$tcsspath['cache'].'> '.$lang['setting_serveropti_csspath_cache'].'</li>
				<li'.($tcsspath['custom'] ? ' class="checked"' : '').'><input class="radio" type="radio" name="settingnew[csspathv]" value="" '.$tcsspath['custom'].'> '.$lang['setting_serveropti_csspath_custom'].' <input type="text" class="txt" style="width: 150px" name="settingnew[csspathcustom]" value="'.$setting['csspathv'].'" size="6"></li></ul>'
	);
	showsetting('setting_serveropti_lazyload', 'settingnew[lazyload]', $setting['lazyload'], 'radio');
	showsetting('setting_serveropti_blockmaxaggregationitem', 'settingnew[blockmaxaggregationitem]', $setting['blockmaxaggregationitem'], 'text');
	$setting['blockcachetimerange'] = empty($setting['blockcachetimerange']) ? ['0', '23'] : explode(',', $setting['blockcachetimerange']);
	$blockcachetimerange = range(0, 23);
	$point = $lang['setting_serveropti_blockcachetimerangepoint'];
	$html = '<select name="settingnew[blockcachetimerange][0]" class="ps" style="width:90px;" >';
	foreach($blockcachetimerange as $value) {
		$html .= '<option value="'.$value.'"'.($value == $setting['blockcachetimerange'][0] ? ' selected="selected"' : '').'>'.$value.$point.'</option>';
	}
	$html .= '</select>- &nbsp;<select name="settingnew[blockcachetimerange][1]" class="ps" style="width:90px;" >';
	foreach($blockcachetimerange as $value) {
		$html .= '<option value="'.$value.'"'.($value == $setting['blockcachetimerange'][1] ? ' selected="selected"' : '').'>'.$value.$point.'</option>';
	}
	$html .= '</select>';
	showsetting('setting_serveropti_blockcachetimerange', '', '', $html);
	showsetting('setting_serveropti_sessionclose', 'settingnew[sessionclose]', $setting['sessionclose'], 'radio', '', 1);
	showsetting('setting_serveropti_onlineguestsmultiple', 'settingnew[onlineguestsmultiple]', $setting['onlineguestsmultiple'] ? $setting['onlineguestsmultiple'] : 10, 'text');
	showtagfooter('tbody');
	/*search*/

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}