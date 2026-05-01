<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

/*search={"nav_makehtml":"action=makehtml&operation=all"}*/
showtips('makehtml_tips_all');

showformheader('makehtml&operation=all');
showtableheader('');
echo '<script type="text/javascript" src="'.STATICURL.'js/calendar.js"></script>',
	'<script type="text/javascript" src="'.STATICURL.'js/makehtml.js?1"></script>',
$css;
showsetting('start_time', 'starttime', dgmdate(TIMESTAMP, 'Y-m-d'), 'calendar', '', '', '', '1');
echo '<tr><td colspan="15"><div class="fixsel"><a href="javascript:void(0);" class="btn_big" id="submit_portal_html">'.$lang['makehtml_createall'].'</a></div></td></tr>', $result;
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
	if(starttime){
		make_html_article(starttime);
	}
	return false;
});

function make_html_ok() {
	var dom = $('mk_index');
	dom.innerHTML = '<div class="mk_msg">{$lang['makehtml_allfilecomplete']}</div>';
}
function make_html_index() {
	var dom = $('mk_index');
	dom.innerHTML = '<div class="mk_msg">{$lang['makehtml_waitmaking']}</div>';
	dom.style.display = 'block';
	new make_html_batch('portal.php?', 0, make_html_ok, dom, 1);
}

function make_html_category(starttime){
	var dom = $('mk_category');
	dom.innerHTML = '<div class="mk_msg">{$lang['makehtml_waitmakingcategory']}</div>';
	dom.style.display = 'block';
	starttime = starttime || form['starttime'].value;
	var x = new Ajax();
	x.get('$adminscript?action=makehtml&operation=catids&inajax=1&frame=no&starttime='+starttime, function (s) {
		if(s) {
			new make_html_batch('portal.php?mod=list&catid=', s.split(','), make_html_topic, dom);
		} else {
			dom.innerHTML = '{$lang['makehtml_nofindcategory']}<br/>{$lang['makehtml_startmaketopic']}<br /><a href="javascript:void(0);" onclick="\$(\'mk_category\').style.display = \'none\';make_html_topic();">{$lang['makehtml_browser_error']}</a>';
			setTimeout(function(){\$('mk_category').style.display = 'none'; make_html_topic();}, 1000);
		}
	});
}

function make_html_topic(starttime){
	var dom = $('mk_topic');
	dom.innerHTML = '<div class="mk_msg">{$lang['makehtml_waitchecktopic']}</div>';
	dom.style.display = 'block';
	starttime = starttime || form['starttime'].value;
	var x = new Ajax();
	x.get('$adminscript?action=makehtml&operation=topicids&inajax=1&frame=no&starttime='+starttime, function (s) {
		if(s) {
			new make_html_batch('portal.php?mod=topic&topicid=', s.split(','), make_html_index, dom);
		} else {
			dom.innerHTML = '{$lang['makehtml_nofindtopic']}<br/>{$lang['makehtml_startmakeindex']}<br /><a href="javascript:void(0);" onclick="\$(\'mk_topic\').style.display = \'none\';make_html_index();">{$lang['makehtml_browser_error']}</a>';
			setTimeout(function(){\$('mk_topic').style.display = 'none'; make_html_index();}, 1000);
		}
	});
}

function make_html_article(starttime) {
	var dom = $('mk_article');
	dom.innerHTML = '<div class="mk_msg">{$lang['makehtml_waitcheckarticle']}</div>';
	dom.style.display = 'block';
	var x = new Ajax();
	x.get('$adminscript?action=makehtml&operation=aids&inajax=1&frame=no&starttime='+starttime, function (s) {
		if(s){
			new make_html_batch('portal.php?mod=view&aid=', s.split(','), make_html_category, dom);
		} else {
			dom.innerHTML = '{$lang['makehtml_nofindarticle']}<br/>{$lang['makehtml_startmakecategory']}<br /><a href="javascript:void(0);" onclick="\$(\'mk_article\').style.display = \'none\';make_html_category();">{$lang['makehtml_browser_error']}</a>';
			setTimeout(function(){\$('mk_article').style.display = 'none'; make_html_category();}, 1000);
		}
	});
}

</script>
EOT;
showtablefooter();
showformfooter();
/*search*/
	