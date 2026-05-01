<?php exit('Access Denied');?>
<!--{template common/header}-->

<div class="upfile">
	<h3 class="flb">
		<em id="return_upload">{lang upload}</em>
		<em id="uploadwindowing" class="mtn" style="visibility:hidden"><img src="{IMGDIR}/uploading.gif" alt="" /></em>
		<span><a href="javascript:;" class="flbc" onclick="hideWindow('upload', 0)" title="{lang close}">{lang close}</a></span>
	</h3>
	<div class="c">
		<form id="uploadform" class="uploadform ptm pbm" method="post" autocomplete="off" target="uploadattachframe" onsubmit="uploadWindowstart()" action="misc.php?mod=swfupload&operation=upload&type=$type&inajax=yes&infloat=yes&simple=2" enctype="multipart/form-data">
			<input type="hidden" name="handlekey" value="upload" />
			<input type="hidden" name="uid" value="$_G['uid']">
			<input type="hidden" name="hash" value="{echo md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])}">
			<input type="hidden" name="thumbBase64" id="thumbBase64" value="">
			<div class="filebtn">
				<input type="file" name="Filedata" id="filedata" class="pf cur1" size="1" onchange="handleFileUpload(this)" />
				<button type="button" class="pn pnc"><strong>{lang upload_selectfile}</strong></button>
			</div>
		</form>
		<p class="xg1 mtn">
			<!--{if $type == 'image'}-->{lang attachment_allow_exts}: <span class="xi1">$imgexts</span><!--{elseif $_G['group']['attachextensions']}-->{lang attachment_allow_exts}: <span class="xi1">{$_G['group']['attachextensions']}</span><!--{/if}-->
		</p>
		<iframe name="uploadattachframe" id="uploadattachframe" style="display: none;" onload="uploadWindowload();"></iframe>
	</div>
</div>
<script>
function handleFileUpload(input) {
	const file = input.files[0];
	if (!file) return;
	if (!file.type.startsWith('video/')) {
		document.getElementById('uploadform').submit();
		return;
	}
	getFirstFrame(file, function(base64Image) {
		document.getElementById('thumbBase64').value = base64Image;
		document.getElementById('uploadform').submit();
	});
}

function getFirstFrame(file, callback) {
	const video = document.createElement('video');
	video.preload = 'metadata';
	video.muted = true;
	video.playsInline = true;
	video.crossOrigin = 'anonymous';

	const canvas = document.createElement('canvas');
	const ctx = canvas.getContext('2d');

	video.onloadedmetadata = () => {
		video.currentTime = 0;
	};

	video.onseeked = () => {
		canvas.width = video.videoWidth;
		canvas.height = video.videoHeight;
		ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
		const dataURL = canvas.toDataURL('image/png');
		callback(dataURL);
	};

	video.src = URL.createObjectURL(file);
}
</script>
<!--{template common/footer}-->