<div class="tedt">
	<div class="bar">
		<span class="y">
			<a href="forum.php?mod=post&action=$_GET[action]&fid=$_G[fid]&extra=$extra{if $_GET[action] == 'reply'}&tid=$_G[tid]{if !empty($_GET[reppost])}&reppost=$_GET[reppost]{/if}{if !empty($_GET[repquote])}&repquote=$_GET[repquote]{/if}{if !empty($page)}&page=$page{/if}{/if}{if !empty($stand)}&stand=$stand{/if}" onclick="switchAdvanceMode(this.href);doane(event);">{lang post_advancemode}</a>
		</span>
		<!--{eval $seditor = array('post', array('bold', 'color', 'img', 'link', 'quote', 'code', 'smilies', 'at'));}-->
		<!--{subtemplate common/seditor}-->
	</div>
	<div class="area">
		<textarea rows="7" cols="80" name="message" id="postmessage" onKeyDown="seditor_ctlent(event, '$(\'postsubmit\').click();')" class="pt">$message</textarea>
	</div>
</div>