<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2><a href="<!--{if $_GET['fromguid'] == 'hot' && $_G['setting']['guidestatus']}-->forum.php?mod=guide&view=hot&page=$_GET['page']<!--{else}-->forum.php?mod=forumdisplay&fid=$_G['fid']&<!--{eval echo rawurldecode($_GET['extra']);}--><!--{/if}-->"><!--{eval echo strip_tags($_G['forum']['name']) ? strip_tags($_G['forum']['name']) : $_G['forum']['name'];}--></a></h2>
	<div class="my"><a href="index.php"><i class="dm-house"></i></a></div>
</div>

<div class="viewthread">
	$threadsortshow[typetemplate]
</div>

<!--{template common/footer}-->
