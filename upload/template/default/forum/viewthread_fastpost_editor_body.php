<!--{if empty($_GET[from]) && $_G[setting][fastsmilies]}--><div id="fastsmiliesdiv" class="y"><div id="fastsmiliesdiv_data"><div id="fastsmilies"></div></div></div><!--{/if}-->
<div{if empty($_GET[from]) && $_G[setting][fastsmilies]} class="hasfsl"{/if} id="fastposteditor">
	<div class="tedt{if !($_G[forum_thread][special] == 5 && empty($firststand))} mtn{/if}">
		<div class="bar">
			<span class="y">
				<!--{hook/viewthread_fastpost_func_extra}-->
				<a href="forum.php?mod=post&action=reply&fid=$_G[fid]&tid=$_G[tid]{if $_GET[from]}&from=$_GET[from]{/if}" onclick="return switchAdvanceMode(this.href)">{lang post_advancemode}</a>
			</span>
			<!--{eval $seditor = array('fastpost', array('at', 'bold', 'color', 'img', 'link', 'quote', 'code', 'smilies'), !$allowfastpost ? 1 : 0, $allowpostattach && $_GET['from'] != 'preview' && $allowfastpost ? '<span class="pipe z">|</span><span id="spanButtonPlaceholder">'.lang('template', 'upload').'</span>' : '');}-->
			<!--{hook/viewthread_fastpost_ctrl_extra}-->
			<!--{subtemplate common/seditor}-->
		</div>
		<div class="area">
			<!--{if $allowfastpost}-->
			<textarea rows="6" cols="80" name="message" id="fastpostmessage" onKeyDown="seditor_ctlent(event, {if $_GET['ordertype'] != 1}'fastpostvalidate($(\'fastpostform\'))'{else}'$(\'fastpostform\').submit()'{/if});" class="pt"{eval echo getreplybg($_G['forum']['replybg']);}></textarea>
			<!--{else}-->
			<div class="pt hm">
				<!--{if !$_G['uid']}-->
				<!--{if !$_G['connectguest']}-->
				{lang login_to_reply} <a href="member.php?mod=logging&action=login" onclick="showWindow('login', this.href)" class="xi2">{lang login}</a> | <a href="member.php?mod={$_G[setting][regname]}" class="xi2">$_G['setting']['reglinkname']</a>
				<!--{elseif $_G['forum_thread']['closed'] == 1}-->
				{lang post_thread_closed}
				<!--{else}-->
				{lang connect_fill_profile_to_post}
				<!--{/if}-->
				<!--{elseif $thread['closed'] && !$_G['forum']['ismoderator'] && !$thread['isgroup']}-->
				{lang post_thread_closed}
				<!--{elseif !$thread['isgroup'] && $post_autoclose = checkautoclose($thread) && $autoclose = $_G['forum']['autoclose']}-->
				<!--{if $post_autoclose == 'post_thread_closed_by_dateline'}-->
				{lang post_thread_closed_by_dateline}
				<!--{else}-->
				{lang post_thread_closed_by_lastpost}
				<!--{/if}-->
				<!--{elseif $_G['forum']['replyperm'] && !forumperm($_G['forum']['replyperm'])}-->
				{lang replyperm_nopermission}
				<!--{else}-->
				{lang no_permission_to_post}<a href="javascript:;" onclick="$('fastpostform').submit()" class="xi2">{lang click_to_show_reason}</a>
				<!--{/if}-->
				<!--{hook/global_login_text}-->
			</div>
			<!--{/if}-->
		</div>
	</div>
</div>