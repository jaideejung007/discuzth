<?php exit('Access Denied');?>
<!--{template common/header}-->
<div id="pt" class="bm cl">
	<div class="z">
		<a href="./" class="nvhm" title="{lang homepage}">$_G[setting][bbname]</a> <em>&rsaquo;</em>
		<a href="forum.php?mod=collection">{lang collection}</a> <em>&rsaquo;</em>
		<!--{if $op == 'edit'}-->
		<a href="forum.php?mod=collection&action=view&ctid={$_G['collection']['ctid']}">{$_G['collection']['name']}</a> <em>&rsaquo;</em>
		{lang collection_edit}
		<!--{else}-->
		{lang collection_create}
		<!--{/if}-->
	</div>
</div>
<script>
var titlelimit = '$titlelimit';
var desclimit = '$desclimit';
function checklen() {
	if(mb_strlen($("formtitle").value) > titlelimit) {
		showError({lang collection_title_exceed});
		return false;
	}
	if(mb_strlen($("formdesc").value) > desclimit) {
		showError({lang collection_desc_exceed});
		return false;
	}
	return true;
}
</script>
 <div id="ct" class="wp cl">
	<div class="bm">
		<div class="bm_h">
			<h2>
				<!--{if $op == 'edit'}-->
				{lang collection_edit}
				<!--{else}-->
				{lang collection_create}
				<!--{/if}-->
			</h2>
		</div>
		<div class="bm_c">
			<form enctype="multipart/form-data" action="forum.php?mod=collection&action=edit" onsubmit="return checklen();" method="POST">
				<table cellspacing="0" cellpadding="0" class="tfm">
					<tr>
						<th>{lang collection_title}</th>
						<td><input type="text" value="{$_G['collection']['name']}" name="title" id="formtitle" class="px" /></td>
					</tr>
					<tr>
						<th>{lang collection_desc}</th>
						<td><textarea name="desc" id="formdesc" rows="10" class="pt">{$_G['collection']['desc']}</textarea></td>
					</tr>
					<tr>
						<th>{lang collection_keywords}</th>
						<td>
							<input type="text" value="{$_G['collection']['keyword']}" name="keyword" id="formkeyword" class="px" />
							<p class="xg1">{lang collection_keywords_desc}</p>
						</td>
					</tr>
					<tr>
						<th>{lang collection_cover}</th>
						<td>
							<input type="file" name="cover" id="cover" class="pf" size="25" />
							<!--{if !empty($_G['collection']['cover'])}-->
							<label><input type="checkbox" name="deletecover" class="pc" value="1" />{lang collection_no_image}</label>
							<!--{/if}-->
							<p class="d">{lang collection_cover_resize}</p>
						</td>
					</tr>
					<!--{if !empty($_G['collection']['cover'])}-->
					<tr>
						<th>&nbsp;</th>
						<td>
							<img onload="thumbImg(this, 1)" _width="400" _height="200" src="$_G['collection']['cover']?{TIMESTAMP}" />
						</td>
					</tr>
					<!--{/if}-->
					<tr>
						<th>{lang collection_icon}</th>
						<td>
							<input type="file" id="icon" class="pf vm" size="25" name="icon" />
							<p class="d">
								{lang collection_icon_resize} &nbsp;
							</p>
						</td>
					</tr>
					<!--{if !empty($_G['collection']['icon'])}-->
					<tr>
						<th>&nbsp;</th>
						<td>
							<img onload="thumbImg(this, 1)" _width="200" _height="200" src="$_G['collection']['icon']?{TIMESTAMP}" />
						</td>
					</tr>
					<!--{/if}-->
					<tr>
						<th></th>
						<td>
							<input type="hidden" value="1" name="submitcollection" />
							<input type="hidden" value="{$op}" name="op" />
							<input type="hidden" value="{$ctid}" name="ctid" />
							<input type="hidden" name="formhash" id="formhash" value="{FORMHASH}" />
							<button type="submit" name="collectionsubmit" class="pn pnc" value="submit"><span><!--{if $op == 'edit'}-->{lang collection_edit}<!--{else}-->{lang collection_create}<!--{/if}--></span></button>
							<!--{if $op != 'edit'}-->
								<span class="xg1">{lang collection_remain_tips}</span>
							<!--{/if}-->
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<!--{template common/footer}-->