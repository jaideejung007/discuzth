<?php exit('Access Denied');?>
<div id="threadlist" class="tl bm bmw"{if $_G['uid']} style="position: relative;"{/if}>
	<!--{if $quicksearchlist && !$_GET['archiveid']}-->
		<!--{subtemplate forum/search_sortoption}-->
	<!--{/if}-->
	<div class="dz_list_top">
		<!--{if CURMODULE != 'guide'}-->
			<!--{if !empty($_G['forum']['picstyle'])}-->
				<a{if empty($_G['cookie']['forumdefstyle'])} href="forum.php?mod=forumdisplay&fid=$_G['fid']&forumdefstyle=yes" class="chked kmy"{else} href="forum.php?mod=forumdisplay&fid=$_G['fid']&forumdefstyle=no" class="unchk"{/if} title="{lang view_thread_imagemode}{lang view_thread}">{lang view_thread_imagemode}</a>
			<!--{/if}-->
			<span id="atarget" {if isset($_G['cookie']['atarget']) && $_G['cookie']['atarget'] > 0}onclick="setatarget(-1)" class="y atarget_1"{else}onclick="setatarget(1)" class="y"{/if} title="{lang new_window_thread}">{lang new_window}</span>
			<a id="filter_kmsj" href="javascript:;" class="showmenu kmy{if $_GET['dateline']} kmon{/if}" onmouseover="showMenu(this.id)">{if !$_GET['dateline']}{lang all}{lang search_any_date}{elseif $_GET['dateline'] == '86400'}{lang last_1_days}{elseif $_GET['dateline'] == '172800'}{lang last_2_days}{elseif $_GET['dateline'] == '604800'}{lang list_one_week}{elseif $_GET['dateline'] == '2592000'}{lang list_one_month}{elseif $_GET['dateline'] == '7948800'}{lang list_three_month}{else}{lang tmp081}{/if}</a>
			<a id="filter_kmpx" href="javascript:;" class="showmenu kmy{if $_GET['dateline']} kmon{/if}" onmouseover="showMenu(this.id)">{if $_GET['orderby'] == 'dateline'}{lang list_post_time}{elseif $_GET['orderby'] == 'replies'}{lang replies}{elseif $_GET['orderby'] == 'views'}{lang views}{else}{lang tmp082}{/if}</a>
			<a id="filter_special" href="javascript:;" class="showmenu kmy{if $_GET['specialtype']} kmon{/if}" onmouseover="showMenu(this.id)"><!--{if $_GET['specialtype'] == 'poll'}-->{lang thread_poll}<!--{elseif $_GET['specialtype'] == 'trade'}-->{lang thread_trade}<!--{elseif $_GET['specialtype'] == 'reward'}-->{lang thread_reward}<!--{elseif $_GET['specialtype'] == 'activity'}-->{lang thread_activity}<!--{elseif $_GET['specialtype'] == 'debate'}-->{lang thread_debate}<!--{else}-->{lang threads_all}<!--{/if}--></a>
			<a href="forum.php?mod=forumdisplay&fid=$_G['fid']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}" class="kmico kmall{if !$_GET['filter']} kmon{/if}">{lang all}</a><span class="pipe z kmall">|</span>
			<a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=lastpost&orderby=lastpost$forumdisplayadd['lastpost']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}" class="kmico kmzx{if isset($_GET['filter']) && $_GET['filter'] == 'lastpost'} kmon{/if}">{lang latest}</a><span class="pipe z">|</span>
			<a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=heat&orderby=heats{$forumdisplayadd['heat'] or ''}{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}" class="kmico kmrm{if isset($_GET['filter']) && $_GET['filter'] == 'heat'} kmon{/if}">{lang order_heats}</a><span class="pipe z">|</span>
			<a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=hot" class="kmico kmrt{if isset($_GET['filter']) && $_GET['filter'] == 'hot'} kmon{/if}">{lang hot_thread}</a><span class="pipe z">|</span>
			<a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=digest&digest=1$forumdisplayadd['digest']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}" class="kmico kmjh{if isset($_GET['filter']) && $_GET['filter'] == 'digest'} kmon{/if}">{lang digest_posts}</a>
			<!--{hook/forumdisplay_filter_extra}-->
			<!--{if isset($_GET['filter']) && $_GET['filter'] == 'hot'}-->
				<span class="pipe z">|</span>
				<script type="text/javascript" src="{$_G['setting']['jspath']}calendar.js?{VERHASH}"></script>
				<span class="xg1" style="font-size:12px">$ctime</span>
				<em class="fico-search cur1 xi2 vm" alt="" id="hottime" value="$ctime" fid="$_G['fid']" onclick="showcalendar(event, this, false, false, false, false, function(){viewhot(this);});"></em>
			<!--{/if}-->
		<!--{else}-->
			{lang title}
		<!--{/if}-->
  	</div>
	<div class="bm_c">
		<!--{if empty($_G['forum']['picstyle']) || $_G['cookie']['forumdefstyle']}-->
			<script type="text/javascript">var lasttime = $_G['timestamp'];var listcolspan= '{if !$_GET['archiveid'] && $_G['forum']['ismoderator']}6{else}5{/if}';</script>
		<!--{/if}-->
		<div id="forumnew" style="display:none"></div>
		<form method="post" autocomplete="off" name="moderate" id="moderate" action="forum.php?mod=topicadmin&action=moderate&fid=$_G[fid]&infloat=yes&nopost=yes">
			<input type="hidden" name="formhash" value="{FORMHASH}" />
			<input type="hidden" name="listextra" value="$extra" />
			<!--{template forum/forumdisplay_list_box}-->	
			<!--{if $_G['forum']['ismoderator'] && $_G['forum_threadcount']}-->
				<!--{template forum/topicadmin_modlayer}-->
			<!--{/if}-->
		</form>
	</div>
	<!--{hook/forumdisplay_threadlist_bottom}-->
</div>
<!--{if !IS_ROBOT}-->
<div id="filter_special_menu" class="p_pop" style="display:none" change="location.href='forum.php?mod=forumdisplay&fid=$_G['fid']&filter='+$('filter_special').value">
		<ul>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang all}{lang forum_threads}</a></li>
			<!--{if $showpoll}--><li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=specialtype&specialtype=poll$forumdisplayadd['specialtype']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang thread_poll}</a></li><!--{/if}-->
			<!--{if $showtrade}--><li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=specialtype&specialtype=trade$forumdisplayadd['specialtype']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang thread_trade}</a></li><!--{/if}-->
			<!--{if $showreward}--><li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=specialtype&specialtype=reward$forumdisplayadd['specialtype']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang thread_reward}</a></li><!--{/if}-->
			<!--{if $showactivity}--><li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=specialtype&specialtype=activity$forumdisplayadd['specialtype']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang thread_activity}</a></li><!--{/if}-->
			<!--{if $showdebate}--><li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=specialtype&specialtype=debate$forumdisplayadd['specialtype']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang thread_debate}</a></li><!--{/if}-->
		</ul>
	</div>
	<div id="filter_reward_menu" class="p_pop" style="display:none" change="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=specialtype&specialtype=reward$forumdisplayadd['specialtype']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}&rewardtype='+$('filter_reward').value">
		<ul>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=specialtype&specialtype=reward$forumdisplayadd['specialtype']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang all_reward}</a></li>
			<!--{if $showpoll}--><li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=specialtype&specialtype=reward$forumdisplayadd['specialtype']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}&rewardtype=1">{lang rewarding}</a></li><!--{/if}-->
			<!--{if $showtrade}--><li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=specialtype&specialtype=reward$forumdisplayadd['specialtype']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}&rewardtype=2">{lang reward_solved}</a></li><!--{/if}-->
		</ul>
	</div>
	<div id="filter_kmpx_menu" class="p_pop" style="display:none">
		<ul>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang all}{lang forum_threads}</a></li>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=author&orderby=dateline$forumdisplayadd['author']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang list_post_time}</a></li>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=reply&orderby=replies$forumdisplayadd['reply']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang replies}</a></li>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=reply&orderby=views$forumdisplayadd['view']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang views}</a></li>
			</li>
		</ul>
	</div>
	<div id="filter_kmsj_menu" class="p_pop" style="display:none">
		<ul>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&orderby={$_GET['orderby']}&filter=dateline$forumdisplayadd['dateline']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang all}{lang search_any_date}</a></li>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&orderby={$_GET['orderby']}&filter=dateline&dateline=86400$forumdisplayadd['dateline']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang last_1_days}</a></li>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&orderby={$_GET['orderby']}&filter=dateline&dateline=172800$forumdisplayadd['dateline']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang last_2_days}</a></li>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&orderby={$_GET['orderby']}&filter=dateline&dateline=604800$forumdisplayadd['dateline']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang list_one_week}</a></li>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&orderby={$_GET['orderby']}&filter=dateline&dateline=2592000$forumdisplayadd['dateline']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang list_one_month}</a></li>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&orderby={$_GET['orderby']}&filter=dateline&dateline=7948800$forumdisplayadd['dateline']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang list_three_month}</a></li>
		</ul>
	</div>	
	<!--{if empty($_G['setting']['closeforumorderby'])}-->
	<div id="filter_orderby_menu" class="p_pop" style="display:none">
		<ul>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang list_default_sort}</a></li>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=author&orderby=dateline$forumdisplayadd['author']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang list_post_time}</a></li>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=reply&orderby=replies$forumdisplayadd['reply']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang replies}</a></li>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=reply&orderby=views$forumdisplayadd['view']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang views}</a></li>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=lastpost&orderby=lastpost$forumdisplayadd['lastpost']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang lastpost}</a></li>
			<li><a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=heat&orderby=heats{$forumdisplayadd['heat'] or ''}{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}">{lang order_heats}</a></li>
		</ul>
	</div>
	<!--{/if}-->
<!--{/if}-->
<!--{if $multipage && $filter != 'hot'}-->
	<!--{if !($_G['forum']['picstyle'] && !$_G['cookie']['forumdefstyle'])}-->
		<a class="bm_h" href="javascript:;" rel="$multipage_more" curpage="$page" id="autopbn" totalpage="$maxpage" picstyle="$_G['forum']['picstyle']" forumdefstyle="$_G['cookie']['forumdefstyle']">{lang next_page_extra}</a>
		<script type="text/javascript" src="{$_G[setting][jspath]}autoloadpage.js?{VERHASH}"></script>
	<!--{else}-->
		<div id="pgbtn" class="pgbtn"><a href="$multipage_more" hidefocus="true">{lang next_page_extra}</a></div>
	<!--{/if}-->
<!--{/if}-->
<div class="bm bw0 pgs cl">
	<span id="fd_page_bottom">$multipage</span>
	<span {if $_G[setting][visitedforums]}id="visitedforumstmp" onmouseover="$('visitedforums').id = 'visitedforumstmp';this.id = 'visitedforums';showMenu({'ctrlid':this.id,'pos':'21'})"{/if} class="pgb y"><a href="forum.php">{lang return_index}</a></span>
	<!--{if !$_GET['archiveid']}--><a href="javascript:;" id="newspecialtmp" onmouseover="$('newspecial').id = 'newspecialtmp';this.id = 'newspecial';showMenu({'ctrlid':this.id})"{if !$_G['forum']['allowspecialonly'] && empty($_G['forum']['picstyle']) && empty($_G['forum']['threadsorts']['required'])} onclick="showWindow('newthread', 'forum.php?mod=post&action=newthread&fid=$_G[fid]')"{else} onclick="location.href='forum.php?mod=post&action=newthread&fid=$_G[fid]';return false;"{/if} title="{lang send_posts}" class="pgsbtn showmenu">{lang send_posts}</a><!--{/if}-->
	<!--{hook/forumdisplay_postbutton_bottom}-->
</div>
