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
	$settingnew['antitheft']['allow'] = intval($settingnew['antitheft']['allow']);
	$settingnew['antitheft']['max'] = intval($settingnew['antitheft']['max']);
	$settingnew['antitheftsetting'] = [];
	if($settingnew['antitheftwhite']) {
		$arr = explode("\n", $settingnew['antitheftwhite']);
		$arr = array_unique(array_filter(array_map('trim', $arr)));
		$settingnew['antitheftsetting']['white'] = implode("\n", $arr);
		unset($arr, $settingnew['antitheftwhite']);
	}
	if($settingnew['antitheftblack']) {
		$arr = explode("\n", $settingnew['antitheftblack']);
		$arr = array_unique(array_filter(array_map('trim', $arr)));
		$settingnew['antitheftsetting']['black'] = implode("\n", $arr);
		unset($arr, $settingnew['antitheftblack']);
	}
} else {
	$_GET['anchor'] = $_GET['anchor'] === 'iplist' ? $_GET['anchor'] : '';

	/*search={"setting_antitheft":"action=setting&operation=antitheft"}*/
	if($_GET['anchor'] == 'iplist') {

		if(submitcheck('antitheftsubmit', true)) {
			$url = 'action=setting&operation=antitheft&anchor=iplist&page='.$page;
			if(empty($_GET['ips'])) {
				cpmsg('setting_antitheft_choose_ip', $url, 'error');
			}
			$antitheftsetting = table_common_setting::t()->fetch_setting('antitheftsetting', true);
			if($_GET['optype'] == 'white' || $_GET['optype'] == 'black') {
				$ips = explode("\n", $antitheftsetting[$_GET['optype']]);
				$ips = array_diff($_GET['ips'], $ips);
				if($ips) {
					$antitheftsetting[$_GET['optype']] = $antitheftsetting[$_GET['optype']]."\n".implode("\n", $ips);
					table_common_setting::t()->update_setting('antitheftsetting', $antitheftsetting);
					updatecache('antitheft');
				}
				table_common_visit::t()->delete($_GET['ips']);
				cpmsg('setting_antitheft_add_'.$_GET['optype'], $url, 'succeed');
			} elseif($_GET['optype'] == 'delete') {
				table_common_visit::t()->delete($_GET['ips']);
				cpmsg('setting_antitheft_delete_view', $url, 'succeed');
			} else {
				cpmsg('setting_antitheft_choose_optype', $url, 'error');
			}
		} else {
			shownav('global', 'setting_'.$operation);

			showsubmenu('setting_antitheft', [
				['setting_antitheft', 'setting&operation=antitheft', false],
				['setting_antitheft_iplist', 'setting&operation=antitheft&anchor=iplist', true],
			]);

			$perpage = 30;
			$start = ($page - 1) * $perpage;
			$mpurl .= '&perpage='.$perpage;
			$mpurl = ADMINSCRIPT.'?action=setting&operation=antitheft&anchor='.$_GET['anchor'];

			showformheader('setting&operation=antitheft&anchor='.$_GET['anchor']);
			showboxheader('setting_antitheft_iplist');
			showtableheader();
			showsubtitle(['', 'setting_antitheft_ip', 'setting_antitheft_view', 'setting_antitheft_op']);

			$multipage = '';
			$count = table_common_visit::t()->count();
			if($count) {
				foreach(table_common_visit::t()->range($start, $perpage) as $value) {
					showtablerow('', ['class="td25"', 'class=""', 'class="td28"'], [
						"<input type=\"checkbox\" class=\"checkbox\" name=\"ips[]\" value=\"{$value['ip']}\">",
						"{$value['ip']} ".ip::convert($value['ip']),
						$value['view'],
						"<a href=\"$mpurl&optype=white&ips[]={$value['ip']}&antitheftsubmit=yes\">{$lang['setting_antitheft_addwhitelist']}</a> |
									 <a href=\"$mpurl&optype=black&ips[]={$value['ip']}&antitheftsubmit=yes\">{$lang['setting_antitheft_addblacklist']}</a> |
									 <a href=\"$mpurl&optype=delete&ips[]={$value['ip']}&antitheftsubmit=yes\">{$lang['delete']}</a>
									",
					]);
				}
				$multipage = multi($count, $perpage, $page, $mpurl);
			}

			$batchradio = '<input type="radio" name="optype" value="white" id="op_white" class="radio" /><label for="op_white">'.cplang('setting_antitheft_addwhitelist').'</label>&nbsp;&nbsp;';
			$batchradio .= '<input type="radio" name="optype" value="black" id="op_black" class="radio" /><label for="op_black">'.cplang('setting_antitheft_addblacklist').'</label>&nbsp;&nbsp;';
			$batchradio .= '<input type="radio" name="optype" value="delete" id="op_remove" class="radio" /><label for="op_remove">'.cplang('delete').'</label>&nbsp;&nbsp;<input type="hidden" name="antitheftsubmit" value="yes" />';
			showsubmit('', '', '', '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'ips\')" /><label for="chkall">'.cplang('select_all').'</label>&nbsp;&nbsp;'
				.$batchradio.'<input type="submit" class="btn" name="antitheftbutton" value="'.cplang('submit').'" />', $multipage);
			showtablefooter();
			showboxfooter();
			showformfooter();
		}
	} else {
		shownav('global', 'setting_'.$operation);

		showsubmenu('setting_antitheft', [
			['setting_antitheft', 'setting&operation=antitheft', true],
			['setting_antitheft_iplist', 'setting&operation=antitheft&anchor=iplist', false],
		]);

		showformheader('setting&edit=yes', 'enctype');
		showhiddenfields(['operation' => $operation]);

		showtips('setting_antitheft_tips');
		$setting['antitheft'] = dunserialize($setting['antitheft']);
		$setting['antitheftsetting'] = dunserialize($setting['antitheftsetting']);
		showtableheader('setting_antitheft_status', 'fixpadding');

		showsetting('setting_antitheft_allow', ['settingnew[antitheft][allow]', [
			[1, $lang['yes'], ['antitheftext' => '']],
			[0, $lang['no'], ['antitheftext' => 'none']]
		], TRUE], !empty($setting['antitheft']['allow']) ? $setting['antitheft']['allow'] : 0, 'mradio');
		showtagheader('tbody', 'antitheftext', !empty($setting['antitheft']['allow']), 'sub');
		showsetting('setting_antitheft_24_max', 'settingnew[antitheft][max]', $setting['antitheft']['max'], 'text');
		showsetting('setting_antitheft_white', 'settingnew[antitheftwhite]', $setting['antitheftsetting']['white'], 'textarea');
		showsetting('setting_antitheft_black', 'settingnew[antitheftblack]', $setting['antitheftsetting']['black'], 'textarea');
		showsetting('setting_antitheft_disable', ['settingnew[antitheft][disable]', [
			['thread', $lang['setting_antitheft_disable_thread'], '1'],
			['article', $lang['setting_antitheft_disable_article'], '1'],
			['blog', $lang['setting_antitheft_disable_blog'], '1'],
		]], $setting['antitheft']['disable'], 'omcheckbox');
		showtagfooter('tbody');
		showsubmit('settingsubmit');
		showtablefooter();
	}
	/*search*/
}