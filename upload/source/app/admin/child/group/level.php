<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$levelid = !empty($_GET['levelid']) ? intval($_GET['levelid']) : 0;
if(empty($levelid)) {
	$grouplevels = '';
	if(!submitcheck('grouplevelsubmit')) {
		$query = table_forum_grouplevel::t()->fetch_all_creditslower_order();
		foreach($query as $level) {
			$grouplevels .= showtablerow('', ['class="td25"', '', 'class="td28"', 'class=td28'], [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[{$level['levelid']}]\" value=\"{$level['levelid']}\">",
				"<input type=\"text\" class=\"txt\" size=\"12\" name=\"levelnew[{$level['levelid']}][leveltitle]}\" value=\"{$level['leveltitle']}\">",
				"<input type=\"text\" class=\"txt\" size=\"6\" name=\"levelnew[{$level['levelid']}][creditshigher]}\" value=\"{$level['creditshigher']}\" /> ~ <input type=\"text\" class=\"txt\" size=\"6\" name=\"levelnew[{$level['levelid']}][creditslower]}\" value=\"{$level['creditslower']}\" disabled />",
				"<a href=\"".ADMINSCRIPT."?action=group&operation=level&levelid={$level['levelid']}\" class=\"act\">{$lang['detail']}</a>"
			], TRUE);
		}
		echo <<<EOT
<script type="text/JavaScript">
var rowtypedata = [
	[
		[1,'', 'td25'],
		[1,'<input type="text" class="txt" size="12" name="levelnewadd[leveltitle][]">'],
		[1,'<input type="text" class="txt" size="6" name="levelnewadd[creditshigher][]">', 'td28'],
		[4,'']
	],
	[
		[1,'', 'td25'],
		[1,'<input type="text" class="txt" size="12" name="leveltitlenewadd[]">'],
		[1,'<input type="text" class="txt" size="2" name="creditshighernewadd[]">', 'td28'],
		[4, '']
	]
];
</script>
EOT;
		shownav('group', 'nav_group_level');
		showsubmenu('nav_group_level');
		/*search={"nav_group_level":"action=group&operation=level"}*/
		showtips('group_level_tips');
		/*search*/

		showformheader('group&operation=level');
		showtableheader('group_level', 'fixpadding', 'id="grouplevel"');
		showsubtitle(['del', 'group_level_title', 'group_level_creditsrange', '']);
		echo $grouplevels;
		echo '<tr><td>&nbsp;</td><td colspan="8"><div><a href="###" onclick="addrow(this, 0)" class="addtr">'.$lang['group_level_add'].'</a></div></td></tr>';
		showsubmit('grouplevelsubmit', 'submit');
		showtablefooter();
		showformfooter();
	} else {
		$levelnewadd = $levelnewkeys = $orderarray = [];
		$maxlevelid = 0;
		if(!empty($_GET['levelnewadd'])) {
			$levelnewadd = array_flip_keys($_GET['levelnewadd']);
			foreach($levelnewadd as $k => $v) {
				if(!$v['leveltitle'] || !$v['creditshigher']) {
					unset($levelnewadd[$k]);
				}
			}
		}
		if(!empty($_GET['levelnew'])) {
			$levelnewkeys = array_keys($_GET['levelnew']);
			$maxlevelid = max($levelnewkeys);
		}

		foreach($levelnewadd as $k => $v) {
			$_GET['levelnew'][$k + $maxlevelid + 1] = $v;
		}
		if(is_array($_GET['levelnew'])) {
			foreach($_GET['levelnew'] as $id => $level) {
				if((is_array($_GET['delete']) && in_array($id, $_GET['delete'])) || ($id == 0 && (!$level['leveltitle'] || $level['creditshigher'] == ''))) {
					unset($_GET['levelnew'][$id]);
				} else {
					$orderarray[$level['creditshigher']] = $id;
				}
			}
		}
		ksort($orderarray);
		$rangearray = [];
		$lowerlimit = array_keys($orderarray);
		for($i = 0; $i < count($lowerlimit); $i++) {
			$rangearray[$orderarray[$lowerlimit[$i]]] =
				[
				'creditshigher' => isset($lowerlimit[$i - 1]) ? $lowerlimit[$i] : -999999999,
				'creditslower' => $lowerlimit[$i + 1] ?? 999999999
				];
		}
		foreach($_GET['levelnew'] as $id => $level) {
			$creditshighernew = $rangearray[$id]['creditshigher'];
			$creditslowernew = $rangearray[$id]['creditslower'];
			if($creditshighernew == $creditslowernew) {
				cpmsg('group_level_update_credits_duplicate', '', 'error');
			}
			$data = [
				'leveltitle' => $level['leveltitle'],
				'creditshigher' => $creditshighernew,
				'creditslower' => $creditslowernew,
			];
			if(in_array($id, $levelnewkeys)) {
				table_forum_grouplevel::t()->update($id, $data);
			} elseif($level['leveltitle'] && $level['creditshigher'] != '') {
				$data = [
					'leveltitle' => $level['leveltitle'],
					'type' => 'default',
					'creditshigher' => $creditshighernew,
					'creditslower' => $creditslowernew,
				];
				$data['type'] = 'default';
				$newlevelid = table_forum_grouplevel::t()->insert($data, 1);
			}
		}
		if($ids = dimplode($_GET['delete'])) {
			$levelcount = table_forum_grouplevel::t()->fetch_count();
			if(!empty($_GET['delete']) && is_array($_GET['delete']) && count($_GET['delete']) == $levelcount) {
				updatecache('grouplevels');
				cpmsg('group_level_succeed_except_all_levels', 'action=group&operation=level', 'succeed');

			}
			table_forum_grouplevel::t()->delete($ids);
		}
		updatecache('grouplevels');
		cpmsg('group_level_update_succeed', 'action=group&operation=level', 'succeed');
	}
} else {
	$grouplevel = table_forum_grouplevel::t()->fetch($levelid);
	if(empty($grouplevel)) {
		cpmsg('group_level_noexist', 'action=group&operation=level', 'error');
	}
	if(!($group_creditspolicy = dunserialize($grouplevel['creditspolicy']))) {
		$group_creditspolicy = [];
	}
	if(!($group_postpolicy = dunserialize($grouplevel['postpolicy']))) {
		$group_postpolicy = [];
	}
	if(!($specialswitch = dunserialize($grouplevel['specialswitch']))) {
		$specialswitch = [];
	}
	if(!submitcheck('editgrouplevel')) {
		shownav('group', 'nav_group_level');
		showchildmenu([['nav_group_level', 'group&operation=level']], $grouplevel['leveltitle']);

		showtips('group_level_tips');

		showformheader('group&operation=level&levelid='.$levelid, 'enctype');
		showtableheader();
		showtitle('groups_setting_basic');
		showsetting('group_level_title', 'levelnew[leveltitle]', $grouplevel['leveltitle'], 'text');
		if($grouplevel['icon']) {
			$valueparse = parse_url($grouplevel['icon']);
			if(isset($valueparse['host'])) {
				$grouplevelicon = $grouplevel['icon'];
			} else {
				$grouplevelicon = $_G['setting']['attachurl'].'common/'.$grouplevel['icon'].'?'.random(6);
			}
			$groupleveliconhtml = '<label><input type="checkbox" class="checkbox" name="deleteicon['.$grouplevel['levelid'].']" value="yes" /> '.$lang['delete'].'</label><br /><img src="'.$grouplevelicon.'" />';
		}
		showsetting('group_level_icon', 'iconnew', $grouplevel['icon'], 'filetext', '', 0, $groupleveliconhtml);

		showtitle('group_level_credits');
		$varname = ['levelnew[creditspolicy]', [], 'isfloat'];
		$varname[1] = [
			['post', cplang('group_level_credits_post'), '1'],
			['reply', cplang('group_level_credits_reply'), '1'],
			['digest', cplang('group_level_credits_digest'), '1'],
			['postattach', cplang('group_level_credits_upload'), '1'],
			['getattach', cplang('group_level_credits_download'), '1'],
			['tradefinished', cplang('group_level_credits_trade'), '1'],
			['joinpoll', cplang('group_level_credits_poll'), '1'],
		];
		showsetting('', $varname, $group_creditspolicy, 'omcheckbox');
		showtitle('group_level_posts');
		$varname = ['levelnew[postpolicy]', [], 'isfloat'];
		$varname[1] = [
			['alloweditpost', cplang('forums_edit_posts_alloweditpost'), '1'],
			['recyclebin', cplang('forums_edit_posts_recyclebin'), '1'],
			['allowsmilies', cplang('forums_edit_posts_smilies'), '1'],
			['allowhtml', cplang('forums_edit_posts_html'), '1'],
			['allowbbcode', cplang('forums_edit_posts_bbcode'), '1'],
			['allowanonymous', cplang('forums_edit_posts_anonymous'), '1'],
			['jammer', cplang('forums_edit_posts_jammer'), '1'],
			['allowimgcode', cplang('forums_edit_posts_imgcode'), '1'],
			['allowmediacode', cplang('forums_edit_posts_mediacode'), '1'],
		];
		showsetting('', $varname, $group_postpolicy, 'omcheckbox');

		showsetting('forums_edit_posts_allowpostspecial', ['levelnew[postpolicy][allowpostspecial]', [
			cplang('thread_poll'),
			cplang('thread_trade'),
			cplang('thread_reward'),
			cplang('thread_activity'),
			cplang('thread_debate')
		]], $group_postpolicy['allowpostspecial'], 'binmcheckbox');
		$threadpluginarray = [];
		if(is_array($_G['setting']['threadplugins'])) foreach($_G['setting']['threadplugins'] as $tpid => $data) {
			$threadpluginarray[] = [$tpid, $data['name']];
		}
		if($threadpluginarray) {
			showsetting('forums_edit_posts_threadplugin', ['levelnew[postpolicy][threadplugin]', $threadpluginarray], $group_postpolicy['threadplugin'], 'mcheckbox');
		}
		showsetting('forums_edit_posts_attach_ext', 'levelnew[postpolicy][attachextensions]', $group_postpolicy['attachextensions'], 'text');

		showtitle('group_level_special');
		showsetting('group_level_special_allowchangename', 'specialswitchnew[allowchangename]', $specialswitch['allowchangename'], 'radio');
		showsetting('group_level_special_allowchangetype', 'specialswitchnew[allowchangetype]', $specialswitch['allowchangetype'], 'radio');
		showsetting('group_level_special_allowclose', 'specialswitchnew[allowclosegroup]', $specialswitch['allowclosegroup'], 'radio');
		showsetting('group_level_special_allowthreadtype', 'specialswitchnew[allowthreadtype]', $specialswitch['allowthreadtype'], 'radio');
		showsetting('group_level_special_membermax', 'specialswitchnew[membermaximum]', $specialswitch['membermaximum'], 'text');

		showsubmit('editgrouplevel');
		showtablefooter();
		showformfooter();
	} else {
		$dataarr = [];
		$levelnew = (!empty($_GET['levelnew']) && is_array($_GET['levelnew'])) ? $_GET['levelnew'] : [];
		$dataarr['leveltitle'] = $levelnew['leveltitle'];
		$default_creditspolicy = ['post' => 0, 'reply' => 0, 'digest' => 0, 'postattach' => 0, 'getattach' => 0, 'tradefinished' => 0, 'joinpoll' => 0];
		$levelnew['creditspolicy'] = empty($levelnew['creditspolicy']) ? $default_creditspolicy : array_merge($default_creditspolicy, $levelnew['creditspolicy']);
		$dataarr['creditspolicy'] = serialize($levelnew['creditspolicy']);
		$default_postpolicy = ['alloweditpost' => 0, 'recyclebin' => 0, 'allowsmilies' => 0, 'allowhtml' => 0, 'allowbbcode' => 0, 'allowanonymous' => 0, 'jammer' => 0, 'allowimgcode' => 0, 'allowmediacode' => 0];
		$levelnew['postpolicy'] = array_merge($default_postpolicy, $levelnew['postpolicy']);

		$levelnew['postpolicy']['allowpostspecial'] = bindec(intval($levelnew['postpolicy']['allowpostspecial'][6]).intval($levelnew['postpolicy']['allowpostspecial'][5]).intval($levelnew['postpolicy']['allowpostspecial'][4]).intval($levelnew['postpolicy']['allowpostspecial'][3]).intval($levelnew['postpolicy']['allowpostspecial'][2]).intval($levelnew['postpolicy']['allowpostspecial'][1]));

		$dataarr['postpolicy'] = serialize($levelnew['postpolicy']);
		$_GET['specialswitchnew']['membermaximum'] = intval($_GET['specialswitchnew']['membermaximum']);
		$dataarr['specialswitch'] = serialize($_GET['specialswitchnew']);
		if($_GET['deleteicon']) {
			@unlink($_G['setting']['attachurl'].'common/'.$grouplevel['icon']);
			ftpcmd('delete', 'common/'.$grouplevel['icon']);
			$dataarr['icon'] = '';
		} else {
			if($_FILES['iconnew']) {
				$data = ['extid' => "$levelid"];
				$dataarr['icon'] = upload_icon_banner($data, $_FILES['iconnew'], 'grouplevel_icon');
			} else {
				$dataarr['icon'] = $_GET['iconnew'];
			}
		}
		table_forum_grouplevel::t()->update($levelid, $dataarr);
		updatecache('grouplevels');
		cpmsg('groups_setting_succeed', 'action=group&operation=level&levelid='.$levelid, 'succeed');
	}
}
	