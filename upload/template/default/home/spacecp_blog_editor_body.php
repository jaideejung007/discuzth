<?php exit('Access Denied');?>
<table cellspacing="0" cellpadding="0" class="tfm">
	<tr>
		<td><input type="text" id="subject" name="subject" value="$blog[subject]" size="60" class="px" style="width: 63%;" /></td>
	</tr>
	<tr>
		<td>
			<!--{subtemplate home/editor_image_menu}-->
			<textarea class="pt" name="message" id="uchome-ttHtmlEditor" style="height:100%;width:100%;display:none;border:0">$blog[message]</textarea>
			<div style="border:1px solid #C5C5C5;height:400px;"><iframe src='home.php?mod=editor&charset={CHARSET}&allowhtml=$allowhtml&doodle={if $_G['setting']['magicstatus'] && !empty($_G['setting']['magics']['doodle'])}1{/if}' name="uchome-ifrHtmlEditor" id="uchome-ifrHtmlEditor" scrolling="no" border="0" frameborder="0" style="width:100%;height:100%;position:relative;"></iframe></div>
		</td>
	</tr>
</table>