<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('globalsubmit')) {
	$feedid = intval($_GET['feedid']);
	$feed = [];
	if($feedid) {
		$feed = table_home_feed::t()->fetch_feed('', '', '', $feedid);

		if($feed['uid']) {
			require_once libfile('function/feed');
			$feed = mkfeed($feed);
		}
		$feed['body_template'] = dhtmlspecialchars($feed['body_template']);
		$feed['body_general'] = dhtmlspecialchars($feed['body_general']);

		$feed['dateline'] = dgmdate($feed['dateline'], 'Y-m-d H:i');

		showchildmenu([['nav_feed', 'feed']], 'id:'.$feedid);
	}
	if(empty($feed['dateline'])) {
		$feed['dateline'] = dgmdate($_G['timestamp'], 'Y-m-d H:i');
	}
	/*search={"nav_feed":"action=feed"}*/
	showformheader('feed&operation=global', $feed['uid'] ? '' : 'onsubmit="edit_save();"');
	echo '<script type="text/javascript" src="'.STATICURL.'image/editor/editor_function.js"></script>';
	echo "<input type=\"hidden\" name=\"feednew[feedid]\" value=\"{$feed['feedid']}\" /><input type=\"hidden\" name=\"{feednew['feeduid']}\" value=\"{$feed['uid']}\" />";
	showtableheader();
	if(empty($feed['uid'])) {
		showsetting('feed_global_title', 'feednew[title_template]', $feed['title_template'], 'text');
		$src = 'home.php?mod=editor&charset='.CHARSET.'&allowhtml=1&doodle=0';
		print <<<EOF
			<tr><td>{$lang['message']}</td><td></td></tr>
			<tr>
				<td colspan="2">
					<textarea class="userData" name="feednew[body_template]" id="uchome-ttHtmlEditor" style="height:100%;width:100%;display:none;border:0px" onkeydown="textareakey(this, event)">{$feed['body_template']}</textarea>
					<iframe src="$src" name="uchome-ifrHtmlEditor" id="uchome-ifrHtmlEditor" scrolling="no" border="0" frameborder="0" style="width:100%;border: 1px solid #C5C5C5;" height="400"></iframe>
				<td>
			</tr>
EOF;
		showsetting('feed_global_body_general', 'feednew[body_general]', $feed['body_general'], 'text');
	} else {
		print <<<EOF
			<tr><td class="td27">{$lang['feed_global_title']}</td><td></td></tr>
			<tr class="noborder"><td colspan="2">{$feed['title_template']}&nbsp;<td></tr>

			<tr><td class="td27">{$lang['message']}</td><td></td></tr>
			<tr class="noborder"><td colspan="2">{$feed['body_template']}&nbsp;<td></tr>

			<tr><td class="td27">{$lang['feed_global_body_general']}</td><td></td></tr>
			<tr class="noborder"><td colspan="2">{$feed['body_general']}&nbsp;<td></tr>
EOF;
	}

	showsetting('feed_global_image_1', 'feednew[image_1]', $feed['image_1'], 'text');
	showsetting('feed_global_image_1_link', 'feednew[image_1_link]', $feed['image_1_link'], 'text');
	showsetting('feed_global_image_2', 'feednew[image_2]', $feed['image_2'], 'text');
	showsetting('feed_global_image_2_link', 'feednew[image_2_link]', $feed['image_2_link'], 'text');
	showsetting('feed_global_image_3', 'feednew[image_3]', $feed['image_3'], 'text');
	showsetting('feed_global_image_3_link', 'feednew[image_3_link]', $feed['image_3_link'], 'text');
	showsetting('feed_global_image_4', 'feednew[image_4]', $feed['image_4'], 'text');
	showsetting('feed_global_image_4_link', 'feednew[image_4_link]', $feed['image_4_link'], 'text');

	showsetting('feed_global_dateline', 'feednew[dateline]', $feed['dateline'], 'text');
	if($feed['id']) {
		showsetting('feed_global_hot', 'feednew[hot]', $feed['hot'], 'text');
	}
	showsubmit('globalsubmit');
	showtablefooter();
	showformfooter();
	/*search*/
} else {
	$feednew = getgpc('feednew');
	$feedid = intval($feednew['feedid']);

	if(empty($feednew['feeduid']) || empty($feedid)) {
		$setarr = [
			'title_template' => trim($feednew['title_template']),
			'body_template' => trim($feednew['body_template'])
		];
		if(empty($setarr['title_template']) && empty($setarr['body_template'])) {
			cpmsg('sitefeed_error', '', 'error');
		}

	} else {
		$setarr = [];
	}

	$feednew['dateline'] = trim($feednew['dateline']);
	if($feednew['dateline']) {
		require_once libfile('function/home');
		$newtimestamp = strtotime($feednew['dateline']);
		if($newtimestamp > $_G['timestamp']) {
			$_G['timestamp'] = $newtimestamp;
		}
	}

	if(empty($feedid)) {
		$_G['uid'] = 0;
		require_once libfile('function/feed');
		$feedid = feed_add('sitefeed',
			trim($feednew['title_template']), [],
			trim($feednew['body_template']), [],
			trim($feednew['body_general']),
			[trim($feednew['image_1']), trim($feednew['image_2']), trim($feednew['image_3']), trim($feednew['image_4'])],
			[trim($feednew['image_1_link']), trim($feednew['image_2_link']), trim($feednew['image_3_link']), trim($feednew['image_4_link'])],
			'', '', '', 1
		);

	} else {
		if(empty($feednew['feeduid'])) {
			$setarr['body_general'] = trim($feednew['body_general']);
		}
		$setarr['image_1'] = trim($feednew['image_1']);
		$setarr['image_1_link'] = trim($feednew['image_1_link']);
		$setarr['image_2'] = trim($feednew['image_2']);
		$setarr['image_2_link'] = trim($feednew['image_2_link']);
		$setarr['image_3'] = trim($feednew['image_3']);
		$setarr['image_3_link'] = trim($feednew['image_3_link']);
		$setarr['image_4'] = trim($feednew['image_4']);
		$setarr['image_4_link'] = trim($feednew['image_4_link']);

		$setarr['dateline'] = $newtimestamp;
		$setarr['hot'] = intval($feednew['hot']);

		table_home_feed::t()->update_feed('', $setarr, '', '', $feedid);
	}
	cpmsg('feed_global_add_success', '', 'succeed');
}
	