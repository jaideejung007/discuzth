<?php exit('Access Denied');?>
<!--{template common/header}-->

<style>
	.ttp a:hover {background-image: none !important;}
	.threadlist tbody:last-child th, .threadlist tbody:last-child td { border-bottom: 1px solid {$_G['style']['contentseparate']}; }
</style>

<style id="diy_style" type="text/css"></style>
<!--[diy=diynavtop]--><div id="diynavtop" class="area"></div><!--[/diy]-->

<div id="pt" class="bm cl">
	<div class="z">
		<a href="./" class="nvhm" title="{lang homepage}">$_G[setting][bbname]</a><em>&raquo;</em><a href="forum.php">{$_G[setting][navs][2][navname]}</a>$navigation
	</div>
</div>

<!--{ad/text/wp a_t}-->

<div class="wp">
	<!--[diy=diy1]--><div id="diy1" class="area"></div><!--[/diy]-->
</div>

<div class="boardnav">
	<div id="ct" class="wp cl{if $allowside} ct2{/if}"{if $leftside} style="margin-left:{$_G['leftsidewidth_mwidth']}px"{/if}>
		<!--{if $leftside}-->
			<div id="sd_bdl" class="bdl" onmouseover="showMenu({'ctrlid':this.id, 'pos':'dz'});" style="width:{$_G['setting']['leftsidewidth']}px;margin-left:-{$_G['leftsidewidth_mwidth']}px">
				<!--[diy=diyleftsidetop]--><div id="diyleftsidetop" class="area"></div><!--[/diy]-->

				<div class="tbn" id="forumleftside">
					<!--{subtemplate forum/forumdisplay_leftside}-->
				</div>

				<!--[diy=diyleftsidebottom]--><div id="diyleftsidebottom" class="area"></div><!--[/diy]-->
			</div>
		<!--{/if}-->

		<div class="mn">
			<div class="drag">
				<!--[diy=diy4]--><div id="diy4" class="area"></div><!--[/diy]-->
			</div>

			<div id="threadlist">
				{cells forum/portal/navlist}
				{cells forum/portal/threadlist threadlist}
			</div>

			<!--[diy=diyindexportalbottom]--><div id="diyforumdisplaybottom" class="area"></div><!--[/diy]-->
		</div>

		<!--{if $allowside}-->
			<div class="sd">
				<div class="drag">
					<!--[diy=diy2]--><div id="diy2" class="area"></div><!--[/diy]-->
				</div>
			</div>
		<!--{/if}-->
	</div>
</div>

<div class="wp mtn">
	<!--[diy=diy3]--><div id="diy3" class="area"></div><!--[/diy]-->
</div>

<script>
	ajaxupdateevents($('threadlist'));
</script>

<!--{template common/footer}-->
