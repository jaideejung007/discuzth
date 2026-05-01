<?php exit('Access Denied');?>
<div id="{$editorid}_body_loading"><div class="loadicon vm"></div> {lang e_editor_loading}</div>
<div class="edt" id="{$editorid}_body" style="display: none">
	<div id="{$editorid}_controls" class="bar">
		<div class="y">
			<div class="b2r nbl nbr" id="{$editorid}_adv_5">
				<p>
					<a id="{$editorid}_undo" title="{lang e_undo}">Undo</a>
				</p>
				<p>
					<a id="{$editorid}_redo" title="{lang e_redo}">Redo</a>
				</p>
			</div>
			<div class="z">
				<span class="mbn"><a id="{$editorid}_fullswitcher"></a><a id="{$editorid}_simple"></a></span>
				<label id="{$editorid}_switcher" class="bar_swch ptn"><input id="{$editorid}_switchercheck" type="checkbox" class="pc" name="checkbox" value="0" {if !$editor[editormode]}checked="checked"{/if} onclick="switchEditor(this.checked?0:1)" />{lang code}</label>
			</div>
		</div>
		<!--{if !empty($_G[setting][pluginhooks][post_editorctrl_right])}-->
			<div class="y"><!--{hook/post_editorctrl_right}--></div>
		<!--{/if}-->
		<div id="{$editorid}_button" class="btn cl">
			{cells editor/toolbar}
		</div>
	</div>

	<div id="rstnotice" class="ntc_l bbs" style="display:none">
		<a href="javascript:;" title="{lang post_topicreset}" class="d y" onclick="userdataoption(0)">close</a>{lang missed_data} <a class="xi2" href="javascript:;" onclick="userdataoption(1)"><strong>{lang post_autosave_restore}</strong></a>
	</div>

	<div class="area">
		<textarea name="$editor[textarea]" id="{$editorid}_textarea" class="pt" rows="15">$editor[value]</textarea>
	</div>
	<!--{subtemplate common/editor}-->
</div>
