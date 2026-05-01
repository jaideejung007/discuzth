<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showtips('makehtml_tips_index');

showformheader('makehtml&operation=index');
showtableheader('');
echo '<script type="text/javascript" src="'.STATICURL.'js/makehtml.js?1"></script>', $css;
echo '<tr><td colspan="15"><div class="fixsel"><a href="javascript:void(0);" class="btn_big" id="submit_portal_html">'.$lang['makehtml_createindex'].'</a></div></td></tr>', $result;
$adminscript = ADMINSCRIPT;
echo <<<EOT
<script type="text/JavaScript">
var form = document.forms['cpform'];
form.onsubmit = function(){return false;};
_attachEvent($('submit_portal_html'), 'click', function(){
	$('mk_result').style.display = 'block';
	$('mk_index').style.display = 'none';
	this.innerHTML = '{$lang['makehtml_recreate']}';
	this.disabled = true;
	make_html_index();
	return false;
});

function make_html_index() {
	var dom = $('mk_index');
	dom.innerHTML = '<div class="mk_msg">{$lang['makehtml_waitmaking']}</div>';
	dom.style.display = 'block';
	new make_html_batch('portal.php?', 0, null, dom, 1);
}
</script>
EOT;
showtablefooter();
showformfooter();
	