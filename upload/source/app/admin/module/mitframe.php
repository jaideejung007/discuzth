<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();

showsubmenu('mitframe_apps');

$allowfuntype = ['portal', 'forum', 'friend', 'follower', 'group', 'follow', 'collection', 'guide', 'feed', 'blog', 'doing', 'album', 'share', 'wall', 'homepage', 'ranklist', 'medal', 'task', 'magic', 'favorite'];
$_GET['type'] = in_array($_GET['type'], $allowfuntype) ? trim($_GET['type']) : '';
echo "<script>disallowfloat = '{$_G['setting']['disallowfloat']}';</script>";
echo "<style>.td25 { width: 60px; } .light { margin-top: 2px; }".
	".apps .drow {height: 65px;float: left;margin: 0 10px 10px 0;padding: 10px;width: calc(33.33333% - 30px);border-radius: 5px;align-items: flex-start;}".
	".apps .c1 { width: 60px; padding-left: 5px !important; padding-right: 5px !important; }".
	".apps .c2 { display: flex;flex: 1 0 0;flex-direction: column;} .apps .c2 h3 { font-size:16px;margin-bottom:2px; }".
	".apps .c3 { line-height: 50px;height: 50px; overflow: hidden; }".
	".apps img.close { opacity: 0.2 } ".
	".apps .hover:hover img.close { opacity: 0.6 } ".
	"</style>";

/*search={"setting_functions":"action=mitframe"}*/

$apps = [];

getapps($apps);

showboxheader('', 'apps');
foreach($apps as $orderid => $oapps) {
	foreach($oapps as $app => $data) {
		[$icon, $status, $name, $desc, $op] = $data;
		showboxrow('',
			['class="dcol c1"', 'class="dcol d-2-3 c2"', 'class="dcol c3"'],
			[
				'<img src="'.$icon.'" '.(!$status ? 'class="close"' : '').' />',
				'<h3>'.cplang($name, mitframeApp: $app).'</h3>'.
				'<p><span class="light">'.cplang($desc, mitframeApp: $app).'</p>',
				'<p>'.$op.'</p>',
			]);
	}
}
showboxfooter();
/*search*/

function getapps(&$apps) {
	global $_G;
	$dir = MITFRAME_APP();
	$advdir = dir($dir);
	while($entry = $advdir->read()) {
		if($entry == '.' || $entry == '..') {
			continue;
		}
		$f = appfile('admin/switch', $entry);
		if(file_exists($f)) {
			include_once $f;
			$c = 'app_'.$entry.'_switch';
			if(!class_exists($c)) {
				continue;
			}
			if(method_exists($c, 'getModules')) {
				$modules = $c::getModules();
				foreach($modules as $module) {
					$c = 'app_'.$entry.'_switch_'.$module;
					if(class_exists($c)) {
						$orderid = defined($c.'::OrderId') ? $c::OrderId : 99;
						$apps[$orderid][$module] = [$c::Icon, $c::getStatus(), $c::Name, $c::Desc, $c::GetOptions()];
					}
				}
			} else {
				$orderid = defined($c.'::OrderId') ? $c::OrderId : 99;
				$apps[$orderid][$entry] = [$c::Icon, $c::getStatus(), $c::Name, $c::Desc, $c::GetOptions()];
			}
		}
	}
	ksort($apps);
}