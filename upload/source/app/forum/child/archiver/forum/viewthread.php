<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
include loadarchiver('common/header');
?>
	<div id="nav">
		<a href="./"><?php echo $_G['setting']['navs']['2']['navname']; ?></a> &rsaquo; <a
			href="?fid-<?php echo $_G['fid']; ?>.html"><?php echo $_G['forum']['name']; ?></a>
		&rsaquo; <?php echo $_G['forum_thread']['subject']; ?>
	</div>

	<div id="content">
		<?php foreach($postlist as $post): ?>
			<?php if($hiddenreplies && !$post['first']) break; ?>
			<p class="author">
				<?php if(!$post['anonymous']): ?>
					<strong><?php echo $post['author']; ?></strong>
				<?php else: ?>
					<strong><i>Anonymous</i></strong>
				<?php endif; ?>
				<?php echo lang('forum/archiver', 'post_time').' '.$post['dateline']; ?>
			</p>
			<?php if($_G['forum_threadpay'] && $post['first']): include template('forum/viewthread_pay'); ?>
			<?php elseif(!$_G['forum']['ismoderator'] && $_G['setting']['bannedmessages'] & 1 && (($post['authorid'] && !$post['username']) || ($_G['thread']['digest'] == 0 && ($post['groupid'] == 4 || $post['groupid'] == 5 || $post['memberstatus'] == '-1')))): ?>
			<?php elseif($post['status'] & 1): ?>
			<?php else: ?>
				<h3><?php echo $post['subject']; ?></h3>
				<?php echo archivermessage($post['message']); ?>
			<?php endif; ?>
		<?php endforeach; ?>
		<div class="page">
			<?php echo arch_multi($_G['forum_thread']['replies'] + 1, $_G['ppp'], $page, "?tid-{$_G['tid']}.html"); ?>
		</div>
	</div>

	<div id="end">
		<?php echo lang('forum/archiver', 'full_version'); ?>:
		<a href="../<?php echo rewriterulecheck('forum_viewthread') ? rewriteoutput('forum_viewthread', 1, '', $_G['tid'], $page) : 'forum.php?mod=viewthread&tid='.$_G['tid'].'&page='.$page; ?>"
		   target="_blank"><strong><?php echo $_G['forum_thread']['subject']; ?></strong></a>
	</div>
<?php include loadarchiver('common/footer');

function archivermessage($message) {
	if(str_contains($message, '[/password]')) {
		return '';
	}
	return nl2br(preg_replace(
		['/&amp;(#\d{3,5};)/', '/\[hide=?\d*\](.*?)\[\/hide\]/is', "/\[postbg\]\s*([^\[\<\r\n;'\"\?\(\)]+?)\s*\[\/postbg\]/is", '/\[\/?\w+=?.*?\]/'],
		['&\\1', '<b>**** Hidden Message *****</b>', ''],
		str_replace(
			['&', '"', '<', '>', "\t", '   ', '', '  '],
			['&amp;', '&quot;', '&lt;', '&gt;', '&nbsp; &nbsp; &nbsp; &nbsp; ', '&nbsp; &nbsp;', '&nbsp;&nbsp;'],
			$message)));
}

?>