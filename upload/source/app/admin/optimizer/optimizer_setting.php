<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace admin;
use table_common_setting;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class optimizer_setting {

	private $setting;

	public function __construct() {
		include_once DISCUZ_ROOT.'./source/i18n/'.currentlang().'/lang_optimizer.php';
		$this->setting = [
			'cacheindexlife' => [
				'initvalue' => '0',
				'optimizedvalue' => '900',
				'title' => $lang['optimizer_setting_cache_index'],
				'description' => $lang['optimizer_setting_cache_index_desc'],
				'optimizerdesc' => $lang['optimizer_setting_cache_optimize_desc'],
			],
			'cachethreadlife' => [
				'initvalue' => '0',
				'optimizedvalue' => '900',
				'title' => $lang['optimizer_setting_cache_post'],
				'description' => $lang['optimizer_setting_cache_post_desc'],
				'optimizerdesc' => $lang['optimizer_setting_cache_post_optimize_desc'],
			],
			'optimizeviews' => [
				'initvalue' => '0',
				'optimizedvalue' => '1',
				'title' => $lang['optimizer_setting_optimizeviews'],
				'description' => $lang['optimizer_setting_optimizeviews_desc'],
				'optimizerdesc' => $lang['optimizer_setting_optimizeviews_optimize_desc'],
			],
			'delayviewcount' => [
				'initvalue' => '0',
				'optimizedvalue' => '1',
				'title' => $lang['optimizer_setting_delayviewcount'],
				'description' => $lang['optimizer_setting_delayviewcount_desc'],
				'optimizerdesc' => $lang['optimizer_setting_delayviewcount_optimize_desc'],
			],
			'preventrefresh' => [
				'initvalue' => '0',
				'optimizedvalue' => '1',
				'title' => $lang['optimizer_setting_preventrefresh'],
				'description' => $lang['optimizer_setting_preventrefresh_desc'],
				'optimizerdesc' => $lang['optimizer_setting_preventrefresh_optimize_desc'],
			],
			'nocacheheaders' => [
				'initvalue' => '1',
				'optimizedvalue' => '0',
				'title' => $lang['optimizer_setting_nocacheheaders'],
				'description' => $lang['optimizer_setting_nocacheheaders_desc'],
				'optimizerdesc' => $lang['optimizer_setting_nocacheheaders_optimize_desc'],
			],
			'jspath' => [
				'initvalue' => 'static/js/',
				'optimizedvalue' => 'data/cache/',
				'title' => $lang['optimizer_setting_jspath'],
				'description' => $lang['optimizer_setting_jspath_desc'],
				'optimizerdesc' => $lang['optimizer_setting_jspath_optimize_desc'],
			],
			'lazyload' => [
				'initvalue' => '0',
				'optimizedvalue' => '1',
				'title' => $lang['optimizer_setting_lazyload'],
				'description' => $lang['optimizer_setting_lazyload_desc'],
				'optimizerdesc' => $lang['optimizer_setting_lazyload_optimize_desc'],
			],
			'sessionclose' => [
				'initvalue' => '0',
				'optimizedvalue' => '1',
				'title' => $lang['optimizer_setting_sessionclose'],
				'description' => $lang['optimizer_setting_sessionclose_desc'],
				'optimizerdesc' => $lang['optimizer_setting_sessionclose_optimize_desc'],
			],
			'rewriteguest' => [
				'initvalue' => '0',
				'optimizedvalue' => '1',
				'title' => $lang['optimizer_setting_rewriteguest'],
				'description' => $lang['optimizer_setting_rewriteguest_desc'],
				'optimizerdesc' => $lang['optimizer_setting_rewriteguest_optimize_desc'],
			],
			'chgusername' => [
				'key' => 'othertable',
				'initvalue' => '1',
				'optimizedvalue' => '0',
				'title' => $lang['optimizer_setting_chgusername_othertable'],
				'description' => $lang['optimizer_setting_chgusername_othertable_desc'],
				'optimizerdesc' => $lang['optimizer_setting_chgusername_othertable_optimize_desc'],
			]
		];
	}

	public function check() {
		$count = 0;
		$options = $this->get_option();
		foreach($options as $option) {
			if($option[4] == '1') {
				$count++;
			}
		}
		if($count) {
			$return = ['status' => 1, 'type' => 'view', 'lang' => lang('optimizer', 'optimizer_setting_need_optimizer', ['count' => $count])];
		} else {
			$return = ['status' => 0, 'type' => 'view', 'lang' => lang('optimizer', 'optimizer_setting_no_need')];
		}
		return $return;
	}

	public function optimizer() {
		$adminfile = defined(ADMINSCRIPT) ? ADMINSCRIPT : 'admin.php';
		dheader('Location: '.$_G['siteurl'].$adminfile.'?action=optimizer&operation=setting_optimizer&type=optimizer_setting&anchor=performance');
	}

	public function option_optimizer($options) {
		$update = [];
		foreach($options as $option) {
			if(isset($this->setting[$option])) {
				if(!empty($this->setting[$option]['key'])) {
					$value = table_common_setting::t()->fetch_setting($option);
					$update[$option] = dunserialize($value);
					$update[$option][$this->setting[$option]['key']] = $this->setting[$option]['optimizedvalue'];
				} else {
					$update[$option] = $this->setting[$option]['optimizedvalue'];
				}
			}
		}
		if($update) {
			table_common_setting::t()->update_batch($update);
			updatecache('setting');
		}
		return true;
	}

	public function get_option() {
		$return = [];
		$settings = table_common_setting::t()->fetch_all_setting(array_keys($this->setting));
		foreach($this->setting as $k => $setting) {
			if(!empty($setting['key'])) {
				$array = dunserialize($settings[$k]);
				$value = $array[$setting['key']];
			} else {
				$value = $settings[$k];
			}
			if($value == $setting['initvalue']) {
				$return[] = [$k, $setting['title'], $setting['description'], $setting['optimizerdesc'], '1'];
			} else {
				$return[] = [$k, $setting['title'], $setting['description'], $setting['optimizerdesc'], '0'];
			}
		}
		return $return;
	}

}

