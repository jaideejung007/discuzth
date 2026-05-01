<?php exit('Access Denied');?>
<!--{template common/header}-->
<!--  Load Layui -->
<link rel="stylesheet" type="text/css" href="{STATICURL}js/layui/layui.css?{VERHASH}" />
<link rel="stylesheet" type="text/css" href="{STATICURL}js/layui/extend/inputTags.css?{VERHASH}" />
<!--  Load Editor.js's Css -->
<link rel="stylesheet" type="text/css" href="{STATICURL}js/editorjs/editorjs.css?{VERHASH}" />

<div class="json-editor" xmlns="http://www.w3.org/1999/html">
    <form method="post" id="postform"
          {if $_GET[action] == 'newthread'}action="forum.php?mod=post&action={if $special != 2}newthread{else}newtrade{/if}&fid=$_G[fid]&extra=$extra&topicsubmit=yes"
    {elseif $_GET[action] == 'reply'}action="forum.php?mod=post&action=reply&fid=$_G[fid]&tid=$_G[tid]&extra=$extra&replysubmit=yes"
    {elseif $_GET[action] == 'edit'}action="forum.php?mod=post&action=edit&extra=$extra&editsubmit=yes" $enctype
    {/if}
    onsubmit="ajaxpost('postform', 'return_postform', 'return_postform', 'onerror');return false;">
    <div>
        <em id="return_postform"></em>
    </div>
    <input type="hidden" name="formhash" id="formhash" value="{FORMHASH}" />
    <input type="hidden" name="posttime" id="posttime" value="{TIMESTAMP}" />
    <!--{if $_GET['action'] == 'edit'}-->
    <input type="hidden" name="delattachop" id="delattachop" value="0" />
    <!--{/if}-->
    <input type="hidden" name="handlekey" id="handlekey" value="postform" />
    <input type="hidden" name="inajax" id="inajax" value="1" />
    <input type="hidden" name="message" id="message" value="" />
    <input type="hidden" name="content" id="content" value="" />
    <input type="hidden" name="contentType" id="contentType" value="json" />
    <input type="hidden" name="contentEditor" id="contentEditor" value="jsonEditor" />
    <!--{if $_GET['action'] == 'edit'}-->
    <input type="hidden" name="noticetrimstr" id="noticetrimstr" value="{$postinfo['noticetrimstr']}" />
    <!--{/if}-->
    <input type="hidden" id="postsave" name="save" value="">
    <input type="hidden" id="mobileeditor" name="mobileeditor" value="0">
    <!--{if !empty($_GET['modthreadkey'])}--><input type="hidden" name="modthreadkey" id="modthreadkey" value="$_GET['modthreadkey']" /><!--{/if}-->
    <!--{if $_GET['action'] == 'reply'}-->
    <input type="hidden" name="noticeauthor" value="$noticeauthor" />
    <input type="hidden" name="noticetrimstr" value="$noticetrimstr" />
    <input type="hidden" name="noticeauthormsg" value="$noticeauthormsg" />
    <!--{if $reppid}-->
    <input type="hidden" name="reppid" value="$reppid" />
    <!--{/if}-->
    <!--{if $_GET['reppost']}-->
    <input type="hidden" name="reppost" value="$_GET['reppost']" />
    <!--{elseif $_GET['repquote']}-->
    <input type="hidden" name="reppost" value="$_GET['repquote']" />
    <!--{/if}-->
    <!--{/if}-->
    <!--{if $_GET[action] == 'edit'}-->
    <input type="hidden" name="fid" id="fid" value="$_G[fid]" />
    <input type="hidden" name="tid" value="$_G[tid]" />
    <input type="hidden" name="pid" value="$pid" />
    <input type="hidden" name="page" value="$_GET[page]" />
    <!--{/if}-->
    <div class="json-editor__content _json-editor__content--small">
	<!--{template forum/jsoneditor_toolbar}-->
        <div class="json-editor-subject">
            <input id="subject" name="subject" class="subject" placeholder="{lang json_editor_title_placeholder}" <!--{if $_GET[action] == 'edit'}-->value="{$postinfo['subject']}"<!--{elseif $_GET[action] == 'reply'}-->value="RE: {$thread['subject']}"<!--{/if}-->/>
        </div>
        <div id="editorjs"></div>
        <div id="editor-param" class="editor-param">
            <div class="publish-line"></div>
            <div class="css-publish">
                <div class="css-publish__title">{lang json_editor_title_publish}</div>
                <div class="css-publish__cover">
                    <label class="css-publish__cover__title">{lang json_editor_title_cover}</label>
                    <div class="css-publish__cover__image">
                        <div>
                            <div class="css-publish__cover__image__icon" style="display: block;" id="upload-cover-drag">
                                <svg style="width:4em; height:4em;" class="icon" aria-hidden="true">
                                    <use xlink:href="#icon-shangchuan"></use>
                                </svg>
                                <div>{lang json_editor_upload_tip}</div>
                                <div class="<!--{if !$postinfo['coverpath']}-->layui-hide<!--{/if}-->" id="upload-cover-preview">
                                    <hr> <img src="<!--{if $postinfo['coverpath']}-->{$postinfo['coverpath']}?{VERHASH}<!--{/if}-->" style="max-width: 100%">
                                </div>
                                <input type="hidden" id="upload-cover-aid" name="cover_aid" autocomplete="off" class="layui-input" <!--{if $_GET[action] == 'edit'}-->value=""<!--{/if}-->/>
                            </div>
                            <div class="css-publish__cover__image__desc">{lang json_editor_upload_desc}</div>
                        </div>
                    </div>
                </div>
                <!--{if $isfirstpost && !empty($_G['forum'][threadtypes][types])}-->
                <div class="css-publish__div">
                    <div class="css-publish__div_content">
                        <label class="css-publish__div_title">{lang json_editor_title_types}</label>
                        <div class="layui-form layui-row layui-col-space16">
                            <div class="layui-col-md12">
                                <div class="ftid">
                                    <!--{if $_G['forum']['ismoderator'] || empty($_G['forum']['threadtypes']['moderators'][$thread[typeid]])}-->
                                    <select name="typeid" id="typeid" width="80">
                                        <option value="0">{lang select_thread_catgory}</option>
                                        <!--{loop $_G['forum'][threadtypes][types] $typeid $name}-->
                                        <!--{if empty($_G['forum']['threadtypes']['moderators'][$typeid]) || $_G['forum']['ismoderator']}-->
                                        <option value="$typeid"{if (isset($thread['typeid']) && $thread['typeid'] == $typeid) || getgpc('typeid') == $typeid} selected="selected"{/if}><!--{echo strip_tags($name)}--></option>
                                        <!--{/if}-->
                                        <!--{/loop}-->
                                    </select>
                                    <!--{else}-->
                                    [<!--{echo strip_tags($_G['forum']['threadtypes']['types'][$thread[typeid]]);}-->]
                                    <!--{/if}-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--{/if}-->
                <div class="css-publish__div">
                    <div class="css-publish__div_content">
                        <label class="css-publish__div_title">{lang json_editor_title_original}</label>
                        <div class="layui-form layui-row layui-col-space16">
                            <div class="layui-col-md12">
                                <select name="original">
                                    <option value="0" <!--{if $postinfo['original'] == '' || $postinfo['original'] == 0}-->selected="selected"<!--{/if}-->>{lang json_editor_title_original_0}</option>
                                    <option value="1" <!--{if $postinfo['original'] == 1}-->selected="selected"<!--{/if}-->>{lang json_editor_title_original_1}</option>
                                    <option value="-1" <!--{if $postinfo['original'] == -1}-->selected="selected"<!--{/if}-->>{lang json_editor_title_original_f1}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="css-publish__div">
                    <div class="css-publish__div_content">
                        <label class="css-publish__div_title">{lang json_editor_title_tags}</label>
                        <div class="layui-form layui-row">
                            <div class="layui-col-sm12 tags">
                                <input type="text" id="inputtags" name="inputtags" placeholder="{lang json_editor_tags_enter}" autocomplete="off" class="layui-input" style="width: 500px;"/>
                                <input type="hidden" id="tags" name="tags" autocomplete="off" <!--{if $postinfo['tag']}-->value="{$postinfo['tag']}"<!--{/if}--> class="layui-input" style="width: 500px;"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="css-publish__div">
                    <div class="css-publish__div_content">
                        <label class="css-publish__div_title">{lang json_editor_title_source}</label>
                        <div class="layui-form layui-row layui-col-space16">
                            <div class="layui-col-sm4">
                                <input type="text" name="source_title" placeholder="{lang json_editor_source_title}" class="layui-input" <!--{if $postinfo['source']['title']}-->value="{$postinfo['source']['title']}"<!--{/if}-->>
                            </div>
                            <div class="layui-col-sm8">
                                <input type="text" name="source_url" placeholder="{lang json_editor_source_url}" class="layui-input" style="width: 305px;" <!--{if $postinfo['source']['url']}-->value="{$postinfo['source']['url']}"<!--{/if}-->>
                            </div>
                        </div>
                    </div>
                </div>
                <!--{if $_GET[action] != 'edit'}-->
	            {cell common/seccheck/code_start}
	                <div class="css-publish__div">
	                    <div class="css-publish__div_content">
	                        <label class="css-publish__div_title">{lang json_editor_title_seccode}</label>
		                <div class="layui-col-sm4" style="width: 150px;">
		                    <input type="text" name="seccodeverify" placeholder="{lang json_editor_seccode_desc}" class="layui-input"/>
	                        </div>
	                        <div class="layui-col-sm4">{cell common/seccheck/code_image} {cell common/seccheck/code_text}</div>
	                    </div>
	                </div>
	            {cell common/seccheck/code_end}
	            {cell common/seccheck/qaa_start}
	            <div class="css-publish__div">
		        <div class="css-publish__div_content">
			    <label class="css-publish__div_title">{lang json_editor_title_seccode}</label>
			    <div class="layui-col-sm4" style="width: 150px;">
			        <input type="text" name="secanswer" class="layui-input"/>
			    </div>
			    <div class="layui-col-sm4">{cell common/seccheck/qaa_question} {cell common/seccheck/qaa_change}</div>
		        </div>
	            </div>
	            {cell common/seccheck/qaa_end}
                <!--{/if}-->
            </div>

        </div>

    </div>
    <div class="json-editor__output">
        <pre class="json-editor__output-content" id="output"></pre>
    </div>
    <div class="css-publish__bar">
        <div class="css-publish__bar__content">
            <div class="css-publish__bar__left" onclick="window.scrollTo({top: 0, left: 0, behavior: 'smooth'})">
                <div class="css-publish__bar__left__title">{lang json_editor_top}</div>
                <svg width="1.2em" height="1.2em" class="icon" aria-hidden="true">
                    <use xlink:href="#icon-fangxiang-xiangshang"></use>
                </svg>
            </div>
            <div class="css-publish__bar__draft__tip"></div>
            <div class="css-publish__bar__save">
                <div class="layui-btn-container">
                    <button type="button" id="saveButton" class="layui-btn layui-btn-primary layui-border" <!--{if $_GET[action] == 'edit'}-->style="display: none;"<!--{/if}-->>{lang json_editor_save}</button>
                </div>
            </div>
            <div class="layui-btn-container">
                <button type="button" id="submitButton" class="layui-btn layui-bg-blue">{lang json_editor_submit}</button>
            </div>
        </div>
    </div>
    </form>
</div>

<!-- 常量 -->
<script type="text/javascript">
    const editor_fid = "{$_G['fid']}";
    const editor_uid = "{$_G['uid']}";
    const editor_hash = "{echo md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])}";
    const editor_remote_attachurl = "{$_G['setting']['ftp']['attachurl']}";
    const editor_attachurl = "{$_G['setting']['attachurl']}";
    // EDITOR_TOOLS
    let EDITOR_TOOLS = {};
    // first define the tools to be made avaliable in the columns
    let column_tools = {};
    // next define the tools in the main block
    // Warning - Dont just use main_tools - you will probably generate a circular reference
    let main_tools = {};
    let i18n_tools = {};
    let content = "";
    <!--{if $_GET[action] == 'edit'}-->
    content = {
        blocks: {$postinfo['content']}
    };
    <!--{/if}-->
</script>

<!-- Load Editor.js's Core -->
<script src="{STATICURL}js/editorjs/editorjs.umd.js?{VERHASH}"></script>

<!-- Load Ajax Core -->
<script src="{STATICURL}js/editorjs/ajax.js?{VERHASH}"></script>
<script src="{STATICURL}js/editorjs/util.js?{VERHASH}"></script>
<!-- Initialization -->
<script src="{STATICURL}js/editorjs/tools/editorjs-drag-drop/editorjs-drag-drop.js?{VERHASH}"></script><!-- editorjs-drag-drop.js -->
<script src="{STATICURL}js/editorjs/tools/editorjs-undo/editorjs-undo.js?{VERHASH}"></script><!-- editorjs-undo.js -->
<script src="{STATICURL}js/editorjs/tools/anchor/anchor.js?{VERHASH}"></script><!-- anchor.js -->
<script src="{STATICURL}js/editorjs/tools/hide/hide.js?{VERHASH}"></script><!-- hide.js -->

<!-- Load Tools -->
<!--{loop $editorblocks $eblock}-->
<script src="$eblock['jspath']?{VERHASH}"></script>
<!--{/loop}-->
<script type="text/javascript">
    let column_available = false;
    EDITOR_TOOLS = Object.assign(EDITOR_TOOLS, {
        tools_anchor: {
            anchorTune: AnchorTune
        },
	tools_hide: {
	    hideTune: HideTune
	}
    });
    main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_anchor);
    column_tools = Object.assign(column_tools, EDITOR_TOOLS.tools_anchor);
    main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_hide);
    column_tools = Object.assign(column_tools, EDITOR_TOOLS.tools_hide);
    <!--{loop $editorblocks $eblock}-->
    EDITOR_TOOLS = Object.assign(EDITOR_TOOLS, $eblock['config']);
    <!--{if $eblock['available'] && $eblock['columns']}-->
    column_tools = Object.assign(column_tools, EDITOR_TOOLS.tools_$eblock['identifier']);
    <!--{/if}-->
    <!--{if $eblock['identifier'] == 'columns' && $eblock['available']}-->
    column_available = true;
    <!--{/if}-->
    main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_$eblock['identifier']);
    if (EDITOR_TOOLS.i18n !== undefined) {
	    i18n_tools = mergeObjects(i18n_tools, EDITOR_TOOLS.i18n);
    }
    <!--{/loop}-->
    // 多列
    if(column_available && Object.keys(column_tools).length !== 0) {
        const tools_columns = {
            columns : {
                class : editorjsColumns,
                config : {
                    EditorJsLibrary : EditorJS, //ref EditorJS - This means only one global thing
                    tools : column_tools,
                }
            },
        }
        main_tools = Object.assign(main_tools, tools_columns);
    }
</script>
<!-- Initialization -->
<script src="{STATICURL}js/editorjs/init.js?{VERHASH}"></script>

<!--  Load icon -->
<script src="{STATICURL}js/iconfont.js?{VERHASH}"></script>
<style type="text/css">
    .icon {
        width: 1em; height: 1em;
        vertical-align: -0.15em;
        fill: currentColor;
        overflow: hidden;
    }
</style>
<!--  Load Layui -->
<script src="{STATICURL}js/layui/layui.js?{VERHASH}"></script>
<!-- 标签输入框 -->
<script type="text/javascript">
    layui.config({
        base: '{STATICURL}js/layui/extend/'
    }).extend({
        inputTags: 'inputTags',
    });
    var tags_default = [];
    <!--{if $postinfo['tag']}-->
    var tagsString = "{$postinfo['tag']}";
    tags_default = tagsString.split(',');
    <!--{/if}-->
    layui.use(['inputTags'],function(){
        var inputTags = layui.inputTags;
        inputTags.render({
            elem:'#inputtags',//定义输入框input对象
            elemSave:'#tags',//定义存储tags的input对象
            content: tags_default,//默认标签
            aldaBtn: true,//是否开启获取所有数据的按钮
            done: function(value){ //回车后的回调
                //console.log(value)
            }
        })
    })
</script>
<!-- 封面图上传 -->
<script>
    layui.use(['jquery','layer'],function(){
        var upload = layui.upload;
        var $ = layui.jquery;
        // 渲染
        upload.render({
            elem: '#upload-cover-drag',
            url: 'misc.php?mod=swfupload&action=swfupload&operation=jsoneditorupload&fid='+editor_fid,
            field: 'Filedata',
            data: {
                'uid': editor_uid,
                'hash': editor_hash,
            },
            done: function(res){
                layer.msg('上传成功');
                // console.log(res);
                $('#upload-cover-aid').val(res.file.aid);
                $('#upload-cover-preview').removeClass('layui-hide')
                    .find('img').attr('src', res.file.url);
            }
        });
    });
</script>