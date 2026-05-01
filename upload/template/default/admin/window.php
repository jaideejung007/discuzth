<?php exit('Access Denied');?>
{template common/header_ajax}
<style>
.adminwin a, .adminwin button { cursor: pointer !important; }
.adminwin input { cursor: text !important; margin-bottom: 5px; }
.adminwin span { margin-left: 5px; margin-right: 5px; color: #999; cursor: pointer !important; }
</style>
<table width="100%" cellpadding="0" cellspacing="0" class="fwin adminwin" style="min-width: 400px">
	<tr>
		<td class="t_l"></td>
		<td class="t_c"></td>
		<td class="t_r"></td>
	</tr>
	<tr>
		<td class="m_l">&nbsp;&nbsp;</td>
		<td class="m_c">
			<div class="mtm mbm">
				<ul class="cl">
					<li style="float:right"><span class="flbc" onclick="display('{$closeid}')">{lang close}</span></li>
					<li><b>{$title}</b></li>
				</ul>
				<div style="padding: 10px">
				{$s}
				</div>
				<div id="{$closeid}_footer">
				</div>
			</div>
		</td>
		<td class="m_r"></td>
	</tr>
	<tr>
		<td class="b_l"></td>
		<td class="b_c"></td>
		<td class="b_r"></td>
	</tr>
</table>
{template common/footer_ajax}