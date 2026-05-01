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
	if(isset($settingnew['watermarktext']['portal'])) {
		watermarkinit('portal');
	}
	if(isset($settingnew['watermarktext']['forum'])) {
		watermarkinit('forum');
	}
	if(isset($settingnew['watermarktext']['album'])) {
		watermarkinit('album');
	}
	foreach(['portal', 'forum', 'album'] as $imgwatertype) {
		if($settingnew['watermarkstatus'][$imgwatertype]) {
			$settingnew['watermarktrans'][$imgwatertype] = intval($settingnew['watermarktrans'][$imgwatertype]);
			$settingnew['watermarkquality'][$imgwatertype] = intval($settingnew['watermarkquality'][$imgwatertype]);
			if(!$settingnew['watermarktrans'][$imgwatertype]) {
				$settingnew['watermarktrans'][$imgwatertype] = 50;
			}
			if(!$settingnew['watermarkquality'][$imgwatertype]) {
				$settingnew['watermarkquality'][$imgwatertype] = 85;
			}
		}
		if($settingnew['watermarktype'][$imgwatertype] == 'text') {
			$settingnew['watermarktext']['angle'][$imgwatertype] = intval($settingnew['watermarktext']['angle'][$imgwatertype]);
			$settingnew['watermarktext']['shadowx'][$imgwatertype] = intval($settingnew['watermarktext']['shadowx'][$imgwatertype]);
			$settingnew['watermarktext']['shadowy'][$imgwatertype] = intval($settingnew['watermarktext']['shadowy'][$imgwatertype]);
			$settingnew['watermarktext']['translatex'][$imgwatertype] = intval($settingnew['watermarktext']['translatex'][$imgwatertype]);
			$settingnew['watermarktext']['translatey'][$imgwatertype] = intval($settingnew['watermarktext']['translatey'][$imgwatertype]);
			$settingnew['watermarktext']['skewx'][$imgwatertype] = intval($settingnew['watermarktext']['skewx'][$imgwatertype]);
			$settingnew['watermarktext']['skewy'][$imgwatertype] = intval($settingnew['watermarktext']['skewy'][$imgwatertype]);
		}
	}
	if(!empty($_FILES['watermarkimg']['size'])) {
		if($_FILES['watermarkimg']['error'] > 0) {
			cpmsg('setting_imgwater_upload_error', '', 'error');
		}
		$extension = pathinfo($_FILES['watermarkimg']['name'], PATHINFO_EXTENSION);

		if(!in_array(strtolower($extension), ['png', 'gif'])) {
			cpmsg('setting_imgwater_upload_ext_error', '', 'error');
		}
		copy($_FILES['watermarkimg']['tmp_name'], DISCUZ_DATA.'./watermark.'.$extension);
	}
} else {
	shownav('global', 'setting_'.$operation);

	$_GET['anchor'] = in_array($_GET['anchor'], ['portal', 'forum', 'album']) ? $_GET['anchor'] : 'portal';
	showsubmenuanchors('setting_imgwater', [
		['setting_imgwater_portal', 'portal', $_GET['anchor'] == 'portal'],
		['setting_imgwater_forum', 'forum', $_GET['anchor'] == 'forum'],
		['setting_imgwater_album', 'album', $_GET['anchor'] == 'album'],
		['setting_imgwater_upload', 'upload', $_GET['anchor'] == 'upload'],
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	$setting['watermarktext'] = (array)dunserialize($setting['watermarktext']);
	$setting['watermarkstatus'] = (array)dunserialize($setting['watermarkstatus']);
	$setting['watermarktype'] = (array)dunserialize($setting['watermarktype']);
	$setting['watermarktrans'] = (array)dunserialize($setting['watermarktrans']);
	$setting['watermarkquality'] = (array)dunserialize($setting['watermarkquality']);
	$setting['watermarkminheight'] = (array)dunserialize($setting['watermarkminheight']);
	$setting['watermarkminwidth'] = (array)dunserialize($setting['watermarkminwidth']);
	$setting['watermarktext']['fontpath'] = str_replace(['ch/', 'en/'], '', $setting['watermarktext']['fontpath']);

	$fontlist = [];
	$dir = opendir(DISCUZ_ROOT.'./source/data/seccode/font/en');
	while($entry = readdir($dir)) {
		if(in_array(strtolower(fileext($entry)), ['ttf', 'ttc'])) {
			$fontlist['portal'] .= '<option value="'.$entry.'"'.($entry == $setting['watermarktext']['fontpath']['portal'] ? ' selected>' : '>').$entry.'</option>';
			$fontlist['forum'] .= '<option value="'.$entry.'"'.($entry == $setting['watermarktext']['fontpath']['forum'] ? ' selected>' : '>').$entry.'</option>';
			$fontlist['album'] .= '<option value="'.$entry.'"'.($entry == $setting['watermarktext']['fontpath']['album'] ? ' selected>' : '>').$entry.'</option>';
		}
	}
	$dir = opendir(DISCUZ_ROOT.'./source/data/seccode/font/ch');
	while($entry = readdir($dir)) {
		if(in_array(strtolower(fileext($entry)), ['ttf', 'ttc'])) {
			$fontlist['portal'] .= '<option value="'.$entry.'"'.($entry == $setting['watermarktext']['fontpath']['portal'] ? ' selected>' : '>').$entry.'</option>';
			$fontlist['forum'] .= '<option value="'.$entry.'"'.($entry == $setting['watermarktext']['fontpath']['forum'] ? ' selected>' : '>').$entry.'</option>';
			$fontlist['album'] .= '<option value="'.$entry.'"'.($entry == $setting['watermarktext']['fontpath']['album'] ? ' selected>' : '>').$entry.'</option>';
		}
	}
	$fontlist['portal'] .= '</select>';
	$fontlist['forum'] .= '</select>';
	$fontlist['album'] .= '</select>';
	$checkwm['portal'] = [$setting['watermarkstatus']['portal'] => 'checked'];
	$checkwm['forum'] = [$setting['watermarkstatus']['forum'] => 'checked'];
	$checkwm['album'] = [$setting['watermarkstatus']['album'] => 'checked'];
	/*search={"setting_imgwater":"action=setting&operation=imgwater","setting_imgwater_portal":"action=setting&operation=imgwater&anchor=portal"}*/
	showtableheader('setting_imgwater_image_watermarks_portal', 'nobottom', 'id="portal"'.($_GET['anchor'] != 'portal' ? ' style="display: none"' : ''));
	$fontlist['portal'] = '<select name="settingnew[watermarktext][fontpath][portal]">'.$fontlist['portal'];
	showhiddenfields(['imagelib' => $_G['setting']['imagelib']]);
	showsetting('setting_imgwater_image_watermarkstatus', '', '', '<table style="margin-bottom: 3px; margin-top:3px;"><tr><td colspan="3"><input class="radio" type="radio" name="settingnew[watermarkstatus][portal]" value="0" '.$checkwm['portal'][0].'>'.$lang['setting_imgwater_image_watermarkstatus_none'].'</td></tr><tr><td><input class="radio" type="radio" name="settingnew[watermarkstatus][portal]" value="1" '.$checkwm['portal'][1].'> #1</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][portal]" value="2" '.$checkwm['portal'][2].'> #2</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][portal]" value="3" '.$checkwm['portal'][3].'> #3</td></tr><tr><td><input class="radio" type="radio" name="settingnew[watermarkstatus][portal]" value="4" '.$checkwm['portal'][4].'> #4</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][portal]" value="5" '.$checkwm['portal'][5].'> #5</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][portal]" value="6" '.$checkwm['portal'][6].'> #6</td></tr><tr><td><input class="radio" type="radio" name="settingnew[watermarkstatus][portal]" value="7" '.$checkwm['portal'][7].'> #7</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][portal]" value="8" '.$checkwm['portal'][8].'> #8</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][portal]" value="9" '.$checkwm['portal'][9].'> #9</td></tr></table>');
	showsetting('setting_imgwater_image_watermarkminwidthheight', ['settingnew[watermarkminwidth][portal]', 'settingnew[watermarkminheight][portal]'], [intval($setting['watermarkminwidth']['portal']), intval($setting['watermarkminheight']['portal'])], 'multiply');
	showsetting('setting_imgwater_image_watermarktype', ['settingnew[watermarktype][portal]', [
		['gif', $lang['setting_imgwater_image_watermarktype_gif'], ['watermarktypeext_portal' => 'none']],
		['png', $lang['setting_imgwater_image_watermarktype_png'], ['watermarktypeext_portal' => 'none']],
		['text', $lang['setting_imgwater_image_watermarktype_text'], ['watermarktypeext_portal' => '']]
	]], $setting['watermarktype']['portal'], 'mradio');
	showsetting('setting_imgwater_image_watermarktrans', 'settingnew[watermarktrans][portal]', $setting['watermarktrans']['portal'], 'text');
	showsetting('setting_imgwater_image_watermarkquality', 'settingnew[watermarkquality][portal]', $setting['watermarkquality']['portal'], 'text');
	showtagheader('tbody', 'watermarktypeext_portal', $setting['watermarktype']['portal'] == 'text', 'sub');
	showsetting('setting_imgwater_image_watermarktext_text', 'settingnew[watermarktext][text][portal]', $setting['watermarktext']['text']['portal'], 'textarea');
	showsetting('setting_imgwater_image_watermarktext_fontpath', '', '', $fontlist['portal']);
	showsetting('setting_imgwater_image_watermarktext_size', 'settingnew[watermarktext][size][portal]', $setting['watermarktext']['size']['portal'], 'text');
	showsetting('setting_imgwater_image_watermarktext_angle', 'settingnew[watermarktext][angle][portal]', $setting['watermarktext']['angle']['portal'], 'text');
	showsetting('setting_imgwater_image_watermarktext_color', 'settingnew[watermarktext][color][portal]', $setting['watermarktext']['color']['portal'], 'color');
	showsetting('setting_imgwater_image_watermarktext_shadowx', 'settingnew[watermarktext][shadowx][portal]', $setting['watermarktext']['shadowx']['portal'], 'text');
	showsetting('setting_imgwater_image_watermarktext_shadowy', 'settingnew[watermarktext][shadowy][portal]', $setting['watermarktext']['shadowy']['portal'], 'text');
	showsetting('setting_imgwater_image_watermarktext_shadowcolor', 'settingnew[watermarktext][shadowcolor][portal]', $setting['watermarktext']['shadowcolor']['portal'], 'color');
	showsetting('setting_imgwater_image_watermarktext_imtranslatex', 'settingnew[watermarktext][translatex][portal]', $setting['watermarktext']['translatex']['portal'], 'text');
	showsetting('setting_imgwater_image_watermarktext_imtranslatey', 'settingnew[watermarktext][translatey][portal]', $setting['watermarktext']['translatey']['portal'], 'text');
	showsetting('setting_imgwater_image_watermarktext_imskewx', 'settingnew[watermarktext][skewx][portal]', $setting['watermarktext']['skewx']['portal'], 'text');
	showsetting('setting_imgwater_image_watermarktext_imskewy', 'settingnew[watermarktext][skewy][portal]', $setting['watermarktext']['skewy']['portal'], 'text');
	showtagfooter('tbody');
	showsetting('setting_imgwater_preview', '', '', cplang('setting_imgwater_preview_portal'));
	showtablefooter();
	/*search*/

	/*search={"setting_imgwater":"action=setting&operation=imgwater","setting_imgwater_forum":"action=setting&operation=imgwater&anchor=forum"}*/
	showtableheader('setting_imgwater_image_watermarks_forum', 'nobottom', 'id="forum"'.($_GET['anchor'] != 'forum' ? ' style="display: none"' : ''));
	$fontlist['forum'] = '<select name="settingnew[watermarktext][fontpath][forum]">'.$fontlist['forum'];
	showsetting('setting_imgwater_image_watermarkstatus', '', '', '<table style="margin-bottom: 3px; margin-top:3px;"><tr><td colspan="3"><input class="radio" type="radio" name="settingnew[watermarkstatus][forum]" value="0" '.$checkwm['forum'][0].'>'.$lang['setting_imgwater_image_watermarkstatus_none'].'</td></tr><tr><td><input class="radio" type="radio" name="settingnew[watermarkstatus][forum]" value="1" '.$checkwm['forum'][1].'> #1</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][forum]" value="2" '.$checkwm['forum'][2].'> #2</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][forum]" value="3" '.$checkwm['forum'][3].'> #3</td></tr><tr><td><input class="radio" type="radio" name="settingnew[watermarkstatus][forum]" value="4" '.$checkwm['forum'][4].'> #4</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][forum]" value="5" '.$checkwm['forum'][5].'> #5</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][forum]" value="6" '.$checkwm['forum'][6].'> #6</td></tr><tr><td><input class="radio" type="radio" name="settingnew[watermarkstatus][forum]" value="7" '.$checkwm['forum'][7].'> #7</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][forum]" value="8" '.$checkwm['forum'][8].'> #8</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][forum]" value="9" '.$checkwm['forum'][9].'> #9</td></tr></table>');
	showsetting('setting_imgwater_image_watermarkminwidthheight', ['settingnew[watermarkminwidth][forum]', 'settingnew[watermarkminheight][forum]'], [intval($setting['watermarkminwidth']['forum']), intval($setting['watermarkminheight']['forum'])], 'multiply');
	showsetting('setting_imgwater_image_watermarktype', ['settingnew[watermarktype][forum]', [
		['gif', $lang['setting_imgwater_image_watermarktype_gif'], ['watermarktypeext_forum' => 'none']],
		['png', $lang['setting_imgwater_image_watermarktype_png'], ['watermarktypeext_forum' => 'none']],
		['text', $lang['setting_imgwater_image_watermarktype_text'], ['watermarktypeext_forum' => '']]
	]], $setting['watermarktype']['forum'], 'mradio');
	showsetting('setting_imgwater_image_watermarktrans', 'settingnew[watermarktrans][forum]', $setting['watermarktrans']['forum'], 'text');
	showsetting('setting_imgwater_image_watermarkquality', 'settingnew[watermarkquality][forum]', $setting['watermarkquality']['forum'], 'text');
	showtagheader('tbody', 'watermarktypeext_forum', $setting['watermarktype']['forum'] == 'text', 'sub');
	showsetting('setting_imgwater_image_watermarktext_text', 'settingnew[watermarktext][text][forum]', $setting['watermarktext']['text']['forum'], 'textarea');
	showsetting('setting_imgwater_image_watermarktext_fontpath', '', '', $fontlist['forum']);
	showsetting('setting_imgwater_image_watermarktext_size', 'settingnew[watermarktext][size][forum]', $setting['watermarktext']['size']['forum'], 'text');
	showsetting('setting_imgwater_image_watermarktext_angle', 'settingnew[watermarktext][angle][forum]', $setting['watermarktext']['angle']['forum'], 'text');
	showsetting('setting_imgwater_image_watermarktext_color', 'settingnew[watermarktext][color][forum]', $setting['watermarktext']['color']['forum'], 'color');
	showsetting('setting_imgwater_image_watermarktext_shadowx', 'settingnew[watermarktext][shadowx][forum]', $setting['watermarktext']['shadowx']['forum'], 'text');
	showsetting('setting_imgwater_image_watermarktext_shadowy', 'settingnew[watermarktext][shadowy][forum]', $setting['watermarktext']['shadowy']['forum'], 'text');
	showsetting('setting_imgwater_image_watermarktext_shadowcolor', 'settingnew[watermarktext][shadowcolor][forum]', $setting['watermarktext']['shadowcolor']['forum'], 'color');
	showsetting('setting_imgwater_image_watermarktext_imtranslatex', 'settingnew[watermarktext][translatex][forum]', $setting['watermarktext']['translatex']['forum'], 'text');
	showsetting('setting_imgwater_image_watermarktext_imtranslatey', 'settingnew[watermarktext][translatey][forum]', $setting['watermarktext']['translatey']['forum'], 'text');
	showsetting('setting_imgwater_image_watermarktext_imskewx', 'settingnew[watermarktext][skewx][forum]', $setting['watermarktext']['skewx']['forum'], 'text');
	showsetting('setting_imgwater_image_watermarktext_imskewy', 'settingnew[watermarktext][skewy][forum]', $setting['watermarktext']['skewy']['forum'], 'text');
	showtagfooter('tbody');
	showsetting('setting_imgwater_preview', '', '', cplang('setting_imgwater_preview_forum'));
	showtablefooter();
	/*search*/

	/*search={"setting_imgwater":"action=setting&operation=imgwater","setting_imgwater_album":"action=setting&operation=imgwater&anchor=album"}*/
	showtableheader('setting_imgwater_image_watermarks_album', 'nobottom', 'id="album"'.($_GET['anchor'] != 'album' ? ' style="display: none"' : ''));
	$fontlist['album'] = '<select name="settingnew[watermarktext][fontpath][album]">'.$fontlist['album'];
	showsetting('setting_imgwater_image_watermarkstatus', '', '', '<table style="margin-bottom: 3px; margin-top:3px;"><tr><td colspan="3"><input class="radio" type="radio" name="settingnew[watermarkstatus][album]" value="0" '.$checkwm['album'][0].'>'.$lang['setting_imgwater_image_watermarkstatus_none'].'</td></tr><tr><td><input class="radio" type="radio" name="settingnew[watermarkstatus][album]" value="1" '.$checkwm['album'][1].'> #1</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][album]" value="2" '.$checkwm['album'][2].'> #2</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][album]" value="3" '.$checkwm['album'][3].'> #3</td></tr><tr><td><input class="radio" type="radio" name="settingnew[watermarkstatus][album]" value="4" '.$checkwm['album'][4].'> #4</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][album]" value="5" '.$checkwm['album'][5].'> #5</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][album]" value="6" '.$checkwm['album'][6].'> #6</td></tr><tr><td><input class="radio" type="radio" name="settingnew[watermarkstatus][album]" value="7" '.$checkwm['album'][7].'> #7</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][album]" value="8" '.$checkwm['album'][8].'> #8</td><td><input class="radio" type="radio" name="settingnew[watermarkstatus][album]" value="9" '.$checkwm['album'][9].'> #9</td></tr></table>');
	showsetting('setting_imgwater_image_watermarkminwidthheight', ['settingnew[watermarkminwidth][album]', 'settingnew[watermarkminheight][album]'], [intval($setting['watermarkminwidth']['album']), intval($setting['watermarkminheight']['album'])], 'multiply');
	showsetting('setting_imgwater_image_watermarktype', ['settingnew[watermarktype][album]', [
		['gif', $lang['setting_imgwater_image_watermarktype_gif'], ['watermarktypeext_album' => 'none']],
		['png', $lang['setting_imgwater_image_watermarktype_png'], ['watermarktypeext_album' => 'none']],
		['text', $lang['setting_imgwater_image_watermarktype_text'], ['watermarktypeext_album' => '']]
	]], $setting['watermarktype']['album'], 'mradio');
	showsetting('setting_imgwater_image_watermarktrans', 'settingnew[watermarktrans][album]', $setting['watermarktrans']['album'], 'text');
	showsetting('setting_imgwater_image_watermarkquality', 'settingnew[watermarkquality][album]', $setting['watermarkquality']['album'], 'text');
	showtagheader('tbody', 'watermarktypeext_album', $setting['watermarktype']['album'] == 'text', 'sub');
	showsetting('setting_imgwater_image_watermarktext_text', 'settingnew[watermarktext][text][album]', $setting['watermarktext']['text']['album'], 'textarea');
	showsetting('setting_imgwater_image_watermarktext_fontpath', '', '', $fontlist['album']);
	showsetting('setting_imgwater_image_watermarktext_size', 'settingnew[watermarktext][size][album]', $setting['watermarktext']['size']['album'], 'text');
	showsetting('setting_imgwater_image_watermarktext_angle', 'settingnew[watermarktext][angle][album]', $setting['watermarktext']['angle']['album'], 'text');
	showsetting('setting_imgwater_image_watermarktext_color', 'settingnew[watermarktext][color][album]', $setting['watermarktext']['color']['album'], 'color');
	showsetting('setting_imgwater_image_watermarktext_shadowx', 'settingnew[watermarktext][shadowx][album]', $setting['watermarktext']['shadowx']['album'], 'text');
	showsetting('setting_imgwater_image_watermarktext_shadowy', 'settingnew[watermarktext][shadowy][album]', $setting['watermarktext']['shadowy']['album'], 'text');
	showsetting('setting_imgwater_image_watermarktext_shadowcolor', 'settingnew[watermarktext][shadowcolor][album]', $setting['watermarktext']['shadowcolor']['album'], 'color');
	showsetting('setting_imgwater_image_watermarktext_imtranslatex', 'settingnew[watermarktext][translatex][album]', $setting['watermarktext']['translatex']['album'], 'text');
	showsetting('setting_imgwater_image_watermarktext_imtranslatey', 'settingnew[watermarktext][translatey][album]', $setting['watermarktext']['translatey']['album'], 'text');
	showsetting('setting_imgwater_image_watermarktext_imskewx', 'settingnew[watermarktext][skewx][album]', $setting['watermarktext']['skewx']['album'], 'text');
	showsetting('setting_imgwater_image_watermarktext_imskewy', 'settingnew[watermarktext][skewy][album]', $setting['watermarktext']['skewy']['album'], 'text');
	showtagfooter('tbody');
	showsetting('setting_imgwater_preview', '', '', cplang('setting_imgwater_preview_album'));
	showtablefooter();
	/*search*/

	/*search={"setting_imgwater":"action=setting&operation=imgwater","setting_imgwater_upload":"action=setting&operation=imgwater&anchor=upload"}*/
	showtableheader('setting_imgwater_upload', 'nobottom', 'id="upload"'.($_GET['anchor'] != 'upload' ? ' style="display: none"' : ''));
	showsetting('setting_imgwater_upload_title', 'watermarkimg', '', 'file', '', 0, cplang('setting_imgwater_upload_comment'));
	showtablefooter();
	/*search*/
	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}