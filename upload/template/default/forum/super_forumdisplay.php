<?php exit('Access Denied');?>
<!--{template common/header}-->

<style id="diy_style" type="text/css"></style>
<!--[diy=diynavtop]--><div id="diynavtop" class="area"></div><!--[/diy]-->
<div id="pt" class="bm cl">
	<div class="z">
		<a href="./" class="nvhm" title="{lang homepage}">$_G[setting][bbname]</a><em>&raquo;</em><a href="forum.php">{$_G[setting][navs][2][navname]}</a>$navigation
	</div>
</div>

<div class="wp">
	<!--[diy=diy1]--><div id="diy1" class="area"></div><!--[/diy]-->
</div>

<div id="wp" class="ct2 wp cl">
	<div class="mn">
		<!--{if $quicksearchlist && !$_GET['archiveid']}-->
			<div class="bm"><!--{subtemplate forum/search_sortoption}--></div>
		<!--{/if}-->
		$sorttemplate['header']
		$sorttemplate['body']
		$sorttemplate['footer']
	</div>
	<div class="sd pph">
		<div class="drag">
			<!--[diy=diyrighttop]--><div id="diyrighttop" class="area"></div><!--[/diy]-->
		</div>
	</div>
</div>



</div>



<!--{template common/footer}-->
