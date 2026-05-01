<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showtips('makehtml_tips_topic');
showformheader('makehtml&operation=topic');
showtableheader('');
echo '<script type="text/javascript" src="'.STATICURL.'js/calendar.js"></script>',
	'<script type="text/javascript" src="'.STATICURL.'js/makehtml.js?1"></script>',
$css;

showsetting('start_time', 'starttime', '', 'calendar', '', '', '', '1');
echo '<tr><td colspan="15"><div class="fixsel"><a href="javascript:void(0);" class="btn_big" id="submit_portal_html">'.$lang['makehtml_createtopic'].'</a></div></td></tr>', $result;
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
	if(starttime) {
		make_html_topic(starttime);
	} else {
		var dom = $('mk_index');
		dom.style.display = 'block';
		dom.innerHTML = '{$lang['makehtml_nofindtopic']}';
	}
	return false;
});

function make_html_topic_ok() {
	var dom = $('mk_index');
	dom.style.display = 'block';
	dom.style.color = 'green';
	dom.innerHTML = '<div class="mk_msg">{$lang['makehtml_alltopiccomplete']}</div>';
}

function make_html_topic(starttime) {
	var dom = $('mk_topic');
	dom.innerHTML = '<div class="mk_msg">{$lang['makehtml_waitchecktopic']}</div>';
	dom.style.display = 'block';
	var x = new Ajax();
	x.get('$adminscript?action=makehtml&operation=topicids&inajax=1&frame=no&starttime='+starttime, function (s) {
		if(s && s.indexOf('<') < 0){
			new make_html_batch('portal.php?mod=topic&topicid=', s.split(','), make_html_topic_ok, dom);
		} else {
			dom.innerHTML = '{$lang['makehtml_nofindtopic']}';
		}
	});
}
</script>
EOT;
showtablefooter();
showformfooter();
	