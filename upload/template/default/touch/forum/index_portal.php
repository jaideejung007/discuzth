<?php exit('Access Denied');?>
<!--{template common/header}-->

<!--{if ($_G['setting']['mobile']['forum']['index'] && $_GET['forumlist'] != 1) || !$_G['setting']['mobile']['forum']['index']}-->
<div class="header cl">
	<div class="mzlogo"><a href="javascript:;">{$_G['style']['touchlogo']}</a></div>
	<div class="myss"><a href="search.php?mod=forum"><i class="dm-search"></i>{lang mobsearchtxt}</a></div>
</div>
<!--{else}-->
<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2>{$_G['setting']['navs'][2]['navname']}</h2>
	<div class="my"><a href="search.php?mod=forum"><i class="dm-search"></i></a></div>
</div>
<!--{/if}-->

{cells forum/portal/navlist}
{cells forum/portal/threadlist threadlist}

<!--{template common/footer}-->
