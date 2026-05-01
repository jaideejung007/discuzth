<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editorblock_columns {

	var $version = '1.0.5';
	var $name = '多列';
	var $available = 1; 
	var $columns = 0; 
	var $identifier = 'columns';
	var $description = '多列区块，原生支持，不支持配置，并且本身不支持多列嵌套';
	var $filename = 'editorjs-columns';
	var $copyright = '<a href="https://addon.dismall.com/developer-32563.html" target="_blank">云诺</a>';
	var $type = '0'; 

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
            "id": "s-FgPhJacf",
            "type": "columns",
            "data": {
                "cols": [
                    {
                        "time": 1703647833755,
                        "blocks": [
                            {
                                "id": "Gi91gbORbV",
                                "type": "paragraph",
                                "data": {
                                    "text": "1列",
                                    "alignment": "left"
                                }
                            }
                        ],
                        "version": "2.28.2"
                    },
                    {
                        "time": 1703647833755,
                        "blocks": [
                            {
                                "id": "-E6VBirxsd",
                                "type": "paragraph",
                                "data": {
                                    "text": "2列",
                                    "alignment": "left"
                                }
                            }
                        ],
                        "version": "2.28.2"
                    },
                    {
                        "time": 1703647833755,
                        "blocks": [
                            {
                                "id": "nhF5HL0xeU",
                                "type": "paragraph",
                                "data": {
                                    "text": "3列",
                                    "alignment": "left"
                                }
                            }
                        ],
                        "version": "2.28.2"
                    }
                ]
            }
}
EOF;
	}

	
	function getConfig() {
		return <<<EOF
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
.ce-editorjsColumns_col {
	flex:50%
}
.ce-editorjsColumns_wrapper {
	display:flex;
	width:auto;
	gap:10px;
	margin-bottom:10px;
	flex-direction:row
}
.ce-editorjsColumns_wrapper .ce-toolbar__actions {
	z-index:0
}
.ce-editorjsColumns_wrapper .ce-toolbar {
	z-index:4
}
.ce-editorjsColumns_wrapper .ce-popover {
	z-index:4000
}
@media(max-width:800px) {
	.ce-editorjsColumns_wrapper {
	flex-direction:column;
	padding:10px;
	border:1px solid #ccc;
	border-radius:4px
}
}.ce-inline-toolbar {
	z-index:1000
}
.ce-toolbar__actions {
	right:calc(100% + 30px);
	background-color:rgba(255,255,255,.5);
	border-radius:4px
}
.codex-editor--narrow .codex-editor__redactor {
	margin:0
}
.ce-toolbar {
	z-index:4
}
.codex-editor {
	z-index:auto !important
}

</style>
EOF;

	}

	function getParser($block = []) {
		global $_G;
		return <<<EOF
<div class="ce-block ce-block--focused" data-id="{id}">
    <div class="ce-block__content">
        <div class="ce-editorjsColumns_wrapper">
        	[loop data.cols]
            <div class="ce-editorjsColumns_col editorjs_col_[loopindex]">
                <div class="codex-editor codex-editor--narrow">
                    <div class="codex-editor__redactor" style="padding-bottom: 50px;">
                        [column blocks]
                    </div>
                </div>
            </div>
            [/loop]
        </div>
    </div>
</div>
EOF;
	}

}