<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($id)) {
	cpmsg('undefined_action');
}

if(!($smtype = table_forum_imagetype::t()->fetch($id))) {
	cpmsg('smilies_type_nonexistence', '', 'error');
} else {
	$smurl = './static/image/smiley/'.$smtype['directory'];
	$smdir = DISCUZ_ROOT.$smurl;
	if(!is_dir($smdir)) {
		cpmsg('smilies_directory_invalid', '', 'error', ['smurl' => $smurl]);
	}
}

$smilies = update_smiles($smdir, $id, $imgextarray);

if($smilies['smilies']) {
	addsmilies($id, $smilies['smilies']);
	updatecache(['smilies', 'smileycodes', 'smilies_js']);
	cpmsg('smilies_update_succeed', 'action=smilies', 'succeed', ['smurl' => $smurl, 'num' => $smilies['num'], 'typename' => $smtype['name']]);
} else {
	cpmsg('smilies_update_error', '', 'error', ['smurl' => $smurl]);
}
	