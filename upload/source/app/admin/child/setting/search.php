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
	foreach($settingnew['search'] as $key => $val) {
		foreach($val as $k => $v) {
			$settingnew['search'][$key][$k] = max(0, intval($v));
		}
	}
} else {
	shownav('global', 'setting_'.$operation);

	showsubmenu('setting_'.$operation);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	/*search={"setting_search":"action=setting&operation=search"}*/
	$setting['search'] = dunserialize($setting['search']);
	showtableheader('setting_search_status', 'fixpadding');
	showsubtitle(['setting_search_onoff', 'search_item_name', 'setting_serveropti_searchctrl', 'setting_serveropti_maxspm', 'setting_serveropti_maxsearchresults']);
	if(helper_access::check_module('portal')) {
		$search_portal = [
			$setting['search']['portal']['status'] ? '<input type="checkbox" class="checkbox" name="settingnew[search][portal][status]" value="1" checked="checked" />' : '<input type="checkbox" class="checkbox" name="settingnew[search][portal][status]" value="1" />',
			cplang('setting_search_status_portal'),
			'<input type="text" class="txt" name="settingnew[search][portal][searchctrl]" value="'.$setting['search']['portal']['searchctrl'].'" />',
			'<input type="text" class="txt" name="settingnew[search][portal][maxspm]" value="'.$setting['search']['portal']['maxspm'].'" />',
			'<input type="text" class="txt" name="settingnew[search][portal][maxsearchresults]" value="'.$setting['search']['portal']['maxsearchresults'].'" />',
		];
	}
	$search_forum = [
		$setting['search']['forum']['status'] ? '<input type="checkbox" class="checkbox" name="settingnew[search][forum][status]" value="1" checked="checked" />' : '<input type="checkbox" class="checkbox" name="settingnew[search][forum][status]" value="1" />',
		cplang('setting_search_status_forum'),
		'<input type="text" class="txt" name="settingnew[search][forum][searchctrl]" value="'.$setting['search']['forum']['searchctrl'].'" />',
		'<input type="text" class="txt" name="settingnew[search][forum][maxspm]" value="'.$setting['search']['forum']['maxspm'].'" />',
		'<input type="text" class="txt" name="settingnew[search][forum][maxsearchresults]" value="'.$setting['search']['forum']['maxsearchresults'].'" />',
	];

	if(helper_access::check_module('blog')) {
		$search_blog = [
			$setting['search']['blog']['status'] ? '<input type="checkbox" class="checkbox" name="settingnew[search][blog][status]" value="1" checked="checked" />' : '<input type="checkbox" class="checkbox" name="settingnew[search][blog][status]" value="1" />',
			cplang('setting_search_status_blog'),
			'<input type="text" class="txt" name="settingnew[search][blog][searchctrl]" value="'.$setting['search']['blog']['searchctrl'].'" />',
			'<input type="text" class="txt" name="settingnew[search][blog][maxspm]" value="'.$setting['search']['blog']['maxspm'].'" />',
			'<input type="text" class="txt" name="settingnew[search][blog][maxsearchresults]" value="'.$setting['search']['blog']['maxsearchresults'].'" />',
		];
	}
	if(helper_access::check_module('album')) {
		$search_album = [
			$setting['search']['album']['status'] ? '<input type="checkbox" class="checkbox" name="settingnew[search][album][status]" value="1" checked="checked" />' : '<input type="checkbox" class="checkbox" name="settingnew[search][album][status]" value="1" />',
			cplang('setting_search_status_album'),
			'<input type="text" class="txt" name="settingnew[search][album][searchctrl]" value="'.$setting['search']['album']['searchctrl'].'" />',
			'<input type="text" class="txt" name="settingnew[search][album][maxspm]" value="'.$setting['search']['album']['maxspm'].'" />',
			'<input type="text" class="txt" name="settingnew[search][album][maxsearchresults]" value="'.$setting['search']['album']['maxsearchresults'].'" />',
		];
	}
	if(helper_access::check_module('group')) {
		$search_group = [
			$setting['search']['group']['status'] ? '<input type="checkbox" class="checkbox" name="settingnew[search][group][status]" value="1" checked="checked" />' : '<input type="checkbox" class="checkbox" name="settingnew[search][group][status]" value="1" />',
			cplang('setting_search_status_group'),
			'<input type="text" class="txt" name="settingnew[search][group][searchctrl]" value="'.$setting['search']['group']['searchctrl'].'" />',
			'<input type="text" class="txt" name="settingnew[search][group][maxspm]" value="'.$setting['search']['group']['maxspm'].'" />',
			'<input type="text" class="txt" name="settingnew[search][group][maxsearchresults]" value="'.$setting['search']['group']['maxsearchresults'].'" />',
		];
	}
	if(helper_access::check_module('collection')) {
		$search_collection = [
			$setting['search']['collection']['status'] ? '<input type="checkbox" class="checkbox" name="settingnew[search][collection][status]" value="1" checked="checked" />' : '<input type="checkbox" class="checkbox" name="settingnew[search][collection][status]" value="1" />',
			cplang('setting_search_status_collection'),
			'<input type="text" class="txt" name="settingnew[search][collection][searchctrl]" value="'.$setting['search']['collection']['searchctrl'].'" />',
			'<input type="text" class="txt" name="settingnew[search][collection][maxspm]" value="'.$setting['search']['collection']['maxspm'].'" />',
			'<input type="text" class="txt" name="settingnew[search][collection][maxsearchresults]" value="'.$setting['search']['collection']['maxsearchresults'].'" />',
		];
	}
	showtablerow('', ['width="100"', 'width="120"', 'width="120"', 'width="120"'], $search_portal);
	showtablerow('', '', $search_forum);
	showtablerow('', '', $search_blog);
	showtablerow('', '', $search_album);
	showtablerow('', '', $search_group);
	showtablerow('', '', $search_collection);
	showtablefooter();

	showtableheader('setting_search_srchsetting');
	showsetting('setting_search_srchcensor', 'settingnew[srchcensor]', $setting['srchcensor'], 'radio');
	showtablefooter();

	showtableheader('setting_search_srchhotkeywords');
	showsetting('setting_search_srchhotkeywords', 'settingnew[srchhotkeywords]', $setting['srchhotkeywords'], 'textarea');

	showtablefooter();

	showtableheader('settings_sphinx', 'fixpadding');
	showsetting('settings_sphinx_sphinxon', 'settingnew[sphinxon]', $setting['sphinxon'], 'radio');
	showsetting('settings_sphinx_sphinxhost', 'settingnew[sphinxhost]', $setting['sphinxhost'], 'text');
	showsetting('settings_sphinx_sphinxport', 'settingnew[sphinxport]', $setting['sphinxport'], 'text');
	showsetting('settings_sphinx_sphinxsubindex', 'settingnew[sphinxsubindex]', $setting['sphinxsubindex'], 'text');
	showsetting('settings_sphinx_sphinxmsgindex', 'settingnew[sphinxmsgindex]', $setting['sphinxmsgindex'], 'text');
	showsetting('settings_sphinx_sphinxmaxquerytime', 'settingnew[sphinxmaxquerytime]', $setting['sphinxmaxquerytime'], 'text');
	showsetting('settings_sphinx_sphinxlimit', 'settingnew[sphinxlimit]', $setting['sphinxlimit'], 'text');
	$spx_ranks = ['SPH_RANK_PROXIMITY_BM25', 'SPH_RANK_BM25', 'SPH_RANK_NONE'];
	$selectspxrank = '';
	$selectspxrank = '<select name="settingnew[sphinxrank]">';
	foreach($spx_ranks as $spx_rank) {
		$selectspxrank .= '<option value="'.$spx_rank.'"'.($spx_rank == $setting['sphinxrank'] ? 'selected="selected"' : '').'>'.$spx_rank.'</option>';
	}
	$selectspxrank .= '</select>';
	showsetting('settings_sphinx_sphinxrank', '', '', $selectspxrank);
	showtablefooter();
	/*search*/
	showtableheader();

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}