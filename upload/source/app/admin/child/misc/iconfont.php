<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// 扫描woff文件函数
function scan_woff_files() {
    $woff_files = array();
    $base_path = DISCUZ_ROOT;
    
    // 检查默认电脑版模板字体文件
    $default_pc_font = $base_path . '/static/image/common/dzicon.woff';
    if (file_exists($default_pc_font)) {
        $woff_files[] = array(
            'name' => 'PC Font (dzicon.woff)',
            'path' => '/static/image/common/dzicon.woff',
            'full_path' => $default_pc_font
        );
    }
    
    // 检查默认手机版模板字体文件
    $default_mobile_font = $base_path . '/static/image/mobile/font/dzmicon.woff';
    if (file_exists($default_mobile_font)) {
        $woff_files[] = array(
            'name' => 'Mobile Font (dzmicon.woff)',
            'path' => '/static/image/mobile/font/dzmicon.woff',
            'full_path' => $default_mobile_font
        );
    }
    
    // 扫描模板目录
    $template_path = $base_path . '/template';
    if (is_dir($template_path)) {
        $dir = dir($template_path);
        while (($file = $dir->read()) !== false) {
            if ($file != '.' && $file != '..' && is_dir($template_path . '/' . $file)) {
                // 扫描每个模板目录下的woff文件
                $template_woff_files = glob($template_path . '/' . $file . '/**/*.woff', GLOB_BRACE);
                foreach ($template_woff_files as $woff_file) {
                    $relative_path = str_replace($base_path, '', $woff_file);
                    $woff_files[] = array(
                        'name' => 'Template ' . $file . ' Font (' . basename($woff_file) . ')',
                        'path' => $relative_path,
                        'full_path' => $woff_file
                    );
                }
            }
        }
        $dir->close();
    }
    
    return $woff_files;
}

// 获取所有woff文件
$woff_files = scan_woff_files();

// 设置页面标题
showsubmenu('misc_iconfont_title', array());
// 显示提示信息
showtips('misc_iconfont_tips');
// 显示字体文件列表盒子
showboxheader('misc_iconfont_scanned_files');
if (!empty($woff_files)) {
    foreach ($woff_files as $file) {
        showboxrow('', '', '<div class="woff-file-item" data-path="' . $file['path'] . '" data-name="' . $file['name'] . '">
			<div class="woff-file-name">' . $file['name'] . '</div>
			<div class="woff-file-path">' . $file['path'] . '</div>
		</div>');
    }
} else {
    showboxrow('', '', '<div class="notice">' . cplang('misc_iconfont_no_files') . '</div>');
}
showboxfooter();

// 显示字体文件输入表格
showtableheader('misc_iconfont_input');
showtablerow('', array('class="td21"', 'class="vtop rowform"'), array(cplang('misc_iconfont_online_url'), 
	'<input type="text" id="font-url" class="txt" style="width: 400px;" placeholder="' . cplang('misc_iconfont_online_url_tip') . '">
	<input type="button" class="btn" id="load-url-btn" value="' . cplang('misc_iconfont_parse_url') . '">'));
showtablerow('', array('class="td21"', 'class="vtop rowform"'), array(cplang('misc_iconfont_local_file'), 
	'<input type="file" id="local-font" accept=".ttf,.woff,.woff2,.otf">
	<input type="button" class="btn" id="load-local-btn" value="' . cplang('misc_iconfont_parse_local') . '">'));
showtablefooter();

// 显示操作按钮
showtableheader('misc_iconfont_operation');
showtablerow('', array('colspan="2"'), '<div class="operation-buttons" id="operation-buttons" style="display: none; margin: 15px 0;">
	<button class="btn" id="copy-type-btn">' . cplang('misc_iconfont_copy_value') . '</button>
	<button class="btn" id="change-background-btn">' . cplang('misc_iconfont_switch_bg') . '</button>
	<button class="btn" id="show-all-btn" style="display: none;">' . cplang('misc_iconfont_show_all') . '</button>
	<button class="btn" id="reload-btn">' . cplang('misc_iconfont_reload') . '</button>
	<button class="btn" id="back-btn">' . cplang('misc_iconfont_back') . '</button>
</div>');
showtablefooter();

// 显示解析结果
showboxheader('misc_iconfont_result', '', 'id="icon-result" style="display: none; margin-top: 10px;"');
showboxrow('', '', '<div id="icon-list" class="icon-list"></div>');
showboxfooter();
?>
<style>
	.icon-item {
		margin: 6px;
		padding: 10px 6px;
		display: inline-block;
		text-align: center;
		border: 1px solid #e5e5e5;
		cursor: pointer;
		min-width: 120px;
		vertical-align: top;
		background: var(--admincp-bgf7);
	}

	.icon-item:hover {
		background: var(--admincp-bgc);
		border-color: #3D8AC7;
	}

	.icon-item .iconfont {
		font-size: 28px;
		display: block;
		margin: 10px 0;
	}

	.icon-item .name {
		font-size: 12px;
		font-weight: bold;
		margin: 5px 0;
		color: #333;
	}

	.icon-item .value {
		font-size: 12px;
		color: #666;
		word-break: break-all;
	}
	
	.woff-file-item {
		padding: 8px 10px;
		border: 1px solid var(--admincp-borderb);
		margin-bottom: 3px;
		cursor: pointer;
		background: var(--admincp-bgc);
	}

	.woff-file-item.active {
		background: var(--admincp-bga);
		border-color: #3D8AC7;
	}

	.woff-file-name {
		font-weight: bold;
	}

	.woff-file-path {
		font-size: 12px;
		color: #666;
		margin-top: 2px;
	}

	.icon-list {
		padding: 10px;
		border: 1px solid #e5e5e5;
		background: var(--admincp-bgc);
	}
</style>
<!--ttf文件解析库-->
<script src="https://cdn.jsdelivr.net/npm/opentype.js@1.3.4/dist/opentype.min.js"></script>
<script>
	// 语言包变量
	const lang = {
		copyValue: '<?php echo cplang('misc_iconfont_copy_value'); ?>',
		copyName: '<?php echo cplang('misc_iconfont_copy_name'); ?>',
		pleaseInputUrl: '<?php echo cplang('misc_iconfont_please_input_url'); ?>',
		pleaseSelectFile: '<?php echo cplang('misc_iconfont_please_select_file'); ?>',
		parsing: '<?php echo cplang('misc_iconfont_parsing'); ?>',
		invalidUrl: '<?php echo cplang('misc_iconfont_invalid_url'); ?>',
		parseFailed: '<?php echo cplang('misc_iconfont_parse_failed'); ?>',
		woff2NotSupport: '<?php echo cplang('misc_iconfont_woff2_not_support'); ?>',
		woff2Failed: '<?php echo cplang('misc_iconfont_woff2_failed'); ?>',
		parseError: '<?php echo cplang('misc_iconfont_parse_error'); ?>',
		formatError: '<?php echo cplang('misc_iconfont_format_error'); ?>',
		noIcons: '<?php echo cplang('misc_iconfont_no_icons'); ?>',
		copyFailed: '<?php echo cplang('misc_iconfont_copy_failed'); ?>',
		copySuccess: '<?php echo cplang('misc_iconfont_copy_success'); ?>'
	};
	
	// 全局变量
	let backgroundIndex = 0;
	let copyType = 'value';
	let bufferStr = null;
	let isSymbol = false;
	let isCSS = false;
	let iconList = [];
	let currentFontUrl = '';
	let currentFontName = '';
	
	// DOM 元素
	const woffFileItems = document.querySelectorAll('.woff-file-item');
	const fontUrlInput = document.getElementById('font-url');
	const loadUrlBtn = document.getElementById('load-url-btn');
	const localFontInput = document.getElementById('local-font');
	const loadLocalBtn = document.getElementById('load-local-btn');
	const operationButtons = document.getElementById('operation-buttons');
	const copyTypeBtn = document.getElementById('copy-type-btn');
	const changeBackgroundBtn = document.getElementById('change-background-btn');
	const showAllBtn = document.getElementById('show-all-btn');
	const reloadBtn = document.getElementById('reload-btn');
	const backBtn = document.getElementById('back-btn');
	const iconResult = document.getElementById('icon-result');
	const iconListContainer = document.getElementById('icon-list');
	
	// 初始化事件监听
	function initEventListeners() {
		// 字体文件项点击事件
		woffFileItems.forEach(item => {
			item.addEventListener('click', function() {
				const path = this.getAttribute('data-path');
				const name = this.getAttribute('data-name');
				loadFontFile(path, name);
			});
		});
		
		// 在线链接加载
		loadUrlBtn.addEventListener('click', function() {
			const url = fontUrlInput.value.trim();
			if (url) {
				loadFontFile(url, '在线字体文件');
			} else {
				alert(lang.pleaseInputUrl);
			}
		});
		
		// 本地文件加载
		loadLocalBtn.addEventListener('click', function() {
			const file = localFontInput.files[0];
			if (file) {
				loadLocalFontFile(file);
			} else {
				alert(lang.pleaseSelectFile);
			}
		});
		
		// 复制类型切换
		copyTypeBtn.addEventListener('click', function() {
			copyType = copyType === 'value' ? 'name' : 'value';
			this.textContent = copyType === 'value' ? lang.copyValue : lang.copyName;
		});
		
		// 背景切换
		changeBackgroundBtn.addEventListener('click', function() {
			backgroundIndex = (backgroundIndex + 1) % 3;
			updateIconBackgrounds();
		});
		
		// 显示所有 Unicode
		showAllBtn.addEventListener('click', function() {
			if (bufferStr) {
				parseIcon(bufferStr, true, /woff2/.test(currentFontUrl));
			}
		});
		
		// 重新载入
		reloadBtn.addEventListener('click', function() {
			loadFontFile(currentFontUrl, currentFontName);
		});
		
		// 返回文件列表
		backBtn.addEventListener('click', function() {
			backToFileList();
		});
	}
	
	// 加载字体文件
	function loadFontFile(path, name) {
		// 标记当前活动的文件项
		woffFileItems.forEach(item => {
			item.classList.remove('active');
			if (item.getAttribute('data-path') === path) {
				item.classList.add('active');
			}
		});
		
		currentFontUrl = path;
		currentFontName = name;
		
		// 隐藏文件列表，显示操作按钮和结果区域
		operationButtons.style.display = 'block';
		iconResult.style.display = 'block';
		
		// 清空之前的图标列表
			iconListContainer.innerHTML = '<div style="text-align: center; padding: 20px; color: #999;">' + lang.parsing + '</div>';
		
		// 判断文件类型
		if (/(ttf|woff2|woff|otf)/.test(path.toLowerCase())) {
			// 字体文件
			isCSS = false;
			isSymbol = false;
			getOnlineTTF(path);
		} else if (path.toLowerCase().indexOf('.css') !== -1) {
			// CSS 文件
			isCSS = true;
			isSymbol = false;
			getOnlineCSS(path);
		} else if (path.toLowerCase().indexOf('.js') !== -1) {
			// JS 文件
			isCSS = false;
			isSymbol = true;
			getOnlineJS(path);
		}
	}
	
	// 返回文件列表
	function backToFileList() {
		// 清除活动状态
		woffFileItems.forEach(item => {
			item.classList.remove('active');
		});
		
		// 隐藏操作按钮和结果区域
		operationButtons.style.display = 'none';
		iconResult.style.display = 'none';
		
		// 清空图标列表
		iconListContainer.innerHTML = '';
		
		// 清空输入
		fontUrlInput.value = '';
		localFontInput.value = '';
	}
	
	// ajax 请求
	function ajax(options) {
		options = options || {};
		let xhr = new XMLHttpRequest();
		if (options.type === 'buffer') {
			xhr.responseType = 'arraybuffer';
		}

		xhr.onreadystatechange = function () {
			if (xhr.readyState === 4) {
				let status = xhr.status;
				if (status >= 200 && status < 300) {
					options.success && options.success(xhr.response);
				} else {
					options.fail && options.fail(status);
				}
			}
		};

		xhr.open("GET", options.url, true);
		xhr.send(null);
	}
	
	// 解析在线 ttf/woff 文件
	function getOnlineTTF(url) {
		// 远程获取文件
		ajax({
			url: url,
			type: 'buffer',
			success: function(params) {
				parseIcon(params, false, /woff2/.test(url));
			},
			fail: function() {
				alert(lang.invalidUrl);
				iconListContainer.innerHTML = '<div style="text-align: center; padding: 20px; color: #ff6666;">' + lang.parseFailed + '</div>';
			}
		});

		setStyle(url);
	}
	
	// 解析本地字体文件
	function loadLocalFontFile(file) {
		// 隐藏文件列表，显示操作按钮和结果区域
		operationButtons.style.display = 'block';
		iconResult.style.display = 'block';
		
		// 清空之前的图标列表
		iconListContainer.innerHTML = '<div style="text-align: center; padding: 20px; color: #999;">' + lang.parsing + '</div>';
		
		currentFontName = file.name;
		
		// 解析文件内容
		let reader = new FileReader();
		reader.readAsArrayBuffer(file);
		reader.onload = function(evt) {
			parseIcon(evt.target.result, false, /woff2/.test(file.name));
		};

		// base64 编码，动态加入 @font-face
		let readerBase64 = new FileReader();
		readerBase64.readAsDataURL(file);
		readerBase64.onload = function(evt) {
			setStyle(evt.target.result);
		};
	}
	
	// 解析 CSS 文件
	function getOnlineCSS(url) {
		// 远程获取文件
		ajax({
			url: url,
			success: function(params) {
				setStyle('', params);
				iconList = [];
				params.replace(/\.([^:^ ]+):[\s\S]+?content: "\\([^"]+)";/gi, function() {
					iconList.push({
						show: arguments[1],
						name: arguments[1],
						value: `&#${arguments[2]};`,
					});
				});
				displayIcons();
			},
			fail: function() {
				alert(lang.invalidUrl);
				iconListContainer.innerHTML = '<div style="text-align: center; padding: 20px; color: #ff6666;">' + lang.parseFailed + '</div>';
			}
		});
	}
	
	// 解析 JS 文件
	function getOnlineJS(url) {
		// 远程获取文件
		ajax({
			url: url,
			success: function(params) {
				let script = document.createElement('script');
				script.src = url;
				document.body.appendChild(script);
				
				iconList = [];
				params.replace(/id="([^"]+)"/gi, function() {
					iconList.push({
						show: arguments[1].replace(/icon/, ''),
						name: arguments[1].replace(/icon/, ''),
						value: `#${arguments[1]}`,
					});
				});
				displayIcons();
			},
			fail: function() {
				alert(lang.invalidUrl);
				iconListContainer.innerHTML = '<div style="text-align: center; padding: 20px; color: #ff6666;">' + lang.parseFailed + '</div>';
			}
		});
	}
	
	// 解析icon
	function parseIcon(bufferStr, showAll, isWoff2) {
		window.bufferStr = bufferStr;
		iconList = [];
		
		try {
			// 目前只处理woff文件，暂不支持woff2解码
			if (isWoff2) {
				alert(lang.woff2NotSupport);
				iconListContainer.innerHTML = '<div style="text-align: center; padding: 20px; color: #ff6666;">' + lang.woff2Failed + '</div>';
				return;
			}
			
			let result = window.opentype.parse(bufferStr);
			for (let key in result.glyphs.glyphs) {
				let item = result.glyphs.glyphs[key];
				if (item.unicode) {
					if (showAll && item.unicodes) { // 是否显示所有 unicodes
						let valueStr = '';
						item.unicodes.forEach(unicode => valueStr += `&#${unicode};\n`);
						iconList.push({
							name: item.name,
							value: valueStr,
							show: `&#${item.unicode};`
						});
					} else {
						iconList.push({
							name: item.name,
							show: `&#${item.unicode};`,
							value: `&#${item.unicode};`
						});
					}
				}
			}
			
			// 转换为十六进制表示
			iconList.forEach(item => {
				item.value = item.value.replace(/&#([^;]+);/ig, function(target, value) {
					return `&#x${parseInt(value).toString(16)};`;
				});
				item.show = item.show.replace(/&#([^;]+);/ig, function(target, value) {
					return `&#x${parseInt(value).toString(16)};`;
				});
			});
			
			// 显示图标
			displayIcons();
			
			// 显示显示所有Unicode按钮（如果有多个unicode）
			showAllBtn.style.display = iconList.some(item => item.value.includes('\n')) ? 'inline-block' : 'none';
			
		} catch (error) {
			alert(lang.parseError);
			iconListContainer.innerHTML = '<div style="text-align: center; padding: 20px; color: #ff6666;">' + lang.formatError + '</div>';
		}
	}
	
	// 显示图标
	function displayIcons() {
		if (iconList.length === 0) {
			iconListContainer.innerHTML = '<div style="text-align: center; padding: 20px; color: #999;">' + lang.noIcons + '</div>';
			return;
		}
		
		// 转义HTML特殊字符
		function escapeHtml(text) {
			return text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
		}
		
		let html = '';
		iconList.forEach((item, index) => {
			// 转义value中的特殊字符，确保显示unicode文本而不是直接渲染字符
			const escapedValue = escapeHtml(item.value);
			html += `
				<div class="icon-item" data-index="${index}" onclick="copyToClipboard(this)">
					${isSymbol ? `<svg class="icon" aria-hidden="true"><use xlink:href="${item.value}"></use></svg>` : `<span class="iconfont">${item.show}</span>`}
					<p class="name">${item.name}</p>
					<p class="value">${escapedValue}</p>
				</div>
			`;
		});
		iconListContainer.innerHTML = html;
		updateIconBackgrounds();
	}
	
	// 更新图标背景
	function updateIconBackgrounds() {
		const iconItems = document.querySelectorAll('.icon-item');
	}
	
	// 添加style
	function setStyle(url, cssFile) {
		// 移除之前的样式
		const oldStyle = document.getElementById('iconfont-style');
		if (oldStyle) {
			document.head.removeChild(oldStyle);
		}
		
		let style = document.createElement('style');
		style.id = 'iconfont-style';
		if (cssFile) {
			style.innerHTML = cssFile;
		} else {
			style.innerHTML = `
			    @font-face {
				font-family: 'iconfont';
				src: url('${url}') format('woff');
			}
			.iconfont {
				font-family: "iconfont" !important;
				font-size: 24px;font-style: normal;
				-webkit-font-smoothing: antialiased;
				-webkit-text-stroke-width: 0.2px;
				-moz-osx-font-smoothing: grayscale;
			}`;
		}
		document.head.appendChild(style);
	}
	
	// 拷贝剪切板
	function copyToClipboard(element) {
		const index = parseInt(element.getAttribute('data-index'));
		const item = iconList[index];
		const content = item[copyType];
		
		// 使用现代的Clipboard API
		if (navigator.clipboard) {
			navigator.clipboard.writeText(content).then(function() {
				showCopySuccess(content);
			}).catch(function(err) {
				// 降级方案
				fallbackCopyTextToClipboard(content);
			});
		} else {
			// 降级方案
			fallbackCopyTextToClipboard(content);
		}
	}
	
	// 降级拷贝方案
	function fallbackCopyTextToClipboard(content) {
		let textArea = document.createElement('textarea');
		textArea.value = content;
		textArea.style.position = 'fixed';
		textArea.style.left = '-999999px';
		textArea.style.top = '-999999px';
		document.body.appendChild(textArea);
		textArea.focus();
		textArea.select();
		
		try {
			const successful = document.execCommand('copy');
			if (successful) {
				showCopySuccess(content);
			} else {
				alert(lang.copyFailed);
			}
		} catch (err) {
			alert(lang.copyFailed);
		}
		
		document.body.removeChild(textArea);
	}
	
	// 显示复制成功提示
	function showCopySuccess(content) {
		// 创建提示元素
		let tip = document.createElement('div');
		tip.style.cssText = `
			position: fixed;
			top: 20px;
			left: 50%;
			transform: translateX(-50%);
			background: rgba(0, 0, 0, 0.8);
			color: white;
			padding: 10px 20px;
			border-radius: 4px;
			z-index: 9999;
			font-size: 14px;
		`;
		tip.textContent = lang.copySuccess + content;
		document.body.appendChild(tip);
		
		// 3秒后移除提示
		setTimeout(function() {
			document.body.removeChild(tip);
		}, 3000);
	}
	
	// 初始化
	initEventListeners();
</script>