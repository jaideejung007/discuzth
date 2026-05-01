<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$setting = $_G['setting']['makehtml'];
if(!empty($setting['flag'])) {
	cpmsg('admincp_makehtml_cleanhtml_error', 'action=makehtml&operation=makehtmlsetting', 'error');
} else {
	if(!submitcheck('cleanhtml')) {
		/*search={"nav_makehtml":"action=makehtml","makehtml_clear":"action=makehtml&operation=cleanhtml"}*/

		showformheader('makehtml&operation=cleanhtml');
		showtableheader();
		showsetting('setting_functions_makehtml_cleanhtml', ['cleandata', [cplang('setting_functions_makehtml_cleanhtml_index'), cplang('setting_functions_makehtml_cleanhtml_category'), cplang('setting_functions_makehtml_cleanhtml_other')]], 0, 'binmcheckbox');
		showtagfooter('tbody');
		showsubmit('cleanhtml', 'submit');
		showtablefooter();
		showformfooter();
		/*search*/
	} else {
		if(isset($_GET['cleandata'])) {
			$cleandata = $_GET['cleandata'];
			if(isset($cleandata[1])) {
				unlink(DISCUZ_ROOT_STATIC.'./'.$setting['indexname'].'.'.$setting['extendname']);
			}
			if(isset($cleandata[2])) {
				loadcache('portalcategory');
				foreach($_G['cache']['portalcategory'] as $cat) {
					if($cat['fullfoldername']) {
						unlink($cat['fullfoldername'].'/index.'.$setting['extendname']);
					}
				}
			}
			if(isset($cleandata[3])) {
				if(!empty($setting['articlehtmldir']) && $setting['articlehtmldir'] === $setting['topichtmldir']) {
					drmdir(DISCUZ_ROOT_STATIC.'./'.$setting['articlehtmldir'], $setting['extendname']);
				} elseif(!empty($setting['topichtmldir'])) {
					drmdir(DISCUZ_ROOT_STATIC.'./'.$setting['topichtmldir'], $setting['extendname']);
				} elseif(!empty($setting['articlehtmldir'])) {
					drmdir(DISCUZ_ROOT_STATIC.'./'.$setting['articlehtmldir'], $setting['extendname']);
				}
				if(empty($setting['articlehtmldir'])) {
					loadcache('portalcategory');
					foreach($_G['cache']['portalcategory'] as $cat) {
						if($cat['fullfoldername']) {
							if(($dirobj = dir(DISCUZ_ROOT_STATIC.'./'.$cat['fullfoldername']))) {
								while(false !== ($file = $dirobj->read())) {
									if($file != '.' && $file != '..') {
										$path = $dirobj->path.'/'.$file;
										if(is_dir($path) && false === check_son_folder($file, $cat)) {
											drmdir($path, $setting['extendname']);
										}
									}
								}
								$dirobj->close();
							}
						}
					}
				}
			}
			cpmsg('admincp_makehtml_cleanhtml_succeed', 'action=makehtml&operation=cleanhtml', 'succeed');
		} else {
			cpmsg('admincp_makehtml_cleanhtml_choose_item', 'action=makehtml&operation=cleanhtml', 'error');
		}
	}
}
	