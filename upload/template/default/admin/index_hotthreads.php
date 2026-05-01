<?php exit('Access Denied'); ?>
<div class="dbox">
	<div class="boxheader">
		<a href="misc.php?mod=ranklist&type=thread&view=replies&orderby=thisweek" target="_blank" style="float: right">{lang stats_more}</a>
		{lang ranklist_hotthread}
	</div>
	<table class="tb tb2 hotthread">
		<!--{loop $threadlist $thread}-->
		<tr>
			<td style="padding-left: 10px;">
				<em class="ico"></em><a href="forum.php?mod=viewthread&tid=$thread['tid']" target="_blank">{$thread['subject']}</a>
			</td>
			<td class="td24">
				{$thread['dateline']}
			</td>
		</tr>
		<!--{/loop}-->
	</table>
</div>