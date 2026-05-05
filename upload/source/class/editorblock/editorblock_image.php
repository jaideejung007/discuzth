<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editorblock_image {

	var $version = '1.2.2';
	var $name = 'รูปภาพ';
	var $available = 1; 
	var $columns = 1; 
	var $identifier = 'image';
	var $description = 'บล็อกรูปภาพ';
	var $filename = 'image';
	var $copyright = '<a href="https://addon.dismall.com/developer-32563.html" target="_blank">Yunnuo</a>';
	var $type = '1'; 

	function __construct() {

	}

	function getsetting() {
		global $_G;
		$settings = [];
		return $settings;
	}

	function setsetting(&$blocknew, &$parameters) {
	}

	function getParameter() {
		return <<<EOF
{
            "id": "ls9YCBJ7V7",
            "type": "image",
            "data": {
                "file": {
                	"aid": 1,
                	"remote": 0,
                	"directory" => "forum",
                        "url": "202312/26/151439rv17ot1mgatw1121.png",
                },
                "caption": "desc",
                "withBorder": false,
                "stretched": false,
                "withBackground": false
            }
}
EOF;
	}

	
	function getConfig() {
		return <<<EOF
{
   tools_image: {
        image: {
            class: ImageTool,
            config: {
                endpoints: {
                    byFile: 'misc.php?mod=swfupload&action=swfupload&operation=jsoneditorupload&type=image&fid='+editor_fid, // Your backend file uploader endpoint
                    byUrl: 'misc.php?mod=swfupload&action=swfupload&operation=jsoneditorupload&type=image&fid='+editor_fid, // Your endpoint that provides uploading by Url
                },
                field: 'Filedata',
                types: 'image/*',
                additionalRequestData: {
                    'uid': editor_uid,
                    'hash': editor_hash,
                },
                remote_attachurl: editor_remote_attachurl,
                attachurl: editor_attachurl,
                captionPlaceholder: 'ข้อมูลคำอธิบาย',
                buttonContent: 'โปรดเลือกรูปภาพที่ต้องการอัปโหลด',
            },
            tunes: ['anchorTune', 'hideTune']
        },
   },
   i18n: {
       messages: {
          tools: {
            'image': {
                  'With border': 'มีเส้นขอบ',
        	  'Stretch image': 'ยืดรูปภาพ',
        	  'With background': 'มีสีพื้นหลัง',
        	  'Unsupported file type': 'ประเภทไฟล์ไม่รองรับ',
        	  'File has exceptions': 'ไฟล์มีความผิดปกติ',
        	  'File size cannot exceed ': 'ขนาดไฟล์ต้องไม่เกิน ',
        	  'User group does not support uploading this type of file': 'กลุ่มผู้ใช้ไม่รองรับการอัปโหลดไฟล์ประเภทนี้',
        	  'Couldn’t upload image. Please try another.': 'ไม่สามารถอัปโหลดรูปภาพได้ โปรดลองไฟล์อื่น',
            }
          }
        },
    },
}
EOF;
	}

	function getI18n() {
		return <<<EOF

EOF;
	}

	function getStyle() {
		return <<<EOF
<style type="text/css">
.ce-block {
    margin-bottom: 20px;
}
.ce-block__content,.ce-toolbar__content {
	/* max-width:calc(100% - 50px) */
	margin-left: auto;
    margin-right: auto;
}
.image-tool {
  --bg-color: #cdd1e0;
  --front-color: #388ae5;
  --border-color: #e8e8eb;

}

  .image-tool__image {
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 10px;
  }

  .image-tool__image-picture {
      max-width: 100%;
      vertical-align: bottom;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }

  .image-tool__image-preloader {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background-size: cover;
      margin: auto;
      position: relative;
      background-color: #cdd1e0;
      background-position: center center;
    }

  .image-tool__image-preloader::after {
        content: "";
        position: absolute;
        z-index: 3;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 2px solid #cdd1e0;
        border-top-color: #388ae5;
        left: 50%;
        top: 50%;
        margin-top: -30px;
        margin-left: -30px;
        animation: image-preloader-spin 2s infinite linear;
        box-sizing: border-box;
      }

  .image-tool__caption[contentEditable="true"][data-placeholder]::before {
      position: absolute !important;
      content: attr(data-placeholder);
      color: #707684;
      font-weight: normal;
      display: none;
    }

  .image-tool__caption[contentEditable="true"][data-placeholder]:empty::before {
        display: block;
      }

  .image-tool__caption[contentEditable="true"][data-placeholder]:empty:focus::before {
        display: none;
      }

  .image-tool--empty .image-tool__image {
      display: none;
    }

  .image-tool--empty .image-tool__caption, .image-tool--loading .image-tool__caption {
      display: none;
    }

  .image-tool .cdx-button {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .image-tool .cdx-button svg {
      height: auto;
      margin: 0 6px 0 0;
    }

  .image-tool--filled .cdx-button {
      display: none;
    }

  .image-tool--filled .image-tool__image-preloader {
        display: none;
      }

  .image-tool--loading .image-tool__image {
      min-height: 200px;
      display: flex;
      border: 1px solid #e8e8eb;
      background-color: #fff;
    }

  .image-tool--loading .image-tool__image-picture {
        display: none;
      }

  .image-tool--loading .cdx-button {
      display: none;
    }

  /**
   * Tunes
   * ----------------
   */

  .image-tool--withBorder .image-tool__image {
      border: 1px solid #e8e8eb;
    }

  .image-tool--withBackground .image-tool__image {
      padding: 15px;
      background: #cdd1e0;
    }

  .image-tool--withBackground .image-tool__image-picture {
        max-width: 60%;
        margin: 0 auto;
      }

  .image-tool--stretched .image-tool__image-picture {
        width: 100%;
      }

  .image-tool__caption {
		text-align: center;
		font-size: 14px;
		color: #a3a3a3;
	}
@keyframes image-preloader-spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
EOF;

	}

	function getParser($block = []) {
		global $_G;
		return <<<EOF
<div class="ce-block ce-block--focused" data-id="{id}" [if tunes.anchorTune.anchor=notnull]id="{tunes.anchorTune.anchor}"[/if]>
    <div class="ce-block__content">
        <div class="cdx-block image-tool image-tool--filled [if data.withBorder=1]image-tool--withBorder[/if] [if data.stretched=1]image-tool--stretched[/if] [if data.withBackground=1]image-tool--withBackground[/if]">
            <div class="image-tool__image">
                <div class="image-tool__image-preloader" style=""></div>
                <img id="aimg_{id}" class="image-tool__image-picture _zoom" src="[url data.file.url,data.file.remote,data.file.directory]" title="{data.caption}" alt="{data.caption}" data-aid="{data.file.aid}"/>
            </div>
            <div class="cdx-input image-tool__caption" data-placeholder="{data.caption}">{data.caption}</div>
        </div>
    </div>
</div>
EOF;
	}

}