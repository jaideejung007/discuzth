<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editor {

	private static $r_dzTpl = [
		"/[\n\r\t]*\{block\/(\d+?)\}[\n\r\t]*/i",
		"/[\n\r\t]*\{blockdata\/(\d+?)\}[\n\r\t]*/i",
		"/[\n\r\t]*\{ad\/(.+?)\}[\n\r\t]*/i",
		"/[\n\r\t]*\{ad\s+([a-zA-Z0-9_\[\]]+)\/(.+?)\}[\n\r\t]*/i",
		"/[\n\r\t]*\{date\((.+?)\)\}[\n\r\t]*/i",
		"/[\n\r\t]*\{avatar\((.+?)\)\}[\n\r\t]*/i",
		"/[\n\r\t]*\{eval\}\s*(\<\!\-\-)*(.+?)(\-\-\>)*\s*\{\/eval\}[\n\r\t]*/is",
		"/[\n\r\t]*\{eval\s+(.+?)\s*\}[\n\r\t]*/is",
		"/[\n\r\t]*\{csstemplate\}[\n\r\t]*/is",
		"/\{(\\\$[a-zA-Z0-9_\-\>\[\]\'\"\$\.\x7f-\xff]+)\s(or|\?\?)\s([a-zA-Z0-9\']+)\}/s",
		"/\{(\\\$[a-zA-Z0-9_\-\>\[\]\'\"\$\.\x7f-\xff]+)\}/s",
		'/\{hook\/(\w+?)(\s+(.+?))?\}/i',
		"/((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\-\>)?[a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)/s",
		"/\<\?\=\<\?\=((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\-\>)?[a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)\?\>\?\>/s",
		"/[\n\r\t]*\{template\s+([a-z0-9_:\/]+)\}[\n\r\t]*/is",
		"/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/is",
		"/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/is",
		"/([\n\r\t]*)\{if\s+(.+?)\}([\n\r\t]*)/is",
		"/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/is",
		'/\{else\}/i',
		'/\{\/if\}/i',
		"/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r\t]*/is",
		"/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/is",
		'/\{\/loop\}/i',
		"/[\n\r\t]*\{block\s+([a-zA-Z0-9_\[\]']+)\}(.+?)\{\/block\}/is",
	];
	private static $r_php = [
		'/<\?/',
		'/\?>/',
	];
	private static $r_js = [
		'/javascrit:/i',
		'/<script(.*?)>/i',
		'/\s+on(?!ly\b)([a-zA-Z]+)/i',
	];

	public static function checkTemplate($template) {
		$template = preg_replace('/\<\!\-\-\{(.+?)\}\-\-\>/s', "{\\1}", $template);
		foreach(self::$r_dzTpl as $r) {
			if(preg_match($r, $template)) {
				return false;
			}
		}
		foreach(self::$r_php as $r) {
			if(preg_match($r, $template)) {
				return false;
			}
		}
		foreach(self::$r_js as $r) {
			if(preg_match($r, $template)) {
				return false;
			}
		}
		return true;
	}

	public static function toArray($content) {
		$content = json_decode($content, true);
		return $content;
	}

	public static function getBlockTemplate($type) {
		$editorblock_parser = $editorblock_style = '';
		$editorblock = memory('get', 'editorblock_'.$type);
		if($editorblock && $editorblock['parser'] && $editorblock['style']) {
			$editorblock_parser = $editorblock['parser'];
			$editorblock_style = $editorblock['style'];
			$editorblock_static = empty($editorblock['plugin']) ? 'static/js/editorjs/tools/' . $type . '/' : 'source/plugin/' . $editorblock['plugin'] . '/editorblock/tools/' . $type . '/';
		} else {
			$editorblock = table_common_editorblock::t()->fetch_by_block_class($type);
			$editorblock_parser = $editorblock['parser'];
			$editorblock_style = $editorblock['style'];
			$editorblock_static = empty($editorblock['plugin']) ? 'static/js/editorjs/tools/' . $type . '/' : 'source/plugin/' . $editorblock['plugin'] . '/editorblock/tools/' . $type . '/';
			memory('set', 'editorblock_'.$type, $editorblock);
		}
		return [$editorblock_parser, $editorblock_style, $editorblock_static];
	}

	public static function getBlockParser($tpl, $block) {
		$parser = $tpl;
		return $parser;
	}

	public static function parser($content) {
		global $_G;
		$blocksData = self::toArray($content);
		$parserData = '';
		$styleData = ['types' => [], 'style' => ''];
		foreach($blocksData['blocks'] as $key => $value) {
			list($editorblock_parser, $editorblock_style, $editorblock_static) = self::getBlockTemplate($value['type']);
			
			$allowParser = true;
			$isHideTuneBlock = false;
			if($value['tunes']['hideTune']['hide']) {
				$isHideTuneBlock = true;
				$authorreplyexist = null;
				if($_G['uid']) {
					$authorreplyexist = table_forum_post::t()->fetch_pid_by_tid_authorid($_G['tid'], $_G['uid']);
				}
				if(!$authorreplyexist) {
					$allowParser = false;
				}
				if($_G['adminid'] > 0) {
					$allowParser = true;
				}
			}
			if(!$allowParser) {
				$parser = '<div class="locked">'.($_G['uid'] ? $_G['username'] : lang('forum/template', 'guest')).lang('forum/template', 'post_hide_reply_hidden_text').'<a href="forum.php?mod=post&action=reply&fid='.$_G['fid'].'&tid='.$_G['tid'].'" onclick="showWindow(\'reply\', this.href)">'.lang('forum/template', 'reply').'</a></div>';
			} else {
				$parser = (new editorBlock($editorblock_parser, $value, $editorblock_static))->replace();
				if($isHideTuneBlock) {
					$parser = '<div class="showhide"><h4>'.lang('forum/template', 'post_hide').'</h4>'.$parser.'</div>';
				}
			}

			
			$anchorparse = explode(',', getglobal('setting/anchorparse')) ?? [];
			if(in_array($value['type'], $anchorparse)) {
				$parser = preg_replace_callback('/<a\s+href=[\'"]#([^\'"]+)[\'"](.*?)>/s', function($m) {
					$href = $m[1];
					
					$onclick = "javascript: document.getElementById('".$href."').scrollIntoView();return false;";
					return '<a href="#'.$href.'" onclick="'.$onclick.'"'.$m[2].'>';
				}, $parser);
			}
			
			$parserData .= $parser;
			if(!in_array($value['type'], $styleData['types'])) {
				$styleData['types'][] = $value['type'];
				$styleData['style'] .= $editorblock_style;
			}
		}
		return [$parserData, $styleData['style']];
	}

}

class editorBlock {
	var $html = '';
	var $block = [];
	var $static = '';

	
	const rLoop = '/\[loop ([a-zA-Z\.]+)\](.+?)\[\/loop\]/s';
	const rLoopIndex = '/\[loopindex\]/s';
	const rLoopObject = '/\[loopobject ([a-zA-Z\.]+)\](.+?)\[\/loopobject\]/s';
	const rLoopObjectData = '/\{loopobjectdata\}/';
	const rVar = '/\{([a-zA-Z\.]+)\}/';
	
	const rIf = '/\[if ([a-zA-Z\.]+)=(.+?)\](.*?)\[\/if\]/s';
	
	const rUrl = '/\[url ([a-zA-Z\.]+),([a-zA-Z\.]+),([a-zA-Z\.]+)\]/s';
	
	const rAttach = '/\[attach ([a-zA-Z\.]+)\]/s';
	
	const rColumn = '/\[column ([a-zA-Z\.]+)\]/s';
	
	const rScript = '/\[script (.+?)\]/s';
	
	const rCodeflask = '/\[codeflask ([a-zA-Z\.]+),([a-zA-Z\.]+),([a-zA-Z\.]+),([a-zA-Z\.]+)\]/s';

	
	const rMedia = '/\[media ([a-zA-Z\.]+),([^,\]]+),([a-zA-Z0-9%\.]+)(?:,([^,\]]+))?\]/s';

	const rJsFile = '/\[jsfile ([a-zA-Z0-9_\.]+\.js)\]/s';
	const rCssFile = '/\[cssfile ([a-zA-Z0-9_\.]+\.css)\]/s';
	const rRecursive = '/\[recursive ([a-zA-Z\.]+),([^\]]+)\]/s';

	public function __construct($html, $block, $static) {
		$this->html = $html;
		$this->block = $block;
		$this->static = $static;
	}

	public function replace() {
		
		$this->html = preg_replace_callback(self::rJsFile, [$this, '_jsfile'], $this->html);
		$this->html = preg_replace_callback(self::rCssFile, [$this, '_cssfile'], $this->html);
		$this->html = preg_replace_callback(self::rCodeflask, [$this, '_codeflask'], $this->html);
		$this->html = preg_replace_callback(self::rLoop, [$this, '_loop'], $this->html);
		$this->html = preg_replace_callback(self::rLoopObject, [$this, '_object'], $this->html);
		$this->html = preg_replace_callback(self::rVar, [$this, '_var'], $this->html);
		$this->html = preg_replace_callback(self::rRecursive, [$this, '_recursive'], $this->html);
		$this->html = preg_replace_callback(self::rUrl, [$this, '_url'], $this->html);
		$this->html = preg_replace_callback(self::rAttach, [$this, '_attach'], $this->html);
		$this->html = preg_replace_callback(self::rIf, [$this, '_if'], $this->html);
		$this->html = preg_replace_callback(self::rMedia, [$this, '_media'], $this->html);
		return $this->html;
	}

	private function _codeflask($m, $value = null) {
		$id = $this->_value($m[1], $value);
		$language = $this->_value($m[2], $value);
		$n_count = $this->_value($m[3], $value) ?? 0;
		$code = $this->_value($m[4], $value);
		$code = str_replace('`', '\`', $code);
		$code = dhtmlspecialchars($code);
		$rand = time().random(5);
		$script = <<<EOF
<script type="application/javascript">
			const editorElem$rand = document.getElementById('codeflask-$id');
			var isCollapsed$rand = true;
			const flask$rand = new CodeFlask(editorElem$rand, {
				language: '$language',
				lineNumbers: true,
				styleParent: this.shadowRoot,
				rtl: false,
				readonly: true
			});
            		var code$rand = `$code`;
            		code$rand = code$rand.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, '"').replace(/&#039;/g, "'"); 
			flask$rand.addLanguage('$language', Prism.languages['$language']);
			flask$rand.onUpdate((code) => {
				// do something with code here.
				// this will trigger whenever the code
				// in the editor changes.
			    // console.log(code)
			});
			// flask.updateCode('');
			// This will also trigger .onUpdate()
			flask$rand.updateCode(code$rand);

			const currentCode$rand = flask$rand.getCode();
            
		            var coderow$rand = parseInt('$n_count');
			    if (coderow$rand === undefined || coderow$rand !== coderow$rand || coderow$rand === 0) {
				    coderow$rand = flask$rand.lineNumber;
			    }
			    
		            if (coderow$rand < 20) {
		                editorElem$rand.parentElement.style.height = '300px';
				editorElem$rand.style.height = '300px';
		            } else if (coderow$rand >= 20) {
		                editorElem$rand.parentElement.style.height = '500px';
		                editorElem$rand.style.height = '500px';
		            }
		            
			//console.log({currentCode$rand})    
			
			const copyBtn$rand = document.getElementById('codeflask-copy-{id}');
			copyBtn$rand.addEventListener('click', function() {
			    try {
			        // ตรวจสอบก่อนว่า Clipboard API สามารถใช้งานได้หรือไม่
			        if (navigator.clipboard && navigator.clipboard.writeText) {
			            // ใช้ Clipboard API เพื่อคัดลอกโค้ด
			            navigator.clipboard.writeText(code$rand).then(function() {
			                // แจ้งเตือนเมื่อคัดลอกสำเร็จ
			                const originalText = copyBtn$rand.innerHTML;
			                copyBtn$rand.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> คัดลอกแล้ว';
			                
			                // กลับสู่ข้อความเดิมหลังจากผ่านไปครู่หนึ่ง
			                setTimeout(function() {
			                    copyBtn$rand.innerHTML = originalText;
			                }, 2000);
			            }).catch(function(err) {
			                // จัดการเมื่อการคัดลอกไม่สำเร็จ
			                console.error('การคัดลอกไม่สำเร็จ:', err);
			                
			                // วิธีสำรอง: ใช้การคัดลอกรูปแบบเดิม
			                fallbackCopyTextToClipboard(code$rand);
			            });
			        } else {
			            // เมื่อ Clipboard API ใช้งานไม่ได้ ให้ใช้วิธีสำรองทันที
			            fallbackCopyTextToClipboard(code$rand);
			        }
			        
			        // แยกวิธีสำรองออกเป็นฟังก์ชันอิสระ
			        function fallbackCopyTextToClipboard(text) {
			            const textArea = document.createElement('textarea');
			            textArea.value = text;
			            textArea.style.position = 'fixed';
			            textArea.style.opacity = '0';
			            document.body.appendChild(textArea);
			            textArea.select();
			            
			            try {
			                document.execCommand('copy');
			                const originalText = copyBtn$rand.innerHTML;
			                copyBtn$rand.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> คัดลอกแล้ว';
			                
			                setTimeout(function() {
			                    copyBtn$rand.innerHTML = originalText;
			                }, 2000);
			            } catch (copyErr) {
			                console.error('การคัดลอกด้วยวิธีสำรองไม่สำเร็จเช่นกัน:', copyErr);
			            } finally {
			                document.body.removeChild(textArea);
			            }
			        }
			    } catch (err) {
			        console.error('ฟีเจอร์การคัดลอกไม่สามารถใช้งานได้:', err);
			    }
			});
			
			const bottomBtn$rand = document.getElementById('codeflask-bottomBtn-{id}');
			const Toggle$rand = document.getElementById('codeflask-Toggle-{id}');
			bottomBtn$rand.addEventListener('click', function() {
			        if (isCollapsed$rand) {
			            // ขยายโค้ด
			            isCollapsed$rand = false;
				    const codeElement$rand = editorElem$rand.querySelector('.codeflask__code');
			            if (codeElement$rand) {
				            const actualHeight$rand = codeElement$rand.scrollHeight + 20;
				           
				            editorElem$rand.parentElement.style.height = actualHeight$rand + 'px';
				            editorElem$rand.style.height = actualHeight$rand + 'px';
			            }
			            bottomBtn$rand.innerHTML = '<button class="editorjs-codeFlask_BottomToggle" title="พับเก็บโค้ด"><span class="toggle-icon">▲</span> พับเก็บ</button>';
			            Toggle$rand.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>';
				} else {
			            // พับเก็บโค้ด
			            isCollapsed$rand = true;
				    if (coderow$rand < 20) {
			                editorElem$rand.parentElement.style.height = '300px';
					editorElem$rand.style.height = '300px';
			            } else if (coderow$rand >= 20) {
			                editorElem$rand.parentElement.style.height = '500px';
			                editorElem$rand.style.height = '500px';
			            }
			            bottomBtn$rand.innerHTML = '<button class="editorjs-codeFlask_BottomToggle expand-mode" title="ขยายโค้ด" data-empty="false"><span class="toggle-icon">▼</span> ขยาย</button>';
			            Toggle$rand.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';
				}
			});
			
			Toggle$rand.addEventListener('click', function() {
				bottomBtn$rand.click();
			});
</script>
EOF;
		return $script;
	}

	
	private function _media($m, $value = null) {
		$id = $this->_value($m[1], $value);
		$url = $m[2];
		$width = intval($m[3]) ?? 300;
		$thumbnail = isset($m[4]) ? $m[4] : null;

		
		$url = addslashes($url);
		if(!in_array(strtolower(substr($url, 0, 6)), ['http:/', 'https:', 'ftp://', 'rtsp:/', 'mms://']) && !preg_match('/^static\//', $url) && !preg_match('/^data\//', $url)) {
			return dhtmlspecialchars($url);
		}

		
		$cleanUrl = parse_url($url, PHP_URL_PATH) ?: $url;
		$type = fileext($cleanUrl);
		$randomid = $id;


		
		$imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'ico', 'svg'];
		if ($thumbnail) {
			
			$thumbExt = fileext(parse_url($thumbnail, PHP_URL_PATH) ?: $thumbnail);
			
			if (!in_array($thumbExt, $imageExtensions)) {
				$thumbnail = '';
			}
		}


		
		$audio = ['aac', 'flac', 'ogg', 'mp3', 'm4a', 'weba', 'wma', 'mid', 'wav', 'ra', 'ram'];
		$video = ['rm', 'rmvb', 'flv', 'swf', 'asf', 'asx', 'wmv', 'avi', 'mpg', 'mpeg', 'mp4', 'm4v', '3gp', 'ogv', 'webm', 'mov'];

		
		if(in_array($type, $audio)) {
			
			
			$thumbParam = $thumbnail ? addslashes($thumbnail) : '';
			return '<ignore_js_op><div id="'.$type.'_'.$randomid.'" class="media" style="margin-left: auto;margin-right: auto;"><div id="'.$type.'_'.$randomid.'_container" class="media_container"></div><div id="'.$type.'_'.$randomid.'_tips" class="media_tips"><a href="'.$url.'" target="_blank">'.lang('template', 'parse_av_tips').'</a></div></div><script type="text/javascript">detectPlayer("'.$type.'_'.$randomid.'", "'.$type.'", "'.$url.'", "'.$width.'", "66", "'.$thumbParam.'");</script></ignore_js_op>';
		} else if(in_array($type, $video)) {
			
			$height = intval($width * 0.75); 
			
			$thumbParam = $thumbnail ? addslashes($thumbnail) : '';
			return '<ignore_js_op><div id="'.$type.'_'.$randomid.'" class="media" style="margin-left: auto;margin-right: auto;"><div id="'.$type.'_'.$randomid.'_container" class="media_container"></div><div id="'.$type.'_'.$randomid.'_tips" class="media_tips"><a href="'.$url.'" target="_blank">'.lang('template', 'parse_av_tips').'</a></div></div><script type="text/javascript">detectPlayer("'.$type.'_'.$randomid.'", "'.$type.'", "'.$url.'", "'.$width.'", "'.$height.'", "'.$thumbParam.'");</script></ignore_js_op>';
		} else {
			
			return '<a href="'.$url.'" target="_blank">'.$url.'</a>';
		}
	}

	private function _object($m, $value = null) {
		$t2 = '';
		if($m[1] != 'null' && $m[1] != null) {
			$value = $this->_value($m[1], $value);
		}
		if(is_object($value)) {
			$jsonString = json_encode($value);
			$array = json_decode($jsonString, true);
		} elseif(is_array($value)) {
			$array = $value;
		} else {
			return $m[2];
		}
		foreach($array as $ad) {
			$t2 .= preg_replace(self::rLoopObjectData, $ad, $m[2]);
		}
		return $t2;
	}

	private function _if($m, $value = null) {
		$result = $this->_value($m[1], $value);
		if(!$result) {
			return '';
		} else {
			if((string)$m[2] == 'notnull') {
				$content = preg_replace_callback(self::rVar, function($m) {
					return $this->_var($m);
				}, $m[3]);
				return !empty($content) ? $content : $m[3];
			} elseif((string)$result != (string)$m[2]) {
				return '';
			} else {
				$content = preg_replace_callback(self::rVar, function($m) {
					return $this->_var($m);
				}, $m[3]);
				return !empty($content) ? $content : $m[3];
			}
		}
	}

	private function _url($m, $value = null) {
		$url = $this->_value($m[1], $value);
		$remoteParam = $this->_value($m[2], $value);
		$directoryParam = $this->_value($m[3], $value);

		if(!$url) {
			return '';
		} else {
			
			$url = isset($remoteParam) && $directoryParam ?
				(($remoteParam ? getglobal('setting/ftp/attachurl') : getglobal('setting/attachurl')).$directoryParam.'/'.$url)
				: (str_contains($url, 'http') ? $url : getglobal('siteurl').$url);
			if(str_contains($url, 'http')) {
				return $url;
			} else {
				return getglobal('siteurl').$url;
			}
		}
	}

	private function _attach($m, $value = null) {
		global $_G;
		$aid = $this->_value($m[1], $value);
		if(!$aid) {
			return '';
		} else {
			return aidencode($aid, 0, $_G['tid']);
		}
	}

	private function _jsfile($m, $value = null) {
		$filename = $m[1];
		$blockType = $this->block['type'] ?? '';
		if(empty($blockType) || empty($this->static) || strpos($this->static, $blockType) === false) {
			return '';
		}

		$path = $this->static . $filename . '?' . getglobal('style/verhash');

		return '<script type="text/javascript" src="' . $path . '"></script>';
	}

	private function _cssfile($m, $value = null) {
		$filename = $m[1];
		$blockType = $this->block['type'] ?? '';
		if(empty($blockType) || empty($this->static) || strpos($this->static, $blockType) === false) {
			return '';
		}

		$path = $this->static . $filename . '?' . getglobal('style/verhash');

		return '<link rel="stylesheet" type="text/css" href="' . $path . '" />';
	}

	private function _script($m, $value = null) {
		$path = $m[1];
		if(str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'ftp://')) {
			return '';
		}
		$path = str_replace('{STATICURL}', getglobal('staticurl'), $path);
		$path = str_replace('{VERHASH}', getglobal('style/verhash'), $path);

		return '<script src="'.$path.'"></script>';
	}

	private function _column($m, $value = null) {
		$result = $this->_value($m[1], $value);
		if(!$result) {
			return '';
		} else {
			return $m[2];
		}
	}

	private function _loop($m) {
		$nodes = $this->_value($m[1]);
		if(!is_array($nodes)) {
			return $m[0];
		}
		$s = '';
		foreach($nodes as $index => $node) {
			$t = preg_replace(self::rLoopIndex, $index, $m[2]);
			$t = preg_replace_callback(self::rLoopObject, function($m_sub) use ($node) {
				return $this->_object($m_sub, $node);
			}, $t);
			$t = preg_replace_callback(self::rVar, function($m_sub) use ($node) {
				return $this->_var($m_sub, $node);
			}, $t);
			$t = preg_replace_callback(self::rUrl, function($m_sub) use ($node) {
				return $this->_url($m_sub, $node);
			}, $t);
			$t = preg_replace_callback(self::rIf, function($m_sub) use ($node) {
				return $this->_if($m_sub, $node);
			}, $t);
			$s .= preg_replace_callback(self::rColumn, function($m_sub) use ($node) {
				$data = ['blocks' => []];
				$data['blocks'] = $this->_var($m_sub, $node);
				list($parserData, $styleData) = editor::parser(json_encode($data));
				return $parserData.$styleData;
			}, $t);
		}
		return $s;
	}


	
	private function _recursiveRender($data, $config, $context = []) {
		
		if(!is_array($data) || empty($data)) {
			return isset($config['emptyTemplate']) ? $config['emptyTemplate'] : '';
		}

		
		$config = $this->_normalizeConfig($config);

		
		$formatNumber = function($number, $type) {
			switch($type) {
				case 'lower-roman':
					return strtolower($this->_intToRoman($number));
				case 'upper-roman':
					return $this->_intToRoman($number);
				case 'lower-alpha':
					return chr(96 + $number); 
				case 'upper-alpha':
					return chr(64 + $number); 
				case 'numeric':
				default:
					return (string)$number;
			}
		};

		
		$itemsHtml = '';
		foreach($data as $index => $item) {
			
			$itemContext = array_merge($context, [
				'index' => $index,
				'depth' => isset($context['depth']) ? $context['depth'] + 1 : 0,
				'parent' => $context
			]);

			
			$startNumber = isset($config['startNumber']) && intval($config['startNumber']) > 0 ? intval($config['startNumber']) : 1;
			$currentNumber = $index + $startNumber;

			
			if(isset($context['levelNumber'])) {
				
				$parentParts = explode('.', $context['levelNumber']);
				$currentPart = $formatNumber($currentNumber, $config['counterType']);
				$levelNumber = implode('.', $parentParts) . '.' . $currentPart;
			} else {
				
				$levelNumber = $formatNumber($currentNumber, $config['counterType']);
			}
			$itemContext['levelNumber'] = $levelNumber;

			
			$itemHtml = $this->_replacePlaceholders($config['itemTemplate'], $item, $itemContext, $config);

			
			if(isset($item[$config['childrenKey']]) && !empty($item[$config['childrenKey']])) {
				
				$childrenHtml = $this->_recursiveRender($item[$config['childrenKey']], $config, $itemContext);

				
				if(isset($config['childrenClass']) && !empty($config['childrenClass'])) {
					
					$childrenHtml = preg_replace('/^<([a-z][a-z0-9]*)\s*/i', '<$1 class="' . $config['childrenClass'] . '" ', $childrenHtml);
				}

				
				$itemHtml = str_replace('{' . $config['childrenKey'] . '}', $childrenHtml, $itemHtml);
			} else {
				
				$itemHtml = str_replace('{' . $config['childrenKey'] . '}', '', $itemHtml);
			}

			$itemsHtml .= $itemHtml;
		}

		
		$containerHtml = $this->_replacePlaceholders($config['containerTemplate'], ['items' => $itemsHtml], $context, $config);

		return $containerHtml;
	}

	
	private function _intToRoman($num) {
		
		$num = intval($num);
		if($num <= 0) return '0';

		
		$romanMap = [
			'M' => 1000,
			'CM' => 900,
			'D' => 500,
			'CD' => 400,
			'C' => 100,
			'XC' => 90,
			'L' => 50,
			'XL' => 40,
			'X' => 10,
			'IX' => 9,
			'V' => 5,
			'IV' => 4,
			'I' => 1
		];

		$result = '';
		foreach($romanMap as $roman => $value) {
			while($num >= $value) {
				$result .= $roman;
				$num -= $value;
			}
		}
		return $result;
	}

	
	private function _replacePlaceholders($template, $data, $context, $config) {
		$html = $template;

		
		foreach($data as $key => $val) {
			if(!is_array($val) && !is_object($val)) {
				$html = str_replace('{' . $key . '}', $val, $html);
			}
		}

		
		foreach($context as $key => $val) {
			if(!is_array($val) && !is_object($val)) {
				$html = str_replace('{context.' . $key . '}', $val, $html);
			}
		}

		
		if(isset($context['depth'])) {
			$html = str_replace('{depth}', $context['depth'], $html);
		} else {
			$html = str_replace('{depth}', '', $html);
		}
		if(isset($context['index'])) {
			$html = str_replace('{index}', $context['index'], $html);
		} else {
			$html = str_replace('{index}', '', $html);
		}
		
		if(isset($context['levelNumber'])) {
			$html = str_replace('{levelNumber}', $context['levelNumber'], $html);
		} else {
			$html = str_replace('{levelNumber}', '', $html);
		}

		
		
		preg_match_all('/\{(?!context\.)([\w]+)\.([\w\.]+)\}/', $html, $matches);

		if (!empty($matches[1])) {
			
			foreach (array_keys($matches[1]) as $index) {
				$rootKey = $matches[1][$index];
				$nestedPath = $matches[2][$index];
				$fullPlaceholder = $matches[0][$index];

				
				$value = '';

				
				if (isset($data[$rootKey])) {
					
					$nestedValue = $this->_value($nestedPath, $data[$rootKey]);

					
					if ($nestedValue !== false && !is_array($nestedValue) && !is_object($nestedValue)) {
						$value = $nestedValue;
					}
				}

				
				$html = str_replace($fullPlaceholder, $value, $html);
			}
		}

		return $html;
	}

	
	private function _normalizeConfig($config) {
		
		$defaultConfig = [
			'childrenKey' => 'items',
			'itemTemplate' => '{content}',
			'containerTemplate' => '{items}',
			'emptyTemplate' => '',
			'separator' => '',
			'childrenClass' => 'cdx-list__item-children', 
			'counterType' => 'numeric', 
			'start' => 1 
		];

		return array_merge($defaultConfig, $config);
	}

	
	private function _recursive($m, $value = null) {
		
		$dataPath = isset($m[1]) ? trim($m[1]) : '';

		
		$configJson = isset($m[2]) ? trim($m[2]) : '{}';

		
		if ((strpos($configJson, '"') === 0 && strrpos($configJson, '"') === strlen($configJson) - 1) ||
			(strpos($configJson, "'") === 0 && strrpos($configJson, "'") === strlen($configJson) - 1)) {
			$configJson = substr($configJson, 1, -1);
		}

		
		$configJson = stripslashes($configJson);

		
		$config = json_decode($configJson, true);
		
		if(json_last_error() !== JSON_ERROR_NONE || !is_array($config)) {
			
			$config = [];
		}

		
		$data = [];
		if($dataPath != 'null' && $dataPath != null) {
			$data = $this->_value($dataPath, $value);
		}

		
		return $this->_recursiveRender($data, $config);
	}

	private function _var($m, $value = null) {
		$v = $this->_value($m[1], $value);
		
		return $v !== false ? $v : $m[0];
	}

	private function _value($var, $value = null) {
		$e = explode('.', $var);
		$value = $value ?? $this->block;
		foreach($e as $_v) {
			if(!isset($value[$_v])) {
				return false;
			}
			$value = $value[$_v];
		}
		return $value;
	}
}