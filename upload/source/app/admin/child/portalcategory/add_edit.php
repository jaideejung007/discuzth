<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$_GET['catid'] = intval($_GET['catid']);
$anchor = in_array($_GET['anchor'], ['basic', 'html']) ? $_GET['anchor'] : 'basic';

if($_GET['catid'] && !$portalcategory[$_GET['catid']]) {
	cpmsg('portalcategory_catgory_not_found', '', 'error');
}

$cate = $_GET['catid'] ? $portalcategory[$_GET['catid']] : [];
if($operation == 'add') {
	$_GET['upid'] = intval($_GET['upid']);
	if($_GET['upid']) {
		$cate['level'] = $portalcategory[$_GET['upid']] ? $portalcategory[$_GET['upid']]['level'] + 1 : 0;
		$cate['upid'] = intval($_GET['upid']);
	} else {
		$cate['level'] = 0;
		$cate['upid'] = 0;
	}
	$cate['displayorder'] = 0;
	$cate['closed'] = 1;
}
loadcache('domain');
$domain = &$_G['cache']['domain'];
$channeldomain = isset($rootdomain['channel']) && $rootdomain['channel'] ? $rootdomain['channel'] : [];

if(!submitcheck('detailsubmit')) {
	shownav('portal', 'portalcategory');
	$url = 'portalcategory&operation='.$operation.($operation == 'add' ? '&upid='.$_GET['upid'] : '&catid='.$_GET['catid']);

	$parents = [['portalcategory', 'portalcategory']];
	if($operation !== 'add') {
		$parents[] = [($cate['catname'] ? $cate['catname'].' ' : ' ')];
	}
	showchildmenu($parents, cplang('portalcategory_detail'));

	showtagheader('div', 'basic', $anchor == 'basic');
	showformheader($url);
	showtableheader();
	$catemsg = '';
	if($cate['username']) $catemsg .= $lang['portalcategory_username'].' '.$cate['username'];
	if($cate['dateline']) $catemsg .= ' '.$lang['portalcategory_dateline'].' '.dgmdate($cate['dateline'], 'Y-m-d m:i:s');
	if($cate['upid']) $catemsg .= ' '.$lang['portalcategory_upname'].': <a href="'.ADMINSCRIPT.'?action=portalcategory&operation=edit&catid='.$cate['upid'].'">'.$portalcategory[$cate['upid']]['catname'].'</a>';
	if($catemsg) showtitle($catemsg);
	showsetting('portalcategory_catname', 'catname', html_entity_decode($cate['catname']), 'text');
	showsetting('display_order', 'displayorder', $cate['displayorder'], 'text');
	showsetting('portalcategory_foldername', 'foldername', $cate['foldername'], 'text');
	showsetting('portalcategory_url', 'url', $cate['url'], 'text');
	showsetting('portalcategory_perpage', 'perpage', $cate['perpage'] ? $cate['perpage'] : 15, 'text');
	showsetting('portalcategory_maxpages', 'maxpages', $cate['maxpages'] ? $cate['maxpages'] : 1000, 'text');

	showportalprimaltemplate($cate['primaltplname'], 'list');
	showportalprimaltemplate($cate['articleprimaltplname'], 'view');

	showsetting('portalcategory_allowpublish', 'allowpublish', $cate['disallowpublish'] ? 0 : 1, 'radio');
	showsetting('portalcategory_notshowarticlesummay', 'notshowarticlesummay', $cate['notshowarticlesummay'] ? 0 : 1, 'radio');
	showsetting('portalcategory_allowcomment', 'allowcomment', $cate['allowcomment'], 'radio');
	if($cate['level']) {
		showsetting('portalcategory_inheritancearticle', 'inheritancearticle', !$cate['notinheritedarticle'] ? '1' : '0', 'radio');
		showsetting('portalcategory_inheritanceblock', 'inheritanceblock', !$cate['notinheritedblock'] ? '1' : '0', 'radio');
	}
	showsetting('portalcategory_is_closed', 'closed', $cate['closed'] ? 0 : 1, 'radio');
	if($cate['level'] != 2) showsetting('portalcategory_shownav', 'shownav', $cate['shownav'], 'radio');
	$setindex = !empty($_G['setting']['defaultindex']) && $_G['setting']['defaultindex'] == $cate['caturl'] ? 1 : 0;
	showsetting('setindex', 'setindex', $setindex, 'radio');
	if($cate['level'] == 0) {
		if(!empty($_G['setting']['domain']['root']['channel'])) {
			showsetting('forums_edit_extend_domain', '', '', $_G['scheme'].'://<input type="text" class="txt" name="domain" class="txt" value="'.$cate['domain'].'" style="width:100px; margin-right:0px;" >.'.$_G['setting']['domain']['root']['channel']);
		} else {
			showsetting('forums_edit_extend_domain', 'domain', '', 'text', 'disabled');
		}
	}
	showsetting('portalcategory_noantitheft', 'noantitheft', $cate['noantitheft'], 'radio');
	showtablefooter();
	showtips('setting_seo_portal_tips', 'tips', true, 'setseotips');
	showtableheader();
	showsetting('portalcategory_seotitle', 'seotitle', $cate['seotitle'], 'text');
	showsetting('portalcategory_keyword', 'keyword', $cate['keyword'], 'text');
	showsetting('portalcategory_summary', 'description', $cate['description'], 'textarea');
	showtablefooter();

	showtableheader();
	showsubmit('detailsubmit');
	if($operation == 'add') showsetting('', '', '', '<input type="hidden" name="level" value="'.$cate['level'].'" />');
	showtablefooter();
	showformfooter();

} else {
	require_once libfile('function/portalcp');
	$domain = $_GET['domain'] ? $_GET['domain'] : '';
	$_GET['closed'] = intval($_GET['closed']) ? 0 : 1;
	$_GET['catname'] = trim($_GET['catname']);
	$foldername = trim($_GET['foldername']);
	$oldsetindex = !empty($_G['setting']['defaultindex']) && $_G['setting']['defaultindex'] == $cate['caturl'] ? 1 : 0;
	$perpage = intval($_GET['perpage']);
	$maxpages = intval($_GET['maxpages']);
	$perpage = empty($perpage) ? 15 : $perpage;
	$maxpages = empty($maxpages) ? 1000 : $maxpages;

	if($_GET['catid'] && !empty($cate['domain'])) {
		require_once libfile('function/delete');
		deletedomain($_GET['catid'], 'channel');
	}
	if(!empty($domain)) {
		require_once libfile('function/domain');
		domaincheck($domain, $_G['setting']['domain']['root']['channel'], 1);
	}

	$updatecategoryfile = [];


	$editcat = [
		'catname' => $_GET['catname'],
		'allowcomment' => $_GET['allowcomment'],
		'url' => $_GET['url'],
		'closed' => $_GET['closed'],
		'seotitle' => $_GET['seotitle'],
		'keyword' => $_GET['keyword'],
		'description' => $_GET['description'],
		'displayorder' => intval($_GET['displayorder']),
		'notinheritedarticle' => $_GET['inheritancearticle'] ? '0' : '1',
		'notinheritedblock' => $_GET['inheritanceblock'] ? '0' : '1',
		'disallowpublish' => $_GET['allowpublish'] ? '0' : '1',
		'notshowarticlesummay' => $_GET['notshowarticlesummay'] ? '0' : '1',
		'perpage' => $perpage,
		'maxpages' => $maxpages,
		'noantitheft' => intval($_GET['noantitheft']),
	];

	$dir = '';
	if(!empty($foldername)) {
		$oldfoldername = empty($_GET['catid']) ? '' : $portalcategory[$_GET['catid']]['foldername'];
		preg_match_all('/[^\w\d\_]/', $foldername, $re);
		if(!empty($re[0])) {
			cpmsg(cplang('portalcategory_foldername_rename_error').','.cplang('return'), NULL, 'error');
		}
		$parentdir = getportalcategoryfulldir($cate['upid']);
		if($parentdir === false) cpmsg(cplang('portalcategory_parentfoldername_empty').','.cplang('return'), NULL, 'error');
		if($foldername == $oldfoldername) {
			$dir = $parentdir.$foldername;
		} elseif(is_dir(DISCUZ_ROOT_STATIC.'./'.$parentdir.$foldername)) {
			cpmsg(cplang('portalcategory_foldername_duplicate').','.cplang('return'), NULL, 'error');
		} elseif($portalcategory[$_GET['catid']]['foldername']) {
			$r = rename(DISCUZ_ROOT_STATIC.'./'.$parentdir.$portalcategory[$_GET['catid']]['foldername'], DISCUZ_ROOT_STATIC.'./'.$parentdir.$foldername);
			if($r) {
				$updatecategoryfile[] = $_GET['catid'];
				$editcat['foldername'] = $foldername;
			} else {
				cpmsg(cplang('portalcategory_foldername_rename_error').','.cplang('return'), NULL, 'error');
			}
		} elseif(empty($portalcategory[$_GET['catid']]['foldername'])) {
			$dir = $parentdir.$foldername;
			$editcat['foldername'] = $foldername;
		}
	} elseif(empty($foldername) && $portalcategory[$_GET['catid']]['foldername']) {
		delportalcategoryfolder($_GET['catid']);
		$editcat['foldername'] = '';
	}
	$primaltplname = $viewprimaltplname = '';
	if(!empty($_GET['listprimaltplname'])) {
		$primaltplname = $_GET['listprimaltplname'];
		if(!isset($_GET['signs']['list'][dsign($primaltplname)])) {
			cpmsg(cplang('diy_sign_invalid').','.cplang('return'), NULL, 'error');
		}
		$checktpl = checkprimaltpl($primaltplname);
		if($checktpl !== true) {
			cpmsg(cplang($checktpl).','.cplang('return'), NULL, 'error');
		}
	}

	if(empty($_GET['viewprimaltplname'])) {
		$_GET['viewprimaltplname'] = getparentviewprimaltplname($_GET['catid'], $_GET['upid'] ?? 0);
	} else if(!isset($_GET['signs']['view'][dsign($_GET['viewprimaltplname'])])) {
		cpmsg(cplang('diy_sign_invalid').','.cplang('return'), NULL, 'error');
	}
	$viewprimaltplname = !str_contains($_GET['viewprimaltplname'], ':') ? './template/default:portal/'.$_GET['viewprimaltplname'] : $_GET['viewprimaltplname'];
	$checktpl = checkprimaltpl($viewprimaltplname);
	if($checktpl !== true) {
		cpmsg(cplang($checktpl).','.cplang('return'), NULL, 'error');
	}

	$editcat['primaltplname'] = $primaltplname;
	$editcat['articleprimaltplname'] = $viewprimaltplname;

	if($_GET['catid']) {
		if($portalcategory[$_G['catid']]['level'] < 2) $editcat['shownav'] = intval($_GET['shownav']);
		if($domain && $portalcategory[$_G['catid']]['level'] == 0) {
			$editcat['domain'] = $domain;
		} else {
			$editcat['domain'] = '';
		}
	} else {
		if($portalcategory[$cate['upid']]) {
			if($portalcategory[$cate['upid']]['level'] == 0) $editcat['shownav'] = intval($_GET['shownav']);
		} else {
			$editcat['shownav'] = intval($_GET['shownav']);
			$editcat['domain'] = $domain;
		}
	}
	$cachearr = ['portalcategory'];
	if($_GET['catid']) {
		table_portal_category::t()->update($cate['catid'], $editcat);
		if($cate['catname'] != $_GET['catname']) {
			table_common_diy_data::t()->update_diy('portal/list_'.$cate['catid'], getdiydirectory($cate['primaltplname']), ['name' => $_GET['catname']]);
			table_common_diy_data::t()->update_diy('portal/view_'.$cate['catid'], getdiydirectory($cate['articleprimaltplname']), ['name' => $_GET['catname']]);
			$cachearr[] = 'diytemplatename';
		}
	} else {
		$editcat['upid'] = $cate['upid'];
		$editcat['dateline'] = TIMESTAMP;
		$editcat['uid'] = $_G['uid'];
		$editcat['username'] = $_G['username'];
		$_GET['catid'] = table_portal_category::t()->insert($editcat, true);
		$cachearr[] = 'diytemplatename';
	}

	if(!empty($domain)) {
		table_common_domain::t()->insert(['domain' => $domain, 'domainroot' => $_G['setting']['domain']['root']['channel'], 'id' => $_GET['catid'], 'idtype' => 'channel']);
		$cachearr[] = 'setting';
	}
	if($_GET['listprimaltplname'] && (empty($cate['primaltplname']) || $cate['primaltplname'] != $primaltplname)) {
		remakediytemplate($primaltplname, 'portal/list_'.$_GET['catid'], $_GET['catname'], getdiydirectory($cate['primaltplname']));
	}

	if($cate['articleprimaltplname'] != $viewprimaltplname) {
		remakediytemplate($viewprimaltplname, 'portal/view_'.$_GET['catid'], $_GET['catname'].'-'.cplang('portalcategory_viewpage'), getdiydirectory($cate['articleprimaltplname']));
	}

	include_once libfile('function/cache');
	updatecache('portalcategory');
	loadcache('portalcategory', true);
	$portalcategory = $_G['cache']['portalcategory'];

	require libfile('class/blockpermission');
	$tplpermsission = &template_permission::instance();
	$tplpre = 'portal/list_';

	require libfile('class/portalcategory');
	$categorypermsission = &portal_category::instance();

	if($operation == 'add') {
		if($cate['upid'] && $_GET['catid']) {
			if(!$editcat['notinheritedblock']) {
				$tplpermsission->remake_inherited_perm($tplpre.$_GET['catid'], $tplpre.$cate['upid']);
			}
			if(!$editcat['notinheritedarticle']) {
				$categorypermsission->remake_inherited_perm($_GET['catid']);
			}
		}
	} elseif($operation == 'edit') {
		if($editcat['notinheritedblock'] != $cate['notinheritedblock']) {
			$tplname = $tplpre.$cate['catid'];
			if($editcat['notinheritedblock']) {
				$tplpermsission->delete_inherited_perm_by_tplname($tplname, $tplpre.$cate['upid']);
			} else {
				if($portalcategory[$cate['catid']]['upid']) {
					$tplpermsission->remake_inherited_perm($tplname, $tplpre.$portalcategory[$cate['catid']]['upid']);
				}
			}
		}
		if($editcat['notinheritedarticle'] != $cate['notinheritedarticle']) {
			if($editcat['notinheritedarticle']) {
				$categorypermsission->delete_inherited_perm_by_catid($cate['catid'], $cate['upid']);
			} else {
				$categorypermsission->remake_inherited_perm($cate['catid']);
			}
		}
	}

	if(!empty($updatecategoryfile)) {
		remakecategoryfile($updatecategoryfile);
	}

	if($dir) {
		if(!makecategoryfile($dir, $_GET['catid'], $domain)) {
			cpmsg(cplang('portalcategory_filewrite_error').','.cplang('return'), NULL, 'error');
		}
		remakecategoryfile($portalcategory[$_GET['catid']]['children']);
	}

	if(($_GET['catid'] && $cate['level'] < 2) || empty($_GET['upid']) || ($_GET['upid'] && $portalcategory[$_GET['upid']]['level'] == 0)) {
		$nav = table_common_nav::t()->fetch_by_type_identifier(4, $_GET['catid']);
		if($editcat['shownav']) {
			if(empty($nav)) {
				$navparentid = 0;
				if($_GET['catid'] && $cate['level'] > 0 || !empty($_GET['upid'])) {
					$identifier = !empty($cate['upid']) ? $cate['upid'] : ($_GET['upid'] ? $_GET['upid'] : 0);
					$navparent = table_common_nav::t()->fetch_by_type_identifier(4, $identifier);
					$navparentid = $navparent['id'];
					if(empty($navparentid)) {
						cpmsg(cplang('portalcategory_parentcategory_no_shownav').','.cplang('return'), NULL, 'error');
					}
				}
				$setarr = [
					'parentid' => $navparentid,
					'name' => $editcat['catname'],
					'url' => $portalcategory[$_GET['catid']]['caturl'],
					'type' => '4',
					'available' => '1',
					'identifier' => $_GET['catid'],
				];
				if($_GET['catid'] && $cate['level'] == 0 || empty($_GET['upid']) && empty($_GET['catid'])) {
					$setarr['subtype'] = '1';
				}
				$navid = table_common_nav::t()->insert($setarr, true);

				if($_GET['catid'] && $cate['level'] == 0) {
					if(!empty($cate['children'])) {
						foreach($cate['children'] as $subcatid) {
							if($portalcategory[$subcatid]['shownav']) {
								$setarr = [
									'parentid' => $navid,
									'name' => $portalcategory[$subcatid]['catname'],
									'url' => $portalcategory[$subcatid]['caturl'],
									'type' => '4',
									'available' => '1',
									'identifier' => $subcatid,
								];
								table_common_nav::t()->insert($setarr);
							}
						}
					}
				}

			} else {
				$setarr = ['available' => '1', 'url' => $portalcategory[$_GET['catid']]['caturl']];
				table_common_nav::t()->update_by_type_identifier(4, $_GET['catid'], $setarr);
				if($portalcategory[$_GET['catid']]['level'] == 0 && $portalcategory[$_GET['catid']]['children']) {
					foreach($portalcategory[$_GET['catid']]['children'] as $subcatid) {
						table_common_nav::t()->update_by_type_identifier(4, $subcatid, ['url' => $portalcategory[$subcatid]['caturl']]);
					}
				}
			}
			$cachearr[] = 'setting';
		} else {
			if(!empty($nav)) {
				table_common_nav::t()->delete($nav['id']);
				if($portalcategory[$_GET['catid']]['level'] == 0 && !empty($portalcategory[$_GET['catid']]['children'])) {
					table_common_nav::t()->delete_by_parentid($nav['id']);
					table_portal_category::t()->update($portalcategory[$_GET['catid']]['children'], ['shownav' => '0']);
				}
				$cachearr[] = 'setting';
			}
		}
	}

	if($_GET['setindex']) {
		table_common_setting::t()->update_setting('defaultindex', $portalcategory[$_GET['catid']]['caturl']);
		$cachearr[] = 'setting';
	} elseif($oldsetindex) {
		table_common_setting::t()->update_setting('defaultindex', '');
		$cachearr[] = 'setting';
	}

	updatecache(array_unique($cachearr));

	$url = $operation == 'add' ? 'action=portalcategory#cat'.$_GET['catid'] : 'action=portalcategory&operation=edit&catid='.$_GET['catid'];
	cpmsg('portalcategory_edit_succeed', $url, 'succeed');
}
	