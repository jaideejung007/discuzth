<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

shownav('topic', 'nav_remoderate');
showsubmenu('nav_remoderate');
/*search={"nav_remoderate":"action=remoderate"}*/
showtips('remoderate_tips');
/*search*/
showformheader('remoderate');
showtableheader();
showsubtitle(['', 'remoderate_amount']);
showhiddenfields(['pertask' => '']);

// 主题/帖子标题及内容重新审核
showtablerow('', ['class="td31 bold"'], [
	"{$lang['remoderate_thread']}:",
	'<input name="pertask1" type="text" class="txt" value="100" /><input type="submit" class="btn" name="threadsubmit" onclick="this.form.pertask.value=this.form.pertask1.value" value="'.$lang['submit'].'" />'
]);
// 日志标题及内容重新审核
showtablerow('', ['class="td31 bold"'], [
	"{$lang['remoderate_blog']}:",
	'<input name="pertask2" type="text" class="txt" value="100" /><input type="submit" class="btn" name="blogsubmit" onclick="this.form.pertask.value=this.form.pertask2.value" value="'.$lang['submit'].'" />'
]);
// 图片标题重新审核
showtablerow('', ['class="td31 bold"'], [
	"{$lang['remoderate_pic']}:",
	'<input name="pertask3" type="text" class="txt" value="100" /><input type="submit" class="btn" name="picsubmit" onclick="this.form.pertask.value=this.form.pertask3.value" value="'.$lang['submit'].'" />'
]);
// 记录内容重新审核
showtablerow('', ['class="td31 bold"'], [
	"{$lang['remoderate_doing']}:",
	'<input name="pertask4" type="text" class="txt" value="100" /><input type="submit" class="btn" name="doingsubmit" onclick="this.form.pertask.value=this.form.pertask4.value" value="'.$lang['submit'].'" />'
]);
// 分享内容重新审核
showtablerow('', ['class="td31 bold"'], [
	"{$lang['remoderate_share']}:",
	'<input name="pertask5" type="text" class="txt" value="100" /><input type="submit" class="btn" name="sharesubmit" onclick="this.form.pertask.value=this.form.pertask5.value" value="'.$lang['submit'].'" />'
]);
// 家园评论内容重新审核
showtablerow('', ['class="td31 bold"'], [
	"{$lang['remoderate_comment']}:",
	'<input name="pertask6" type="text" class="txt" value="100" /><input type="submit" class="btn" name="commentsubmit" onclick="this.form.pertask.value=this.form.pertask6.value" value="'.$lang['submit'].'" />'
]);
// 文章标题及内容重新审核
showtablerow('', ['class="td31 bold"'], [
	"{$lang['remoderate_article']}:",
	'<input name="pertask7" type="text" class="txt" value="100" /><input type="submit" class="btn" name="articlesubmit" onclick="this.form.pertask.value=this.form.pertask7.value" value="'.$lang['submit'].'" />'
]);
// 文章评论内容重新审核
showtablerow('', ['class="td31 bold"'], [
	"{$lang['remoderate_articlecomment']}:",
	'<input name="pertask8" type="text" class="txt" value="100" /><input type="submit" class="btn" name="articlecommentsubmit" onclick="this.form.pertask.value=this.form.pertask8.value" value="'.$lang['submit'].'" />'
]);
// 专题评论内容重新审核
showtablerow('', ['class="td31 bold"'], [
	"{$lang['remoderate_topiccomment']}:",
	'<input name="pertask9" type="text" class="txt" value="100" /><input type="submit" class="btn" name="topiccommentsubmit" onclick="this.form.pertask.value=this.form.pertask9.value" value="'.$lang['submit'].'" />'
]);

showtablefooter();
showformfooter();
	