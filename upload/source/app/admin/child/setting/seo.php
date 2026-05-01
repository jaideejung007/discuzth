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
	if(!$settingnew['rewritestatus']) {
		$settingnew['rewritestatus'] = [];
	}
	$settingnew['baidusitemap_life'] = max(1, min(24, intval($settingnew['baidusitemap_life'])));
	$rewritedata = rewritedata();
	foreach($settingnew['rewriterule'] as $k => $v) {
		if(!$v) {
			$settingnew['rewriterule'][$k] = $rewritedata['rulesearch'][$k];
		}
	}
	if(!empty($_GET['seothreadlist']) && is_array($_GET['seothreadlist'])) {
		foreach($_GET['seothreadlist'] as $seofid => $val) {
			$seofid = intval($seofid);
			table_forum_forumfield::t()->update($seofid, ['seotitle' => $val['seotitle'], 'keywords' => $val['keywords'], 'seodescription' => $val['description']]);
		}
	}
	if(!empty($_GET['seoarticlelist']) && is_array($_GET['seoarticlelist'])) {
		foreach($_GET['seoarticlelist'] as $seocateid => $val) {
			$seocateid = intval($seocateid);
			table_portal_category::t()->update($seocateid, ['seotitle' => $val['seotitle'], 'keyword' => $val['keywords'], 'description' => $val['description']]);
		}
		updatecache('portalcategory');
	}
} else {
	shownav('global', 'nav_seo');

	$_GET['anchor'] = in_array($_GET['anchor'], ['rewrite', 'portal', 'forum', 'home', 'group']) ? $_GET['anchor'] : 'rewrite';
	showsubmenuanchors('nav_seo', [
		['nav_seo_rewrite', 'rewrite', $_GET['anchor'] == 'rewrite'],
		['nav_seo_portal', 'portal', $_GET['anchor'] == 'portal'],
		['nav_seo_forum', 'forum', $_GET['anchor'] == 'forum'],
		['nav_seo_home', 'home', $_GET['anchor'] == 'home'],
		['nav_seo_group', 'group', $_GET['anchor'] == 'group'],
		['other', 'other', $_GET['anchor'] == 'other'],
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	$setting['seotitle'] = dunserialize($setting['seotitle']);
	$setting['seodescription'] = dunserialize($setting['seodescription']);
	$setting['seokeywords'] = dunserialize($setting['seokeywords']);

	$rewritedata = rewritedata();
	$setting['rewritestatus'] = isset($setting['rewritestatus']) ? dunserialize($setting['rewritestatus']) : [];
	$setting['rewriterule'] = isset($setting['rewriterule']) ? dunserialize($setting['rewriterule']) : '';
	/*search={"setting_optimize":"action=setting&operation=seo","setting_seo":"action=setting&operation=seo"}*/
	echo '<div id="rewrite"'.($_GET['anchor'] != 'rewrite' ? ' style="display: none"' : '').'>';
	showtips('setting_tips', 'tips_rewrite');
	showboxheader('<em class="right">'.cplang('setting_seo_rewritestatus_viewrule').'</em>'.cplang('setting_seo_rewritestatus'), 'nobottom');
	showtableheader('', 'nobottom');
	showtablerow('', ['class="vtop tips2" colspan="3"'], [cplang('setting_seo_rewritestatus_comment')]);
	showsubtitle(['setting_seo_pages', 'setting_seo_vars', 'setting_seo_rule', 'available']);
	foreach($rewritedata['rulesearch'] as $k => $v) {
		$v = empty($setting['rewriterule'][$k]) ? $v : $setting['rewriterule'][$k];
		showtablerow('', ['class="td24"', 'class="td31"', 'class="longtxt"', 'class="td25"'], [
			cplang('setting_seo_rewritestatus_'.$k),
			implode(', ', array_keys($rewritedata['rulevars'][$k])),
			'<input onclick="doane(event)" name="settingnew[rewriterule]['.$k.']" class="txt" value="'.dhtmlspecialchars($v).'"/>',
			'<input type="checkbox" name="settingnew[rewritestatus][]" class="checkbox" value="'.$k.'" '.((is_array($setting['rewritestatus']) && in_array($k, $setting['rewritestatus'])) ? 'checked="checked"' : '').'/>'
		]);
	}
	showtablefooter();
	showboxfooter();
	showtableheader('', 'nobottom');
	showsetting('setting_seo_rewritecompatible', 'settingnew[rewritecompatible]', $setting['rewritecompatible'], 'radio');
	showsetting('setting_seo_rewriteguest', 'settingnew[rewriteguest]', $setting['rewriteguest'], 'radio');
	showsetting('setting_seo_rewritemobile', 'settingnew[rewritemobile]', $setting['rewritemobile'], 'radio');
	showtablefooter();
	echo '</div>';

	echo '<div id="other"'.($_GET['anchor'] != 'other' ? ' style="display: none"' : '').'>';
	showtips('<li>'.cplang('setting_seo_seotitle_comment').'</li><li>'.cplang('setting_seo_seodescription_comment').'</li><li>'.cplang('setting_seo_seokeywords_comment').'</li>', 'tips', true, '<em class="right">'.cplang('setting_seo_robots_output').'</em>'.cplang('setting_seo'));
	showtableheader('', 'nobottom');
	showsetting('setting_seo_seohead', 'settingnew[seohead]', $setting['seohead'], 'textarea');
	showsetting('setting_seo_seohead_mobile', 'settingnew[seohead_mobile]', $setting['seohead_mobile'], 'textarea');
	showtablefooter();
	echo '</div>';
	$seotypes = [
		'portal' => ['portal', 'articlelist', 'article'],
		'forum' => ['forum', 'threadlist', 'viewthread'],
		'home' => ['home', 'blog', 'album'],
		'group' => ['group', 'grouppage', 'viewthread_group']
	];
	$codetypes = [
		'portal' => 'bbname',
		'articlelist' => 'bbname,curcat,firstcat,secondcat,page',
		'article' => 'bbname,curcat,firstcat,secondcat,subject,summary,user,page',
		'forum' => 'bbname',
		'threadlist' => 'bbname,forum,fup,fgroup,page',
		'viewthread' => 'bbname,forum,fup,fgroup,subject,summary,tags,page',
		'home' => 'bbname',
		'blog' => 'bbname,subject,summary,tags,user',
		'album' => 'bbname,album,depict,user',
		'group' => 'bbname,forum,first,second',
		'grouppage' => 'bbname,forum,first,second,gdes,page',
		'viewthread_group' => 'bbname,forum,first,second,gdes,subject,summary,tags,page',
	];
	foreach($codetypes as $key => $val) {
		$jscodetypes .= "codetypes['{$key}'] = '{$val}';\r\n";
		foreach(explode(',', $val) as $code) {
			$cname = $code == 'bbname' ? cplang('setting_seo_code_bbname') : cplang('setting_seo_code_'.$key.'_'.$code);
			$jscodenames .= "codenames['{$key}_{$code}'] = '{$cname}';\r\n";
		}
	}
	$staticurl = STATICURL;
	print <<<EOF
		<div id="codediv" style="display:none; top: 707px;background: url('{$staticurl}image/common/mdly.png') no-repeat scroll 0 0 transparent; height: 120px; line-height: 32px; margin-top: -16px; overflow: hidden; padding: 10px 15px; position: absolute; left: 500px; width: 300px;">
		<p>
EOF;
	echo cplang('setting_seo_insallowcode');
	print <<<EOF
		</p>
		<p id="seocodes">
		<a onclick="insertcode('subject');return false;" href="javascript:;">{subject}</a>
		<span class="pipe">|</span>
		<a onclick="insertcode('forum');return false;" href="javascript:;">{forum}</a>
		</p>
		</div>
		<script src="{$staticurl}js/home.js" type="text/javascript"></script>
		<script language="javascript">
		var codediv = $('codediv');
		var codetypes = new Array(), codenames = new Array();
		$jscodetypes
		$jscodenames
		function getcodetext(obj, ctype) {
			var top_offset = obj.offsetTop;
			var codecontent = '';
			var targetid = obj.id;
			while((obj = obj.offsetParent).tagName != 'BODY') {
				top_offset += obj.offsetTop;
			}
			if(!codetypes[ctype]) {
				return true;
			}
			types = codetypes[ctype].split(',');
			for(var i = 0; i < types.length; i++) {
				if(codecontent != '') {
					codecontent += '&nbsp;&nbsp;';
				}
				codecontent += '<a onclick="insertContent(\''+targetid+'\', \'{'+types[i]+'}\');return false;" href="javascript:;" title="'+codenames[ctype+'_'+types[i]]+'">{'+types[i]+'}</a>';
			}
			$('seocodes').innerHTML = codecontent;
			codediv.style.top = top_offset + 'px';
			codediv.style.display = '';
			_attachEvent($('submenu'), 'mouseover', function(){codediv.style.display='none';});
		}
		</script>
EOF;
	$first = $seconds = $thirds = $afirst = $aseconds = $athirds = [];
	$query = table_forum_forum::t()->fetch_all_forum_for_sub_order();
	foreach($query as $forum) {
		$forum['description'] = $forum['seodescription'];
		$forum['id'] = $forum['fid'];
		if($forum['type'] == 'group') {
			$first[$forum['fid']] = $forum;
		} elseif($forum['type'] == 'sub') {
			$thirds[$forum['fup']][] = $forum;
		} else {
			$seconds[$forum['fup']][] = $forum;
		}
	}
	loadcache('portalcategory');
	$portalcategory = $_G['cache']['portalcategory'];
	if($portalcategory) {
		foreach($portalcategory as $category) {
			$category['id'] = $category['catid'];
			$category['name'] = $category['catname'];
			$category['keywords'] = $category['keyword'];
			if($category['level'] == 0) {
				$afirst[$category['catid']] = $category;
			} elseif($category['level'] == 1) {
				$aseconds[$category['upid']][] = $category;
			} else {
				$athirds[$category['upid']][] = $category;
			}
		}
	}
	foreach($seotypes as $type => $subtypes) {
		echo '<div id="'.$type.'"'.($_GET['anchor'] != $type ? ' style="display: none"' : '').'>';
		showtips(cplang('setting_seo_global_tips').cplang('setting_seo_'.$type.'_tips'), 'tips_'.$type);
		foreach($subtypes as $subtype) {
			showboxheader(cplang('setting_seo_'.$subtype).($subtype == 'threadlist' || $subtype == 'articlelist' ? ' &nbsp; <a href="javascript:;" class="act" onclick="if($(\''.$subtype.'_detail\').style.display){$(\''.$subtype.'_detail\').style.display=\'\';this.innerHTML=\''.cplang('setting_seo_closedetail').'\';}else{$(\''.$subtype.'_detail\').style.display=\'none\';this.innerHTML=\''.cplang('setting_seo_opendetail').'\';};return false;">'.cplang('setting_seo_opendetail').'</a>' : ''));
			showtableheader();
			showtablerow('', ['width="12%"', ''], [
					cplang('setting_seo_seotitle'),
					'<input type="text" id="t_'.$type.$subtype.'" onfocus="getcodetext(this, \''.$subtype.'\');" name="settingnew[seotitle]['.$subtype.']" value="'.dhtmlspecialchars($setting['seotitle'][$subtype]).'" class="txt" style="width:280px;" />',
				]
			);
			showtablerow('', ['width="12%"', ''], [
					cplang('setting_seo_seokeywords'),
					'<input type="text" id="k_'.$type.$subtype.'" onfocus="getcodetext(this, \''.$subtype.'\');" name="settingnew[seokeywords]['.$subtype.']" value="'.dhtmlspecialchars($setting['seokeywords'][$subtype]).'" class="txt" style="width:280px;" />'
				]
			);
			showtablerow('', ['width="12%"', ''], [
					cplang('setting_seo_seodescription'),
					'<input type="text" id="d_'.$type.$subtype.'" onfocus="getcodetext(this, \''.$subtype.'\');" name="settingnew[seodescription]['.$subtype.']" value="'.dhtmlspecialchars($setting['seodescription'][$subtype]).'" class="txt" style="width:280px;" />',
				]
			);
			if($subtype == 'threadlist') {
				showlist($first, $seconds, $thirds, $subtype);
			}
			if($subtype == 'articlelist') {
				showlist($afirst, $aseconds, $athirds, $subtype);
			}
			showtablefooter();
			showboxfooter();
		}
		echo '</div>';
	}
	showtagfooter('tbody');
	/*search*/

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showformfooter();
}