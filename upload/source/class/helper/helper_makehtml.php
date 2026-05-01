<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class helper_makehtml {

	public static $callback;

	public static $callbackdata;

	public static $htmlfilename;

	public static $returndata = [];

	public static $viewurl;

	public static function make_html() {
		global $_G;
		if(self::$htmlfilename) {
			$filepath = DISCUZ_ROOT_STATIC.'/'.self::$htmlfilename.'.'.$_G['setting']['makehtml']['extendname'];
			dmkdir(dirname($filepath));
			$cend = '</body></html>';
			$code = ob_get_clean().$cend;
			$code = preg_replace('/language\s*=[\s|\'|\"]*php/is', '_', $code);
			$code = str_replace(['<?', '?>'], ['&lt;?', '?&gt;'], $code);
			if(file_put_contents($filepath, $code) !== false) {
				$_G['gzipcompress'] ? ob_start('ob_gzhandler') : ob_start();
				if(self::$callback && is_callable(self::$callback)) {
					call_user_func(self::$callback);
					self::$callback = self::$callbackdata = null;
				}
				self::$returndata['status'] = 'html_ok';
				if(isset(self::$viewurl)) {
					self::$returndata['path'] = self::$viewurl;
				} else {
					self::$returndata['path'] = self::$htmlfilename.'.'.$_G['setting']['makehtml']['extendname'];
				}
				showmessage('do_success', null, self::$returndata);
			}
		}
	}

	public static function portal_index() {
		global $_G;
		if(!empty($_G['setting']['makehtml']['flag'])) {
			$_G['dynamicurl'] = 'portal.php';
			self::$htmlfilename = $_G['setting']['makehtml']['indexname'] ? $_G['setting']['makehtml']['indexname'] : 'index';
		}
	}

	public static function portal_list($cat) {
		global $_G;
		if(!empty($_G['setting']['makehtml']['flag']) && $cat['fullfoldername']) {
			$_G['dynamicurl'] = 'portal.php?mod=list&catid='.$cat['catid'];
			self::$htmlfilename = $cat['fullfoldername'].'/index';
		} else if(!empty($_G['setting']['makehtml']['flag']) && !$cat['fullfoldername']) {
			showmessage('portal_category_has_no_folder_name');
		}
	}

	public static function portal_article($cat, $article, $page) {
		global $_G;
		if(!empty($_G['setting']['makehtml']['flag']) && $cat['fullfoldername']) {
			$_G['dynamicurl'] = 'portal.php?mod=view&aid='.$article['aid'];
			self::$callbackdata['data'] = [];
			if(!$article['htmlmade']) {
				self::$callbackdata['data']['htmlmade'] = 1;
			}
			if(!$article['htmlname']) {
				self::$callbackdata['data']['htmlname'] = $article['htmlname'] = str_pad($article['aid'], 8, '0', STR_PAD_LEFT);
			}

			$htmldir = self::fetch_dir($cat['fullfoldername'], $article['timestamp']);
			if($article['htmldir'] != $htmldir) {
				self::$callbackdata['data']['htmldir'] = $htmldir;
			}
			if($article['contents'] > 1 && $page > 1) {
				$article['htmlname'] = $article['htmlname'].$page;
			}
			if($article['contents'] > $page) {
				self::$returndata['nexturl'] = "portal.php?mod=view&aid={$article['aid']}&page=".(++$page);
				self::$returndata['current'] = $page;
				self::$returndata['count'] = $article['contents'];
			}

			if(!empty($cat['topid'])) {
				$caturl = $_G['cache']['portalcategory'][$cat['topid']]['domain'] ? $_G['cache']['portalcategory'][$cat['topid']]['caturl'] : '';
				self::$viewurl = $caturl.$article['htmldir'].$article['htmlname'].'.'.$_G['setting']['makehtml']['extendname'];
			}

			self::$htmlfilename = $htmldir.$article['htmlname'];
			if(self::$callbackdata['data']) {
				self::$callback = [get_class(), 'portal_article_success'];
				self::$callbackdata['id'] = $article['aid'];
			}
			if($article['allowcomment']) {
				$_G['htmlcheckupdate'] = '1';
			}
		} else if(!empty($_G['setting']['makehtml']['flag']) && !$cat['fullfoldername']) {
			showmessage('portal_category_has_no_folder_name');
		}
	}

	public static function fetch_dir($folder, $time) {
		global $_G;
		$formatarr = ['/Ym/', '/Ym/d/', '/Y/m/', '/Y/m/d/'];
		$htmldirformat = $formatarr[$_G['setting']['makehtml']['htmldirformat']] ?? $formatarr[0];
		$htmldir = $folder.dgmdate($time, $htmldirformat);
		if(!empty($_G['setting']['makehtml']['articlehtmldir'])) {
			$htmldir = $_G['setting']['makehtml']['articlehtmldir'].'/'.$htmldir;
		}
		return $htmldir;
	}

	public static function portal_article_success() {
		if(!empty(self::$callbackdata['data'])) {
			table_portal_article_title::t()->update(self::$callbackdata['id'], self::$callbackdata['data']);
		}

	}

	public static function portal_topic($topic) {
		global $_G;
		if(!empty($_G['setting']['makehtml']['flag']) && !empty($_G['setting']['makehtml']['topichtmldir']) && $topic['name']) {
			$_G['dynamicurl'] = 'portal.php?mod=topic&topicid='.$topic['topicid'];
			self::$callbackdata['data'] = [];
			if(!$topic['htmlmade']) {
				self::$callbackdata['data']['htmlmade'] = 1;
			}

			if($topic['htmldir'] != $_G['setting']['makehtml']['topichtmldir']) {
				self::$callbackdata['data']['htmldir'] = $_G['setting']['makehtml']['topichtmldir'];
			}
			self::$htmlfilename = $_G['setting']['makehtml']['topichtmldir'].'/'.$topic['name'];
			if(self::$callbackdata['data']) {
				self::$callback = [get_class(), 'portal_topic_success'];
				self::$callbackdata['id'] = $topic['topicid'];
			}
			if($topic['allowcomment']) {
				$_G['htmlcheckupdate'] = '1';
			}
		}
	}


	public static function portal_topic_success() {
		if(!empty(self::$callbackdata['data'])) {
			table_portal_topic::t()->update(self::$callbackdata['id'], self::$callbackdata['data']);
		}

	}

}

