<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

function show_user_bar() {
	global $_G;

	showsubmenu('home_welcome', [], '', ['bbname' => $_G['setting']['bbname']]);
}

function show_todo() {
	global $_G;

	$membersmod = table_common_member_validate::t()->count_by_status(0);
	$threadsdel = $_G['setting']['forumstatus'] ? table_forum_thread::t()->count_by_displayorder(-1) : 0;
	$groupmod = $_G['setting']['forumstatus'] ? table_forum_forum::t()->validate_level_num() : 0;
	$reportcount = table_common_report::t()->fetch_count();

	$modcount = [];
	foreach(table_common_moderate::t()->count_group_idtype_by_status(0) as $value) {
		$modcount[$value['idtype']] = $value['count'];
	}

	$medalsmod = $_G['setting']['medalstatus'] ? table_forum_medallog::t()->count_by_type(2) : 0;
	$threadsmod = $modcount['tid'];
	$postsmod = $modcount['pid'];
	$blogsmod = $modcount['blogid'];
	$doingsmod = $modcount['doid'];
	$picturesmod = $modcount['picid'];
	$sharesmod = $modcount['sid'];
	$commentsmod = $modcount['uid_cid'] + $modcount['blogid_cid'] + $modcount['sid_cid'] + $modcount['picid_cid'];
	$articlesmod = $modcount['aid'];
	$articlecommentsmod = $modcount['aid_cid'];
	$topiccommentsmod = $modcount['topicid_cid'];
	$verify = [];
	if(!empty($_G['setting']['verify']['enabled'])) {
		foreach(table_common_member_verify_info::t()->group_by_verifytype_count() as $value) {
			if($value['num']) {
				if($value['verifytype']) {
					$verifyinfo = !empty($_G['setting']['verify'][$value['verifytype']]) ? $_G['setting']['verify'][$value['verifytype']] : [];
					if(!empty($verifyinfo['available'])) {
						$verify[$value['verifytype']] = [$verifyinfo['title'], $value['num']];
					}
				} else {
					$verify[0] = [cplang('members_verify_profile'), $value['num']];
				}
			}
		}
	}

	$errcredits = table_common_credit_log::t()->count_by_search(0, 'ERR', TIMESTAMP - 86400 * 7);

	$show = $membersmod || $threadsmod || $postsmod || $medalsmod || $blogsmod || $picturesmod || $doingsmod || $sharesmod || $commentsmod || $articlesmod || $articlecommentsmod || $topiccommentsmod || $reportcount || $threadsdel || !empty($verify) || $errcredits;
	if(!$show) {
		return;
	}

	require_once template('admin/index_todo');
}

function show_edittips() {
	showtips('index_edit_tips');
}

function show_releasetips() {
	global $_G, $reldisp, $newversion, $lang;

	$siteuniqueid = $_G['setting']['siteuniqueid'] ?? table_common_setting::t()->fetch_setting('siteuniqueid');
	if(empty($siteuniqueid) || strlen($siteuniqueid) < 16) {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$siteuniqueid = 'DX'.$chars[date('y') % 60].$chars[date('n')].$chars[date('j')].$chars[date('G')].$chars[date('i')].$chars[date('s')].substr(md5($_G['clientip'].$_G['username'].TIMESTAMP), 0, 4).random(4);
		table_common_setting::t()->update_setting('siteuniqueid', $siteuniqueid);
		require_once libfile('function/cache');
		updatecache('setting');
	}

	if(!empty($_GET['closesitereleasetips'])) {
		table_common_setting::t()->update('sitereleasetips', 0);
		$sitereleasetips = 0;
		require_once libfile('function/cache');
		updatecache('setting');
	} else {
		$sitereleasetips = $_G['setting']['sitereleasetips'] ?? table_common_setting::t()->fetch('sitereleasetips');
	}

	$siterelease = $_G['setting']['siterelease'] ?? table_common_setting::t()->fetch('siterelease');
	$releasehash = substr(hash('sha512', $_G['config']['security']['authkey'].DISCUZ_VERSION.DISCUZ_RELEASE.$siteuniqueid), 0, 32);
	if(empty($siterelease) || strcmp($siterelease, $releasehash) !== 0) {
		table_common_setting::t()->update('siteversion', DISCUZ_VERSION);
		table_common_setting::t()->update('siterelease', $releasehash);
		table_common_setting::t()->update('sitereleasetips', 1);
		$sitereleasetips = 1;
		require_once libfile('function/cloudaddons');
		$newversion = json_decode(cloudaddons_open('&mod=app&ac=upgrade'), true);
		if(!empty($newversion['newversion'])) {
			$newversion['updatetime'] = $_G['timestamp'];
			table_common_setting::t()->update_setting('cloudaddons_newversion', ((CHARSET == 'utf-8') ? $newversion : json_encode($newversion)));
		} else {
			$newversion = [];
		}
		require_once libfile('function/cache');
		updatecache('setting');
	}

	$tips = '';
	if($sitereleasetips) {
		$tips .= lang('admincp', 'version_tips_msg', ['ADMINSCRIPT' => ADMINSCRIPT, 'version' => constant('DISCUZ_VERSION').' '.$reldisp]);
	}

	if(isfounder()) {
		$tips .= !$_G['config']['admincp']['founder'] ? $lang['home_security_nofounder'] : '';
		$tips .= !$_G['config']['admincp']['checkip'] ? $lang['home_security_checkip'] : '';
		$tips .= $_G['config']['admincp']['runquery'] ? $lang['home_security_runquery'] : '';
	}

	if($tips) {
		showtips($tips);
	}
}

function show_onlines() {
	$admincp_session = table_common_admincp_session::t()->fetch_all_by_panel(1);
	if(count($admincp_session) == 1) {
		return;
	}
	$onlines = '';
	$members = table_common_member::t()->fetch_all(array_keys($admincp_session), false, 0);
	foreach($admincp_session as $uid => $online) {
		$onlines .= '<a href="home.php?mod=space&uid='.$online['uid'].'" title="'.dgmdate($online['dateline']).'" target="_blank">'.$members[$uid]['username'].'</a>&nbsp;&nbsp;&nbsp;';
	}
	showboxheader('home_onlines', '', 'id="home_onlines"');
	echo $onlines;
	showboxfooter();
}

function show_note() {
	global $_G;

	showformheader('index');
	showboxheader('home_notes', '', 'id="home_notes"');

	$notemsghtml = '';
	foreach(table_common_adminnote::t()->fetch_all_by_access(0) as $note) {
		if($note['expiration'] < TIMESTAMP) {
			table_common_adminnote::t()->delete_note($note['id']);
		} else {
			$note['adminenc'] = rawurlencode($note['admin']);
			$note['expiration'] = ceil(($note['expiration'] - $note['dateline']) / 86400);
			$note['dateline'] = dgmdate($note['dateline'], 'dt');
			$notemsghtml .= '<div class="dcol"><div class="adminnote">'.'<a'.(isfounder() || $_G['member']['username'] == $note['admin'] ? ' href="'.ADMINSCRIPT.'?action=index&notesubmit=yes&noteid='.$note['id'].'" title="'.cplang('delete').'" class="ndel"' : '').'></a>'.
				("<div><p><span class=\"bold\"><a href=\"home.php?mod=space&username={$note['adminenc']}\" target=\"_blank\">{$note['admin']}</a></span></p><p>{$note['dateline']}</p><p class=\"marginbot\">(".cplang('home_notes_add').cplang('validity').": {$note['expiration']} ".cplang('days').")</p><p>{$note['message']}</p>").'</div></div></div>';
		}
	}

	if($notemsghtml) {
		echo '<div class="drow">'.$notemsghtml.'</div></div><div class="boxbody">';
	}

	showboxrow('style="align-items: center"', ['class="dcol lineheight"', 'class="dcol lineheight"'], [
		cplang('home_notes_add'),
		'<input type="text" class="txt" name="newmessage" value="" style="width:300px;" />'.cplang('validity').': <input type="text" class="txt" name="newexpiration" value="30" style="width:60px;" />'.cplang('days').'&nbsp;<input name="notesubmit" value="'.cplang('submit').'" type="submit" class="btn" />'
	]);
	showboxfooter();
	showformfooter();
}

function show_filecheck() {
	global $lang;

	if(!isfounder()) {
		return;
	}

	$filecheck = table_common_cache::t()->fetch('checktools_filecheck_result');
	if($filecheck) {
		list($modifiedfiles, $deletedfiles, $unknownfiles, $doubt) = dunserialize($filecheck['cachevalue']);
		$filecheckresult = "<div><em class=\"".($modifiedfiles ? 'edited' : 'correct')."\">{$lang['filecheck_modify']}<span class=\"bignum\">$modifiedfiles</span></em>".
			"<em class=\"".($deletedfiles ? 'del' : 'correct')."\">{$lang['filecheck_delete']}<span class=\"bignum\">$deletedfiles</span></em>".
			"<em class=\"unknown\">{$lang['filecheck_unknown']}<span class=\"bignum\">$unknownfiles</span></em>".
			"<em class=\"unknown\">{$lang['filecheck_doubt']}<span class=\"bignum\">$doubt</span></em></div><p>".
			$lang['filecheck_last_homecheck'].': '.dgmdate($filecheck['dateline'], 'u').' <a href="'.ADMINSCRIPT.'?action=checktools&operation=filecheck&step=3">['.$lang['filecheck_view_list'].']</a></p>';
	} else {
		$filecheckresult = '';
	}

	showboxheader($lang['nav_filecheck'].' <a href="javascript:;" onclick="ajaxget(\''.ADMINSCRIPT.'?action=checktools&operation=filecheck&homecheck=yes\', \'filecheck_div\')">['.$lang['filecheck_check_now'].']</a>', 'nobottom fixpadding', 'id="filecheck"');
	echo '<div id="filecheck_div">'.$filecheckresult.'</div>';
	showboxfooter();
	if(TIMESTAMP - $filecheck['dateline'] > 86400 * 7) {
		echo '<script>ajaxget(\''.ADMINSCRIPT.'?action=checktools&operation=filecheck&homecheck=yes\', \'filecheck_div\');</script>';
	}
}

function _getSysLang() {
	$lang = [];
	@include DISCUZ_ROOT.'./source/i18n/'.currentlang().'/lang.php';
	if(!empty($lang['name'])) {
		return $lang['name'];
	}
	return '';
}

function show_sysinfo() {
	global $newversion, $reldisp, $lang, $_G;

	loaducenter();

	$newversion['newversion'] = !empty($newversion['newversion']) ? $newversion['newversion'] : [];
	$reldisp_addon = is_numeric($newversion['newversion']['release']) ? ('Release '.$newversion['newversion']['release']) : $newversion['newversion']['release'];

	showboxheader('home_sys_info', 'listbox', 'id="home_sys_info"');
	showboxrow('', ['class="dcol lineheight d-14"', 'class="dcol lineheight d-1"'], [
		cplang('home_discuz_version'),
		'<i class="dzlogo"></i> '.DISCUZ_VERSION_NAME.' / Discuz! '.DISCUZ_VERSION.DISCUZ_SUBVERSION.' '.$reldisp.
		((strlen(DISCUZ_RELEASE) == 8) ? '' : cplang('home_git_version')).
		(!empty($downlist) ? implode('&#x3001;', $downlist) : '')
	]);

	if(!UC_STANDALONE) {
		showboxrow('', ['class="dcol lineheight d-14"', 'class="dcol lineheight d-1"'], [
			cplang('home_ucclient_version'),
			'<i class="uclogo"></i> UCenter '.UC_CLIENT_VERSION.' Release '.UC_CLIENT_RELEASE
		]);
	}

	require_once DISCUZ_ROOT.'./source/mitframe_version.php';
	showboxrow('', ['class="dcol lineheight d-14"', 'class="dcol lineheight d-1"'], [
		cplang('home_mitframe_version'),
		'<i class="mitframe_gray"></i> '.MITFRAME_VERSION_NAME.' '.MITFRAME_VERSION,
	]);

	showboxrow('', ['class="dcol lineheight d-14"', 'class="dcol lineheight d-1"'], [
		cplang('home_sys_lang'),
		'<i class="i18n_ico"></i> '._getSysLang(),
	]);

	showboxrow('', ['class="dcol lineheight d-14"', 'class="dcol lineheight d-1"'], [
		cplang('home_os'),
		'<i class="sysicon sy '.get_sysicon().'"></i> '.PHP_OS.' / '.php_uname()
	]);
	showboxrow('', ['class="dcol lineheight d-14"', 'class="dcol lineheight d-1"'], [
		cplang('home_serversoftware'),
		'<i class="sysicon '.get_webicon().'"></i> '.$_SERVER['SERVER_SOFTWARE']
	]);
	showboxrow('', ['class="dcol lineheight d-14"', 'class="dcol lineheight d-1"'], [
		cplang('home_environment'),
		'<i class="sysicon sy sys_php"></i> PHP '.PHP_VERSION.(PHP_ZTS ? ' TS' : '').(PHP_DEBUG ? ' DEBUG' : '').' , '.PHP_SAPI
	]);
	showboxrow('', ['class="dcol lineheight d-14"', 'class="dcol lineheight d-1"'], [
		cplang('home_database'),
		'<i class="sysicon sy sys_mysql"></i> MySQL '.helper_dbtool::dbversion().' , '.$_G['mysql_driver']
	]);
	$meminfo = memory('check');
	showboxrow('', ['class="dcol lineheight d-14"', 'class="dcol lineheight d-1"'], [
		cplang('home_memory'),
		'<i class="sysicon sy '.get_memicon().'"></i> '.get_meminfo()
	]);

	if(@ini_get('file_uploads')) {
		require_once libfile('function/upload');
		$fileupload = getmaxupload();
	} else {
		$fileupload = '<font color="red">'.$lang['no'].'</font>';
	}
	showboxrow('', ['class="dcol lineheight d-14"', 'class="dcol lineheight d-1"'], [
		cplang('home_upload_perm'),
		$fileupload
	]);
	if(isset($_GET['dbsize']) && FORMHASH == $_GET['formhash']) {
		$dbsize = helper_dbtool::dbsize();
		$dbsize = $dbsize ? sizecount($dbsize) : $lang['unknown'];
	} else {
		$append = (isset($_GET['attachsize']) ? '&attachsize' : '').(isset($_GET['benchmark']) ? '&benchmark' : '');
		$dbsize = '<a href="'.ADMINSCRIPT.'?action=index&formhash='.FORMHASH.'&dbsize'.$append.'">[ '.$lang['detail'].' ]</a>';
	}
	showboxrow('', ['class="dcol lineheight d-14"', 'class="dcol lineheight d-1"'], [
		cplang('home_database_size'),
		$dbsize
	]);
	if(isset($_GET['attachsize']) && FORMHASH == $_GET['formhash']) {
		$attachsize = table_forum_attachment_n::t()->get_total_filesize();
		$attachsize = is_numeric($attachsize) ? sizecount($attachsize) : $lang['unknown'];
	} else {
		$append = (isset($_GET['dbsize']) ? '&dbsize' : '').(isset($_GET['benchmark']) ? '&benchmark' : '');
		$attachsize = '<a href="'.ADMINSCRIPT.'?action=index&formhash='.FORMHASH.'&attachsize'.$append.'">[ '.$lang['detail'].' ]</a>';
	}
	showboxrow('', ['class="dcol lineheight d-14"', 'class="dcol lineheight d-1"'], [
		cplang('home_attach_size'),
		$attachsize
	]);
	$advice = $msg = '';
	if(function_exists('opcache_get_status')) {
		$msg = 'OPcache: On';
		$value = opcache_get_status();
		if(!empty($value['jit']['enabled'])) {
			$msg .= ' , JIT: On';
		} else {
			$msg .= ' , JIT: Off';
		}
		if(!empty($value['preload_statistics'])) {
			$count = [];
			if(!empty($value['preload_statistics']['functions'])) {
				$count[] = count($value['preload_statistics']['functions']).' functions';
			}
			if(!empty($value['preload_statistics']['classes'])) {
				$count[] = count($value['preload_statistics']['classes']).' classes';
			}
			if(!empty($value['preload_statistics']['scripts'])) {
				$count[] = count($value['preload_statistics']['scripts']).' scripts';
			}
			$msg .= ' , Preload: On ['.implode('/', $count).']';
		} else {
			$msg .= ' , Preload: Off';
		}
	} else {
		$msg = 'OPcache: Off';
	}
	if(isset($_GET['benchmark']) && FORMHASH == $_GET['formhash']) {
		$times = 3;
		$r = 0;
		for($i = 0; $i < $times; $i++) {
			$r += get_benchmark();
		}
		$benchmark = sprintf('%1.6f', $r / $times);
		if($benchmark > 2) {
			$advice = ' '.cplang('home_benchmark_advice');
		}
		$benchmark .= 's';
	} else {
		$append = (isset($_GET['attachsize']) ? '&attachsize' : '').(isset($_GET['dbsize']) ? '&dbsize' : '');
		$benchmark = '<a href="'.ADMINSCRIPT.'?action=index&formhash='.FORMHASH.'&benchmark'.$append.'">[ '.$lang['home_benchmark_run'].' ]</a>';
	}
	$meminfo = memory('check');
	showboxrow('', ['class="dcol lineheight d-14"', 'class="dcol lineheight d-1"'], [
		cplang('home_benchmark'),
		$benchmark.$advice.' <span class="xg1">('.$msg.')</span>'
	]);
	showboxfooter();
}

function show_news() {
	global $newversion;

	showboxheader('discuz_news', 'listbox', 'id="discuz_news"');

	if(!empty($newversion['newversion'])) {
		$newver = $newversion['newversion']['release'] != DISCUZ_RELEASE;
		$downlist = [];
		foreach($newversion['newversion']['downlist'] as $key => $value) {
			$downlist[] = '<a href="'.diconv($value['url'], 'utf-8', CHARSET).'" target="_blank">'.discuzcode(strip_tags(diconv($value['title'], 'utf-8', CHARSET)), 1, 0).'</a>';
		}

		$tips .= cplang('download_latest').': <a href="https://gitee.com/Discuz/DiscuzX/attach_files" target="_blank"'.($newver ? ' style="font-weight: bold;color:red"' : '').'>Discuz! '.$newversion['newversion']['version'].' '.$newversion['newversion']['release'].'</a>';
		if($newver && isfounder()) {
			$tips .= '&#x3001;<a style="font-weight: bold;color:red" href="'.ADMINSCRIPT.'?action=founder&operation=upgrade">'.cplang('menu_upgrade').'</a>';
		}
		if(!empty($downlist)) {
			$tips .= '&#x3001;'.implode('&#x3001;', $downlist);
		}

		if(empty($newversion['newversion']['qqqun'])) {
			$newversion['newversion']['qqqun'] = '73'.'210'.'36'.'90';
		}
		$tips .= cplang('qq_group').': '.$newversion['newversion']['qqqun'];
		showboxrow('', ['class="dcol d-1 lineheight"'], [
			$tips,
		]);
	}

	if(!empty($newversion['news'])) {
		$newversion['news'] = dhtmlspecialchars($newversion['news']);
		foreach($newversion['news'] as $v) {
			showboxrow('', ['class="dcol d-1 lineheight"', 'class="dcol"'], [
				'<a href="'.$v['url'].'" target="_blank">'.discuzcode(strip_tags(diconv($v['title'], 'utf-8', CHARSET)), 1, 0).'</a>',
				discuzcode(strip_tags($v['date']), 1, 0),
			]);
		}
	} else {
		showboxrow('', ['class="dcol d-1"', 'class="dcol td21" style="text-align:right;"'], [
			'<a href="https://www.dismall.com/" target="_blank">'.cplang('log_in_to_update').'</a>',
			'',
		]);
		showboxrow('', ['class="dcol d-1"', 'class="dcol td21" style="text-align:right;"'], [
			'<a href="https://gitee.com/3dming/DiscuzL/attach_files" target="_blank">'.cplang('download_latest').'</a>',
			'',
		]);
	}

	showboxfooter();
}

function show_widgets($type) {
	if(!empty($_GET['edit'])) {
		admin\widget_edit::output($type);
	} else {
		admin\widget_view::output($type);
	}
}

function show_charts() {
	global $_G;
	if(!$_G['setting']['updatestat']) {
		return;
	}

	loadcache('statvars');

	require_once childfile('stat/function', 'misc');

	$statvars = getstatvars('basic');
	$statvars['online'] = C::app()->session->count();
	$statvars['onlinemembers'] = C::app()->session->count(1);

	require_once template('admin/index_charts');
}

function show_hotthreads() {
	require_once childfile('ranklist/function', 'misc');
	$threadlist = getranklistdata('thread', 'replies', 'thisweek');

	if(!$threadlist) {
		return;
	}

	require_once template('admin/index_hotthreads');
}

function show_forever_thanks() {
	$copyRightMessage = [
		'&#x7248;&#x6743;&#x6240;&#x6709;',
		'&#x5408;&#x80A5;&#x8D30;&#x9053;&#x7F51;&#x7EDC;&#x79D1;&#x6280;&#x6709;&#x9650;&#x516C;&#x53F8;',
		'&#x817E;&#x8BAF;&#x79D1;&#x6280;&#xFF08;&#x5317;&#x4EAC;&#xFF09;&#x6709;&#x9650;&#x516C;&#x53F8;',
	];
	$gitTeamStr = '';
	$gitTeam = [
		'yunnuowangluo' => '&#x4e91;&#x8bfa;',
		'guohui1301' => 'Hyman',
		'ytjxzxy' => '&#x5e73;&#x5b89;&#x7f51;&#x7edc;&#x79d1;&#x6280;',
		'comiis' => '&#x514b;&#x7c73;&#x8bbe;&#x8ba1;',
		'zoewho' => '&#x6E56;&#x4E2D;&#x6C89;',
		'3dming' => '&#x8BF8;&#x845B;&#x6653;&#x660E;',
		'laozhoubuluo' => '&#x8001;&#x5468;&#x90E8;&#x843D;',
		'popcorner' => 'popcorner',
		'brotherand2' => 'brotherand2',
		'nftstudio' => '&#x9006;&#x98CE;&#x5929;',
		'dzlab' => '&#x79D1;&#x7AD9;&#x7F51;',
		'ONEXIN' => 'ONEXIN',
	];
	foreach($gitTeam as $id => $name) {
		$gitTeamStr .= '<a href="https://gitee.com/'.$id.'" class="lightlink2" target="_blank">'.$name.'</a>';
	}
	$devTeamStr = '';
	$devTeam = [
		'174393' => 'Guode \'sup\' Li',
		'859' => 'Hypo \'Cnteacher\' Wang',
		'263098' => 'Liming \'huangliming\' Huang',
		'706770' => 'Jun \'Yujunhao\' Du',
		'80629' => 'Ning \'Monkeye\' Hou',
		'246213' => 'Lanbo Liu',
		'322293' => 'Qingpeng \'andy888\' Zheng',
		'401635' => 'Guosheng \'bilicen\' Zhang',
		'2829' => 'Mengshu \'msxcms\' Chen',
		'492114' => 'Liang \'Metthew\' Xu',
		'1087718' => 'Yushuai \'Max\' Cong',
		'875919' => 'Jie \'tom115701\' Zhang',
	];
	foreach($devTeam as $id => $name) {
		$devTeamStr .= '<a href="https://discuz.dismall.com/home.php?mod=space&uid='.$id.'" class="lightlink2" target="_blank">'.$name.'</a>';
	}
	$devSkins = [
		'294092' => 'Fangming \'Lushnis\' Li',
		'674006' => 'Jizhou \'Iavav\' Yuan',
		'717854' => 'Ruitao \'Pony.M\' Ma',
	];
	$devSkinsStr = '';
	foreach($devSkins as $id => $name) {
		$devSkinsStr .= '<a href="https://discuz.dismall.com/home.php?mod=space&uid='.$id.'" class="lightlink2" target="_blank">'.$name.'</a>';
	}
	$devThanksStr = '';
	$devThanks = [
		'122246' => 'Heyond',
		'632268' => 'JinboWang',
		'15104' => 'Redstone',
		'10407' => 'Qiang Liu',
		'210272' => 'XiaoDunFang',
		'86282' => 'Jianxieshui',
		'9600' => 'Theoldmemory',
		'2629' => 'Rain5017',
		'26926' => 'Snow Wolf',
		'17149' => 'Hehechuan',
		'9132' => 'Pk0909',
		'248' => 'feixin',
		'675' => 'Laobing Jiuba',
		'13877' => 'Artery',
		'233' => 'Huli Hutu',
		'122' => 'Lao Gui',
		'159' => 'Tyc',
		'177' => 'Stoneage',
		'7155' => 'Gregry',
	];
	foreach($devThanks as $id => $name) {
		$devThanksStr .= '<a href="https://discuz.dismall.com/home.php?mod=space&uid='.$id.'" class="lightlink2" target="_blank">'.$name.'</a>';
	}

	showboxheader('home_dev', 'listbox fixpadding', 'id="home_dev"');
	showboxrow('', ['class="dcol d-1 lineheight"', 'class="dcol lineheight team"'], [$copyRightMessage[0], '<span class="bold">'.$copyRightMessage[1].', '.$copyRightMessage[2].'</span>']);
	showboxrow('', ['class="dcol d-1 lineheight"', 'class="dcol lineheight team"'], [cplang('contributors'), $gitTeamStr]);
	showboxrow('', ['class="dcol d-1 lineheight"', 'class="dcol lineheight team"'], [cplang('home_forever'), '<a href="javascript:display(\'history\')">点击查看</a>']);
	showtagheader('div', 'history');
	showboxrow('', ['class="dcol d-1 lineheight"', 'class="dcol lineheight team"'], [cplang('home_dev_manager'), '<a href="https://discuz.dismall.com/home.php?mod=space&uid=1" class="lightlink2" target="_blank">'.cplang('dev_manager').'</a>']);
	showboxrow('', ['class="dcol d-1 lineheight"', 'class="dcol lineheight team"'], [cplang('home_dev_team'), $devTeamStr]);
	showboxrow('', ['class="dcol d-1 lineheight"', 'class="dcol lineheight team"'], [cplang('home_dev_skins'), $devSkinsStr]);
	showboxrow('', ['class="dcol d-1 lineheight"', 'class="dcol lineheight team"'], [cplang('home_dev_thanks'), $devThanksStr]);
	showtagfooter('div');
	showboxfooter();
}

function get_sysicon() {
	$n = strtolower(php_uname());
	if(str_contains($n, 'windows')) {
		return 'sys_win';
	} elseif(str_contains($n, 'debian')) {
		return 'sys_debian';
	} elseif(str_contains($n, 'fedora')) {
		return 'sys_fedora';
	} elseif(str_contains($n, 'ubuntu')) {
		return 'sys_ubuntu';
	} elseif(str_contains($n, 'tencentos')) {
		return 'sys_linux';
	} elseif(str_contains($n, 'centos')) {
		return 'sys_centos';
	} elseif(str_contains($n, 'linux')) {
		return 'sys_linux';
	} else {
		return '';
	}
}

function get_memicon() {
	$n = strtolower(memory('check'));
	if(!$n) {
		return '';
	}
	if(str_contains($n, 'redis')) {
		return 'sys_redis';
	} elseif(str_contains($n, 'memcache')) {
		return 'sys_memcache';
	} else {
		return 'sys_memory';
	}
}

function get_meminfo() {
	$n = memory('check');
	if(!$n) {
		return cplang('none').' '.cplang('home_memory_advice');
	}
	if($n == 'Redis') {
		$v = memory('info', 'server');
		if(!empty($v['redis_version'])) {
			$n .= ' '.$v['redis_version'];
		}
	}
	return $n;
}

function get_webicon() {
	$n = strtolower($_SERVER['SERVER_SOFTWARE']);
	if(str_contains($n, 'nginx')) {
		return 'sys_nginx';
	} elseif(str_contains($n, 'apache')) {
		return 'sys_apache';
	} elseif(str_contains($n, 'lighttpd')) {
		return 'sys_lighttpd';
	} elseif(str_contains($n, 'iis')) {
		return 'sys_iis';
	} else {
		return 'sys_webserver';
	}
}

function get_benchmark() {
	$start = microtime(true);

	$r = 0;
	for($c = 0; $c < 1000000000; $c++) {
		$r += $c;
	}

	$end = microtime(true);
	return $end - $start;
}