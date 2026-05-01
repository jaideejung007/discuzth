<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once libfile('function/attachment');
$operation == 'group' && $_GET['inforum'] = 'isgroup';
$inforum = $_GET['inforum'] != 'all' && $_GET['inforum'] != 'isgroup' ? intval($_GET['inforum']) : $_GET['inforum'];
$authorid = $_GET['author'] ? table_common_member::t()->fetch_uid_by_username($_GET['author']) : 0;
$authorid = $_GET['author'] && !$authorid ? table_common_member_archive::t()->fetch_uid_by_username($_GET['author']) : $authorid;
$attachments = '';
$attachuids = $attachusers = [];
$_GET['perpage'] = intval($_GET['perpage']) < 1 ? 20 : intval($_GET['perpage']);
$perpage = ($_GET['pp'] ? $_GET['pp'] : $_GET['perpage']) / 10;
$attachmentcount = 0;
for($attachi = 0; $attachi < 10; $attachi++) {
	$attachmentarray = [];
	$attachmentcount += table_forum_attachment::t()->fetch_all_for_manage($attachi, $inforum, $authorid, $_GET['filename'], $_GET['keywords'], $_GET['sizeless'], $_GET['sizemore'], $_GET['dlcountless'], $_GET['dlcountmore'], $_GET['daysold'], 1);
	$query = table_forum_attachment::t()->fetch_all_for_manage($attachi, $inforum, $authorid, $_GET['filename'], $_GET['keywords'], $_GET['sizeless'], $_GET['sizemore'], $_GET['dlcountless'], $_GET['dlcountmore'], $_GET['daysold'], 0, (($page - 1) * $perpage), $perpage);
	foreach($query as $attachment) {
		$attachuids[$attachment['uid']] = $attachment['uid'];
		$attachmentarray[] = $attachment;
	}
	$attachusers += table_common_member::t()->fetch_all($attachuids);

	foreach($attachmentarray as $attachment) {
		if(!$attachment['remote']) {
			$matched = file_exists($_G['setting']['attachdir'].'/forum/'.$attachment['attachment']) ? '' : cplang('attach_lost');
			$attachment['url'] = $_G['setting']['attachurl'].'forum/';
		} else {
			@set_time_limit(0);
			if(@fclose(@fopen($_G['setting']['ftp']['attachurl'].'forum/'.$attachment['attachment'], 'r'))) {
				$matched = '';
			} else {
				$matched = cplang('attach_lost');
			}
			$attachment['url'] = $_G['setting']['ftp']['attachurl'].'forum/';
		}
		$attachsize = sizecount($attachment['filesize']);
		if(!$_GET['nomatched'] || ($_GET['nomatched'] && $matched)) {
			$attachment['url'] = trim($attachment['url'], '/');
			$attachments .= showtablerow('', ['class="td25"', 'title="'.$attachment['description'].'" class="td21"'], [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$attachment['aid']}\" />",
				$attachment['remote'] ? "<span class=\"diffcolor3\">{$attachment['filename']}" : $attachment['filename'],
				$attachusers[$attachment['uid']]['username'],
				"<a href=\"forum.php?mod=viewthread&tid={$attachment['tid']}\" target=\"_blank\">".cutstr($attachment['subject'], 20).'</a>',
				$attachsize,
				$attachment['downloads'],
				$matched ? "<em class=\"error\">$matched<em>" : "<a href=\"forum.php?mod=attachment&aid=".aidencode($attachment['aid'])."&noupdate=yes\" target=\"_blank\" class=\"act nomargin\">{$lang['download']}</a>"
			], TRUE);
		}
	}
}

$multipage = '<div class="cuspages right"><div class="pg">'.
	($page > 1 ? '<a href="javascript:page('.($page - 1).')" class="nxt">&lsaquo;&lsaquo;</a>' : '').
	'<a href="javascript:page('.($page + 1).')" class="nxt">&rsaquo;&rsaquo;</a>'.
	'</div></div>';

echo <<<EOT
<script type="text/JavaScript">
	function page(number) {
		$('attachmentforum').page.value=number;
		$('attachmentforum').searchsubmit.click();
	}
</script>
EOT;
showtagheader('div', 'admin', $searchsubmit);
showformheader('attach'.($operation ? '&operation='.$operation : ''), '', 'attachmentforum');
showhiddenfields([
	'page' => $page,
	'nomatched' => $_GET['nomatched'],
	'inforum' => $_GET['inforum'],
	'sizeless' => $_GET['sizeless'],
	'sizemore' => $_GET['sizemore'],
	'dlcountless' => $_GET['dlcountless'],
	'dlcountmore' => $_GET['dlcountmore'],
	'daysold' => $_GET['daysold'],
	'filename' => $_GET['filename'],
	'keywords' => $_GET['keywords'],
	'author' => $_GET['author'],
	'pp' => $_GET['pp'] ? $_GET['pp'] : $_GET['perpage']
]);
echo '<input type="submit" name="searchsubmit" value="'.cplang('submit').'" class="btn" style="display: none" />';
showformfooter();

showformheader('attach&frame=no'.($operation ? '&operation='.$operation : ''), 'target="attachmentframe"');
showboxheader();
showtableheader();
showsubtitle(['', 'filename', 'author', 'attach_thread', 'size', 'attach_downloadnums', '']);
echo $attachments;
showsubmit('deletesubmit', 'submit', 'del', '<a href="###" onclick="$(\'admin\').style.display=\'none\';$(\'search\').style.display=\'\';$(\'attachmentforum\').pp.value=\'\';$(\'attachmentforum\').page.value=\'\';" class="act lightlink normal">'.cplang('research').'</a>', $multipage);
showtablefooter();
showboxfooter();
showformfooter();
echo '<iframe name="attachmentframe" style="display:none"></iframe>';
showtagfooter('div');
		