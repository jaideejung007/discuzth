<?php exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
	<title><!--{if !empty($navtitle)}-->$navtitle - <!--{/if}--><!--{if empty($nobbname)}--> $_G['setting']['bbname'] - <!--{/if}--> {lang waptitle} - Powered by Discuz!</title>
	$_G['setting']['seohead_mobile']
	{cells common/header/meta}
	<!--{hook/global_meta_mobile}-->
	<base href="{$_G['siteurl']}" />
	<script type="text/javascript">var STYLEID = '{STYLEID}', STATICURL = '{STATICURL}', IMGDIR = '{IMGDIR}', VERHASH = '{VERHASH}', charset = '{CHARSET}', discuz_uid = '{$_G['uid']}', cookiepre = '{$_G['config']['cookie']['cookiepre']}', cookiedomain = '{$_G['config']['cookie']['cookiedomain']}', cookiepath = '{$_G['config']['cookie']['cookiepath']}', showusercard = '{$_G['setting']['showusercard']}', attackevasive = '{$_G['config']['security']['attackevasive']}', disallowfloat = '{$_G['setting']['disallowfloat']}', creditnotice = '<!--{if $_G['setting']['creditnotice']}-->$_G['setting']['creditnames']<!--{/if}-->', defaultstyle = '$_G['style']['defaultextstyle']', REPORTURL = '$_G['currenturl_encode']', SITEURL = '$_G['siteurl']', JSPATH = '$_G['setting']['jspath']', DEFAULTAVATAR = '$_G[setting][defaultavatar]';;</script>