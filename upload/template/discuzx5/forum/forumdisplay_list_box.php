<?PHP exit('Access Denied');?>
<!--{eval require_once libfile('function/forumlist');loadcache(array('stamps', 'usergroups', 'forums')); $attach_on = 0; $list_attach_num = 9;}-->
<div class="forumportal_listc pbn">
	<div id="forumnew" style="display:none"></div>			
	<div id="threadlisttableid">
		<!--{if !$separatepos || !$_G['setting']['forumseparator']}-->
			<!--{if !empty($_G['forum']['picstyle'])}--><!--{ad/threadlist}--><!--{/if}-->
		<!--{/if}-->
		<!--{if $_G['forum_threadlist']}-->
			<!--{if empty($_G['forum']['picstyle']) || $_G['cookie']['forumdefstyle']}-->
			<!--{eval $threadlist_data = get_attach($_G['forum_threadlist']);}-->
				<!--{loop $_G['forum_threadlist'] $key $thread}-->			
	
						<!--{if $separatepos <= $key + 1}-->
							<!--{ad/threadlist}-->
						<!--{/if}-->
						<li{if CURMODULE == 'forumdisplay'} id="$thread['id']"{/if} class="kmlist $thread['folder']"{if $_G['hiddenexists'] && $thread['hidden']} style='display:none'{/if}>
							<a href="javascript:;" id="content_$thread['tid']" class="showcontent y" title="{lang content_actions}" onclick="CONTENT_TID='$thread['tid']';CONTENT_ID='$thread['id']';showMenu({'ctrlid':this.id,'menuid':'content_menu'})"></a>
							<!--{if !$_GET['archiveid'] && $_G['forum']['ismoderator']}-->
							<div class="km_moderate">
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
							<!--{elseif $permission}-->
								<div class="km_moderate"><input type="checkbox" value="$thread['tid']" name="delthread[]" class="pc" /></div>
							<!--{/if}-->
							<!--{if in_array($thread['displayorder'], array(1, 2, 3, 4)) && CURMODULE == 'forumdisplay'}-->
								<a href="javascript:void(0);" onclick="hideStickThread('$thread['tid']')" class="closeprev y" title="{lang hidedisplayorder}">{lang hidedisplayorder}</a>
							<!--{/if}-->
							<!--{if $_G['basescript'] != 'group' && CURMODULE != 'group' && !$thread['forumstick'] && $thread['closed'] > 1 && ($thread['isgroup'] == 1 || $thread['fid'] != $_G['fid'])}-->
									<!--{eval $thread['tid']=$thread['closed'];}-->
							<!--{/if}-->
							
							<a href="forum.php?mod=viewthread&tid=$thread['tid']&{if $_GET['archiveid']}archiveid={$_GET['archiveid']}&{/if}extra=$extra"$thread['highlight']{if $thread['isgroup'] == 1 || $thread['forumstick']} target="_blank"{else} onclick="atarget(this)"{/if} title="{if $thread['displayorder'] == 1}{lang thread_type1}{/if}
									{if $thread['displayorder'] == 2}{lang thread_type2}{/if}
									{if $thread['displayorder'] == 3}{lang thread_type3}{/if}
									{if $thread['displayorder'] == 4}{lang thread_type4}{/if}
									{if $thread['folder'] == 'lock'}{lang closed_thread}{/if}
									{if $thread['special'] == 1}{lang thread_poll}{lang forum_threads}{/if}
									{if $thread['special'] == 2}{lang thread_trade}{lang forum_threads}{/if}
									{if $thread['special'] == 3}{lang thread_reward}{lang forum_threads}{/if}
									{if $thread['special'] == 4}{lang thread_activity}{lang forum_threads}{/if}
									{if $thread['special'] == 5}{lang thread_debate}{lang forum_threads}{/if}
									{if $thread['rushreply']}{lang rushreply}{lang forum_threads}{/if}
									{if $thread['folder'] == "new"}{lang have_newreplies}{lang forum_threads}{/if}" class="kmtit">
								<!--{if $thread['folder'] == 'lock'}-->
									<img src="{STYLEIMGDIR}/svg/dz_ico_folder_lock.svg" alt="" class="kmimgico" />
								<!--{elseif $thread['special'] == 1}-->
									<img src="{STYLEIMGDIR}/svg/dz_ico_pollsmall.svg" alt="{lang thread_poll}" class="kmimgico" />
								<!--{elseif $thread['special'] == 2}-->
									<img src="{STYLEIMGDIR}/svg/dz_ico_tradesmall.svg" alt="{lang thread_trade}" class="kmimgico" />
								<!--{elseif $thread['special'] == 3}-->
									<img src="{STYLEIMGDIR}/svg/dz_ico_rewardsmall.svg" alt="{lang thread_reward}" class="kmimgico" />
								<!--{elseif $thread['special'] == 4}-->
									<img src="{STYLEIMGDIR}/svg/dz_ico_activitysmall.svg" alt="{lang thread_activity}" class="kmimgico" />
								<!--{elseif $thread['special'] == 5}-->
									<img src="{STYLEIMGDIR}/svg/dz_ico_debatesmall.svg" alt="{lang thread_debate}" class="kmimgico" />
								<!--{/if}-->
								<!--{if $thread['rushreply']}-->
									<img src="{STYLEIMGDIR}/svg/dz_ico_rushreply_s.svg" alt="{lang rushreply}" class="kmimgico" />
								<!--{/if}-->
								<!--{if in_array($thread['displayorder'], array(1, 2, 3, 4))}-->
									<span class="kmico kmding">{lang thread_stick}</span>
								<!--{/if}-->
								<!--{if $thread['digest'] > 0 && $filter != 'digest'}--><span class="kmico kmjing">{lang thread_digest}</span><!--{/if}-->
								<!--{if !empty($thread['icon']) && $thread['icon'] >= 0}--><span class="kmico kmicotxt">{$_G['cache']['stamps'][$thread['icon']]['text']}</span><!--{/if}-->									
								<!--{if $thread['displayorder'] == 0}-->
									<!--{if $thread['recommendicon'] && $filter != 'recommend'}-->
										<span class="kmico kmding" title="{lang thread_recommend} $thread['recommends']">{lang thread_recommend_icon}</span>
									<!--{/if}-->
								<!--{/if}-->
								<!--{if $_G['basescript'] != 'group' && CURMODULE != 'group' && $thread['moved']}-->
									<span class="kmico kmding">{lang thread_moved}</span><!--{eval $thread['tid']=$thread['closed'];}-->
								<!--{/if}-->
								<!--{if $_G['setting']['threadhidethreshold'] && $thread['hidden'] >= $_G['setting']['threadhidethreshold']}-->><span class="kmico kmding">{lang hidden}</span><!--{/if}-->
								<!--{hook/forumdisplay_thread $key}-->
								<span class="km_subject">$thread['subject']</span>
								<!--{hook/forumdisplay_thread_subject $key}-->
								<!--{if $thread['weeknew']}-->
									<em class="kmnew xi1">New</em>
								<!--{/if}-->
								<!--{if $thread['attachment'] == 2}-->
									<i class="fico-image fnmr vm" title="{lang attach_img}"></i>
								<!--{elseif $thread['attachment'] == 1}-->
									<i class="fico-attachment fnmr vm" title="{lang attachment}"></i>
								<!--{/if}-->
								<!--{if $thread['displayorder'] == 0}-->
									<!--{if $thread['rate'] > 0}-->
										<i class="fico-thumbup fc-l fnmr vm" title="{lang rate_credit_add}"></i>
									<!--{elseif $thread['rate'] < 0}-->
										<i class="fico-thumbdown fc-a fnmr vm" title="{lang posts_deducted}"></i>
									<!--{/if}-->
								<!--{/if}-->						
							</a>
							<div class="kmtxt">{$threadlist_data[$thread['tid']]['message']}</div>										
							<!--{if is_array($threadlist_data[$thread['tid']]['attachment'])}-->
								<!--{if count($threadlist_data[$thread['tid']]['attachment']) == 1}-->
								<div class="kmimg_onebox">
								<!--{else}-->
								<div class="kmimg_box{if count($threadlist_data[$thread['tid']]['attachment']) == 4} km_4img{/if}">
								<!--{/if}-->
								<!--{eval $attach_on = 0;}-->
								<!--{loop $threadlist_data[$thread['tid']]['attachment'] $value}-->
									<!--{eval $attach_on++; if($attach_on > $list_attach_num) break;}-->
									<!--{if count($threadlist_data[$thread['tid']]['attachment']) == 1}-->
										<a href="forum.php?mod=viewthread&tid={$thread['tid']}" class="kmimg" target="_blank"><img src="$value"></a>
									<!--{else}-->
										<a href="forum.php?mod=viewthread&tid={$thread['tid']}" class="kmshowimg{if count($threadlist_data[$thread['tid']]['attachment']) > $list_attach_num && $attach_on == $list_attach_num} kmmunbg{/if}" target="_blank"><img src="$value" class="vm"><!--{if count($threadlist_data[$thread['tid']]['attachment']) > $list_attach_num && $attach_on == $list_attach_num}--><em class="kmmun">+{echo count($threadlist_data[$thread['tid']]['attachment']) - $list_attach_num}</em><!--{/if}--></span></a>
									<!--{/if}-->
								<!--{/loop}-->
								</div>
							<!--{/if}-->
							<div class="kmfoot">
								<span class="kmpl">{if $thread['allreplies']}$thread['allreplies']{else}$thread['replies']{/if}</span><span class="kmck"><!--{if $thread['isgroup'] != 1 || empty($groupnames[$thread['tid']]['views'])}-->$thread['views']<!--{else}-->{$groupnames[$thread['tid']]['views']}<!--{/if}--></span>
								<!--{if $_GET['view'] != 'me'}-->
									<!--{if $thread['authorid'] && $thread['author']}-->
										<a href="home.php?mod=space&uid=$thread['authorid']" c="1" class="kmimg" target="_blank"><!--{avatar($thread['authorid'],'middle')}--></a>
									<!--{else}-->
										<a href="javascript:;" class="kmimg"><!--{avatar(0,'middle')}--></a>
									<!--{/if}-->
								<!--{/if}-->
								<!--{if $thread['authorid'] && $thread['author']}-->
									<a href="home.php?mod=space&uid=$thread['authorid']" target="_blank"{if $groupcolor[$thread['authorid']]} style="color: $groupcolor[$thread['authorid']];"{/if}>$thread['author']</a>
									<!--{if !empty($verify[$thread['authorid']])}-->$verify[$thread['authorid']]<!--{/if}-->
								<!--{else}-->
									<a href="javascript:;">$_G['setting']['anonymoustext']</a>
								<!--{/if}-->
								<span class="kmtime{if $thread['istoday'] && CURMODULE == 'forumdisplay'} xi1{/if}">{lang tmp083} $thread['dateline']</span>						
								<!--{if $_G['basescript'] != 'group' && CURMODULE != 'group' && !$thread['forumstick'] && ($thread['isgroup'] == 1 || $thread['fid'] != $_G['fid'])}-->
									<!--{if $thread['related_group'] == 0 && $thread['closed'] > 1}-->
										<!--{eval $thread['tid']=$thread['closed'];}-->
									<!--{/if}-->
									<!--{if $groupnames[$thread['tid']]}-->
										<a href="forum.php?mod={if $groupnames[$thread['tid']]['status'] == 3}group{else}forumdisplay{/if}&fid={$groupnames[$thread['tid']]['fid']}" target="_blank" class="kmbg kmico_bk">{$groupnames[$thread['tid']]['name']}</a>
									<!--{elseif $thread['forumname']}-->
										<a href="forum.php?mod=forumdisplay&fid={$thread['fid']}" target="_blank" class="kmbg kmico_bk">{$thread['forumname']}</a>
									<!--{else}-->
										{if $_G['cache']['forums'][$thread['fid']]['name']}<a href="forum.php?mod=forumdisplay&fid={$thread['fid']}" target="_blank" class="kmbg kmico_bk">{$_G['cache']['forums'][$thread['fid']]['name']}</a>{/if}
									<!--{/if}-->
								<!--{elseif $thread['typehtml'] || $thread['sorthtml']}-->
									{echo str_replace(array('<em>[', ']</em>', '">'), array('', '', '" class="kmbg kmico_bk" target="_blank">'), $thread['typehtml'].$thread['sorthtml']);}
								<!--{elseif $_G['cache']['forums'][$thread['fid']]['name']}-->
									<a href="forum.php?mod=forumdisplay&fid={$thread['fid']}" target="_blank" class="kmbg kmico_bk">{$_G['cache']['forums'][$thread['fid']]['name']}</a>
								<!--{elseif $thread['forumname']}-->
									<a href="forum.php?mod=forumdisplay&fid={$thread['fid']}" target="_blank" class="kmbg kmico_bk">{$thread['forumname']}</a>
								<!--{/if}-->
								<!--{if $stemplate && $sortid}--><span class="kmbga kmico_xs">$stemplate[$sortid][$thread['tid']]</span><!--{/if}-->
								<!--{if $thread['readperm']}--><span class="kmbgb kmico_qx">{lang readperm} <strong>{$thread['readperm']}</strong></span><!--{/if}-->	
								<!--{if $thread['price'] > 0}-->
									<!--{if $thread['special'] == '3'}-->
										<a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=specialtype&specialtype=reward$forumdisplayadd['specialtype']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}&rewardtype=1" title="{lang show_rewarding_only}" class="kmbga kmico_xs">{lang thread_reward} <strong>$thread['price']</strong> {$_G['setting']['extcredits'][$_G['setting']['creditstransextra'][2]]['unit']}{$_G['setting']['extcredits'][$_G['setting']['creditstransextra'][2]]['title']}</a>
									<!--{else}-->
										<span class="kmbga kmico_xs">{lang price} <strong>$thread['price']</strong> {$_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]]['unit']}{$_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]]['title']}</span>
									<!--{/if}-->
								<!--{elseif $thread['special'] == '3' && $thread['price'] < 0}-->
									<a href="forum.php?mod=forumdisplay&fid=$_G['fid']&filter=specialtype&specialtype=reward$forumdisplayadd['specialtype']{if $_GET['archiveid']}&archiveid={$_GET['archiveid']}{/if}&rewardtype=2" title="{lang show_rewarded_only}" class="kmbga kmico_xs">{lang reward_solved}</a>
								<!--{/if}-->
								<!--{if $thread['replycredit'] > 0}-->
									<span class="kmbga kmico_xs">{lang replycredit} <strong>$thread['replycredit']</strong></span>
								<!--{/if}-->						
								<!--{if $thread['mobile']}--><span class="kmtxt">{lang post_mobile}</span><!--{/if}-->
							</div>
						</li>
			
				<!--{/loop}-->
		
				<!--{if $_G['hiddenexists']}-->							
					<div id="hiddenthread"><a href="javascript:;" onclick="display_blocked_thread()">{lang other_reply_hide}</a></div>
				<!--{/if}-->
			<!--{else}-->
			</div>
			<div class="dz_forumdisplay_waterfall">
				<!--{subtemplate forum/forumdisplay_waterfall}-->
			<!--{/if}-->
		<!--{else}-->
			<p class="emp">{lang tmp063}{lang tmp088}{lang tmp089}</p>
		<!--{/if}-->
	</div>			
</div>