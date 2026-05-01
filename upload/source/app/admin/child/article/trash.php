<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('batchsubmit', true)) {
	$_POST['optype'] = empty($_POST['optype']) ? $_GET['optype'] : $_POST['optype'];
	if(empty($_POST['ids']) && $_POST['optype'] != 'clear') {
		cpmsg('article_choose_at_least_one_article', 'action=article&operation=trash', 'error');
	}

	if($_POST['optype'] == 'recover') {

		$inserts = $ids = $catids = [];
		foreach(table_portal_article_trash::t()->fetch_all($_POST['ids']) as $value) {
			$ids[] = intval($value['aid']);
			$article = dunserialize($value['content']);
			$catids[] = intval($article['catid']);
			$inserts[] = [
				'aid' => $article['aid'],
				'catid' => $article['catid'],
				'uid' => $article['uid'],
				'username' => $article['username'],
				'title' => $article['title'],
				'url' => $article['url'],
				'summary' => $article['summary'],
				'pic' => $article['pic'],
				'id' => $article['id'],
				'idtype' => $article['idtype'],
				'contents' => $article['contents'],
				'dateline' => $article['dateline'],
				'thumb' => $article['thumb'],
				'remote' => $article['remote'],
				'click1' => $article['click1'],
				'click2' => $article['click2'],
				'click3' => $article['click3'],
				'click4' => $article['click4'],
				'click5' => $article['click5'],
				'click6' => $article['click6'],
				'click7' => $article['click7'],
				'click8' => $article['click8'],
				'author' => $article['author'],
				'from' => $article['from'],
				'fromurl' => $article['fromurl'],
				'bid' => $article['bid'],
				'allowcomment' => $article['allowcomment'],
				'tags' => $article['tags'],
				'owncomment' => $article['owncomment'],
				'status' => $article['status'],
				'highlight' => $article['highlight'],
				'showinnernav' => $article['showinnernav'],
				'preaid' => $article['preaid'],
				'nextaid' => $article['nextaid'],
				'htmlmade' => $article['htmlmade'],
				'htmlname' => $article['htmlname'],
				'htmldir' => $article['htmldir'],
			];
		}

		if($inserts) {
			foreach($inserts as $data) {
				table_portal_article_title::t()->insert($data, 0, 1);
			}
			table_portal_article_trash::t()->delete($ids);
		}

		$catids = array_unique($catids);
		if($catids) {
			foreach($catids as $catid) {
				$cnt = table_portal_article_title::t()->fetch_count_for_cat($catid);
				table_portal_category::t()->update($catid, ['articles' => dintval($cnt)]);
			}
		}
		cpmsg('article_trash_recover_succeed', 'action=article&operation=trash', 'succeed');

	} elseif($_POST['optype'] == 'delete') {

		require_once libfile('function/delete');
		deletetrasharticle($_POST['ids']);
		cpmsg('article_trash_delete_succeed', 'action=article&operation=trash', 'succeed');

	} elseif($_POST['optype'] == 'clear') {
		$aids = [];
		foreach(table_portal_article_trash::t()->range(50) as $value) {
			$aids[$value['aid']] = $value['aid'];
		}
		if(!empty($aids)) {
			require_once libfile('function/delete');
			deletetrasharticle($aids);
			cpmsg('article_trash_is_clearing', 'action=article&operation=trash&optype=clear&batchsubmit=yes&formhash='.FORMHASH);
		} else {
			cpmsg('article_trash_is_empty', 'action=article');
		}
	} else {
		cpmsg('article_choose_at_least_one_operation', 'action=article&operation=trash', 'error');
	}

} else {

	$perpage = 50;

	$start = ($page - 1) * $perpage;

	$mpurl .= '&perpage='.$perpage;
	$perpages = [$perpage => ' selected'];

	$mpurl = ADMINSCRIPT.'?mod=portal&action=article&operation='.$operation;

	showformheader('article&operation=trash');
	showtableheader('article_trash_list');
	showsubtitle(['', 'article_title', 'article_category', 'article_username', 'article_dateline']);

	$multipage = '';
	$count = table_portal_article_trash::t()->count();
	if($count) {
		foreach(table_portal_article_trash::t()->range($start, $perpage) as $value) {
			$value = dunserialize($value['content']);
			showtablerow('', ['class="td25"', 'class=""', 'class="td28"'], [
				"<input type=\"checkbox\" class=\"checkbox\" name=\"ids[]\" value=\"{$value['aid']}\">",
				$value['title'],
				$category[$value['catid']]['catname'],
				"<a href=\"home.php?mod=space&uid={$value['uid']}&do=profile\" target=\"_blank\">{$value['username']}</a>",
				dgmdate($value['dateline'])
			]);
		}
		$multipage = multi($count, $perpage, $page, $mpurl);
	}

	$batchradio = '<input type="radio" name="optype" value="recover" id="op_recover" class="radio" /><label for="op_recover">'.cplang('article_trash_recover').'</label>&nbsp;&nbsp;';
	$batchradio .= '<input type="radio" name="optype" value="delete" id="op_delete" class="radio" /><label for="op_delete">'.cplang('article_trash_delete').'</label>&nbsp;&nbsp;';
	$batchradio .= '<input type="radio" name="optype" value="clear" id="op_clear" class="radio" style="display:none;"/><input type="hidden" name="batchsubmit" value="yes" />';
	showsubmit('', '', '', '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'ids\')" /><label for="chkall">'.cplang('select_all').'</label>&nbsp;&nbsp;'
		.$batchradio.'<input type="submit" class="btn" name="batchbutton" value="'.cplang('submit').'" />
					<input type="button" class="btn" name="clearbutton" value="'.cplang('article_clear_trash').'" onclick="if(confirm(\''.cplang('article_clear_trash_confirm').'?\')){this.form.optype[2].checked=\'checked\';this.form.submit();}"/>', $multipage);
	showtablefooter();
	showformfooter();
}
	