<?php exit('Access Denied');?>
<!--{template common/header}-->
<h3 class="flb">
	{lang uch_selectfromalbum}
	<span>
		<a href="javascript:;" class="flbc" onclick="hideWindow('$_GET['handlekey']')" title="{lang close}">{lang close}</a>
	</span>
</h3>

<div class="c" style="width: 600px">
	<div class="p_c">
		<div class="p_opt">
			<div class="upfilelist pbm bbda">
				{lang uch_selectfromalbum}:
				<select onchange="if(this.value) {ajaxget('forum.php?mod=post&action=albumphoto&aid='+this.value+'&from=albumWin&ajaxtarget=albumlist_photo', 'albumlist_photo');}">
					<option value="">{lang select_album}</option>
					<!--{loop $albumlist $k $album}-->
					<option value="$album[albumid]"{if $k == 0} selected{/if}>$album[albumname]</option>
					<!--{/loop}-->
				</select>
				<div id="albumlist_photo"></div>
			</div>
			<p class="notice">
				<span class="y"><button onclick="window.open('home.php?mod=spacecp&ac=upload')" class="pn"><strong>{lang e_img_attach}</strong></button></span>
				{lang album_click_select}
			</p>
		</div>
	</div>
</div>

<script type="text/javascript" reload="1">
	<!--{if $albumlist}-->
	ajaxget('forum.php?mod=post&action=albumphoto&aid={$albumlist[0][albumid]}&from=albumWin&ajaxtarget=albumlist_photo', 'albumlist_photo');
	<!--{/if}-->
</script>
<!--{template common/footer}-->