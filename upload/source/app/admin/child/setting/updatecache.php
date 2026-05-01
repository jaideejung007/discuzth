<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$updatecache = FALSE;
$settings = [];
foreach($settingnew as $key => $val) {
	if(in_array($key, ['siteuniqueid', 'my_sitekey', 'my_siteid'])) {
		continue;
	}
	if($setting[$key] != $val) {
		$updatecache = TRUE;
		if(in_array($key, ['newbiespan', 'topicperpage', 'postperpage', 'hottopic', 'starthreshold', 'delayviewcount', 'attachexpire',
			'visitedforums', 'maxsigrows', 'timeoffset', 'statscachelife', 'pvfrequence', 'oltimespan', 'seccodestatus',
			'maxprice', 'rssttl', 'maxonlines', 'floodctrl', 'regctrl', 'regfloodctrl',
			'searchctrl', 'extcredits1', 'extcredits2', 'extcredits3', 'extcredits4', 'extcredits5', 'extcredits6',
			'extcredits7', 'extcredits8', 'transfermincredits', 'exchangemincredits', 'maxincperthread', 'maxchargespan',
			'maxspm', 'maxsearchresults', 'maxsmilies', 'threadmaxpages', 'maxpostsize', 'minpostsize', 'sendmailday',
			'maxpolloptions', 'karmaratelimit', 'losslessdel', 'smcols', 'allowdomain', 'feedday', 'feedmaxnum', 'feedhotday', 'feedhotmin',
			'feedtargetblank', 'updatestat', 'namechange', 'namecheck', 'networkpage', 'maxreward', 'groupnum', 'starlevelnum', 'friendgroupnum',
			'maxpage',
			'starcredit', 'topcachetime', 'newspacerealname', 'newspaceavatar', 'newspacenum', 'shownewuser',
			'feedhotnum', 'showallfriendnum', 'feedread', 'maxsubjectsize', 'minsubjectsize',
			'need_friendnum', 'need_avatar', 'need_secmobile', 'uniqueemail', 'need_email', 'allowquickviewprofile', 'preventrefresh',
			'jscachelife', 'maxmodworksmonths', 'maxonlinelist'])) {
			$val = (float)$val;
		}

		if($key == 'privacy') {
			foreach($val['view'] as $var => $value) {
				$val['view'][$var] = intval($value);
			}
			if(!isset($val['feed']) || !is_array($val['feed'])) {
				$val['feed'] = [];
			}
			foreach($val['feed'] as $var => $value) {
				$val['feed'][$var] = 1;
			}
		}

		if($key == 'maxsubjectsize' && $val > 255) {
			cpmsg('maxsubjectsize_no_more', '', 'error');
		}

		$settings[$key] = $val;
	}
}

if($settings) {
	table_common_setting::t()->update_batch($settings);
}

if($updatecache) {
	updatecache('setting');
	if(isset($settingnew['forumlinkstatus']) && $settingnew['forumlinkstatus'] != $setting['forumlinkstatus']) {
		updatecache('forumlinks');
	}
	if(isset($settingnew['userstatusby']) && $settingnew['userstatusby'] != $setting['userstatusby']) {
		updatecache('usergroups');
	}
	if((isset($settingnew['smthumb']) && $settingnew['smthumb'] != $setting['smthumb']) || (isset($settingnew['smcols']) && $settingnew['smcols'] != $setting['smcols']) || (isset($settingnew['smrows']) && $settingnew['smrows'] != $setting['smrows'])) {
		updatecache('smilies_js');
	}
	if(isset($settingnew['customauthorinfo']) && $settingnew['customauthorinfo'] != $setting['customauthorinfo']) {
		updatecache('custominfo');
	}
	if($operation == 'credits') {
		if($settingnew['extcredits'] != $setting['extcredits']) {
			include_once libfile('function/block');
			blockclass_cache();
		}
		updatecache('custominfo');
	}
	if($operation == 'access') {
		updatecache('ipctrl');
	}
	if($operation == 'styles') {
		updatecache('styles');
	}
	if(isset($settingnew['domainwhitelist'])) {
		updatecache('domainwhitelist');
	}
	if(isset($settingnew['modreasons'])) {
		updatecache('modreasons');
	}
	if(isset($settingnew['groupstatus'])) {
		updatecache('heats');
	}
	if(isset($settingnew['antitheftsetting'])) {
		updatecache('antitheft');
	}
}