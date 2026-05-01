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
	if(!empty($settingnew['thumbstatus']) && !function_exists('imagejpeg')) {
		$settingnew['thumbstatus'] = 0;
	}

	if($isfounder && isset($settingnew['ftp'])) {
		$setting['ftp'] = dunserialize($setting['ftp']);
		$setting['ftp']['password'] = authcode($setting['ftp']['password'], 'DECODE', md5($_G['config']['security']['authkey']));
		if(!empty($settingnew['ftp']['password'])) {
			$pwlen = strlen($settingnew['ftp']['password']);
			if($pwlen < 3) {
				cpmsg('ftp_password_short', '', 'error');
			}
			if($settingnew['ftp']['password'][0] == $setting['ftp']['password'][0] && $settingnew['ftp']['password'][$pwlen - 1] == $setting['ftp']['password'][strlen($setting['ftp']['password']) - 1] && substr($settingnew['ftp']['password'], 1, $pwlen - 2) == '********') {
				$settingnew['ftp']['password'] = $setting['ftp']['password'];
			}
			$settingnew['ftp']['password'] = authcode($settingnew['ftp']['password'], 'ENCODE', md5($_G['config']['security']['authkey']));
		}

		$setting['oss'] = dunserialize($setting['oss']);
		$setting['oss']['oss_key'] = authcode($setting['oss']['oss_key'], 'DECODE', md5($_G['config']['security']['authkey']));
		if(!empty($settingnew['oss']['oss_key'])) {
			$pwlen = strlen($settingnew['oss']['oss_key']);
			if($settingnew['oss']['oss_key'][0] == $setting['oss']['oss_key'][0] && $settingnew['oss']['oss_key'][$pwlen - 1] == $setting['oss']['oss_key'][strlen($setting['oss']['oss_key']) - 1] && substr($settingnew['oss']['oss_key'], 1, $pwlen - 2) == '********') {
				$settingnew['oss']['oss_key'] = $setting['oss']['oss_key'];
			}
			$settingnew['oss']['oss_key'] = authcode($settingnew['oss']['oss_key'], 'ENCODE', md5($_G['config']['security']['authkey']));
		}
		if($settingnew['ftp']['on'] == 2) {
			$settingnew['attachurl'] = $settingnew['ftp']['attachurl'] = $settingnew['oss']['oss_url'].($settingnew['oss']['oss_rootpath'] ? '/'.$settingnew['oss']['oss_rootpath'] : '');
			if($settingnew['oss']['oss_avatar']) {
				ftpcmd('upload', 'avatar/noavatar.svg');
			}
			if(empty($settingnew['ftp']['host'])) {
				$settingnew['ftp']['host'] = $settingnew['oss']['oss_url'];
			}
		} elseif($settingnew['ftp']['on'] == 0) {
			if($_G['setting']['ftp']['on'] == 2) {
				$settingnew['attachurl'] = 'data/attachment';
			}
		}
	}

	$ossTypeParts = explode('_', $settingnew['oss']['oss_type']);
	$lastPart = end($ossTypeParts);

	$subValue = str_starts_with($lastPart, 'sub') ? substr($lastPart, 3) : '';
	!empty($subValue) && ($settingnew['oss']['oss_type'] = str_replace('_'.$lastPart, '', $settingnew['oss']['oss_type']));
	$settingnew['oss']['oss_subtype'] = $subValue;

	if($settingnew['allowattachurl'] && !in_array($_G['config']['download']['readmod'], [1, 4])) {
		// 如需附件URL地址、媒体附件播放，需选择支持Range参数的读取模式1或4，其他模式会导致部分浏览器下视频播放异常
		cpmsg('attach_readmod_error', '', 'error');
	}
	$settingnew['thumbwidth'] = intval($settingnew['thumbwidth']) > 0 ? intval($settingnew['thumbwidth']) : 200;
	$settingnew['thumbheight'] = intval($settingnew['thumbheight']) > 0 ? intval($settingnew['thumbheight']) : 300;
	$settingnew['maxthumbwidth'] = intval($settingnew['maxthumbwidth']);
	$settingnew['maxthumbheight'] = intval($settingnew['maxthumbheight']);
	if($settingnew['maxthumbwidth'] < 300 || $settingnew['maxthumbheight'] < 300) {
		$settingnew['maxthumbwidth'] = 0;
		$settingnew['maxthumbheight'] = 0;
	}
	$settingnew['portalarticleimgthumbclosed'] = intval($settingnew['portalarticleimgthumbclosed']) ? '0' : 1;
	$settingnew['portalarticleimgthumbwidth'] = intval($settingnew['portalarticleimgthumbwidth']);
	$settingnew['portalarticleimgthumbheight'] = intval($settingnew['portalarticleimgthumbheight']);

	if(isset($settingnew['thumbdisabledmobile'])) {
		$settingnew['thumbdisabledmobile'] = !$settingnew['thumbdisabledmobile'] ? 1 : 0;
	}
} else {
	shownav('global', 'setting_'.$operation);

	$_GET['anchor'] = in_array($_GET['anchor'], ['basic', 'forumattach', 'remote', 'albumattach', 'portalarticle']) ? $_GET['anchor'] : 'basic';
	showsubmenuanchors('setting_attach', [
		['setting_attach_basic', 'basic', $_GET['anchor'] == 'basic'],
		$isfounder ? ['setting_attach_remote', 'remote', $_GET['anchor'] == 'remote'] : '',
		['setting_attach_forumattach', 'forumattach', $_GET['anchor'] == 'forumattach'],
		['setting_attach_album', 'albumattach', $_GET['anchor'] == 'albumattach'],
		['setting_attach_portal_article_attach', 'portalarticle', $_GET['anchor'] == 'portalarticle'],
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	/*search={"setting_attach":"action=setting&operation=attach","setting_attach_basic":"action=setting&operation=attach&anchor=basic"}*/
	showtableheader('', '', 'id="basic"'.($_GET['anchor'] != 'basic' ? ' style="display: none"' : ''));
	showsetting('setting_attach_basic_dir', 'settingnew[attachdir]', $setting['attachdir'], 'text');
	showsetting('setting_attach_basic_url', 'settingnew[attachurl]', $setting['attachurl'], 'text');
	showsetting('setting_attach_image_lib', ['settingnew[imagelib]', [
		[0, $lang['setting_attach_image_watermarktype_GD'], ['imagelibext' => 'none']],
		[1, $lang['setting_attach_image_watermarktype_IM'], ['imagelibext' => '']]
	]], $setting['imagelib'], 'mradio');
	showsetting('setting_attach_image_gdlimit', 'settingnew[gdlimit]', $setting['gdlimit'], 'text');
	showsetting('setting_attach_image_thumbquality', 'settingnew[thumbquality]', $setting['thumbquality'], 'text');
	showsetting('setting_attach_image_disabledmobile', 'settingnew[thumbdisabledmobile]', !$setting['thumbdisabledmobile'], 'radio');
	showsetting('setting_attach_image_preview', '', '', cplang('setting_attach_image_thumb_preview_btn'));
	showtagfooter('tbody');
	showsubmit('settingsubmit');
	showtablefooter();
	/*search*/

	/*search={"setting_attach":"action=setting&operation=attach","setting_attach_forumattach":"action=setting&operation=attach&anchor=forumattach"}*/
	showtableheader('', '', 'id="forumattach"'.($_GET['anchor'] != 'forumattach' ? ' style="display: none"' : ''));
	showsetting('setting_attach_basic_imgpost', 'settingnew[attachimgpost]', $setting['attachimgpost'], 'radio');
	showsetting('setting_attach_basic_allowattachurl', 'settingnew[allowattachurl]', $setting['allowattachurl'], 'radio');
	showsetting('setting_attach_image_thumbstatus', ['settingnew[thumbstatus]', [
		['', $lang['setting_attach_image_thumbstatus_none'], ['thumbext' => 'none']],
		['fixnone', $lang['setting_attach_image_thumbstatus_fixnone'], ['thumbext' => '']],
		['fixwr', $lang['setting_attach_image_thumbstatus_fixwr'], ['thumbext' => '']],
	]], $setting['thumbstatus'], 'mradio');
	showtagheader('tbody', 'thumbext', $setting['thumbstatus'], 'sub');
	showsetting('setting_attach_image_thumbwidthheight', ['settingnew[thumbwidth]', 'settingnew[thumbheight]'], [intval($setting['thumbwidth']), intval($setting['thumbheight'])], 'multiply');
	showtagfooter('tbody');
	showsetting('setting_attach_basic_thumbsource', 'settingnew[thumbsource]', $setting['thumbsource'], 'radio', 0, 1);
	showsetting('setting_attach_image_thumbsourcewidthheight', ['settingnew[sourcewidth]', 'settingnew[sourceheight]'], [intval($setting['sourcewidth']), intval($setting['sourceheight'])], 'multiply');
	showtagfooter('tbody');
	showsetting('setting_attach_antileech_expire', 'settingnew[attachexpire]', $setting['attachexpire'], 'text');
	showsetting('setting_attach_antileech_refcheck', 'settingnew[attachrefcheck]', $setting['attachrefcheck'], 'radio');
	showtagfooter('tbody');
	/*search*/

	showsubmit('settingsubmit');
	showtablefooter();

	if($isfounder) {

		$setting['ftp'] = dunserialize($setting['ftp']);
		$setting['ftp'] = is_array($setting['ftp']) ? $setting['ftp'] : [];
		$setting['ftp']['password'] = authcode($setting['ftp']['password'], 'DECODE', md5($_G['config']['security']['authkey']));
		$setting['ftp']['password'] = $setting['ftp']['password'] ? $setting['ftp']['password'][0].'********'.$setting['ftp']['password'][strlen($setting['ftp']['password']) - 1] : '';
		$setting['oss'] = dunserialize($setting['oss']);
		$setting['oss'] = is_array($setting['oss']) ? $setting['oss'] : [];
		$setting['oss']['oss_key'] = authcode($setting['oss']['oss_key'], 'DECODE', md5($_G['config']['security']['authkey']));
		$setting['oss']['oss_key'] = $setting['oss']['oss_key'] ? $setting['oss']['oss_key'][0].'********'.$setting['oss']['oss_key'][strlen($setting['oss']['oss_key']) - 1] : '';

		require_once libfile('function/cache');

		$ossBasePath = DISCUZ_ROOT.'source/class/oss/';
		$dir = opendir($ossBasePath);
		$oss_type = $oss_langs = [];
		if(is_dir($ossBasePath)) {
			while($entry = readdir($dir)) {
				if(file_exists($f = $ossBasePath.$entry.'/init.php')) {
					require_once $f;
					$c = 'doss_'.$entry;
					if(!class_exists($c) || !defined($c.'::Name') || !defined($c.'::Desc')) {
						continue;
					}
					$oss_langs[$entry] = [
						'title' => cplang($c::Name),
						'desc' => cplang($c::Desc),
					];
				}
			}
		}

		if(!empty($_G['setting']['plugins']['available'])) {
			foreach($_G['setting']['plugins']['available'] as $pluginid) {
				$ossBasePath = DISCUZ_PLUGIN($pluginid).'/oss/';
				if(file_exists($f = $ossBasePath.'/init.php')) {
					require_once $f;
					$c = 'oss_plugin_'.$pluginid;

					if(!class_exists($c) || ((!defined($c.'::Name') || !defined($c.'::Desc')) && !defined($c.'::SubType'))) {
						continue;
					}
					if(defined($c.'::Name')) {
						$oss_langs['plugin:'.$pluginid] = [
							'title' => lang('plugin/'.$pluginid, $c::Name),
							'desc' => lang('plugin/'.$pluginid, $c::Desc),
						];
					} else {
						foreach($c::SubType as $key => $ossItem) {
							$oss_langs['plugin:'.$pluginid.'_sub'.$key] = [
								'title' => lang('plugin/'.$pluginid, $ossItem['name']),
								'desc' => lang('plugin/'.$pluginid, $ossItem['desc']),
							];
						}
					}

				}
			}
		}
		$typeDesc = '<br />';

		!empty($setting['oss']['oss_subtype']) && $setting['oss']['oss_type'] = $setting['oss']['oss_type'].'_sub'.$setting['oss']['oss_subtype'];

		foreach($oss_langs as $type => $oss_lang) {
			$desc = [];
			foreach($oss_langs as $type1 => $oss_lang1) {
				$desc['ossext_'.$type1] = $type1 == $type ? '' : 'none';
			}
			$oss_type[] = [$type, $oss_lang['title'], $desc];
			$typeDesc .= '<div id="ossext_'.$type.'" style="display:'.($setting['oss']['oss_type'] == $type ? '' : 'none').'">'.
				$oss_lang['desc'].
				'</div>';
		}

		/*search={"setting_attach":"action=setting&operation=attach","setting_attach_remote":"action=setting&operation=attach&anchor=remote"}*/
		showtableheader('', '', 'id="remote"'.($_GET['anchor'] != 'remote' ? ' style="display: none"' : ''));
		showsetting('setting_attach_remote_enabled', ['settingnew[ftp][on]', [
			$oss_langs ? [2, $lang['setting_attach_remote_cos'], ['ossext' => '', 'ftpext' => 'none', 'ftpcheckbutton' => '']] : null,
			[1, $lang['setting_attach_remote_ftp'], ['ossext' => 'none', 'ftpext' => '', 'ftpcheckbutton' => '']],
			[0, $lang['no'], ['ossext' => 'none', 'ftpext' => 'none', 'ftpcheckbutton' => 'none']]
		], TRUE], $setting['ftp']['on'], 'mradio');
		showtagheader('tbody', 'ftpext', $setting['ftp']['on'] == 1, 'sub');
		showsetting('setting_attach_remote_enabled_ssl', 'settingnew[ftp][ssl]', $setting['ftp']['ssl'], 'radio');
		showsetting('setting_attach_remote_ftp_host', 'settingnew[ftp][host]', $setting['ftp']['host'], 'text');
		showsetting('setting_attach_remote_ftp_port', 'settingnew[ftp][port]', $setting['ftp']['port'], 'text');
		showsetting('setting_attach_remote_ftp_user', 'settingnew[ftp][username]', $setting['ftp']['username'], 'text');
		showsetting('setting_attach_remote_ftp_pass', 'settingnew[ftp][password]', $setting['ftp']['password'], 'text');
		showsetting('setting_attach_remote_ftp_pasv', 'settingnew[ftp][pasv]', $setting['ftp']['pasv'], 'radio');
		showsetting('setting_attach_remote_dir', 'settingnew[ftp][attachdir]', $setting['ftp']['attachdir'], 'text');
		showsetting('setting_attach_remote_url', 'settingnew[ftp][attachurl]', $setting['ftp']['attachurl'], 'text');
		showsetting('setting_attach_remote_timeout', 'settingnew[ftp][timeout]', $setting['ftp']['timeout'], 'text');
		showtagfooter('tbody');

		showtagheader('tbody', 'ossext', $setting['ftp']['on'] == 2, 'sub');
		showsetting('setting_attach_remote_oss_type', ['settingnew[oss][oss_type]', $oss_type], $setting['oss']['oss_type'] ?? '', 'mradio', '', 0, $lang['setting_attach_remote_oss_type_comment'].$typeDesc);
		showsetting('setting_attach_remote_oss_id', 'settingnew[oss][oss_id]', $setting['oss']['oss_id'], 'text');
		showsetting('setting_attach_remote_oss_key', 'settingnew[oss][oss_key]', $setting['oss']['oss_key'], 'text');
		showsetting('setting_attach_remote_oss_bucket', 'settingnew[oss][oss_bucket]', $setting['oss']['oss_bucket'], 'text');
		showsetting('setting_attach_remote_oss_endpoint', 'settingnew[oss][oss_endpoint]', $setting['oss']['oss_endpoint'], 'text');
		showsetting('setting_attach_remote_oss_bucket_url', 'settingnew[oss][oss_bucket_url]', $setting['oss']['oss_bucket_url'], 'text');
		showsetting('setting_attach_remote_oss_url', 'settingnew[oss][oss_url]', $setting['oss']['oss_url'], 'text');
		showsetting('setting_attach_remote_oss_rootpath', 'settingnew[oss][oss_rootpath]', $setting['oss']['oss_rootpath'], 'text');
		showsetting('setting_attach_remote_oss_avatar', 'settingnew[oss][oss_avatar]', $setting['oss']['oss_avatar'], 'radio');
		showtagfooter('tbody');
		showtagheader('tbody', 'ftpcheckbutton', $setting['ftp']['on'], 'sub');
		showsetting('setting_attach_remote_preview', '', '', cplang('setting_attach_remote_preview_btn'));
		showtagfooter('tbody');

		showsetting('setting_attach_remote_allowedexts', 'settingnew[ftp][allowedexts]', $setting['ftp']['allowedexts'], 'textarea');
		showsetting('setting_attach_remote_disallowedexts', 'settingnew[ftp][disallowedexts]', $setting['ftp']['disallowedexts'], 'textarea');
		showcomponent('setting_attach_remote_minsize', 'settingnew[ftp][minsize]', $setting['ftp']['minsize'], 'component_size');
		showsetting('setting_attach_antileech_remote_hide_dir', 'settingnew[ftp][hideurl]', $setting['ftp']['hideurl'], 'radio');

		showsubmit('settingsubmit');
		showtablefooter();
		/*search*/
	}

	/*search={"setting_attach":"action=setting&operation=attach","setting_attach_album":"action=setting&operation=attach&anchor=albumattach"}*/
	showtableheader('', '', 'id="albumattach"'.($_GET['anchor'] != 'albumattach' ? ' style="display: none"' : ''));
	showsetting('setting_attach_album_maxtimage', ['settingnew[maxthumbwidth]', 'settingnew[maxthumbheight]'], [intval($setting['maxthumbwidth']), intval($setting['maxthumbheight'])], 'multiply');
	showsubmit('settingsubmit');
	showtablefooter();
	/*search*/

	/*search={"setting_attach":"action=setting&operation=attach","setting_attach_portal_article_attach":"action=setting&operation=attach&anchor=portalarticle"}*/
	showtableheader('', '', 'id="portalarticle"'.($_GET['anchor'] != 'portalarticle' ? ' style="display: none"' : ''));
	showsetting('setting_attach_portal_article_img_thumb_closed', 'settingnew[portalarticleimgthumbclosed]', !$setting['portalarticleimgthumbclosed'], 'radio');
	showsetting('setting_attach_portal_article_imgsize', ['settingnew[portalarticleimgthumbwidth]', 'settingnew[portalarticleimgthumbheight]'], [intval($setting['portalarticleimgthumbwidth']), intval($setting['portalarticleimgthumbheight'])], 'multiply');
	showsubmit('settingsubmit');
	showtablefooter();
	/*search*/

	showformfooter();
}