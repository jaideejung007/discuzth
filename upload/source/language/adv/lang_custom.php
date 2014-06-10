<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: lang_custom.php 27449 2012-02-01 05:32:35Z zhangguosheng $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$lang = array
(
	'custom_name' => 'โฆษณาแบบกำหนดเอง',
	'custom_desc' => 'Add custom adv code in templates or HTML file.<br /><br />
		<a href="javascript:;" onclick="prompt(\'Please copy (CTRL+C) the content below to templates\', \'<!--{ad/custom_'.$_GET['customid'].'}-->\')" />Internal js call</a>&nbsp;
		<a href="javascript:;" onclick="prompt(\'Please copy (CTRL+C) the content below to HTML files\', \'&lt;script type=\\\'text/javascript\\\' src=\\\''.$_G['siteurl'].'api.php?mod=ad&adid=custom_'.$_GET['customid'].'\\\'&gt;&lt;/script&gt;\')" />External js call</a>',
	'custom_id_notfound' => 'Custom adv does not exist',
	'custom_codelink' => 'Internal js call',
	'custom_text' => 'Custom advertising',
);

?>
