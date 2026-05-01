<?php exit('Access Denied');?>
<div class="bm bw0 mdcp">
	<!--{if $_G[fid] && $_G['forum']['ismoderator']}-->

		<h1 class="mt cl">
			<span class="z">
				<!--{if $op == 'editforum'}-->{lang mod_option_forum_edit}<!--{elseif $op == 'recommend'}-->{lang mod_option_forum_recommend}<!--{/if}-->
				<!--{if $modforums['fids']}--> -&nbsp;
			</span>
			<span class="ftid">
				<select name="fid" id="fid" width="150" class="ps" change="location.href='{$cpscript}?mod=modcp&action=$_GET[action]&op=$op&fid='+$('fid').value">
					<!--{loop $modforums[list] $id $name}-->
					<option value="$id" {if $id == $_G[fid]}selected="selected"{/if}>$name</option>
					<!--{/loop}-->
				</select>
			</span>
			<!--{else}-->
				{lang mod_message_forum_nopermission}
			<!--{/if}-->
		</h1>
	<!--{/if}-->

	<!--{if $_G[fid] && $_G['forum']['ismoderator']}-->

		<!--{if $op == 'editforum'}-->
			<script type="text/javascript">
				var allowbbcode = allowimgcode = 1;
				var allowhtml = forumallowhtml = allowsmilies = 0;
			</script>
			<div class="exfm">
				<script type="text/javascript" src="{$_G[setting][jspath]}bbcode.js?{VERHASH}"></script>
				<form method="post" autocomplete="off" action="{$cpscript}?mod=modcp&action=$_GET[action]&op=$op">
					<input type="hidden" name="formhash" value="{FORMHASH}">
					<input type="hidden" name="fid" value="$_G[fid]">
					<table cellspacing="0" cellpadding="0">
						<caption>
							<h4>{lang forum_rules}</h4>
							<div id="rulespreview"></div>
						</caption>
						<tr>
							<td class="ptm">
								<div class="tedt">
									<div class="bar">
										<div class="y"><a href="javascript:;" onclick="$('rulespreview').innerHTML = bbcode2html($('rulesmessage').value)">{lang memcp_profile_preview}</a></div>
										<!--{eval $seditor = array('rules', array('bold', 'color', 'img', 'link'));}-->
										<!--{subtemplate common/seditor}-->
									</div>
									<div class="area">
										<textarea id="rulesmessage" name="rulesnew" class="pt" rows="8" {if !$alloweditrules}disabled="disabled" readonly="readonly"{/if}>$_G['forum']['rules']</textarea>
									</div>
								</div>
								<!--{if !$alloweditrules}-->
								<div>{lang forum_not_allow}</div>
								<!--{/if}-->
							</td>
							<td width="15%" valign="top" class="ptm">
								<p>{lang discuzcode} {lang enabled}</p>
								<p>{lang post_imgcode} {lang enabled}</p>
								<p>{lang post_html} {lang disabled}</p>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<button type="submit" name="editsubmit" class="pn" value="true"><strong>{lang submit}</strong></button>
								<!--{if $forumupdate}-->
									{lang mod_message_forum_update}
								<!--{/if}-->
							</td>
						</tr>
					</table>
				</form>
			</div>

		<!--{elseif $op == 'recommend'}-->
			<script type="text/javascript" src="{$_G[setting][jspath]}forum_moderate.js?{VERHASH}"></script>
			<!--{if $threadlist}-->
				<form method="post" autocomplete="off" action="{$cpscript}?mod=modcp&action=$_GET[action]&show=$_GET['show']">
					<input type="hidden" name="formhash" value="{FORMHASH}" />
					<input type="hidden" name="op" value="$op" />
					<input type="hidden" name="page" value="$page" />
					<input type="hidden" name="fid" value="$_G[fid]" />
					<input type="hidden" name="check" value="$check" />
					<table cellspacing="0" cellpadding="0" class="dt">
						<thead>
							<tr>
								<th class="c">&nbsp;</th>
								<th>{lang displayorder}</th>
								<th>{lang subject}</th>
								<th>{lang author}</th>
								<th>{lang recommend_moderator}</th>
								<th>{lang mod_forum_recommend_expiration}</th>
								<th></th>
							</tr>
						</thead>
						<!--{loop $threadlist $thread}-->
							<tr>
								<td><input{if $_G['forum']['modrecommend']['sort'] == 1} readonly="readonly"{/if} type="checkbox" name="delete[]" class="pc" value="$thread[tid]" /></td>
								<td><input{if $_G['forum']['modrecommend']['sort'] == 1} readonly="readonly"{/if} type="text" name="order[{$thread[tid]}]" class="px" size="3" value="$thread[displayorder]" /></td>
								<td><input{if $_G['forum']['modrecommend']['sort'] == 1} readonly="readonly"{/if} type="text" name="subject[{$thread[tid]}]" class="px" value="$thread[subject]" /></td>
								<td class="xi2">$thread[authorlink]</td>
								<td class="xi2"><!--{if $thread['moderatorid']}--><a href="home.php?mod=space&uid=$thread[moderatorid]" target="_blank">{$moderatormembers[$thread[moderatorid]][username]}</a><!--{else}-->System<!--{/if}--></td>
								<td><input type="text" name="expirationrecommend[{$thread[tid]}]" id="expirationrecommend" class="px" value="{$thread[expiration]}" autocomplete="off" {if $_G['forum']['modrecommend']['sort'] == 1} readonly="readonly"{else} onclick="showcalendar(event, this, true)"{/if} /></td>
								<td><!--{if $_G['forum']['modrecommend']['sort'] != 1}--><a href="javascript:;" onclick="showWindow('mods', 'forum.php?mod=topicadmin&optgroup=1&action=moderate&operation=recommend&frommodcp=2&show={$show}&tid={$thread[tid]}')" class="xi2">{lang more_settings}</a><!--{/if}--></td>
							</tr>
						<!--{/loop}-->
						<tr class="bw0_all">
							<td><label for="chkall" onclick="checkall(this.form)"><input type="checkbox" name="chkall" id="chkall" class="pc" />{lang delete_check}</label></td>
							<td colspan="6">
								<!--{if !empty($reportlist[pagelink])}-->$reportlist[pagelink]<!--{/if}-->
								<button type="submit" name="editsubmit" class="pn" value="yes"><strong>{lang mod_forum_recommend_list}</strong></button>
								<!--{if $listupdate}-->
									{lang mod_message_forum_updaterecommend}
								<!--{/if}-->
							</td>
						</tr>
					</table>
				</form>
			<!--{else}-->
				<div class="emp">{lang search_nomatch}</div>
			<!--{/if}-->

		<!--{elseif $op == 'member'}-->
			<!--{if $_G['forum']['jointype'] != 2}-->
				<div class="emp">{lang mod_forum_member_closed}</div>
			<!--{else}-->
				<ul class="tb cl">
					<li{if $do == 'mod'} class="a"{/if}><a href="{$cpscript}?mod=modcp&action=forum&op=member$forcefid" hidefocus="true">{lang mod_forum_member_join}</a></li>
					<li{if $do == 'list'} class="a"{/if}><a href="{$cpscript}?mod=modcp&action=forum&op=member&do=list$forcefid" hidefocus="true">{lang mod_forum_member_list}</a></li>
				</ul>
			<!--{/if}-->
			<!--{if $do == 'mod'}-->
				<div class="xld xlda">
					<!--{if $checkusers}-->
						<p class="tbmu cl">
							<a href="{$cpscript}?mod=modcp&action=forum&op=member$forcefid&checkall=2&formhash={FORMHASH}">{lang ignore_all}</a><span class="pipe">|</span>
							<a href="{$cpscript}?mod=modcp&action=forum&op=member$forcefid&checkall=1&formhash={FORMHASH}">{lang pass_all}</a>
						</p>
						<!--{loop $checkusers $uid $user}-->
						<dl class="bbda cl">
							<dd class="m avt"><!--{echo avatar($user['uid'], 'small')}--></dd>
							<dt><a href="home.php?mod=space&uid=$user[uid]">$user[username]</a> <span class="xw0">({date($user['joindateline'])})</span></dt>
							<dd class="pns">
								<button type="submit" name="checkusertrue" class="pn pnc" value="true"
								        onclick="location.href='{$cpscript}?mod=modcp&action=forum&op=member$forcefid&uid=$user[uid]&checktype=1&formhash={FORMHASH}&formhash={FORMHASH}'"><em>{lang pass}</em></button> &nbsp; <button type="submit" name="checkuserfalse" class="pn" value="true" onclick="location.href='forum.php?mod=modcp&action=forum&op=member$forcefid&uid=$user[uid]&checktype=2&formhash={FORMHASH}'"><em>{lang ignore}</em></button></dd>
						</dl>
						<!--{/loop}-->
					<!--{else}-->
						<p class="tbmu cl">
							{lang mod_forum_member_join_empty}
						</p>
					<!--{/if}-->
				</div>
				<!--{if $multipage}--><div class="pgs cl mtm">$multipage</div><!--{/if}-->
			<!--{elseif $do == 'list'}-->
				<form method="post" autocomplete="off" action="{$cpscript}?mod=modcp&action=forum&op=member&do=list$forcefid">
					<input type="hidden" name="formhash" value="{FORMHASH}">
					<div class="xld xlda">
						<!--{if $alluserlist}-->
						<ul class="ml mls cl ptn">
							<!--{loop $alluserlist $user}-->
							<li>
								<a href="home.php?mod=space&uid=$user[uid]" class="avt" c="1"><!--{echo avatar($user['uid'], 'small')}--></a>
								<p><a href="home.php?mod=space&uid=$user[uid]">$user[username]</a></p>
								<p><input type="checkbox" class="pc" name="uid[]" value="$user[uid]" /></p>
							</li>
							<!--{/loop}-->
						</ul>
						<!--{if $multipage}--><div class="pgs cl mtm">$multipage</div><!--{/if}-->
						<div class="cl bbta ptn">
							<button type="submit" name="delsubmit" value="true" class="pn"><span>{lang delete}</span></button>
						</div>
						<!--{else}-->
						<p class="tbmu cl">
							{lang mod_forum_member_list_empty}
						</p>
						<!--{/if}-->
					</div>
				</form>
			<!--{/if}-->
		<!--{/if}-->
	<!--{/if}-->
</div>
<script type="text/javascript" reload="1">
	if($('fid')) {
		simulateSelect('fid');
	}
</script>
<script type="text/javascript" src="{$_G[setting][jspath]}calendar.js?{VERHASH}"></script>