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
showtips('makehtml_tips_category');
showformheader('makehtml&operation=category');
showtableheader('');
echo '<script type="text/javascript" src="'.STATICURL.'js/calendar.js"></script>',
	'<script type="text/javascript" src="'.STATICURL.'js/makehtml.js?1"></script>',
$css;

showsetting('start_time', 'starttime', '', 'calendar', '', '', '', '1');
$selectdata = ['category', [[0, $lang['makehtml_createallcategory']]]];
mk_format_category(array_keys($_G['cache']['portalcategory']));
showsetting('makehtml_selectcategory', $selectdata, 0, 'mselect');
echo '<tr><td colspan="15"><div class="fixsel"><a href="javascript:void(0);" class="btn_big" id="submit_portal_html">'.$lang['makehtml_createcategory'].'</a></div></td></tr>', $result;
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
		make_html_category(starttime);
	} else {
		var category = form['category'];
		var allcatids = [];
		var selectedids = [];
		for(var i = 0; i < category.options.length; i++) {
			var option = category.options[i];
			if(option.value > 0) {
				allcatids.push(option.value);
			}
			if(option.selected) {
				selectedids.push(option.value);
			}
		}
		if(selectedids.length) {
			new make_html_batch('portal.php?mod=list&catid=', selectedids[0] == 0 ? allcatids : selectedids, make_html_category_ok, $('mk_category'));
		} else {
			var dom = $('mk_index');
			dom.style.display = 'block';
			dom.innerHTML = '{$lang['makehtml_nofindcategory']}';
		}
	}
	return false;
});

function make_html_category_ok() {
	var dom = $('mk_index');
	dom.style.display = 'block';
	dom.style.color = 'green';
	dom.innerHTML = '<div class="mk_msg">{$lang['makehtml_selectcategorycomplete']}</div>';
}
function make_html_category(starttime){
	var dom = $('mk_category');
	dom.innerHTML = '<div class="mk_msg">{$lang['makehtml_waitmakingcategory']}</div>';
	dom.style.display = 'block';
	starttime = starttime || form['starttime'].value;
	var x = new Ajax();
	x.get('$adminscript?action=makehtml&operation=catids&inajax=1&frame=no&starttime='+starttime, function (s) {
		if(s) {
			new make_html_batch('portal.php?mod=list&catid=', s.split(','), make_html_category_ok, dom);
		} else {
			dom.innerHTML = '{$lang['makehtml_nofindcategory']}';
			setTimeout(function(){\$('mk_category').style.display = 'none'; make_html_index();}, 1000);
		}
	});
}

</script>
EOT;
showtablefooter();
showformfooter();
	