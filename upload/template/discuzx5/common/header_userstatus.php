<?php echo '';exit;?>
<div class="header-right">
	<!--{if $_G['style']['is_search']}-->
	<div class="header-searcher">
		<div class="searcher-wrap">
			<form method="{if $_G[fid] && !empty($searchparams[url])}get{else}post{/if}" autocomplete="off"
				action="{if $_G[fid] && !empty($searchparams[url])}$searchparams[url]{else}search.php?searchsubmit=yes{/if}">
				<input type="hidden" name="mod" value="forum" />
				<input type="text" name="srchtxt" placeholder="{lang enter_content}" value="{if $keyword}$keyword{/if}"
					class="search-input">
				<button type="submit" name="searchsubmit" sc="1" class="search-icon" value="true"></button>
			</form>
		</div>
	</div>
	<!--{/if}-->
	{cells common/header/i18n_switch}
	<!--{if $_G['uid']}-->
	<!--{if strpos($_G['setting']['pluginhooks']['global_usernav_extra1'], 'function showmyrepeats()')}-->
	<div class="header-notice">
		<div class="notice-icon"><a href="home.php?mod=spacecp&ac=plugin&id=myrepeats:memcp" id="myrepeats" class="kmico" onmouseover="delayShow(this, showmyrepeats)" title="{lang tmp070}">
		<i class="dzicon myrepeats"></i>

		</a></div>
	</div>
	<!--{/if}-->
	<div class="header-notice">
		<div class="notice-icon">
			<i class="dzicon noticeicon"></i>
			<!--{if $_G['member']['newprompt'] || $_G['member']['newpm']}-->
			{eval $newprompt = $_G['member']['newprompt'] + $_G['member']['newpm'];}
			<span class="dot">{if $newprompt >99}99{else}$newprompt{/if}</span>
			<!--{/if}-->
		</div>

		<div class="notice-dropdown poptip-popper">
			<div class="poptip-arrow"></div>
			<ul class="notice-content">
				<li class="notice-item"><a href="home.php?mod=space&do=notice">{lang remind}<!--{if $_G[member][newprompt]}--><span class="num">($_G[member][newprompt])</span><span class="dot"></span><!--{/if}--></a>
				<!--{if !empty($_G['setting']['pmstatus'])}-->
					<li class="notice-item"><a href="home.php?mod=space&do=pm">{lang pm_center}{if $_G[member][newpm]}<span class="dot"></span>{/if}</a></li>
				<!--{/if}-->
				<li class="notice-item"><a href="home.php?mod=follow&do=follower"><!--{lang notice_interactive_follower}-->{if $_G[member][newprompt_num][follower]} <span class="num">$_G[member][newprompt_num][follower]</span>{/if}{if $_G[member][newprompt_num][follower]}<span class="dot"></span>{/if}</a></li>
				<!--{if $_G[member][newprompt] && $_G[member][newprompt_num][follow]}-->
					<li class="notice-item"><a href="home.php?mod=follow"><!--{lang notice_interactive_follow}-->($_G[member][newprompt_num][follow])<span class="dot"></span></a></li>
				<!--{/if}-->
				<!--{if $_G[member][newprompt]}-->
				<!--{loop $_G['member']['category_num'] $key $val}-->
					<li class="notice-item"><a href="home.php?mod=space&do=notice&view=$key"><!--{echo lang('template', 'notice_'.$key)}--><span class="num">$val</span><span class="dot"></span></a></li>
				<!--{/loop}-->
				<!--{/if}-->
			</ul>
		</div>
	</div>
	<div class="header-user">
		<div class="header-user-info">
			<div class="header-user-avatar">
				<a href="home.php?mod=space&uid=$_G[uid]">
					<!--{avatar($_G['uid'],'big')}-->
				</a>
			</div>
		</div>
		<div class="header-user-dropdown poptip-popper">
			<div class="poptip-arrow"></div>
			<div class="user-card">
				<div class="user-card-top">
					<div class="block_avatar e-avatar"><a href="home.php?mod=space&uid=$_G[uid]"><!--{avatar($_G['uid'],'big')}--></a></div>
					<div class="block_name">

						<a href="home.php?mod=space&uid=$_G[uid]">{$_G[member][username]}</a>
						<a href="home.php?mod=spacecp&ac=usergroup" target="_blank">{$_G['group']['grouptitle']}</a>
					</div>
				</div>
				<div class="author_intro">
					<p>
						<!--{if $_G['group']['allowinvisible']}-->
						<span id="loginstatus" class="xi2">
							<a id="loginstatusid" href="member.php?mod=switchstatus" title="{lang login_switch_invisible_mode}" onclick="ajaxget(this.href, 'loginstatus');return false;" class="xi2"></a>
						</span>
						<!--{/if}-->
						<!--{if $_G['member']['freeze']}--><span class="xi1">{lang freeze}</span><!--{/if}-->
						<a href="home.php?mod=spacecp&ac=credit&showcredit=1" target="_blank">{lang credits}: $_G['member']['credits']</a>
					</p>
				</div>
				<div class="user-card-area">
					<!--{if check_diy_perm($topic)}-->
					<a href="javascript:saveUserdata('diy_advance_mode', '1');openDiy();">{lang tmp071}</a>
					<!--{/if}-->
					<!--{if !empty($_G['setting']['taskstatus']) && !empty($_G['cookie']['taskdoing_'.$_G['uid']])}--><a href="home.php?mod=task&item=doing" id="task_ntc" class="info_task" target="_blank">{lang task_doing}</a><!--{/if}-->
					<!--{if ($_G['group']['allowmanagearticle'] || $_G['group']['allowpostarticle'] || $_G['group']['allowdiy'] || getstatus($_G['member']['allowadmincp'], 4) || getstatus($_G['member']['allowadmincp'], 6) || getstatus($_G['member']['allowadmincp'], 2) || getstatus($_G['member']['allowadmincp'], 3))}-->
						<a href="portal.php?mod=portalcp" class="info_portalcp" target="_blank"><!--{if $_G['setting']['portalstatus'] }-->{lang portal_manage}<!--{else}-->{lang portal_block_manage}<!--{/if}--></a>
					<!--{/if}-->
					<!--{if $_G['uid'] && $_G['group']['radminid'] > 1}-->
						<a href="forum.php?mod=modcp&fid=$_G['fid']" class="info_portalcp" target="_blank">{lang forum_manager}</a>
					<!--{/if}-->
					<!--{if $_G['uid'] && getstatus($_G['member']['allowadmincp'], 1)}-->
						<a href="?app=admin" class="info_admin" target="_blank">{lang admincp}</a>
					<!--{/if}-->
				</div>
				<div class="user-card-area">
					<a href="home.php?mod=spacecp" class="info_setup" target="_blank">{lang myitem}{lang setup}</a>
					<a href="home.php?mod=space&do=thread&view=me" class="info_post" target="_blank">{lang myitem}{lang mypost}</a>
					<a href="home.php?mod=space&do=favorite&view=me" class="info_fav" target="_blank">{lang myitem}{lang favorite}</a>
					<a href="home.php?mod=space&do=friend" class="info_friend" target="_blank">{lang myitem}{lang friends}</a>
					<!--{hook/global_usernav_extra1}-->
					<!--{hook/global_usernav_extra2}-->
					<!--{hook/global_usernav_extra3}-->
					<!--{hook/global_usernav_extra4}-->
					<!--{hook/global_myitem_extra}-->
				</div>
				<!--{if $_G['style']['top_fastnav'] }-->
				<div class="dz_user_qmenu cl">
					<div class="qmenu_an" id="qmenu_an">
						<a class="next" href="javascript:qmenu_move('1');"><em></em></a>
						<a class="prev" href="javascript:qmenu_move('0');"><em></em></a>
					</div>
					<div class="qmenu_ico" id="qmenu_loop">
						<ul class="cl" id="qmenu_loopul">
							<!--{loop $_G['setting']['mynavs'] $nav}-->
								<!--{if $nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))}-->
									<li>{echo str_replace($nav['navname'], '<span>'. $nav['navname'].'</span>', $nav['code']);}</li>
								<!--{/if}-->
							<!--{/loop}-->
						</ul>
					</div>
				</div>
				<!--{/if}-->

				<!--{if $_G['uid'] && !empty($_G['style']['extstyle'])}-->
				<div class="user-card-skin">
					<!--{if !$_G[style][defaultextstyle]}-->
					<span onclick="extstyle('')" title="{lang default}"><i></i></span><!--{/if}-->
					<!--{loop $_G['style']['extstyle'] $extstyle}-->
					<span onclick="extstyle('$extstyle[0]')" title="$extstyle[1]"><i
							style='background:$extstyle[2]'></i></span>
					<!--{/loop}-->
				</div>
				<!--{/if}-->
				<div class="user-card-bottom">
					<a href="member.php?mod=logging&action=logout&formhash={FORMHASH}" class="user-logout-btn">{lang logout}</a>
				</div>
			</div>
		</div>
	</div>
	<!--{else}-->
	<!--{hook/global_usernav_extra1}-->
	<div class="header-user-login">
		<div class="login_btn"><a href="member.php?mod=logging&action=login" onclick="showWindow('login', this.href)">{lang login}</a></div>
	</div>
	<!--{/if}-->
</div>
<script type="text/javascript">
document.querySelectorAll('.header-user, .header-notice, .header-i18n, .header-client').forEach(function(element) {
    element.addEventListener('mouseenter', function() {
        this.classList.add('open');
    });

    element.addEventListener('mouseleave', function() {
        this.classList.remove('open');
    });
});
if($("qmenu_loop")){
	var qmenu_timer, qmenu_scroll_l;
	var qmenu_in = 0;
	var qmenu_width = 163;
	var qmenu_loop = $('qmenu_loop');
	var qmenu_all_width = 41 * $('qmenu_loopul').getElementsByTagName("li").length - qmenu_width;
	if(qmenu_all_width < 20){
		$('qmenu_an').style.display = 'none';
	}
}
function qmenu_move(qmenu_lr){
	if(qmenu_in == 0 && ((qmenu_lr == 1 && qmenu_loop.scrollLeft < qmenu_all_width) || (qmenu_lr == 0 && qmenu_loop.scrollLeft > 0))){
		qmenu_in = 1;
		qmenu_scroll_l = qmenu_loop.scrollLeft;
		qmenu_timer = setInterval(function(){
			qmenu_scroll(qmenu_lr);
		}, 10);
	}
}
function qmenu_scroll(qmenu_lr){
	if((qmenu_lr == 1 && qmenu_loop.scrollLeft >= qmenu_width + qmenu_scroll_l) || (qmenu_lr == 0 && ((qmenu_loop.scrollLeft <= qmenu_scroll_l - qmenu_width) || qmenu_loop.scrollLeft == 0))){
		clearInterval(qmenu_timer);
		qmenu_in = 0;
	}else{
		if(qmenu_lr == 1){
			qmenu_loop.scrollLeft += Math.round((qmenu_width + qmenu_scroll_l - qmenu_loop.scrollLeft) / 15) + 1;
		}else{
			qmenu_loop.scrollLeft -= Math.round((qmenu_width - (qmenu_scroll_l - qmenu_loop.scrollLeft)) / 15) + 1;
		}
	}
}
</script>