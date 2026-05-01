<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$operation = $operation ? $operation : 'list';

$defaulttargets = ['portal', 'home', 'member', 'forum', 'group', 'plugin'];

if(!empty($_GET['preview'])) {
	$_GET['advnew'][$_GET['advnew']['style']]['url'] = $_GET['TMPadvnew'.$_GET['advnew']['style']] ? $_GET['TMPadvnew'.$_GET['advnew']['style']] : $_GET['advnew'.$_GET['advnew']['style']];
	$data = encodeadvcode($_GET['advnew']);

	?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="<?php echo CHARSET; ?>"/>
		<meta name="renderer" content="webkit"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<script type="text/javascript">var IMGDIR = '<?php echo $_G['style']['imgdir']; ?>',
                        cookiepre = '<?php echo $_G['config']['cookie']['cookiepre'];?>',
                        cookiedomain = '<?php echo $_G['config']['cookie']['cookiedomain'];?>',
                        cookiepath = '<?php echo $_G['config']['cookie']['cookiepath'];?>';</script>
		<script type="text/javascript" src="<?php echo STATICURL; ?>js/common.js"></script>
		<link rel="stylesheet" type="text/css"
		      href="data/cache/style_<?php echo $_G['setting']['styleid']; ?>_common.css"/>
	</head>
	<body>
	<div id="append_parent"></div>
	<div id="ajaxwaitid"></div>
	<div id="hd">
		<div class="wp">
			<?php echo $data; ?>
		</div>
	</div>
	</body>
	</html>
	<?php

	exit;
}

cpheader();

if($operation == 'add' && !empty($_GET['type']) || $operation == 'edit' && !empty($_GET['advid'])) {
	require_once childfile('adv/add');
} else {
	$file = childfile('adv/'.$operation);
	if(!file_exists($file)) {
		cpmsg('undefined_action');
	}
	require_once $file;
}

function encodeadvcode($advnew) {
	switch($advnew['style']) {
		case 'code':
			$advnew['code'] = $advnew['code']['html'];
			break;
		case 'text':
			$advnew['code'] = '<a href="'.$advnew['text']['link'].'" target="_blank" '.($advnew['text']['size'] ? 'style="font-size: '.$advnew['text']['size'].'"' : '').'>'.$advnew['text']['title'].'</a>';
			break;
		case 'image':
			$advnew['code'] = '<a href="'.$advnew['image']['link'].'" target="_blank"><img src="'.$advnew['image']['url'].'"'.($advnew['image']['height'] ? ' height="'.$advnew['image']['height'].'"' : '').($advnew['image']['width'] ? ' width="'.$advnew['image']['width'].'"' : '').($advnew['image']['alt'] ? ' alt="'.$advnew['image']['alt'].'"' : '').' border="0"></a>';
			break;
		case 'flash':
			$advnew['code'] = '<embed width="'.$advnew['flash']['width'].'" height="'.$advnew['flash']['height'].'" src="'.$advnew['flash']['url'].'" type="application/x-shockwave-flash" wmode="transparent"></embed>';
			break;
	}
	return $advnew['code'];
}

function getadvs() {
	global $_G;
	$checkdirs = array_merge([''], $_G['setting']['plugins']['available']);
	$advs = [];
	foreach($checkdirs as $key) {
		if($key) {
			$dir = DISCUZ_PLUGIN($key).'/adv';
		} else {
			$dir = DISCUZ_ROOT.'./source/class/adv';
		}
		if(!file_exists($dir)) {
			continue;
		}
		$advdir = dir($dir);
		while($entry = $advdir->read()) {
			if(!in_array($entry, ['.', '..']) && preg_match('/^adv\_[\w\.]+$/', $entry) && str_ends_with($entry, '.php') && strlen($entry) < 30 && is_file($dir.'/'.$entry)) {
				@include_once $dir.'/'.$entry;
				$advclass = substr($entry, 0, -4);
				if(class_exists($advclass)) {
					$adv = new $advclass();
					$script = substr($advclass, 4);
					$script = ($key ? $key.':' : '').$script;
					$advs[$entry] = [
						'class' => $script,
						'name' => lang('adv/'.$script, $adv->name),
						'version' => $adv->version,
						'copyright' => lang('adv/'.$script, $adv->copyright),
						'filemtime' => @filemtime($dir.'/'.$entry)
					];
				}
			}
		}
	}
	uasort($advs, 'filemtimesort');
	return $advs;
}

?>