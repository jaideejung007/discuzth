<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2><!--{eval echo strip_tags($_G['forum']['name']) ? strip_tags($_G['forum']['name']) : $_G['forum']['name'];}--></h2>
	<div class="my"><a href="forum.php?mod=post&action=newthread&fid={$_G['fid']}"><i class="dm-edit"></i></a></div>
</div>

<div class="threadlist_box cl">
	$sorttemplate['header']
	$sorttemplate['body']
	$sorttemplate['footer']
</div>

<!--{template common/footer}-->
