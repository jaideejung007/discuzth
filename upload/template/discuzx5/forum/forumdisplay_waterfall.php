<?PHP exit('Access Denied');?>
<ul id="waterfall" class="dz_waterfall pbn cl">
	<!--{loop $_G['forum_threadlist'] $key $thread}-->
	<!--{if $_G['hiddenexists'] && $thread['hidden']}-->
		<!--{eval continue;}-->
	<!--{/if}-->
	<!--{if !$thread['forumstick'] && ($thread['isgroup'] == 1 || $thread['fid'] != $_G['fid'])}-->
		<!--{if $thread['related_group'] == 0 && $thread['closed'] > 1}-->
			<!--{eval $thread['tid']=$thread['closed'];}-->
		<!--{/if}-->
	<!--{/if}-->
	<!--{eval $waterfallwidth = $_G['setting']['forumpicstyle']['thumbwidth']; }-->
	<li style="width:{$waterfallwidth}px">
		<!--{if !$_GET['archiveid'] && $_G['forum']['ismoderator']}-->
			<div class="wfmod">
			<!--{if $thread['fid'] == $_G['fid']}-->
				<!--{if $thread['displayorder'] <= 3 || $_G['adminid'] == 1}-->
					<input onclick="tmodclick(this)" type="checkbox" name="moderate[]" value="$thread['tid']" />
				<!--{else}-->
					<input type="checkbox" disabled="disabled" />
				<!--{/if}-->
			<!--{else}-->
				<input type="checkbox" disabled="disabled" />
			<!--{/if}-->
			</div>
		<!--{/if}-->
		<div class="wfpic">
			<a href="forum.php?mod=viewthread&tid=$thread['tid']&{if $_GET['archiveid']}archiveid={$_GET['archiveid']}&{/if}extra=$extra" {if $thread['isgroup'] == 1 || $thread['forumstick'] || CURMODULE == 'guide'} target="_blank"{else} onclick="atarget(this)"{/if} title="$thread['subject']">
				<!--{if $thread['cover']}-->
					<img src="$thread['coverpath']" alt="$thread['subject']" width="{$_G['setting']['forumpicstyle']['thumbwidth']}" />
				<!--{else}-->
					<span class="nophoto nopic" style="width:{$_G['setting']['forumpicstyle']['thumbwidth']}px; height:{$_G['setting']['forumpicstyle']['thumbwidth']}px; line-height:{$_G['setting']['forumpicstyle']['thumbwidth']}px;"></span>
				<!--{/if}-->
			</a>
		</div>
		<div class="wftitle">
			<!--{hook/forumdisplay_thread $key}-->
			<a href="forum.php?mod=viewthread&tid=$thread['tid']&{if $_GET['archiveid']}archiveid={$_GET['archiveid']}&{/if}extra=$extra"$thread['highlight']{if $thread['isgroup'] == 1 || $thread['forumstick']} target="_blank"{else} onclick="atarget(this)"{/if} title="$thread['subject']">$thread['subject']</a>
		</div>
		<div class="wftit cl">
			<span class="wftxt"><em class="wfhf" title="$thread['replies'] {lang reply}">$thread['replies']</em><em class="wfck" title="{if $thread['isgroup'] != 1}$thread['views']{else}{$groupnames[$thread['tid']]['views']}{/if}"><!--{if $thread['isgroup'] != 1}-->$thread['views']<!--{else}-->{$groupnames[$thread['tid']]['views']}<!--{/if}--></em></span>
			<!--{hook/forumdisplay_author $key}-->
			<!--{if $thread['authorid'] && $thread['author']}-->
				<a href="home.php?mod=space&uid=$thread['authorid']" class="wfuser"><!--{avatar($thread['authorid'], 'small')}-->{$thread['author']}</a>
			<!--{else}-->
				<span class="wfuser"><!--{avatar(0, 'small')}-->$_G['setting']['anonymoustext']</span>
			<!--{/if}-->
		</div>
	</li>
	<!--{/loop}-->
</ul>
<div id="tmppic" style="display: none;"></div>
<script type="text/javascript" src="{$_G['setting']['jspath']}redef.js?{VERHASH}"></script>
<script type="text/javascript" reload="1">
var wf = {};
_attachEvent(window, "load", function () {
	if($("waterfall")) {
		wf = waterfall({"space": 15,"container":$("waterfall")});
	}
	<!--{if $page < $_G['page_next'] && !$subforumonly}-->
		var page = $page + 1,
			maxpage = Math.min($page + 10,$maxpage + 1),
			stopload = 0,
			scrolltimer = null,
			tmpelems = [],
			tmpimgs = [],
			markloaded = [],
			imgsloaded = 0,
			loadready = 0,
			showready = 1,
			nxtpgurl = 'forum.php?mod=forumdisplay&fid={$_G['fid']}&{$forumdisplayadd['page']}{eval echo ($multiadd ? '&'.implode('&', $multiadd) : '');}{$multipage_archive}&page=',
			wfloading = "<div class=\"loadicon vm\"></div> {lang onloading}...",
			pgbtn = $("pgbtn").getElementsByTagName("a")[0];
		function loadmore() {
			var url = nxtpgurl + page + '&t=' + parseInt((+new Date()/1000)/(Math.random()*1000));
			var x = new Ajax("HTML");
			x.get(url, function (s) {
				s = s.replace(/\n|\r/g, "");
				if(s.indexOf("id=\"pgbtn\"") == -1) {
					$("pgbtn").style.display = "none";
					stopload++;
					window.onscroll = null;
				}
				var pageinfo = s.match(/\<span id="fd_page_bottom"\>(.+?)\<\/span\>/);
				$('fd_page_bottom').innerHTML = pageinfo[1];
				var pageinfo = s.match(/\<span id="fd_page_top"\>(.+?)\<\/span\>/);
				$('fd_page_top').innerHTML = pageinfo[1];
				s = s.substring(s.indexOf("<ul id=\"waterfall\""), s.indexOf("<div id=\"tmppic\""));
				s = s.replace("id=\"waterfall\"", "");
				$("tmppic").innerHTML = s;
				loadready = 1;
			});
		}
		window.onscroll = function () {
			if(scrolltimer == null) {
				scrolltimer = setTimeout(function () {
					try {
						if(page < maxpage && stopload < 2 && showready && ((document.documentElement.scrollTop || document.body.scrollTop) + document.documentElement.clientHeight + 500) >= document.documentElement.scrollHeight) {
							pgbtn.innerHTML = wfloading;
							loadready = 0;
							showready = 0;
							loadmore();
							tmpelems = $("tmppic").getElementsByTagName("li");
							var waitingtimer = setInterval(function () {
								stopload >= 2 && clearInterval(waitingtimer);
								if(loadready && stopload < 2) {
									if(!tmpelems.length) {
										page++;
										pgbtn.href = nxtpgurl + Math.min(page, $maxpage);
										pgbtn.innerHTML = "{lang next_page_extra}";
										showready = 1;
										clearInterval(waitingtimer);
									}
									for(var i = 0, j = tmpelems.length; i < j; i++) {
										if(tmpelems[i]) {
											tmpimgs = tmpelems[i].getElementsByTagName("img");
											imgsloaded = 0;
											for(var m = 0, n = tmpimgs.length; m < n; m++) {
												tmpimgs[m].onerror = function () {
													this.style.display = "none";
												};
												markloaded[m] = tmpimgs[m].complete ? 1 : 0;
												imgsloaded += markloaded[m];
											}
											if(imgsloaded == tmpimgs.length) {
												$("waterfall").appendChild(tmpelems[i]);
												wf = waterfall({
													"index": wf.index,
													"totalwidth": wf.totalwidth,
													"totalheight": wf.totalheight,
													"columnsheight": wf.columnsheight,
													"space": 15,
													"container":$("waterfall")
												});
											}
										}
									}
								}
							}, 40);
						}
					} catch(e) {}
					scrolltimer = null;
				}, 320);
			}
		};
	<!--{/if}-->
});
</script>