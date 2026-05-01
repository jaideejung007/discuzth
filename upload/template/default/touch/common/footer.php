<?php exit('Access Denied');?>
<!--{hook/global_footer_mobile}-->
<div id="mask" style="display:none;"></div>
<!--{if $_G['setting']['statcode']}--><div id="statcode" style="display:none;">{$_G['setting']['statcode']}</div><!--{/if}-->
<!--{if !$nofooter}-->
<div class="foot_height"></div>
<div id="mfoot" class="foot flex-box">
	<!--{loop $_G['setting']['mnavs'] $nav}-->
		<!--{if is_array($nav) && $nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] == 1) || ($nav['level'] == 3 && $_G['adminid'] == 2))}-->
			<!--{if $nav['is_post']}-->
				<!--{if !empty($post_url)}-->
					<a href="{$post_url}" class="flex foot-post">
				<!--{else}-->
					<a href="forum.php?mod=misc&action=nav" class="flex foot-post">
				<!--{/if}-->
			<!--{else}-->
				<a href="{$nav['url']}" class="flex">
			<!--{/if}-->
			<span class="foot-ico">{$nav['icon']}</span><span class="foot-txt">{$nav['name']}</span></a>
		<!--{/if}-->
	<!--{/loop}-->
</div>
<!--{/if}-->

<!--{if getgpc('mobilediy')}-->
	<script>mobileDiy.init('{$_G['style']['tpldirectory']}', 'touch/{$_G['style']['tplfile']}', '{echo dsign({$_G['style']['tpldirectory']}.'touch/'.{$_G['style']['tplfile']})}');</script>
<!--{/if}-->

{cells common/footer/js}

</body>
</html>
<!--{eval updatesession();}-->
<!--{if defined('IN_MOBILE')&&!defined('IN_PREVIEW')}-->
	<!--{eval output();}-->
<!--{else}-->
	<!--{eval output_preview();}-->
<!--{/if}-->