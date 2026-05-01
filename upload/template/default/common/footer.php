<?php exit('Access Denied');?>
	</div>
<!--{if empty($topic) || ($topic[usefooter])}-->
	{cells common/footer/focus}

	<!--{ad/footerbanner/wp a_f/1}--><!--{ad/footerbanner/wp a_f/2}--><!--{ad/footerbanner/wp a_f/3}-->
	<!--{ad/float/a_fl/1}--><!--{ad/float/a_fr/2}-->
	<!--{ad/couplebanner/a_fl a_cb/1}--><!--{ad/couplebanner/a_fr a_cb/2}-->
	<!--{ad/cornerbanner/a_cn}-->

	<!--{hook/global_footer}-->
	<div id="ft" class="wp cl">
		<div id="flk" class="y">
			<p>
				<!--{loop $_G['setting']['footernavs'] $nav}--><!--{if is_array($nav) && $nav['available'] && ($nav['type'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1)) ||
						!$nav['type'] && ($nav['id'] == 'stat' && $_G['group']['allowstatdata'] || $nav['id'] == 'report' && $_G['uid'] || $nav['id'] == 'archiver' || $nav['id'] == 'mobile' || $nav['id'] == 'darkroom'))}-->$nav[code]<span class="pipe">|</span><!--{/if}--><!--{/loop}-->
						<strong><a href="$_G['setting']['siteurl']" target="_blank">$_G['setting']['sitename']</a></strong>
				<!--{if $_G['setting']['icp'] || !empty($_G['setting']['mps'])}-->( <!--{if $_G['setting']['icp']}--><a href="https://beian.miit.gov.cn/" target="_blank">$_G['setting']['icp']</a><!--{/if}--><!--{if !empty($_G['setting']['mps'])}--><!--{if $_G['setting']['icp']}--><span class="pipe">|</span><!--{/if}--><a href="https://beian.mps.gov.cn/#/query/webSearch?code=$_G['setting']['mpsid']" target="_blank"><img width="14" height="14" src="{IMGDIR}/ico_mps.png" />$_G['setting']['mps']</a><!--{/if}--> )<!--{/if}-->
				<!--{hook/global_footerlink}-->
				<!--{if $_G['setting']['statcode']}-->$_G['setting']['statcode']<!--{/if}-->
			</p>
			<p class="xs0">
				{lang time_now}
				<span id="debuginfo">
				<!--{if debuginfo()}-->, Processed in $_G[debuginfo][time] second(s), $_G[debuginfo][queries] queries
					<!--{if $_G['gzipcompress']}-->, Gzip On<!--{/if}--><!--{if C::memory()->type}-->, <!--{echo C::memory()->type}--> On<!--{/if}-->.
				<!--{/if}-->
				</span>
			</p>
		</div>
		<div id="frt">
			<p>{cells common/footer/copyright}</p>
			<p class="xs0">{lang copyright}</p>
		</div>
		<!--{eval updatesession();}-->
	</div>
<!--{/if}-->

<div id="scrolltop">
	<!--{if $_G[fid] && $_G['mod'] == 'viewthread'}-->
	<span><a href="forum.php?mod=post&action=reply&fid=$_G[fid]&tid=$_G[tid]&extra=$_GET[extra]&page=$page{if $_GET[from]}&from=$_GET[from]{/if}" onclick="showWindow('reply', this.href)" class="replyfast" title="{lang fastreply}"><b>{lang fastreply}</b></a></span>
	<!--{/if}-->
	<span hidefocus="true"><a title="{lang scrolltop}" onclick="window.scrollTo('0','0')" class="scrolltopa" ><b>{lang scrolltop}</b></a></span>
	<!--{if $_G[fid]}-->
	<span>
		<!--{if $_G['mod'] == 'viewthread'}-->
		<a href="forum.php?mod=forumdisplay&fid=$_G[fid]" hidefocus="true" class="returnlist" title="{lang return_list}"><b>{lang return_list}</b></a>
		<!--{else}-->
		<a href="forum.php" hidefocus="true" class="returnboard" title="{lang return_forum}"><b>{lang return_forum}</b></a>
		<!--{/if}-->
	</span>
	<!--{/if}-->
</div>

{cells common/footer/js}

<!--{eval output();}-->
</body>
</html>
