<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('portalcategory');
showtips('makehtml_tips_article');
showformheader('makehtml&operation=category');
showtableheader('');
echo '<script type="text/javascript" src="'.STATICURL.'js/calendar.js"></script>',
	'<script type="text/javascript" src="'.STATICURL.'js/makehtml.js?1"></script>',
$css;

showsetting('start_time', 'starttime', dgmdate(TIMESTAMP - 86400, 'Y-m-d'), 'calendar', '', '', '', '1');
$selectdata = ['category', [[0, $lang['makehtml_createallcategory']]]];
mk_format_category(array_keys($_G['cache']['portalcategory']));
showsetting('makehtml_selectcategory', $selectdata, 0, 'mselect');
showsetting('makehtml_startid', 'startid', 0, 'text');
showsetting('makehtml_endid', 'endid', 0, 'text');
echo '<tr><td colspan="15"><div class="fixsel"><a href="javascript:void(0);" class="btn_big" id="submit_portal_html">'.$lang['makehtml_createarticle'].'</a></div></td></tr>', $result;
$adminscript = ADMINSCRIPT;
echo <<<EOT
<script type="text/JavaScript">
var form = document.forms['cpform'];
form.onsubmit = function(){return false;};
_attachEvent($('submit_portal_html'), 'click', function(){
	$('mk_result').style.display = 'block';
	$('mk_index').style.display = 'none';
	this.innerHTML = '{$lang['makehtml_recreate']}';
	var starttime = form['starttime'].value;
	var category = form['category'];
	var allcatids = [];
	var selectedids = [];
	for(var i = 0; i < category.options.length; i++) {
		var option = category.options[i];
		allcatids.push(option.value);
		if(option.selected) {
			selectedids.push(option.value);
		}
	}
	var startid = parseInt(form['startid'].value);
	var endid = parseInt(form['endid'].value);
	if(starttime || selectedids.length || startid || endid) {
		make_html_article(starttime, selectedids[0] == 0 ? -1 : selectedids, startid, endid);
	} else {
		var dom = $('mk_index');
		dom.style.display = 'block';
		dom.innerHTML = '{$lang['makehtml_nofindarticle']}';
	}
	return false;
});

function make_html_article_ok() {
	var dom = $('mk_index');
	dom.style.display = 'block';
	dom.style.color = 'green';
	dom.innerHTML = '<div class="mk_msg">{$lang['makehtml_allarticlecomplete']}</div>';
}

function make_html_article(starttime, catids, startid, endid) {
	catids = catids || -1;
	startid = startid || 0;
	endid = endid || 0;
	var dom = $('mk_article');
	dom.innerHTML = '<div class="mk_msg">{$lang['makehtml_waitcheckarticle']}</div>';
	dom.style.display = 'block';
	var x = new Ajax();
	x.get('$adminscript?action=makehtml&operation=aids&inajax=1&frame=no&starttime='+starttime+'&catids='+(catids == -1 ? '' : catids.join(','))+'&startid='+startid+'&endid='+endid, function (s) {
		if(s && s.indexOf('<') < 0){
			new make_html_batch('portal.php?mod=view&aid=', s.split(','), make_html_article_ok, dom);
		} else {
			dom.innerHTML = '{$lang['makehtml_nofindarticle']}';
		}
	});
}
</script>
EOT;
showtablefooter();
showformfooter();
	