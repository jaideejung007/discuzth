<?php echo '';exit;?>
<!--{subtemplate common/header_common}-->
	{cells common/header/meta}
	{cells common/header/css}
	{cells common/header/js}
	<script src="{STATICURL}js/mobile/jquery.min.js?{VERHASH}"></script><script>jQuery.noConflict();</script>
</head>

<body id="nv_{$_G[basescript]}" class="pg_{CURMODULE} dz_pg_{CURMODULE}	dz_tbnvb {if $_G['basescript'] === 'portal' && CURMODULE === 'list' && !empty($cat)} {$cat['bodycss']}{/if} dz_wide discuzx5" onkeydown="if(event.keyCode==27) return false;">
	<div id="append_parent"></div><div id="ajaxwaitid"></div>
	<!--{if $_GET['diy'] == 'yes' && check_diy_perm($topic)}-->
		<!--{template common/header_diy}-->
	<!--{/if}-->
	<!--{if check_diy_perm($topic)}-->
		<!--{template common/header_diynav}-->
	<!--{/if}-->
	<!--{if CURMODULE == 'topic' && $topic && empty($topic['useheader']) && check_diy_perm($topic)}-->
		$diynav
	<!--{/if}-->
	<!--{if empty($topic) || $topic['useheader']}-->
		<!--{if $_G['setting']['mobile']['allowmobile'] && (!$_G['setting']['cacheindexlife'] && !$_G['setting']['cachethreadon'] || $_G['uid']) && ($_GET['diy'] != 'yes' || !$_GET['inajax']) && ($_G['mobile'] != '' && $_G['cookie']['mobile'] == '' && $_GET['mobile'] != 'no')}-->
			<div class="xi1 bm bm_c">
			    {lang your_mobile_browser}<a href="{$_G['siteurl']}forum.php?mobile=yes">{lang go_to_mobile}</a> <span class="xg1">|</span> <a href="$_G['setting']['mobile']['nomobileurl']">{lang to_be_continue}</a>
			</div>
		<!--{/if}-->
		<!--{if $_G['setting']['shortcut'] && $_G['member'][credits] >= $_G['setting']['shortcut']}-->
			<div id="shortcut">
				<span><a href="javascript:;" id="shortcutcloseid" title="{lang close}">{lang close}</a></span>
				{lang shortcut_notice}
				<a href="javascript:;" id="shortcuttip">{lang shortcut_add}</a>

			</div>
			<script type="text/javascript">setTimeout(setShortcut, 2000);</script>
		<!--{/if}-->
		<div id="toptb" class="cl" style="display:none;">
			<!--{hook/global_cpnav_top}-->
			<div class="wp">
				<div class="z">
					<!--{loop $_G['setting']['topnavs'][0] $nav}-->
						<!--{if is_array($nav) && $nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))}-->$nav[code]<!--{/if}-->
					<!--{/loop}-->
					<!--{hook/global_cpnav_extra1}-->
				</div>
				<div class="y">
					<!--{hook/global_cpnav_extra2}-->
				</div>
				<div class="clear"></div>
			</div>
		</div>
		
		<!--{if !IS_ROBOT}-->
			<!--{if $_G['uid']}-->
			<ul id="myprompt_menu" class="p_pop" style="display: none;">
				<!--{if !empty($_G['setting']['pmstatus'])}-->
					<li><a href="home.php?mod=space&do=pm" id="pm_ntc" style="background-repeat: no-repeat; background-position: 0 50%;"><em class="prompt_news{if empty($_G[member][newpm])}_0{/if}"></em>{lang pm_center}</a></li>
				<!--{/if}-->
				<li><a href="home.php?mod=follow&do=follower"><em class="prompt_follower{if empty($_G[member][newprompt_num][follower])}_0{/if}"></em><!--{lang notice_interactive_follower}-->{if $_G[member][newprompt_num][follower]}($_G[member][newprompt_num][follower]){/if}</a></li>
				<!--{if $_G[member][newprompt] && $_G[member][newprompt_num][follow]}-->
					<li><a href="home.php?mod=follow"><em class="prompt_concern"></em><!--{lang notice_interactive_follow}-->($_G[member][newprompt_num][follow])</a></li>
				<!--{/if}-->
				<!--{if $_G[member][newprompt]}-->
					<!--{loop $_G['member']['category_num'] $key $val}-->
						<li><a href="home.php?mod=space&do=notice&view=$key"><em class="notice_$key"></em><!--{echo lang('template', 'notice_'.$key)}-->(<span class="rq">$val</span>)</a></li>
					<!--{/loop}-->
				<!--{/if}-->
				<!--{if empty($_G['cookie']['ignore_notice'])}-->
				<li class="ignore_noticeli"><a href="javascript:;" onClick="setcookie('ignore_notice', 1);hideMenu('myprompt_menu')" title="{lang temporarily_to_remind}"><em class="ignore_notice"></em></a></li>
				<!--{/if}-->
				</ul>
			<!--{/if}-->
			<!--{if $_G['uid'] && !empty($_G['style']['extstyle'])}-->
				<div id="sslct_menu" class="cl p_pop" style="display: none;">
					<!--{if !$_G[style][defaultextstyle]}--><span class="sslct_btn" onClick="extstyle('')" title="{lang default}"><i></i></span><!--{/if}-->
					<!--{loop $_G['style']['extstyle'] $extstyle}-->
						<span class="sslct_btn" onClick="extstyle('$extstyle[0]')" title="$extstyle[1]"><i style='background:$extstyle[2]'></i></span>
					<!--{/loop}-->
				</div>
			<!--{/if}-->
			<!--{if $_G['uid']}-->
				<ul id="myitem_menu" class="p_pop" style="display: none;">
					<!--{if $_G['setting']['forumstatus']}--><li><a href="home.php?mod=space&do=thread&view=me">{lang mypost}</a></li><!--{/if}-->
					<!--{if $_G['setting']['favoritestatus']}--><li><a href="home.php?mod=space&do=favorite&view=me">{lang favorite}</a></li><!--{/if}-->
					<!--{if $_G['setting']['friendstatus']}--><li><a href="home.php?mod=space&do=friend">{lang friends}</a></li><!--{/if}-->
					<!--{if $_G['setting']['followerstatus']}-->
						<li><a href="home.php?mod=follow&do=follower">{lang follower}</a></li>
						<li><a href="home.php?mod=follow&do=following">{lang following}</a></li>
					<!--{/if}-->
					<!--{hook/global_myitem_extra}-->
				</ul>
			<!--{/if}-->
			<!--{subtemplate common/header_qmenu}-->
		<!--{/if}-->

		<!--{ad/headerbanner/wp a_h}-->

		<div id="dz_btm_layer" {if $_G['style']['is_fixtop']} class="topbox"{/if}>
			<div class="dz_layer_top">
				<div  class="dz_btm_layer dz_nav">
					<!--{eval $mnid = getcurrentnav();}-->
					<div class="dz_nav_logo">
						<!--{if !isset($_G['setting']['navlogos'][$mnid])}--><a href="{if $_G['setting']['domain']['app']['default']}{$_G['scheme']}://{$_G['setting']['domain']['app']['default']}/{else}./{/if}" title="$_G['setting']['bbname']" class="dzlogo">{$_G['style']['boardlogo']}</a><!--{else}--><div class="dzlogo">$_G['setting']['navlogos'][$mnid]</div><!--{/if}-->
					</div>
					<div class="dz_layer_nav">						
						<ul>
							<!--{eval $mnid = getcurrentnav(); $n=0; $dz_menumore = ''; $nn = $_G['style']['top_navnum'] ? $_G['style']['top_navnum'] : 10;}-->
							<!--{loop $_G['setting']['navs'] $nav}-->
								<!--{if is_array($nav) && $nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))}-->
									<!--{eval $n++;}-->					
									<!--{if $n <= $nn}-->
									<!--{if is_array($nav) && $nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))}--><li {if $mnid == $nav[navid] || substr($_SERVER['REQUEST_URI'], 1) == str_replace('./', '', $nav[filename])}class="a" {/if}$nav[nav]></li><!--{/if}-->
									<!--{/if}-->					
								<!--{/if}-->
								<!--{if $n > $nn}-->
									<li id="top_menumore" class="dz_menumore" onmouseover="showMenu({'ctrlid':'top_menumore'});"><a href="javascript:;" >{lang tmp133}</a></li>
									<!--{eval $n=9999;break;}-->
								<!--{/if}-->				
							<!--{/loop}-->
						</ul>
						<!--{hook/global_nav_extra}-->
					</div>
					<div class="dz_layer_dl"><!--{template common/header_userstatus}--></div>
					<div class="clear"></div>
				</div>
				
			</div>
		</div>
		<div class="wp" id="hds">
			<!--{if !empty($_G['setting']['plugins']['jsmenu'])}-->
			<ul class="p_pop h_pop" id="plugin_menu" style="display: none">
				<!--{loop $_G['setting']['plugins']['jsmenu'] $module}-->
				<!--{if !$module['adminid'] || ($module['adminid'] && $_G['adminid'] > 0 && $module['adminid'] >= $_G['adminid'])}-->
				<li>$module[url]</li>
				<!--{/if}-->
				<!--{/loop}-->
			</ul>
			<!--{/if}-->
			$_G[setting][menunavs]
			<div id="mu" class="cl">
				<!--{if $_G['setting']['subnavs']}-->
				<!--{loop $_G[setting][subnavs] $navid $subnav}-->
				<!--{if $_G['setting']['navsubhover'] || $mnid == $navid}-->
				<ul class="cl {if $mnid == $navid}current{/if}" id="snav_$navid" style="display:{if $mnid != $navid}none{/if}">
					$subnav
				</ul>
				<!--{/if}-->
				<!--{/loop}-->
				<!--{/if}-->
			</div>
			<!--{ad/subnavbanner/a_mu}-->
			<!--{if $n == 9999}-->
			<!--{eval $n=0;}-->
			<ul id="top_menumore_menu" class="p_pop" style="display:none">
				<!--{loop $_G['setting']['navs'] $nav}-->
					<!--{if is_array($nav) && $nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))}-->
						<!--{eval $n++;}-->
						<!--{if $n <= $nn}--><!--{eval continue;}--><!--{/if}-->	
						<!--{if is_array($nav) && $nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))}--><li {if $mnid == $nav[navid] || substr($_SERVER['REQUEST_URI'], 1) == str_replace('./', '', $nav[filename])}class="a" {/if}$nav[nav]></li><!--{/if}-->
					<!--{/if}-->
				<!--{/loop}-->
			</ul>
			<!--{/if}-->
		
		</div>

		<!--{hook/global_header}-->
	<!--{/if}-->

	<div id="wp" class="wp">
    
		