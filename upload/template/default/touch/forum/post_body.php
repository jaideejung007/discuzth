<?php exit('Access Denied');?>
<li class="mtext">
	<textarea class="pt" id="needmessage" autocomplete="off" id="{$editorid}_textarea" name="$editor['textarea']" placeholder="<!--{if $_GET['action'] != 'reply'}-->{lang posts}<!--{/if}-->{lang thread_content}" fwin="reply"><!--{if $special != 127}-->$postinfo['message']<!--{/if}--></textarea>
	<div class="mimg cl">
		<!--{if $allowpostimg && $allowuploadtoday}-->
		<a href="javascript:;" class="post_camerabtn"><i class="dm-camera"></i>{lang e_camera_attach}<input type="file" name="Filedata" id="cameradata" capture="environment" accept=".jpg,.jpeg,.gif,.png,.bmp,image/jpeg,image/gif,image/png,image/bmp" /></a>
		<a href="javascript:;" class="post_imgbtn"><i class="dm-image"></i>{lang e_img_attach}<input type="file" name="Filedata" id="filedata" multiple="multiple" accept=".jpg,.jpeg,.gif,.png,.bmp,image/jpeg,image/gif,image/png,image/bmp" /></a>
		<!--{/if}-->
		<!--{if $_G['group']['allowpostattach'] && $allowuploadtoday}-->
		<a href="javascript:;" class="post_attbtn"><i class="dm-star-fill"></i>{lang upload_attach}<input type="file" name="Filedata" id="attfiledata" multiple="multiple" /></a>
		<!--{/if}-->
	</div>
	<div class="cl">
		<ul id="imglist" class="post_imglist cl">
		<!--{loop $imgattachs['used'] $temp}-->
			<li><span aid="$temp['aid']" class="del" up="1"><a href="javascript:;"><i class="dm-error"></i></a></span><span class="p_img"><a href="javascript:;"><img style="height:54px;width:54px;" id="aimg_$temp['aid']" src="{$temp['url']}/$temp['attachment']" /></a></span><input type="hidden" name="attachnew[{$temp['aid']}]['description']" /></li>
		<!--{/loop}-->
		</ul>
	</div>
	<div class="cl">
		<ul id="attlist" class="post_attlist setbox cl">
		<!--{loop $attachs['used'] $temp}-->
			<li class="b_t"><div class="tit"><span aid="{$temp['aid']}" up="1" class="del btn f_f"><a href="javascript:;"><i class="dm-trash z"></i></a></span>
			<!--{if $_G['setting']['allowattachurl']}-->
			<!--{if $temp['ext'] == 'mp3'}-->
				<span class="btn" onclick="addsmilies('[audio]attach://{$temp['aid']}.mp3[/audio]')">{lang forum_post_insert}{lang e_audio}</span>
			<!--{elseif $temp['ext'] == 'mp4'}-->
				<span class="btn" onclick="addsmilies('[media=x,500,375]attach://{$temp['aid']}.mp4[/media]')">{lang forum_post_insert}{lang e_video}</span>
			<!--{/if}-->
			<!--{/if}-->
			<span class="btn" onclick="addsmilies('[attach]{$temp['aid']}[/attach]')">{lang forum_post_insert}</span>{$temp['filetype']}<span class="link">{$temp['filename']}</span></div><div class="minput"><div class="attms flex-box"><span class="f_c">{lang description}</span><input type="text" name="attachnew[{$temp['aid']}][description]" value="{$temp['description']}" class="input flex"></div></div><div class="minput">
			<!--{if $_G['group']['allowsetattachperm']}-->
			<!--{if $_G['cache']['groupreadaccess']}-->
			<div class="attqx flex-box"><span>{lang forum_post_perm}</span>
				<div class="flex">
					<select name="attachnew[{$temp['aid']}][readperm]" id="readperm{$temp['aid']}" class="sort_sel">
						<option value="" selected="selected">{lang unlimited}</option>
						<!--{loop $_G['cache']['groupreadaccess'] $val}-->
						<option value="$val['readaccess']"{if $temp['readperm'] == $val['readaccess']} selected="selected"{/if}>$val['grouptitle']</option>
						<!--{/loop}-->
						<option value="255"{if $temp['readperm'] == 255} selected{/if}>{lang highest_right}</option>
					</select>
				</div>
			</div>
			<!--{/if}-->
			<!--{/if}-->
			<!--{if $_G['group']['maxprice']}--><div class="attjg flex-box"><span>{lang price}</span><input type="text" name="attachnew[{$temp['aid']}][price]" value="{$temp['price']}" class="input price flex"><em>{$_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]][title]}</em></div><!--{/if}--></div></li>
				<!--{/loop}-->
		</ul>
	</div>
</li>

<script type="text/javascript" src="{STATICURL}js/mobile/ajaxfileupload.js?{VERHASH}"></script>
<script type="text/javascript" src="{STATICURL}js/mobile/buildfileupload.js?{VERHASH}"></script>
<script type="text/javascript">
	var imgexts = typeof imgexts == 'undefined' ? 'jpg, jpeg, gif, png' : imgexts;
	var STATUSMSG = {
		'-1' : '{lang uploadstatusmsgnag1}',
		'0' : '{lang uploadstatusmsg0}',
		'1' : '{lang uploadstatusmsg1}',
		'2' : '{lang uploadstatusmsg2}',
		'3' : '{lang uploadstatusmsg3}',
		'4' : '{lang uploadstatusmsg4}',
		'5' : '{lang uploadstatusmsg5}',
		'6' : '{lang uploadstatusmsg6}',
		'7' : '{lang uploadstatusmsg7}(' + imgexts + ')',
		'8' : '{lang uploadstatusmsg8}',
		'9' : '{lang uploadstatusmsg9}',
		'10' : '{lang uploadstatusmsg10}',
		'11' : '{lang uploadstatusmsg11}',
		'12' : '{lang uploadstatusmsg12}',
		'13' : '{lang uploadstatusmsg13}'
	};
	var form = $('#postform');
	
	(function($){
		$.fn.extend({
			insertAtCaret: function(myValue){
				var t = $(this)[0];
				if (document.selection) {
					this.focus();
					sel = document.selection.createRange();
					sel.text = myValue;
					this.focus();
				}
				else
				if (t.selectionStart || t.selectionStart == '0') {
					var startPos = t.selectionStart;
					var endPos = t.selectionEnd;
					var scrollTop = t.scrollTop;
					t.value = t.value.substring(0, startPos) + myValue + t.value.substring(endPos, t.value.length);
					this.focus();
					t.selectionStart = startPos + myValue.length;
					t.selectionEnd = startPos + myValue.length;
					t.scrollTop = scrollTop;
				}
				else {
					this.value += myValue;
					this.focus();
				}
			}
		})
	})(jQuery);
	function addsmilies(a){
		$('#needmessage').insertAtCaret(a);
	}
	$('#needmessage').on('scroll', function() {
		var obj = $(this);
		if(obj.scrollTop() > 0) {
			obj.attr('rows', parseInt(obj.attr('rows'))+2);
		}
	}).scrollTop($(document).height());
	$(document).on('change', '#cameradata', function() {
			popup.open('<img src="' + IMGDIR + '/imageloading.gif">');
			uploadsuccess = function(data) {
				if(data == '') {
					popup.open('{lang uploadpicfailed}', 'alert');
				}
				var dataarr = data.split('|');
				if(dataarr[0] == 'DISCUZUPLOAD' && dataarr[2] == 0) {
					popup.close();
					$('#imglist').append('<li><div><span aid="'+dataarr[3]+'" class="del"><a href="javascript:;"><i class="dm-error"></i></a></span><span class="p_img"><a href="javascript:;" onclick="addsmilies(\'[attachimg]'+dataarr[3]+'[/attachimg]\')"><img style="height:54px;width:54px;" id="aimg_'+dataarr[3]+'" src="{$_G['setting']['attachurl']}forum/'+dataarr[5]+'" /></a></span><input type="hidden" name="attachnew['+dataarr[3]+'][description]" /><div></li>');
				} else {
					var sizelimit = '';
					if(dataarr[7] == 'ban') {
						sizelimit = '{lang uploadpicatttypeban}';
					} else if(dataarr[7] == 'perday') {
						sizelimit = '{lang donotcross}'+Math.ceil(dataarr[8]/1024)+'K)';
					} else if(dataarr[7] > 0) {
						sizelimit = '{lang donotcross}'+Math.ceil(dataarr[7]/1024)+'K)';
					}
					popup.open(STATUSMSG[dataarr[2]] + sizelimit, 'alert');
				}
			};
			if(typeof FileReader != 'undefined' && this.files[0]) {//note 支持html5上传新特性
				for (const file of this.files) {
					var tmpfiles = [];
					tmpfiles[0] = file;
					$.buildfileupload({
						uploadurl:'misc.php?mod=swfupload&operation=upload&type=image&inajax=yes&infloat=yes&simple=2',
						files:tmpfiles,
						uploadformdata:{uid:"$_G['uid']", hash:"<!--{eval echo md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])}-->"},
						uploadinputname:'Filedata',
						maxfilesize:"$swfconfig['max']",
						success:uploadsuccess,
						error:function() {
							popup.open('{lang uploadpicfailed}', 'alert');
						}
					});
				}
			} else {
				$.ajaxfileupload({
					url:'misc.php?mod=swfupload&operation=upload&type=image&inajax=yes&infloat=yes&simple=2',
					data:{uid:"$_G['uid']", hash:"<!--{eval echo md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])}-->"},
					dataType:'text',
					fileElementId:'filedata',
					success:uploadsuccess,
					error: function() {
						popup.open('{lang uploadpicfailed}', 'alert');
					}
				});
			}
	});
	$(document).on('change', '#filedata', function() {
			popup.open('<img src="' + IMGDIR + '/imageloading.gif">');
			uploadsuccess = function(data) {
				if(data == '') {
					popup.open('{lang uploadpicfailed}', 'alert');
				}
				var dataarr = data.split('|');
				if(dataarr[0] == 'DISCUZUPLOAD' && dataarr[2] == 0) {
					popup.close();
					$('#imglist').append('<li><div><span aid="'+dataarr[3]+'" class="del"><a href="javascript:;"><i class="dm-error"></i></a></span><span class="p_img"><a href="javascript:;" onclick="addsmilies(\'[attachimg]'+dataarr[3]+'[/attachimg]\')"><img style="height:54px;width:54px;" id="aimg_'+dataarr[3]+'" src="{$_G['setting']['attachurl']}forum/'+dataarr[5]+'" /></a></span><input type="hidden" name="attachnew['+dataarr[3]+'][description]" /><div></li>');
				} else {
					var sizelimit = '';
					if(dataarr[7] == 'ban') {
						sizelimit = '{lang uploadpicatttypeban}';
					} else if(dataarr[7] == 'perday') {
						sizelimit = '{lang donotcross}'+Math.ceil(dataarr[8]/1024)+'K)';
					} else if(dataarr[7] > 0) {
						sizelimit = '{lang donotcross}'+Math.ceil(dataarr[7]/1024)+'K)';
					}
					popup.open(STATUSMSG[dataarr[2]] + sizelimit, 'alert');
				}
			};
			if(typeof FileReader != 'undefined' && this.files[0]) {//note 支持html5上传新特性
				for (const file of this.files) {
					var tmpfiles = [];
					tmpfiles[0] = file;
					$.buildfileupload({
						uploadurl:'misc.php?mod=swfupload&operation=upload&type=image&inajax=yes&infloat=yes&simple=2',
						files:tmpfiles,
						uploadformdata:{uid:"$_G['uid']", hash:"<!--{eval echo md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])}-->"},
						uploadinputname:'Filedata',
						maxfilesize:"$swfconfig['max']",
						success:uploadsuccess,
						error:function() {
							popup.open('{lang uploadpicfailed}', 'alert');
						}
					});
				}
			} else {
				$.ajaxfileupload({
					url:'misc.php?mod=swfupload&operation=upload&type=image&inajax=yes&infloat=yes&simple=2',
					data:{uid:"$_G['uid']", hash:"<!--{eval echo md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])}-->"},
					dataType:'text',
					fileElementId:'filedata',
					success:uploadsuccess,
					error: function() {
						popup.open('{lang uploadpicfailed}', 'alert');
					}
				});
			}
	});
	$(document).on('change', '#attfiledata', function() {
			popup.open('<img src="' + IMGDIR + '/imageloading.gif">');
			uploadsuccess = function(data) {
				if(data == '') {
					popup.open('{lang uploadpicfailed}', 'alert');
				}
				var dataarr = data.split('|');
				if(dataarr[0] == 'DISCUZUPLOAD' && dataarr[2] == 0) {
					popup.close();
					var video_file = '';
					var file_ex = 'unknown.gif';
					if (/bittorrent$|torrent$/.test(dataarr[6].toLowerCase())) {
						file_ex = 'torrent.gif';
					} else if (/pdf$|pdf$/.test(dataarr[6].toLowerCase())) {
						file_ex = 'pdf.gif';
					} else if (/(jpg|gif|png|bmp)$/.test(dataarr[6].toLowerCase())) {
						file_ex = 'image.gif';
					} else if (/(swf|fla|flv|swi)$/.test(dataarr[6].toLowerCase())) {
						file_ex = 'flash.gif';
					} else if (/(wav|mid|mp3|m3u|wma|asf|asx|vqf|mpg|mpeg|avi|wmv|mp4|ogv|webm|ogg)$/.test(dataarr[6].toLowerCase())) {
						file_ex = 'av.gif';
					} else if (/(ra|rm|rv)$/.test(dataarr[6].toLowerCase())) {
						file_ex = 'real.gif';
					} else if (/(php|js|pl|cgi|asp|htm|html|xml)$/.test(dataarr[6].toLowerCase())) {
						file_ex = 'html.gif';
					} else if (/(txt|rtf|wri|chm)$/.test(dataarr[6].toLowerCase())) {
						file_ex = 'text.gif';
					} else if (/(doc|ppt)$/.test(dataarr[6].toLowerCase())) {
						file_ex = 'msoffice.gif';
					} else if (/rar$/.test(dataarr[6].toLowerCase())) {
						file_ex = 'rar.gif';
					} else if (/(zip|arj|arc|cab|lzh|lha|tar|gz)$/.test(dataarr[6].toLowerCase())) {
						file_ex = 'zip.gif';
					} else if (/(exe|com|bat|dll)$/.test(dataarr[6].toLowerCase())) {
						file_ex = 'binary.gif';
					} else {
						file_ex = 'unknown.gif';
					}
					<!--{if $_G['setting']['allowattachurl']}-->
					if (/mp3$/.test(dataarr[6].toLowerCase())) {
						video_file = '<span class="btn" onclick="addsmilies(\'[audio]attach://' + dataarr[3] + '.mp3[/audio]\')">{lang forum_post_insert}{lang e_audio}</span>';
					} else if (/(mp4)$/.test(dataarr[6].toLowerCase())) {
						video_file = '<span class="btn" onclick="addsmilies(\'[media=x,500,375]attach://' + dataarr[3] + '.mp4[/media]\')">{lang forum_post_insert}{lang e_video}</span>';
					}
					<!--{/if}-->
					$('#attlist').append('<li class="b_t"><div class="tit"><span aid="'+dataarr[3]+'" up="1" class="del btn f_f"><a href="javascript:;"><i class="dm-trash z"></i></a></span>'+video_file+'<span class="btn" onclick="addsmilies(\'[attach]'+dataarr[3]+'[/attach]\')">{lang forum_post_insert}</span><img src="static/image/filetype/'+file_ex+'" border="0" class="vm mimg" alt=""><span class="link">'+dataarr[6]+'</span></div><!--{if $_GET['result'] != 'simple'}--><div class="minput"><div class="attms flex-box"><span class="f_c">{lang description}</span><input type="text" name="attachnew['+dataarr[3]+'][description]" value="" class="input flex"></div></div><div class="minput"><!--{if $_G['group']['allowsetattachperm']}--><!--{if $_G['cache']['groupreadaccess']}--><div class="attqx flex-box"><span>{lang forum_post_perm}</span><div class="flex"><select name="attachnew['+dataarr[3]+'][readperm]" id="readperm'+dataarr[3]+'" class="sort_sel"><option value="" selected="selected">{lang unlimited}</option><!--{loop $_G['cache']['groupreadaccess'] $val}--><option value="$val['readaccess']">$val['grouptitle']({lang readperm}: $val['readaccess'])</option><!--{/loop}--><option value="255"{if $temp['readperm'] == 255} selected{/if}>{lang highest_right}</option></select></div></div><!--{/if}--><!--{/if}--><!--{if $_G['group']['maxprice']}--><div class="attjg flex-box"><span>{lang price}</span><input type="text" name="attachnew['+dataarr[3]+'][price]" value="0" class="input price flex"><em>{$_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]][title]}</em></div><!--{/if}--></div><!--{/if}--></li>');
				} else {
					var sizelimit = '';
					if(dataarr[7] == 'ban') {
						sizelimit = '{lang uploadpicatttypeban}';
					} else if(dataarr[7] == 'perday') {
						sizelimit = '{lang donotcross}'+Math.ceil(dataarr[8]/1024)+'K)';
					} else if(dataarr[7] > 0) {
						sizelimit = '{lang donotcross}'+Math.ceil(dataarr[7]/1024)+'K)';
					}
					popup.open(STATUSMSG[dataarr[2]] + sizelimit, 'alert');
				}
			};
			if(typeof FileReader != 'undefined' && this.files[0]) {//note 支持html5上传新特性
				for (const file of this.files) {
					if (file.type.startsWith('video/')) {
						getFirstFrame(file, function(base64Image) {
							var tmpfiles = [];
							tmpfiles[0] = file;
							$.buildfileupload({
								uploadurl:'misc.php?mod=swfupload&operation=upload&fid={$_G['fid']}&inajax=yes&infloat=yes&simple=2',
								files:tmpfiles,
								uploadformdata:{uid:"$_G['uid']", hash:"<!--{eval echo md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])}-->",thumbBase64: base64Image},
								uploadinputname:'Filedata',
								maxfilesize:"$swfconfig['max']",
								success:uploadsuccess,
								error:function() {
									popup.open('{lang uploadpicfailed}', 'alert');
								}
							});
						});
					}else{
						var tmpfiles = [];
						tmpfiles[0] = file;
						$.buildfileupload({
							uploadurl:'misc.php?mod=swfupload&operation=upload&fid={$_G['fid']}&inajax=yes&infloat=yes&simple=2',
							files:tmpfiles,
							uploadformdata:{uid:"$_G['uid']", hash:"<!--{eval echo md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])}-->"},
							uploadinputname:'Filedata',
							maxfilesize:"$swfconfig['max']",
							success:uploadsuccess,
							error:function() {
								popup.open('{lang uploadpicfailed}', 'alert');
							}
						});
					}
				}
			} else {
				if (file.type.startsWith('video/')) {
					getFirstFrame(file, function(base64Image) {
					});
					$.ajaxfileupload({
						url:'misc.php?mod=swfupload&operation=upload&fid={$_G['fid']}&inajax=yes&infloat=yes&simple=2',
						data:{uid:"$_G['uid']", hash:"<!--{eval echo md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])}-->",thumbbase64: base64Image},
						dataType:'text',
						fileElementId:'attfiledata',
						success:uploadsuccess,
						error: function() {
							popup.open('{lang uploadpicfailed}', 'alert');
						}
					});
				}else{
					$.ajaxfileupload({
						url:'misc.php?mod=swfupload&operation=upload&fid={$_G['fid']}&inajax=yes&infloat=yes&simple=2',
						data:{uid:"$_G['uid']", hash:"<!--{eval echo md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])}-->"},
						dataType:'text',
						fileElementId:'attfiledata',
						success:uploadsuccess,
						error: function() {
							popup.open('{lang uploadpicfailed}', 'alert');
						}
					});
				}
			}
	});
	$(document).on('click', '.del', function() {
		var obj = $(this);
		$.ajax({
			type:'GET',
			url:'forum.php?mod=ajax&action=deleteattach&inajax=yes&aids[]=' + obj.attr('aid') + (obj.attr('up') == 1 ? '&tid={$postinfo['tid']}&pid={$postinfo['pid']}&formhash={FORMHASH}' : ''),
		})
		.success(function(s) {
			obj.closest('li').remove();
		})
		.error(function() {
			popup.open('{lang networkerror}', 'alert');
		});
		return false;
	});
</script>