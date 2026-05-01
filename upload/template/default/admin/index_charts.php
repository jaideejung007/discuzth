<?php exit('Access Denied'); ?>
<div class="dbox">
	<div class="boxheader">
		<a href="misc.php?mod=stat&op=trend" target="_blank" style="float: right">{lang stats_more}</a>
		{lang stats_trend}
	</div>
	<div class="boxbody">
		<div class="stat_box cl">
			<div class="statitem ico01">{lang stats_main_members}
				<em>{lang stats_main_today} {echo number_format($statvars['membersaddtoday'])}</em>
				<span><a href="{ADMINSCRIPT}?action=members&operation=search&submit=yes" target="_blank">{echo number_format($statvars['members'])}</a></span>
			</div>
			<div class="statitem ico02">{lang stats_main_onlinemembers}
				<em>{lang stats_main_registers} {echo number_format($statvars['onlinemembers'])}</em>
				<span><a href="{ADMINSCRIPT}?action=members&operation=search&sid_noempty=yes" target="_blank">{echo number_format($statvars['online'])}</a></span>
			</div>
			<div class="statitem ico03">{lang stats_main_posters}
				<em>{lang stats_main_ratio} $statvars['mempostpercent']%</em>
				<span><a href="{ADMINSCRIPT}?action=members&operation=search&submit=yes&posts_low=1" target="_blank">{echo number_format($statvars['mempost'])}</a></span>
			</div>
			<div class="statitem ico04">{lang stats_main_posts}
				<em>{lang stats_main_newposts} {echo number_format($statvars['postsaddtoday'])}</em>
				<span><a href="{ADMINSCRIPT}?action=threads" target="_blank">{echo number_format($statvars['posts'])}</a></span>
			</div>
		</div>
		<div class="charts">
			<script src="{STATICURL}js/echarts/echarts.common.min.js"></script>
			<script src="{$_G['setting']['jspath']}stat.js"></script>
			<div id="statchart"></div>
			<script type="text/javascript">
				drawstatchart('{ADMINSCRIPT}?action=index&chart=yes', 300);
			</script>
		</div>
	</div>
</div>