<?php exit('Access Denied'); ?>
<div class="dbox">
	<div class="boxheader">
		{lang home_todo}
	</div>
	<div class="boxbody">
		<div class="todo">
			<!--{if $membersmod}-->
			<em>{lang home_mod_members}<span><a href="{ADMINSCRIPT}?action=moderate&operation=members">$membersmod</a></span></em>
			<!--{/if}-->
			<!--{if $threadsmod}-->
			<em>{lang home_mod_threads}<span><a href="{ADMINSCRIPT}?action=moderate&operation=threads&dateline=all">$threadsmod</a></span></em>
			<!--{/if}-->
			<!--{if $postsmod}-->
			<em>{lang home_mod_posts}<span><a href="{ADMINSCRIPT}?action=moderate&operation=replies&dateline=all">$postsmod</a></span></em>
			<!--{/if}-->
			<!--{if $medalsmod}-->
			<em>{lang home_mod_medals}<span><a href="{ADMINSCRIPT}?action=medals&operation=mods">$medalsmod</a></span></em>
			<!--{/if}-->
			<!--{if $groupmod}-->
			<em>{lang home_mod_wait}<span><a href="{ADMINSCRIPT}?action=group&operation=mod">$groupmod</a></span></em>
			<!--{/if}-->
			<!--{if $blogsmod}-->
			<em>{lang home_mod_blogs}<span><a href="{ADMINSCRIPT}?action=moderate&operation=blogs&dateline=all">$blogsmod</a></span></em>
			<!--{/if}-->
			<!--{if $doingsmod}-->
			<em>{lang home_mod_doings}<span><a href="{ADMINSCRIPT}?action=moderate&operation=doings&dateline=all">$doingsmod</a></span></em>
			<!--{/if}-->
			<!--{if $picturesmod}-->
			<em>{lang home_mod_pictures}<span><a href="{ADMINSCRIPT}?action=moderate&operation=pictures&dateline=all">$picturesmod</a></span></em>
			<!--{/if}-->
			<!--{if $sharesmod}-->
			<em>{lang home_mod_shares}<span><a href="{ADMINSCRIPT}?action=moderate&operation=shares&dateline=all">$sharesmod</a></span></em>
			<!--{/if}-->
			<!--{if $commentsmod}-->
			<em>{lang home_mod_comments}<span><a href="{ADMINSCRIPT}?action=moderate&operation=comments&dateline=all">$commentsmod</a></span></em>
			<!--{/if}-->
			<!--{if $articlesmod}-->
			<em>{lang home_mod_articles}<span><a href="{ADMINSCRIPT}?action=moderate&operation=articles&dateline=all">$articlesmod</a></span></em>
			<!--{/if}-->
			<!--{if $articlecommentsmod}-->
			<em>{lang home_mod_articlecomments}<span><a href="{ADMINSCRIPT}?action=moderate&operation=articlecomments&dateline=all">$articlecommentsmod</a></span></em>
			<!--{/if}-->
			<!--{if $topiccommentsmod}-->
			<em>{lang home_mod_topiccomments}<span><a href="{ADMINSCRIPT}?action=moderate&operation=topiccomments&dateline=all">$topiccommentsmod</a></span></em>
			<!--{/if}-->
			<!--{if $reportcount}-->
			<em>{lang home_mod_reports}<span><a href="{ADMINSCRIPT}?action=report">$reportcount</a></span></em>
			<!--{/if}-->
			<!--{if $threadsdel}-->
			<em>{lang home_del_threads}<span><a href="{ADMINSCRIPT}?action=recyclebin">$threadsdel</a></span></em>
			<!--{/if}-->
			<!--{loop $verify $n $row}-->
			<em>{lang home_mod_verify_prefix}{$row[0]}<span><a href="{ADMINSCRIPT}?action=verify&operation=verify&do={$n}">{$row[1]}</a></span></em>
			<!--{/loop}-->
			<!--{if $errcredits}-->
			<em>{lang home_err_credits}<span><a href="{ADMINSCRIPT}?action=logs&operation=credit&srch_operation=ERR" style="color: red">{$errcredits}</a></span></em>
			<!--{/if}-->
		</div>
	</div>
</div>