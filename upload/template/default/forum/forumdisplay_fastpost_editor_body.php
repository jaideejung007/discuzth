<!--{if $_G[setting][fastsmilies]}--><div id="fastsmiliesdiv" class="y"><div id="fastsmiliesdiv_data"><div id="fastsmilies"></div></div></div><!--{/if}-->
<div{if $_G[setting][fastsmilies]} class="hasfsl"{/if} id="fastposteditor">
	<div class="tedt">
		<div class="bar">
			<span class="y">
				<!--{hook/forumdisplay_fastpost_func_extra}-->
				<a href="forum.php?mod=post&action=newthread&fid=$_G[fid]" onclick="switchAdvanceMode(this.href);doane(event);">{lang post_advancemode}</a>
			</span>
			<!--{eval $seditor = array('fastpost', array('at', 'bold', 'color', 'img', 'link', 'quote', 'code', 'smilies'), !$allowfastpost ? 1 : 0, $allowpostattach && $allowfastpost ? '<span class="pipe z">|</span><span id="spanButtonPlaceholder">'.lang('template', 'upload').'</span>' : '');}-->
			<!--{hook/forumdisplay_fastpost_ctrl_extra}-->
			<!--{subtemplate common/seditor}-->
		</div>
		<div class="area">
			<!--{if $allowfastpost}-->
			<textarea rows="6" cols="80" name="message" id="fastpostmessage" onKeyDown="seditor_ctlent(event, '$(\'fastpostsubmit\').click()');" class="pt"{eval echo getreplybg($_G['forum']['replybg']);}></textarea>
			<!--{else}-->
			<div class="pt hm">
				<!--{if !$_G['uid']}-->
				<!--{if !$_G['connectguest']}-->
				{lang login_to_post} <a href="member.php?mod=logging&action=login" onclick="showWindow('login', this.href)" class="xi2">{lang login}</a> | <a href="member.php?mod={$_G[setting][regname]}" class="xi2">$_G['setting']['reglinkname']</a>
				<!--{else}-->
				{lang connect_fill_profile_to_post}
				<!--{/if}-->
				<!--{elseif $_G['forum']['replyperm'] && !forumperm($_G['forum']['replyperm'])}-->
				{lang replyperm_nopermission}
				<!--{else}-->
				{lang no_permission_to_post}<a href="javascript:;" onclick="$('newspecial').onclick()" class="xi2">{lang click_to_show_reason}</a>
				<!--{/if}-->
				<!--{hook/global_login_text}-->
			</div>
			<!--{/if}-->
		</div>
	</div>
</div>