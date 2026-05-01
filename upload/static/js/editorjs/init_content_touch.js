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
	autofocus: false,

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
	i18n: i18n_tools !== undefined ? mergeObjects(i18n_default, i18n_tools) : i18n_default,

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

		undo = new Undo({ editor, config });
		new DragDrop(editor);

		setTimeout(() => {
			//saveButton.click();
		}, 2000)

	},
	onChange: function (e) {
		console.log(e)
		contentChanged = true;
		scheduleAutoSave();
		var needsubject = document.getElementById("needsubject");
		var replysubmit = document.getElementsByName("replysubmit")[0];
		var postBtn = $('#postsubmit'); // 使用正确的选择器

		// 检查编辑器内容是否为空
		// 确保editor.save是一个函数
		if (editor && typeof editor.save === 'function') {
			editor.save().then((savedData) => {
				var hasContent = false;
				if (savedData && savedData.blocks && savedData.blocks.length > 0) {
					// 检查是否有实际内容（不仅仅是空块）
					hasContent = savedData.blocks.some(block => {
						if (block.data && typeof block.data === 'object') {
							// 对于文本块，检查文本内容
							if (block.data.text && block.data.text.trim() !== '') {
								return true;
							}
							// 对于其他类型块，检查是否有数据
							if (Object.keys(block.data).length > 0) {
								return Object.values(block.data).some(val => {
									if (val === null || val === undefined) return false;
									if (typeof val === 'string' && val.trim() === '') return false;
									return true;
								});
							}
						}
						return false;
					});
				}

				// 根据条件设置按钮状态
				if (e && (needsubject && needsubject.value && needsubject.value.trim() !== '' || replysubmit) && hasContent) {
					postBtn.removeClass('btn_pn_grey').addClass('btn_pn_blue');
					postBtn.attr('disable', 'false');
				} else {
					postBtn.removeClass('btn_pn_blue').addClass('btn_pn_grey');
					postBtn.attr('disable', 'true');
				}
			}).catch((error) => {
				console.error('检查编辑器内容时出错:', error);
				// 出错时默认禁用按钮
				postBtn.removeClass('btn_pn_blue').addClass('btn_pn_grey');
				postBtn.attr('disable', 'true');
			});
		}
	}
});

const needsubject = document.getElementById('needsubject');
if (needsubject) {
	needsubject.addEventListener('change', (e) => {
		needmessage = true;
	});
}

/**
 * 保存编辑器内容为JSON格式
 * @param {Event} [event] - 可选的事件对象
 * @param {Object} [options] - 可选配置参数
 * @param {Boolean} [options.validate=true] - 是否验证内容是否为空
 * @param {String} [options.targetElementId='content'] - 目标表单元素ID
 * @returns {Promise<Object|Boolean>} 返回保存的数据或false（当验证失败时）
 */
function saveJsonContent(event, options = {}) {
	// 默认配置
	const defaultOptions = {
		validate: true,
		targetElementId: 'content'
	};

	// 合并配置
	const config = Object.assign(defaultOptions, options);

	return editor.save().then((savedData) => {
		const contentElement = document.getElementById(config.targetElementId);

		// 验证内容是否为空
		if (config.validate && (!savedData.blocks || savedData.blocks.length === 0)) {
			if (event && typeof event.stopPropagation === 'function') {
				event.stopPropagation();
			}
			return false;
		}

		// 设置内容到目标元素
		if (contentElement) {
			contentElement.value = JSON.stringify(savedData);
		}

		// 触发自定义事件，方便其他页面监听
		if (typeof CustomEvent !== 'undefined' && typeof document.dispatchEvent !== 'undefined') {
			const saveEvent = new CustomEvent('editorContentSaved', {
				detail: {
					data: savedData,
					element: contentElement
				}
			});
			document.dispatchEvent(saveEvent);
		}

		return savedData;
	}).catch((error) => {
		console.error('保存编辑器内容时出错:', error);

		// 触发错误事件
		if (typeof CustomEvent !== 'undefined' && typeof document.dispatchEvent !== 'undefined') {
			const errorEvent = new CustomEvent('editorContentSaveError', {
				detail: {
					error: error
				}
			});
			document.dispatchEvent(errorEvent);
		}

		throw error;
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