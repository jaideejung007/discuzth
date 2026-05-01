<?php exit('Access Denied');?>

<!--  Load Editor.js's Css -->
<link rel="stylesheet" type="text/css" href="{STATICURL}js/editorjs/editorjs_mobile.css?{VERHASH}" />
<!--{if $_GET['action'] == 'edit' && !empty($postinfo['noticetrimstr_html'])}-->
$postinfo['noticetrimstr_html']
<!--{/if}-->
<div class="json-editor" xmlns="http://www.w3.org/1999/html">
		<input type="hidden" name="message" id="needmessage" value="" />
		<input type="hidden" name="content" id="content" value="" placeholder="" />
		<input type="hidden" name="contentType" id="contentType" value="json" />
		<input type="hidden" name="contentEditor" id="contentEditor" value="jsonEditor" />
		<!--{if $_GET['action'] == 'edit'}-->
		<input type="hidden" name="noticetrimstr" id="noticetrimstr" value="{$postinfo['noticetrimstr']}" />
		<!--{/if}-->
		<div class="json-editor__content _json-editor__content--small">
			<div id="editorjs"></div>
		</div>
		<div class="json-editor__output">
			<pre class="json-editor__output-content" id="output"></pre>
		</div>
</div>
<style>
	body {
		background-color: #EEEEEE;
	}
	.json-editor {
		min-height: 200px;
	}
</style>
<!-- 常量 -->
<script type="text/javascript">
    const editorid = '{$editorid}';
    const editor_fid = "{$_G['fid']}";
    const editor_uid = "{$_G['uid']}";
    const editor_tid = "{$_G['tid']}";
    const editor_hash = "{echo md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])}";
    const editor_remote_attachurl = "{$_G['setting']['ftp']['attachurl']}";
    const editor_attachurl = "{$_G['setting']['attachurl']}";
    const isEditMode = "{$_GET['action'] == 'edit'}" ? 'true' : 'false';
    // EDITOR_TOOLS
    let EDITOR_TOOLS = {};
    // first define the tools to be made avaliable in the columns
    let column_tools = {};
    // next define the tools in the main block
    // Warning - Dont just use main_tools - you will probably generate a circular reference
    let main_tools = {};
    let i18n_tools = {};
    let content = {blocks: []};
    let originalContent = {};
    <!--{if $_GET[action] == 'edit'}-->
    try {
	    const blocks = {$postinfo['content']};
	    content = {
		    blocks: Array.isArray(blocks) ? blocks : []
	    };
	    originalContent = JSON.stringify(content);
    } catch (e) {
	    content = {blocks: []};
	    originalContent = JSON.stringify(content);
    }
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
<script >

// 启动周期性自动保存
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', startPeriodicAutoSave);
} else {
	startPeriodicAutoSave();
}
// 自动保存相关变量和函数
var autoSaveTimeout;
var AUTO_SAVE_DELAY = 30000;

/**
 * 获取草稿存储键名
 */
function getDraftKey() {
    return 'editor_draft_' + (typeof editor_tid !== 'undefined' ? editor_tid : 'new');
}

/**
 * 调度自动保存 - 防抖处理
 */
function scheduleAutoSave() {
    // 清除之前的定时器
    clearTimeout(autoSaveTimeout);

    // 设置新的定时器
    autoSaveTimeout = setTimeout(() => {
        saveDraft();
    }, 5000); // 用户停止操作5秒后保存
}

/**
 * 定时自动保存 - 周期性保存
 */
function startPeriodicAutoSave() {
    // 确保在DOM加载完成后启动
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setInterval(() => {
                saveDraft();
            }, AUTO_SAVE_DELAY);
        });
    } else {
        setInterval(() => {
            saveDraft();
        }, AUTO_SAVE_DELAY);
    }
}

// 修改保存草稿函数
function saveDraft() {
    // 检查editor是否已正确初始化
    if (typeof editor === 'undefined' || !editor || typeof editor.save !== 'function') {
        return;
    }

    // 只有在编辑模式下且内容发生更改时才保存草稿
    if (isEditMode && !contentChanged) {
        return;
    }

    editor.save().then((outputData) => {
	    try {
		    // 比较当前内容与原始内容
		    const currentContent = JSON.stringify(outputData);
		    if (isEditMode && currentContent === originalContent) {
			    // 如果是编辑模式且内容没有变化，则不保存草稿
			    return;
		    }

		    // 添加时间戳到草稿数据中
		    const draftWithTimestamp = {
			    ...outputData,
			    _draftTimestamp: new Date().getTime()
		    };

		    localStorage.setItem(getDraftKey(), JSON.stringify(draftWithTimestamp));

		    // console.log('草稿已保存:', new Date().toLocaleString());
	    } catch (e) {
		    // console.error('保存草稿失败:', e);
	    }
    }).catch((error) => {
        // console.error('获取编辑器内容失败:', error);
    });
}

// 修改加载草稿函数
function loadDraft() {
    try {
        const draft = localStorage.getItem(getDraftKey());
        if (draft) {
            const draftData = JSON.parse(draft);
            return draftData;
        }
    } catch (e) {
        // console.error('加载草稿失败:', e);
    }
    return null;
}

// 修改清除草稿函数
function clearDraft() {
    try {
        localStorage.removeItem(getDraftKey());
        // 重置内容变更标记
        contentChanged = false;
    } catch (e) {
        // console.error('清除草稿失败:', e);
    }
}

var draft = loadDraft();
var useDraft = false;

// 初始默认使用数据库数据
var initialData = content;

// 检查是否有需要用户选择的情况
if (draft && isEditMode && JSON.stringify(draft).replace(/_draftTimestamp":\d+/g, '') !== originalContent) {
	// 创建非阻塞的自定义提示框
	createDraftNotification();
}

// 创建非阻塞的草稿通知
function createDraftNotification() {
	// 检查是否已存在提示框，避免重复创建
	if (document.getElementById('draft-notification') || document.getElementById('draft-overlay')) return;

	// 创建遮罩层
	const overlay = document.createElement('div');
	overlay.id = 'draft-overlay';
	overlay.style.cssText = `
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0, 0, 0, 0.5);
			z-index: 9998;
			/* 防止移动端滚动穿透 */
			touch-action: none;
			/* 支持平滑过渡效果 */
			transition: opacity 0.2s ease;
		`;

	// 创建提示框元素
	const notification = document.createElement('div');
	notification.id = 'draft-notification';
	notification.style.cssText = `
			position: fixed;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			background: white;
			padding: 20px;
			border-radius: 8px;
			box-shadow: 0 4px 12px rgba(0,0,0,0.15);
			z-index: 9999;
			/* 响应式宽度设置 */
			width: 90%;
			max-width: 400px;
			min-width: 280px;
			text-align: center;
			/* 支持平滑过渡效果 */
			transition: transform 0.2s ease, opacity 0.2s ease;
		`;

	notification.innerHTML = `
			<p style="margin-bottom: 20px; font-size: 16px; line-height: 1.5;">{lang json_editor_tip_draft_title}</p>
			<div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
				<button id="use-draft-btn" style="
					padding: 10px 20px;
					background: #007bff;
					color: white;
					border: none;
					border-radius: 4px;
					cursor: pointer;
					font-size: 16px;
					min-width: 120px;
					/* 移动端适配 */
					flex: 1;
					max-width: 150px;
				">
					{lang json_editor_tip_draft_yes}
				</button>
				<button id="use-newest-btn" style="
					padding: 10px 20px;
					background: #6c757d;
					color: white;
					border: none;
					border-radius: 4px;
					cursor: pointer;
					font-size: 16px;
					min-width: 120px;
					/* 移动端适配 */
					flex: 1;
					max-width: 150px;
				">
					{lang json_editor_tip_draft_no}
				</button>
			</div>
		`;

	// 添加到页面
	document.body.appendChild(overlay);
	document.body.appendChild(notification);

	// 为移动设备添加触摸反馈
	const buttons = notification.querySelectorAll('button');
	buttons.forEach(button => {
		button.style.userSelect = 'none';
		button.style.touchAction = 'manipulation';
		button.addEventListener('touchstart', function() {
			this.style.opacity = '0.8';
		});
		button.addEventListener('touchend', function() {
			this.style.opacity = '1';
		});
	});

	// 绑定事件
	document.getElementById('use-draft-btn').addEventListener('click', function() {
		// 用户选择使用草稿
		if (editor) {
			editor.isReady
				.then(() => {
					editor.render(draft);
				})
				.catch((err) => {
					// console.error('切换到草稿内容失败:', err);
				});
		}
		notification.remove();
		overlay.remove();
	});

	document.getElementById('use-newest-btn').addEventListener('click', function() {
		// 用户选择使用最新数据
		clearDraft();

		// 重新渲染编辑器内容为数据库中的最新数据
		if (editor) {
			editor.isReady
				.then(() => {
					// 使用原始的content（数据库中的最新数据）重新渲染
					editor.render(content);
				})
				.catch((err) => {
					// console.error('切换到最新数据失败:', err);
				});
		}

		notification.remove();
		overlay.remove();
	});
}
</script>
<!-- Initialization -->
<script src="{STATICURL}js/editorjs/init_content_touch.js?{VERHASH}"></script>

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