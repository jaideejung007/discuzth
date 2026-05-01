<?php exit('Access Denied');?>
<!--{subtemplate common/header_common}-->
{cells common/header/css}
{cells common/header/js}
<style>
	body {
		--dz-BG-body:{if $_G['style']['touch_style_bodybg']}$_G['style']['touch_style_bodybg']{else}#EEEEEE{/if};
		--dz-BG-color:{if $_G['style']['touch_style_color']}$_G['style']['touch_style_color']{else}#2B7ACD{/if};
		--dz-BG-0:{if $_G['style']['touch_style_bg0']}$_G['style']['touch_style_bg0']{else}#FFFFFF{/if};
		--dz-BG-1:{if $_G['style']['touch_style_bg1']}$_G['style']['touch_style_bg1']{else}#333333{/if};
		--dz-BG-2:{if $_G['style']['touch_style_bg2']}$_G['style']['touch_style_bg2']{else}#FF5656{/if};
		--dz-BG-3:{if $_G['style']['touch_style_bg3']}$_G['style']['touch_style_bg3']{else}#FF9C00{/if};
		--dz-BG-4:{if $_G['style']['touch_style_bg4']}$_G['style']['touch_style_bg4']{else}#B3CC0D{/if};
		--dz-BG-5:{if $_G['style']['touch_style_bg5']}$_G['style']['touch_style_bg5']{else}#F3F3F3{/if};
		--dz-BG-6:{if $_G['style']['touch_style_bg6']}$_G['style']['touch_style_bg6']{else}#CCCCCC{/if};
		--dz-BG-n:{if $_G['style']['touch_style_bgn']}$_G['style']['touch_style_bgn']{else}#A0C8EA{/if};
		--dz-FC-color:{if $_G['style']['touch_style_color']}$_G['style']['touch_style_color']{else}#2B7ACD{/if};
		--dz-FC-fff:{if $_G['style']['touch_style_tfff']}$_G['style']['touch_style_tfff']{else}#FFFFFF{/if};
		--dz-FC-333:{if $_G['style']['touch_style_t333']}$_G['style']['touch_style_t333']{else}#333333{/if};
		--dz-FC-666:{if $_G['style']['touch_style_t666']}$_G['style']['touch_style_t666']{else}#666666{/if};
		--dz-FC-777:{if $_G['style']['touch_style_t777']}$_G['style']['touch_style_t777']{else}#777777{/if};
		--dz-FC-888:{if $_G['style']['touch_style_t888']}$_G['style']['touch_style_t888']{else}#888888{/if};
		--dz-FC-999:{if $_G['style']['touch_style_t999']}$_G['style']['touch_style_t999']{else}#999999{/if};
		--dz-FC-aaa:{if $_G['style']['touch_style_taaa']}$_G['style']['touch_style_taaa']{else}#AAAAAA{/if};
		--dz-FC-bbb:{if $_G['style']['touch_style_tbbb']}$_G['style']['touch_style_tbbb']{else}#BBBBBB{/if};
		--dz-FC-ccc:{if $_G['style']['touch_style_tccc']}$_G['style']['touch_style_tccc']{else}#CCCCCC{/if};
		--dz-FC-ddd:{if $_G['style']['touch_style_tddd']}$_G['style']['touch_style_tddd']{else}#DDDDDD{/if};
		--dz-FC-nnn:{if $_G['style']['touch_style_tnnn']}$_G['style']['touch_style_tnnn']{else}#7DA0CC{/if};
		--dz-FC-light:{if $_G['style']['touch_style_tlight']}$_G['style']['touch_style_tlight']{else}#FF9C00{/if};
		--dz-FC-a:{if $_G['style']['touch_style_ta']}$_G['style']['touch_style_ta']{else}#F26C4F{/if};
		--dz-FC-v:{if $_G['style']['touch_style_tv']}$_G['style']['touch_style_tv']{else}#7CBE00{/if};		
		--dz-FC-t:transparent;		
		--dz-BOR-ed:{if $_G['style']['touch_style_border']}$_G['style']['touch_style_border']{else}#EDEDED{/if};
	}
	<!--{if $_GET['diy'] != 'yes' && $_GET['mobilediy'] != 'yes'}-->.discuz_diy .area {background:none;min-height:0}<!--{/if}-->
	<!--{if $_G['style']['touch_style_addcss']}-->$_G['style']['touch_style_addcss']<!--{/if}-->
</style>
</head>
<body id="{$_G['basescript']}" class="pg_{CURMODULE} discuz_diy">
<!--{hook/global_header_mobile}-->
<div id="append_parent"></div>