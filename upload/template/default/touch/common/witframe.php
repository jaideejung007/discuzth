<?php exit('Access Denied');?>
<!--{if empty($_G['setting']['witframe_plugins']['page'][$_GET['path']]['notemplate'])}-->
<!--{template common/header}-->
<!--{/if}-->
<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2>{$navtitle}</h2>
	<div class="my"><a href="index.php"><i class="dm-house"></i></a></div>
</div>
<iframe src="{$url}" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>
<!--{if empty($_G['setting']['witframe_plugins']['page'][$_GET['path']]['notemplate'])}-->
<!--{template common/footer}-->
<!--{/if}-->