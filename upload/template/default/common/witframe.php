<?php exit('Access Denied');?>
<!--{if empty($_G['setting']['witframe_plugins']['page'][$_GET['path']]['notemplate'])}-->
<!--{template common/header}-->
<!--{/if}-->
<div id="ct" class="wp cl w">
	<div class="nfl">
		<iframe src="{$url}" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>
	</div>
</div>
<!--{if empty($_G['setting']['witframe_plugins']['page'][$_GET['path']]['notemplate'])}-->
<!--{template common/footer}-->
<!--{/if}-->