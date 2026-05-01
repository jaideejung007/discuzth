<?php exit('Access Denied');?>
<!--{if $type}-->
<dt>
	<span class="y xg1">{lang share_count}&nbsp;&nbsp;</span>
	<span class="z">{lang share_description}:</span>
</dt>
<!--{/if}-->
<div id="moodfm" class="moodfm">
	<form method="post" autocomplete="off" id="mood_addform" action="home.php?mod=spacecp&ac=doing&view=$_GET['view']" enctype="multipart/form-data">
		<div class="moodfm_post">
			<div class="moodfm_text">
				<textarea name="message" id="message" class="xg1" placeholder="{$defaultstr}" rows="3"></textarea>
			</div>
			<!--{if !$type}-->
			<div class="specialpost s_clear">
				<li class="upload-main">
					<div class="image-main" id="MultiPicList">
						<div id="multipic_img">
							<div id="multipic_btn" class="image-list image-upload image-upload-mp">
								<div class="file_pic"></div>
								<input name="photos[]" type="file" class="file" id="multipic_sel" multiple="multiple"
								       accept=".jpg,.jpeg,.png,image/jpeg,image/png">
							</div>
						</div>
					</div>
				</li>
			</div>
			<!--{else}-->
			<ul class="post_box cl">
				<!--{if $commentcable[$type]}-->
				<label><li class="flex-box b0"><div class="flex tit"><!--{if $type == 'thread'}-->{lang post_add_inonetime}<!--{else}-->{lang comment_add_inonetime}<!--{/if}--></div><div class="flex"></div><div class="flex y"><input type="checkbox" class="pc" name="iscomment" value="1"/></div></li></label>
				<!--{/if}-->
			</ul>
			<!--{/if}-->
			<div class="moodfm_f">
				<div class="moodfm_btn">
					<button type="submit" name="add" id="add" class="pgsbtn button">{lang publish}</button>
				</div>
			</div>
		</div>
		<!--{if $type}--><input type="hidden" name="type" value="$type" /><!--{/if}-->
		<!--{if $id}--><input type="hidden" name="id" value="$id" /><!--{/if}-->
		<!--{if !$type}--><input type="hidden" name="referer" value="home.php?mod=space&do=doing" /><!--{/if}-->
		<input type="hidden" name="addsubmit" value="true" />
		<input type="hidden" name="refer" value="$theurl" />
		<input type="hidden" name="topicid" value="$topicid" />
		<input type="hidden" name="formhash" value="{FORMHASH}" />
	</form>
</div>

<script type="text/javascript" reload="1">
	<!--{if !$type}-->
	listenup();
	function listenup() {
		if (typeof FileReader === 'undefined') {
		} else {
			document.getElementById('multipic_sel').addEventListener('change', function(event) {
				const files = event.target.files;
				const imgContainer = document.getElementById('multipic_img');

				for (let i = 0; i < files.length; i++) {
					const file = files[i];
					if (file) {
						(function(currentFile, index) {
							const reader = new FileReader();
							reader.onload = function(e) {
								const img = new Image();
								img.src = e.target.result;
								const imgWrapper = document.createElement('div');
								imgWrapper.className = 'previewbigpic z';
								imgWrapper.setAttribute('data-file-index', index);
                                const removeDiv = document.createElement('i');
								removeDiv.className = 'fico-error';
								removeDiv.onclick = function() {
									MultiPicDel(this);
								};
								imgWrapper.prepend(img);
								imgWrapper.prepend(removeDiv);
								img.style.width = '100%';
								img.style.height = '100%';
								img.style.objectFit = 'cover';
								imgContainer.prepend(imgWrapper);
							};
							reader.onerror = function() {
							};
							reader.readAsDataURL(currentFile);
						})(file);
					}
				}
				document.getElementById("multipic_sel").style.display = "none";
				document.getElementById("multipic_sel").removeAttribute("id");
				const newbtn = document.createElement('input');
				newbtn.type = 'file';
				newbtn.name = 'photos[]';
				newbtn.id = 'multipic_sel';
				newbtn.className = "file";
				newbtn.multiple = "multiple";
				newbtn.accept = ".jpg,.jpeg,.png,image/jpeg,image/png";
				document.getElementById('multipic_btn').append(newbtn);
				listenup();
			});
		}
	}

	var MultiPicUploaded = 0;
	var mpimgmax = 12;
	var mpimgmax_low = mpimgmax - 1;
	if (MultiPicUploaded >= mpimgmax_low) {
		document.querySelector('.image-upload-mp').style.display = 'none';
	}

	function MultiPicDel(obj) {
		var oldAid = obj.getAttribute('dataid');
		console.log(oldAid);
		MultiPicUploaded--;
		obj.parentNode.remove();
		if (MultiPicUploaded < mpimgmax) {
			document.querySelector('.image-upload-mp').style.display = '';
		}
	}

	function preview_pic(obj) {
		var hlthumb = obj.parentNode.childNodes[0];
		zoom(hlthumb, hlthumb.src);
	}
	<!--{/if}-->
</script>