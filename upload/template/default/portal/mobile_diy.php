<?php exit('Access Denied');?>
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
	<base href="{$_G['siteurl']}" />

	<script type="text/javascript">var STYLEID = '{STYLEID}', STATICURL = '{STATICURL}', IMGDIR = '{IMGDIR}', VERHASH = '{VERHASH}', charset = '{CHARSET}', discuz_uid = '$_G[uid]', cookiepre = '{$_G[config][cookie][cookiepre]}', cookiedomain = '{$_G[config][cookie][cookiedomain]}', cookiepath = '{$_G[config][cookie][cookiepath]}', showusercard = '{$_G[setting][showusercard]}', attackevasive = '{$_G[config][security][attackevasive]}', disallowfloat = '{$_G[setting][disallowfloat]}', creditnotice = '<!--{if $_G['setting']['creditnotice']}-->$_G['setting']['creditnames']<!--{/if}-->', defaultstyle = '$_G[style][defaultextstyle]', REPORTURL = '$_G[currenturl_encode]', SITEURL = '$_G[siteurl]', JSPATH = '$_G[setting][jspath]', CSSPATH = '$_G[setting][csspath]', DYNAMICURL = '{$_G[dynamicurl] or ''}';</script>
	<script type="text/javascript" src="{$_G[setting][jspath]}common.js?{VERHASH}"></script>
	<!--{if empty($_GET['diy'])}--><!--{eval $_GET['diy'] = '';}--><!--{/if}-->
	<!--{if !isset($topic)}--><!--{eval $topic = array();}--><!--{/if}-->

	{cells common/header/meta}
	<script type="text/javascript" src="{$_G[setting][jspath]}portal.js?{VERHASH}"></script>
</head>

<link rel="stylesheet" type="text/css" href="{$_G['setting']['csspath']}{STYLEID}_css_mobile_diy.css?{VERHASH}" />
<link rel="stylesheet" type="text/css" href="{$_G['setting']['csspath']}{STYLEID}_css_mobile_diy_page.css?{VERHASH}" />
</head>

<body id="nv_{$_G[basescript]}" class="pg_{CURMODULE}{if $_G['basescript'] === 'portal' && CURMODULE === 'list' && !empty($cat)} {$cat['bodycss']}{/if}" onkeydown="if(event.keyCode==27) return false;">
	<div id="append_parent"></div><div id="ajaxwaitid"></div>
	<style id="diy_style" type="text/css"></style>
	
	<div class="m-diy-header">
	<div class="design-header-back">{lang mobilediy_title}</div>
		<div id="dztopcontrol" class="design-header-more hide">
			<a href="javascript:" onclick="spaceDiy.recover();return false;" title="{lang header_restore_backup_desc}"><i class='dzicon fa_backup'></i>{lang header_restore_backup}</a>
			<a href="javascript:" onclick="drag.openFrameImport();return false;" title="{lang header_import_desc}"><i class='dzicon fa_import' ></i>{lang header_import}</a>
			<a href="javascript:" onclick="drag.frameExport();return false;" title="{lang header_export_desc}"><i class='dzicon fa_export' ></i>{lang header_export}</a>
			<a href="javascript:" onclick="drag.blockForceUpdateBatch();return false;" title="{lang header_update_desc}"><i class='dzicon fa_reset'></i>{lang header_update}</a>
			<a href="javascript:" onclick="drag.clearAll();return false;" title="{lang header_clearall_desc}"><i class='dzicon fa_bin'></i>{lang header_clearall}</a>
			<a href="javascript:" onclick="spaceDiy.save();return false;" id="savebtn"><i class='dzicon fa_save'></i>{lang save}</a>
			<a href="javascript:" onclick="spaceDiy.cancel();return false;" id="diycancel"><i class='dzicon fa_x'></i>{lang close}</a>
		</div>
		<div id="samplepanel" class="hide design-header-more">
			<a id="open_diy_module" href="javascript:saveUserdata('diy_advance_mode', '1');openDiy();"><i class='dzicon fa_layer'></i>{lang mobilediy_start}</a>
		</div>
	</div>
	<div class="m-diy-container">
		<div class="m-diy-preview">
			<div class="m-diy-mobile">
				<div class="design-mobile-head design-mobile-head-h5">
					<div class="design-mobile-head-title" id="preview_title"></div>
				</div>
				<div class="design-mobile-preview">
					<iframe id="diyframe" src="{$url}&mobilediy=yes" style="width: 100%;height:100%" frameborder="0"></iframe>
					<div id="mask" style="width: 350px;height: 100%;position: relative;"></div>
				</div>
			</div>
		</div>
		<div class="m-diy-sidebar" id="diyside_box">
			<div class="design-sidebar-title">{lang mobilediy_drag_in}</div>
			<div class="design-sidebar-nav">
				<div id="panel"></div>
			</div>
			<div class="design-sidebar-bottom"></div>
		</div>
		<div class="m-diy-action" id="controlpanel">
			<div class="design-action-header">
				<div class="action-header_title">{lang mobilediy_setting}</div>
				<a href="javascript:" class="action-header_link"></a>
				<div class="action-header_description">{lang mobilediy_setting_comment}</div>
			</div>
			<div class="design-action-scroll">
					<div >
						<div id="controlheader">
							<ul id="controlnav" class="control-nav">
								<li id="navframe" class="current"><a href="javascript:" onclick="spaceDiy.getdiy('frame');this.blur();return false;">{lang header_frame}</a></li>
								<li id="navblockclass"><a href="javascript:" onclick="spaceDiy.getdiy('blockclass');this.blur();return false;" id="hd_mod">{lang header_module}</a></li>
								<li id="navpagestyle"><a href="javascript:;" onclick="spaceDiy.getdiy('pagestyle');this.blur();return false;">{lang page_style}</a></li>
								<li id="navmodulestyle"><a href="javascript:;" onclick="spaceDiy.getdiy('modulestyle');this.blur();return false;">{lang module_style}</a></li>
							</ul>
						</div>
						<div id="controlcontent">
							
							<ul id="contentframe" class="content-frame content">
								<li><a href="javascript:;" id="frame_1" onmousedown="drag.createObj(event,'frame','1');" onfocus="this.blur();" data="$widthstr"><img src="{STATICURL}image/diy/layout-1.png" />100%{lang header_frame}</a></li>
							</ul>
							<div id="contentblockclass" class="content-block-class content"></div>
						</div>
					</div>

					<form method="post" autocomplete="off" name="diyform" id="diyform" action="$_G[siteurl]portal.php?mod=portalcp&ac=diy">
					<input type="hidden" name="template" value="$_G['style']['tplfile']" />
					<input type="hidden" name="tpldirectory" value="$_G['style']['tpldirectory']" />
					<input type="hidden" name="diysign" value="{echo dsign({$_G['style']['tpldirectory']}.{$_G['style']['tplfile']})}" />
					<input type="hidden" name="prefile" id="prefile" value="$_G['style']['prefile']" />
					<input type="hidden" name="savemod" value="$_G['style']['tplsavemod']" />
					<input type="hidden" name="spacecss" value="" />
					<input type="hidden" name="style" value="" />
					<input type="hidden" name="rejs" value="" />
					<input type="hidden" name="handlekey" value="" />
					<input type="hidden" name="layoutdata" value="" />
					<input type="hidden" name="formhash" value="{FORMHASH}" />
					<input type="hidden" name="gobackurl" id="gobackurl" value=""/>
					<input type="hidden" name="recover" value=""/>
					<input type="hidden" name="optype" value=""/>

					<input type="hidden" name="diysubmit" value="true"/>
					</form>
			</div>
		</div>
	</div>



<script>
    function start_diy() {
        appendscript('{$_G[setting][jspath]}common_m_diy.js?{VERHASH}', '', false, null, function () {
        	appendscript('{$_G[setting][jspath]}portal_m_diy{if !check_diy_perm($topic, 'layout')}_data{/if}.js?{VERHASH}');
        });
    }
    $('mask').style.top = '-' + $('diyframe').clientHeight + 'px'; 
</script>

<!--{eval output();}-->
</body>

</html>
