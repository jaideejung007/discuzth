<?php echo '';exit;?>
<!DOCTYPE html>
<html>
	<head>
	<meta charset="{CHARSET}" />
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><!--{if !empty($navtitle)}-->$navtitle - <!--{/if}--><!--{if empty($nobbname)}--> $_G['setting']['bbname'] - <!--{/if}--> Powered by Discuz!</title>
	$_G['setting']['seohead']

	<meta name="keywords" content="{if !empty($metakeywords)}{echo dhtmlspecialchars($metakeywords)}{/if}" />
	<meta name="description" content="{if !empty($metadescription)}{echo dhtmlspecialchars($metadescription)} {/if}{if empty($nobbname)},$_G['setting']['bbname']{/if}" />
	<meta name="generator" content="Discuz! $_G['setting']['version']" />
	<meta name="author" content="Discuz! Team" />
	<meta name="copyright" content="{lang copyright_s}" />
	<meta name="MSSmartTagsPreventParsing" content="True" />
	<meta http-equiv="MSThemeCompatible" content="Yes" />
	<!--{hook/global_meta}-->
	<base href="{$_G['siteurl']}" />
	<!--{csstemplate}-->
	<script type="text/javascript">var STYLEID = '{STYLEID}', STATICURL = '{STATICURL}', IMGDIR = '{IMGDIR}', VERHASH = '{VERHASH}', charset = '{CHARSET}', discuz_uid = '$_G[uid]', cookiepre = '{$_G[config][cookie][cookiepre]}', cookiedomain = '{$_G[config][cookie][cookiedomain]}', cookiepath = '{$_G[config][cookie][cookiepath]}', showusercard = '{$_G[setting][showusercard]}', attackevasive = '{$_G[config][security][attackevasive]}', disallowfloat = '{$_G[setting][disallowfloat]}', creditnotice = '<!--{if $_G['setting']['creditnotice']}-->$_G['setting']['creditnames']<!--{/if}-->', defaultstyle = '$_G[style][defaultextstyle]', REPORTURL = '$_G[currenturl_encode]', SITEURL = '$_G[siteurl]', JSPATH = '$_G[setting][jspath]', CSSPATH = '$_G[setting][csspath]', DYNAMICURL = '{$_G[dynamicurl] or ''}', DEFAULTAVATAR = '$_G[setting][defaultavatar]';</script>
	<script type="text/javascript" src="{$_G[setting][jspath]}common.js?{VERHASH}"></script>
	<!--{if empty($_GET['diy'])}--><!--{eval $_GET['diy'] = '';}--><!--{/if}-->
	<!--{if !isset($topic)}--><!--{eval $topic = array();}--><!--{/if}-->
	<style>
	<!--{if !$_G['style']['top_nav_widthauto']}-->
	.dz_btm_layer{width: 1200px;margin: 0 auto;}
	.dz_btm_layer .dz_layer_nav{width: 609px;}
	.dz_btm_layer .header-searcher .search-input{width: 200px;}
	<!--{/if}-->
	<!--{if $_G['style']['top_nav_bgc']}-->
	.dz_layer_top{background: $_G['style']['top_nav_bgc'];}
	<!--{/if}-->
	<!--{if $_G['style']['top_nav_dark'] && $_G['style']['top_nav_bgc']}-->
	.dzlogo {display:inline-block;width:140px;height:36px;background:url({STYLEIMGDIR}/logo_hei.png) no-repeat 0 50%;background-size:auto 36px}
	.dzlogo img {display:none}
	.dz_layer_nav ul li a{color: var(--dz-ff);}
	.dz_layer_nav ul li.a a,.dz_layer_nav ul li a:hover{color: var(--dz-ff);}
	.dz_layer_nav ul li a:before{background: var(--dz-bgf);}
	.dz_layer_nav ul li a::after{background: var(--dz-bgf);}
	.header-notice .notice-icon .dzicon {color: var(--dz-ff);}
	.header-searcher input:focus{border-color: var(--dz-bgfglass);}
	.header-notice .notice-icon:hover, .header-notice.open .notice-icon {background: var(--dz-bgfglass);}
	.dz_menumore::after{color: var(--dz-ff);}
	<!--{/if}-->
	<!--{if $_G['style']['bottom_bgc']}-->
	.dz_footc{background: $_G['style']['bottom_bgc'];}
	<!--{/if}-->
	<!--{if $_G['style']['bottom_dark'] && $_G['style']['bottom_bgc']}-->
	.dz_footc a,.dz_footc_nav .pipe{color: var(--dz-bgfglass);}
	.dz_footc_copy{color: var(--dz-bgfglass);}
	.dz_footc_nav{border-bottom: 1px solid var(--dz-bgfglass);}
	.dz_footc_dico{background: none;}
	<!--{/if}-->
	<!--{if $_G['style']['viewthread_fastpost'] == 3}-->
	#f_pst{display: none;}
	<!--{/if}-->
	</style>	