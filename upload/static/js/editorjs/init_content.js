// json editor undo config
const config = {
	shortcuts: {
		undo: ["CMD+Z"],
		redo: ["CMD+Y", "CMD+SHIFT+Z"],
	}
};

var undo = undefined;
var contentChanged = false;

var i18n_default = {
	messages: {
		tools: {
			image: {
				'Upload an image': $L("json_editor_tools_uploadimage"),
			},
			attaches: {
				'Select file to upload': $L("json_editor_tools_Selectfiletoupload"),
			},
			hyperlink: {
				'Save': $L("json_editor_tools_Save"),
				'Select target': $L("json_editor_tools_Selecttarget"),
				'Select rel': $L("json_editor_tools_Selectrel"),
			},
			list: {
				"Ordered": $L("json_editor_tools_Ordered"),
				"Unordered": $L("json_editor_tools_Unordered"),
			},
			anchorTune: {
				'Anchor': $L("json_editor_tools_Anchor"),
			},
			hideTune: {
				'hide': $L("json_editor_tools_Hide"),
			},
			columns: {
				'2 Columns': $L("json_editor_tools_2Columns"),
				'3 Columns': $L("json_editor_tools_3Columns"),
				'Roll Columns': $L("json_editor_tools_RollColumns"),
				'Are you sure?': $L("json_editor_tools_Areyousure"),
				'This will delete Column 3!': $L("json_editor_tools_willDeleteColumn3"),
				'Yes, delete it!': $L("json_editor_tools_deleteit"),
				'Cancel': $L("json_editor_tools_cancel"),
			},
		},
		toolNames: {
			"Text": $L("json_editor_toolNames_text"),
			"Heading": $L("json_editor_toolNames_Heading"),
			"ImageTool": $L("json_editor_toolNames_ImageTool"),
			"Image": $L("json_editor_toolNames_Image"),
			"Video": $L("json_editor_toolNames_Video"),
			"Audio": $L("json_editor_toolNames_Audio"),
			"Attach": $L("json_editor_toolNames_Attach"),
			"Caption": $L("json_editor_toolNames_Caption"),
			"NestedList": $L("json_editor_toolNames_NestedList"),
			"List": $L("json_editor_toolNames_List"),
			"Warning": $L("json_editor_toolNames_Warning"),
			"Checklist": $L("json_editor_toolNames_Checklist"),
			"Quote": $L("json_editor_toolNames_Quote"),
			"Code": $L("json_editor_toolNames_Code"),
			"CodeFlask": $L("json_editor_toolNames_Code"),
			"raw": $L("json_editor_toolNames_raw"),
			"Delimiter": $L("json_editor_toolNames_Delimiter"),
			"Table": $L("json_editor_toolNames_Table"),
			"Link": $L("json_editor_toolNames_Link"),
			"Marker": $L("json_editor_toolNames_Marker"),
			"Bold": $L("json_editor_toolNames_Bold"),
			"Italic": $L("json_editor_toolNames_Italic"),
			"InlineCode": $L("json_editor_toolNames_InlineCode"),
			"Download Markdown": $L("json_editor_toolNames_DownloadMarkdown"),
			"Import Markdown": $L("json_editor_toolNames_ImportMarkdown"),
			"Alert": $L("json_editor_toolNames_Alert"),
			"Columns": $L("json_editor_toolNames_Columns"),
			"Attachment": $L("json_editor_toolNames_Attachment"),
			"Hyperlink": $L("json_editor_toolNames_Hyperlink"),
			'Anchor': $L("json_editor_toolNames_Anchor"),
			'hide': $L("json_editor_tools_Hide"),
			'Embed': $L("json_editor_toolNames_Embed"),
		},
		"ui": {
			"blockTunes": {
				"toggler": {
					"Click to tune": $L("json_editor_tools_Clicktotune"),
					"or drag to move": $L("json_editor_tools_ordragtomove"),
				},
			},
			"inlineToolbar": {
				"converter": {
					"Convert to": $L("json_editor_tools_Convertto"),
				}
			},
			"toolbar": {
				"toolbox": {
					"Add": $L("json_editor_tools_toolboxAdd"),
				}
			},
			"popover": {
				"Filter": $L("json_editor_tools_Filter"),
				"Nothing found": $L("json_editor_tools_Nothingfound"),
				"Convert to": $L("json_editor_tools_Convertto"),
			}
		},

		/**
		 * Section allows to translate Block Tunes
		 */
		blockTunes: {
			/**
			 * Each subsection is the i18n dictionary that will be passed to the corresponded Block Tune plugin
			 * The name of a plugin should be equal the name you specify in the 'tunes' section for that plugin
			 *
			 * Also, there are few internal block tunes: "delete", "moveUp" and "moveDown"
			 */
			"delete": {
				"Delete": $L("json_editor_tools_Delete"),
				"Click to delete": $L("json_editor_tools_Clicktodelete"),
			},
			"moveUp": {
				"Move up": $L("json_editor_tools_Moveup"),
			},
			"moveDown": {
				"Move down": $L("json_editor_tools_moveDown"),
			},
			"anchorTune": {
				'Anchor': $L("json_editor_tools_Anchor"),
			},
			"hideTune": {
				'hide': $L("json_editor_tools_Hide"),
			},
			"image": {
				'With border': $L("json_editor_tools_Withborder"),
				'Stretch image': $L("json_editor_tools_Stretchimage"),
				'With background': $L("json_editor_tools_Withbackground"),
			}
		},
	}
};

/**
 * To initialize the Editor, create a new instance with configuration object
 * @see docs/installation.md for mode details
 */
var editor = new EditorJS({
	/**
	 * Enable/Disable the read only mode
	 */
	readOnly: false,
	/** 启用自动对焦 */
	autofocus: true,

	/**
	 * Wrapper of Editor
	 */
	holder: 'editorjs',

	/**
	 * Common Inline Toolbar settings
	 * - if true (or not specified), the order from 'tool' property will be used
	 * - if an array of tool names, this order will be used
	 */
	// inlineToolbar: ['link', 'marker', 'bold', 'italic', 'list'],
	// inlineToolbar: true,

	/**
	 * Tools list
	 */
	tools: main_tools,
	/*
	toolbar: {
	    // 这里设置需要显示的按钮
	    buttons: ['header', 'bold', 'italic', 'underline', 'strike', 'code', 'link', 'image']
	},
	 */
	i18n: i18n_tools !== undefined ? mergeObjects(i18n_default,  i18n_tools) : i18n_default,

	/**
	 * This Tool will be used as default
	 */
	// defaultBlock: 'paragraph',

	/**
	 * Initial Editor data
	 */
	data: window.initialData || loadDraft() || content,
	onReady: function () {
		console.log("Delaying Save to launch Column Editors")

		undo = new Undo({editor, config});
		new DragDrop(editor);

		setTimeout(() => {
			//saveButton.click();
		}, 2000)

	},
	onChange: function (e) {
		console.log(e);
		// 触发自动保存草稿
		contentChanged = true;
		scheduleAutoSave();
		// console.log('something changed');
	}
});

function saveJsonContent(event) {
	editor.save()
	    .then((savedData) => {
		    //console.log(JSON.stringify(savedData, null, 4));
		    var postform = document.getElementById("postform");

		    var content = document.getElementById("content");
		    if (savedData.blocks == '' || savedData.blocks == undefined) {
			    editor.notifier.show({
				    message: $L("json_editor_tip_content_null"),
				    style: 'error',
				    // time: 30
			    });
			    event.stopPropagation();
			    return false;
		    }
		    content.value = JSON.stringify(savedData);
		    //console.log(content.value);

	    }).then(() => {
		ajaxpost('postform', 'return_postform', 'return_postform', 'onerror');
	})
	    .catch((error) => {
		    console.error($L("json_editor_tip_content_null"), error);
	    });
}

function succeedhandle_postform(url, msg, param) {
	editor.notifier.show({
		message: $L("json_editor_tip_publish_success"),
		style: 'success'
	});
	clearDraft();
	window.location.href = url;
}
const addBlock = (type, data = undefined, e) => {
	const index = editor.blocks.getCurrentBlockIndex() + 1;
	editor.blocks.insert(type, data, undefined, index);
	//editor.caret.setToLastBlock('end', 0);
	editor.caret.setToBlock(index);
	editor.toolbar.close();
	if (e && e.stopPropagation) {
		e.stopPropagation();
	}
	return false;
}

const convertBlock = (type, data = undefined, e) => {
	const index = editor.blocks.getCurrentBlockIndex();
	const currentBlock = editor.blocks.getBlockByIndex(index);
	editor.blocks.convert(currentBlock.id, type, {
		...data, // 合并预设数据
	}).then((newBlock) => {
		//console.log("New Block:", newBlock);
		editor.caret.setToBlock(newBlock.id, 'end');
	}).catch((error) => {
		console.log('Block Tool with type not found', error);
	});

	if (e && e.stopPropagation) {
		e.stopPropagation();
	}
	return false;
}

const blockEvent = (type, data = undefined, e) => {
	try {
		if (editor.blocks.getBlocksCount() === undefined || editor.blocks.getBlocksCount() <= 0) {
			return false;
		}
		const editorContainer = document.querySelector('#editorjs');
		editorContainer.focus();
		switch (type) {
			case 'undo':
				undo.undo();
				break;
			case 'redo':
				undo.redo();
				break;
			case 'bold':
				document.execCommand('bold');
				editor.inlineToolbar.close();
				break;
			case 'italic':
				document.execCommand('italic');
				editor.inlineToolbar.close();
				break;
			case 'underline':
				const underlineBtn = document.getElementById('json-editor-underline');
				underlineBtn.click();
				editor.inlineToolbar.close();
				break;
			case 'clearFormatting':
				const clearFormattingBtn = document.getElementById('json-editor-clear-formatting');
				clearFormattingBtn.click();
				editor.inlineToolbar.close();
				break;
			case 'emoji':
				const emojiTool = window.EmojiInlineTool.getInstance(null, main_tools?.emoji?.config);
				emojiTool.saveSelection();
				emojiTool.togglePicker();
				break;
			default:
				break;
		}
	} catch (e) {
		return false;
	}
	if (e && e.stopPropagation) {
		e.stopPropagation();
	}
	return false;
}

/**
 * 绑定按钮悬浮显示指定ID的弹窗
 * @param buttonSelector 按钮选择器
 * @param popoverSelector Popover 选择器 (如 Popover-Header)
 */
function bindPopover(buttonSelector, popoverSelector) {
	const button = document.getElementById(buttonSelector);
	const popover = document.getElementById(popoverSelector);

	if (!button || !popover) return;

	let hideTimeout;
	// 动态定位函数
	const updatePosition = () => {
		const btnRect = button.getBoundingClientRect();

		popover.style.position = 'absolute';
		popover.style.top = `${btnRect.bottom + window.scrollY}px`; // 下方8px间距
		popover.style.left = `${btnRect.left + window.scrollX - 20 }px`;
	};

	// 鼠标进入时激活
	button.addEventListener('mouseenter', () => {
		clearTimeout(hideTimeout);
		popover.classList.remove('Popover-hidden');
		updatePosition();

		// 动态监听
		window.addEventListener('scroll', updatePosition, { passive: true });
		window.addEventListener('resize', updatePosition);
	});
	button.addEventListener('click', () => {
		if (popover.classList.contains('Popover-hidden')) {
			clearTimeout(hideTimeout);
			popover.classList.remove('Popover-hidden');
			updatePosition();

			// 动态监听
			window.addEventListener('scroll', updatePosition, { passive: true });
			window.addEventListener('resize', updatePosition);
		} else {
			popover.classList.add('Popover-hidden');
			window.removeEventListener('scroll', updatePosition);
			window.removeEventListener('resize', updatePosition);
		}
	});

	button.addEventListener('mouseleave', () => {
		hideTimeout = setTimeout(() => {
			popover.classList.add('Popover-hidden');
			window.removeEventListener('scroll', updatePosition);
			window.removeEventListener('resize', updatePosition);
		}, 150);
	});

	popover.addEventListener('mouseenter', () => {
		clearTimeout(hideTimeout);
		popover.classList.remove('Popover-hidden');
		updatePosition();

		// 动态监听
		window.addEventListener('scroll', updatePosition, { passive: true });
		window.addEventListener('resize', updatePosition);
	});
	popover.addEventListener('click', () => {
		clearTimeout(hideTimeout);
		popover.classList.add('Popover-hidden');
		window.removeEventListener('scroll', updatePosition);
		window.removeEventListener('resize', updatePosition);
	});
	popover.addEventListener('mouseleave', () => {
		clearTimeout(hideTimeout);
		popover.classList.add('Popover-hidden');
		window.removeEventListener('scroll', updatePosition);
		window.removeEventListener('resize', updatePosition);
	});
}


const jsonEditorToolbar = document.getElementById('json-editor-toolbar');
let isDragging = false;
let startX, scrollLeft;

jsonEditorToolbar.addEventListener('mousedown', (e) => {
	isDragging = true;
	startX = e.pageX - jsonEditorToolbar.offsetLeft;
	scrollLeft = jsonEditorToolbar.scrollLeft;
});

jsonEditorToolbar.addEventListener('mouseleave', () => {
	isDragging = false;
});

jsonEditorToolbar.addEventListener('mouseup', () => {
	isDragging = false;
});

jsonEditorToolbar.addEventListener('mousemove', (e) => {
	if (!isDragging) return;
	e.preventDefault();
	const x = e.pageX - jsonEditorToolbar.offsetLeft;
	const walk = x - startX;
	jsonEditorToolbar.scrollLeft = scrollLeft - walk;
});
jsonEditorToolbar.addEventListener('wheel', (e) => {
	if (e.deltaY !== 0) {
		jsonEditorToolbar.scrollLeft += e.deltaY;
		e.preventDefault();
	}
});