<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_myrepeats {

	var $value = [];

	function __construct() {
		global $_G;
		if(!$_G['uid']) {
			return;
		}

		$myrepeatsusergroups = (array)dunserialize($_G['cache']['plugin']['myrepeats']['usergroups']);
		if(in_array('', $myrepeatsusergroups)) {
			$myrepeatsusergroups = [];
		}
		$userlist = [];
		if(!in_array($_G['groupid'], $myrepeatsusergroups)) {
			if(!isset($_G['cookie']['myrepeat_rr'])) {
				$users = count(myrepeats\table_myrepeats::t()->fetch_all_by_username($_G['username']));
				dsetcookie('myrepeat_rr', 'R'.$users, 86400);
			} else {
				$users = substr($_G['cookie']['myrepeat_rr'], 1);
			}
			if(!$users) {
				return '';
			}
		}

		$this->value['global_usernav_extra1'] = '<script>'.
			'function showmyrepeats() {if(!$(\'myrepeats_menu\')) {'.
			'menu=document.createElement(\'div\');menu.id=\'myrepeats_menu\';menu.style.display=\'none\';menu.className=\'p_pop\';'.
			'$(\'append_parent\').appendChild(menu);'.
			'ajaxget(\'plugin.php?id=myrepeats:switch&list=yes\',\'myrepeats_menu\',\'ajaxwaitid\');}'.
			'showMenu({\'ctrlid\':\'myrepeats\',\'duration\':2});}'.
			'</script>'.
			'<span class="pipe">|</span><a id="myrepeats" href="home.php?mod=spacecp&ac=plugin&id=myrepeats:memcp" class="showmenu cur1" onmouseover="delayShow(this, showmyrepeats)">'.lang('plugin/myrepeats', 'switch').'</a>'."\n";
	}

	function global_usernav_extra1() {
		return $this->value['global_usernav_extra1'];
	}

}

