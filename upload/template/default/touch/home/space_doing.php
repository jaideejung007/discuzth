<?php exit('Access Denied'); ?>
<!--{template common/header}-->

<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2>{lang doing}</h2>
	<div class="my"><a href="home.php?mod=spacecp&ac=doing"><i class="dm-edit"></i></a></div>
</div>
<div class="dhnav_box">
	<div class="dhnv cl">
		<div id="dhnav_li">
			<ul class="swiper-wrapper">
				<li class="swiper-slide{if $_GET['view'] == 'all'} mon{/if}"><a href="home.php?mod=space&do=$do&view=all">{lang view_all}</a></li>
				<li class="swiper-slide{if $_GET['view'] == 'me'} mon{/if}"><a href="home.php?mod=space&do=$do&view=me">{lang doing_view_me}</a></li>
				<!--{if helper_access::check_module('follower')}-->
				<li class="swiper-slide{if $_GET['view'] == 'follow'} mon{/if}"><a href="home.php?mod=space&do=$do&view=follow">{lang me_follow_doing}</a></li>
				<!--{/if}-->
				<!--{if helper_access::check_module('friend')}-->
				<li class="swiper-slide{if $_GET['view'] == 'we'} mon{/if}"><a href="home.php?mod=space&do=$do&view=we">{lang me_friend_doing}</a></li>
				<!--{/if}-->
			</ul>
		</div>
	</div>
</div>
<script type="text/javascript">
	initdhnav("#dhnav_li");
</script>
<div class="doing_list threadlist_box cl">
	<div class="doing_list_box threadlist cl">
		<!--{if $tagname}-->
		<p class="p10 tbmu cl">{lang doing_tag_record} <span style="color: red; font-weight: 700;">#{$tag['tagname']}#</span> {lang doing_record_list}</p>
		<!--{/if}-->
		<!--{if $dolist}-->
		<!--{loop $dolist $dv}-->
		<!--{eval $doid = $dv['doid'];}-->
		<!--{eval $_GET['key'] = $key = random(8);}-->
		<div class="doing_card">
			<div class="doing_card_content">
				<div class="doing_card_top">
					<div class="doing_card_top_left">
						<div class="hl-author-icon">
							<span class="mio-avatar-badge">
								<a href="home.php?mod=space&uid=$dv['uid']&do=profile" class="mio-avatar">
									<!--{avatar($dv['uid'],'small')}-->
								</a>
							</span>
						</div>
						<div class="doing_card_info">
							<div class="doing_card_info_top">
								<a href="home.php?mod=space&uid=$dv['uid']&do=profile" id="author_$value['cid']" class="doing_card_info_name">$dv['username']</a>
							</div>
							<div class="doing_card_info_top">
								<span class="xg1"><!--{date($dv['dateline'], 'u')}--></span>
								<!--{if $_G['setting']['showiplocation']}--><span class="pl15 xg1">$dv['iplocation']</span><!--{/if}-->
							</div>
						</div>

					</div>
				</div>
				<div class="doing_card_text">
					<div class="doing_card_text_textcontent">
						<div id="comment_$doid" class="newmessage{if $value['magicflicker']} magicflicker{/if}">$dv[message]<!--{if $dv['status'] == 1}--> <span style="font-weight: bold;">({lang moderate_need})</span><!--{/if}--></div>
						<!--{if $dv['attachments']}-->
						<div class="doing_card_piclistbox">
							<div class="doing_card_piclist">
								<!--{loop $dv['attachments'] $attach}-->
								<!--{if $attach['isimage']}-->
								<div class="doing_card_piclist_item "><img lass="hl_noloadimage lazy lazy-fade-in" width="300" height="300" src="{$attach['thumb']}" data-src="{if $attach['remote']}{$_G['setting']['ftp']['attachurl']}{else}{$_G['setting']['attachurl']}{/if}doing/{$attach['attachment']}" zoomfile="{if $attach['remote']}{$_G['setting']['ftp']['attachurl']}{else}{$_G['setting']['attachurl']}{/if}doing/{$attach['attachment']}"></div>
								<!--{/if}-->
								<!--{/loop}-->
							</div>
						</div>
						<!--{/if}-->
					</div>
				</div>
				<!--{if $dv['body_template']}-->
					<div class="share_card_box mbm {$dv['type']} cl">
						<!--{if $dv['image']}-->
						<div class="share_card_img">
							<a href="$dv[image_link]" target="_blank" style="background: url($dv[image]) 50% 50%;background-size: cover;" ></a>
						</div>
						<!--{/if}-->
						<div class="share_card_imnfo">
							$dv[body_template]
						</div>
					</div>
				<!--{/if}-->
				<div class="doing_card_bottom">
					<div class="doing_card_bottom_left">
						<span class="doing_card_info-bottom-info"></span>
					</div>
					<div class="doing_card_bottom_right">
						<!--{if helper_access::check_module('doing')}-->
						<!--{if count($dolist) == 1}-->
						<!-- 单条记录页面：弹出评论框 -->
						<a href="home.php?mod=spacecp&ac=doing&op=docomment&handlekey=msg_0&doid=$doid&docid=0&key=$key" class="recommend{if $_G['uid']} dialog{/if}" data-doid="$doid">
							<i class="dm-chat-s"></i>
							<span class="bottom_num"><!--{if $dv['replynum']}-->$dv['replynum']<!--{else}-->{lang reply}<!--{/if}--></span>
						</a>
						<!--{else}-->
						<!-- 列表页面：跳转详情页 -->
						<a href="home.php?mod=space&do=doing&doid=$doid" class="recommend doing_comment_btn" data-doid="$doid">
							<i class="dm-chat-s"></i>
							<span class="bottom_num"><!--{if $dv['replynum']}-->$dv['replynum']<!--{else}-->{lang reply}<!--{/if}--></span>
						</a>
						<!--{/if}-->
						<!--{/if}-->
						<a href="javascript:;" class="recommend doing_recommend_btn" data-doid="$doid" data-status="<!--{if $dv['recommendstatus']}-->1<!--{else}-->0<!--{/if}-->">
							<i class="<!--{if $dv['recommendstatus']}-->fico-thumbup fc-i<!--{else}-->fico-thumbup fc-s<!--{/if}-->"></i>
							<span class="bottom_num recommend_count"><!--{if $dv['recomends']}-->$dv['recomends']<!--{else}-->{$_G['setting']['recommendthread']['addtext']}<!--{/if}--></span>
						</a>
						<a href="home.php?mod=spacecp&ac=doing&type=doing&id={$doid}" class="doing_share_btn {if $_G['uid']} dialog{/if}" data-doid="$doid" title="{lang share}">
							<i class="fico-launch fc-s"></i>
							<span class="bottom_num"><!--{if $dv['sharetimes']}-->$dv['sharetimes']<!--{else}-->{lang share}<!--{/if}--></span>
						</a>
						<!--{if $dv['uid']==$_G['uid'] || checkperm('managedoing')}-->
						<a href="home.php?mod=spacecp&ac=doing&op=delete&doid=$doid&docid=$dv['id']&handlekey=doinghk_{$doid}_$dv['id']" id="{$key}_doing_delete_{$doid}_{$dv['id']}" class="recommend{if $_G['uid']} dialog{/if}">
							<i class="dm-delete"></i>
							<span class="bottom_num">{lang delete}</span>
						</a>
						<!--{/if}-->
					</div>
				</div>
			</div>
			<div id="{$key}dl{$doid}" class="doing_card_comment">
				<!--{eval $list = $clist[$doid];}-->
				<div class="doing_card_quote" id="{$key}_$doid" {if empty($list) || !$showdoinglist[$doid]} style="display:none;" {/if}>
				<span id="{$key}_form_{$doid}_0"></span>
				<!--{template home/space_doing_li}-->
			</div>
		</div>
	</div>
	<!--{/loop}-->

	<!--{if $multi}-->
	<div class="pgs cl mtm">$multi</div>
	<!--{/if}-->

	<!--{else}-->
	<div class="threadlist_box mt10 cl">
		<h4>{lang doing_no_replay}</h4>
	</div>
	<!--{/if}-->
</div>
</div>
<script type="html" id="commentItemTemplate">
	<!-- 完整的评论项模板 -->
	<div id="comment_{:id:}_li" class="{:comment_class:}" data-root-id="{:root_id:}">
		<!--{:avatar_html:}-->
		<div class="comment-content">
			<div class="comment-header">
				<a href="home.php?mod=space&uid={:uid:}" class="comment-author">{:username:}</a>
				<!--{:reply_to_user:}-->
			</div>
			<div class="comment-body">{:message:}</div>
			<div class="comment-footer">
				<span class="comment-time">{:dateline_formatted:}</span>
				<div class="comment-actions">
					<a href="home.php?mod=spacecp&ac=doing&op=docomment&handlekey=msg_{:doid:}&doid={:doid:}&docid={:docid:}&key={:key:}" class="comment-action comment-action-reply dialog" onclick="return false;">ตอบกลับ</a>
					<!--{:delete_btn:}-->
				</div>
			</div>
			<div id="{:key:}_form_{:doid:}_{:docid:}" class="comment-form-container"></div>
		</div>
	</div>
</script>

<script type="html" id="commentToggleTemplate">
	<!-- 评论展开/折叠按钮模板 -->
	<div class="comment-toggle" data-root-id="{:root_id:}">
		<a href="javascript:;" class="toggle-comment-btn" onclick="toggle_child_comments({:root_id:});">
			<span class="toggle-icon">+</span> ดูความคิดเห็นเพิ่มเติมอีก {:hide_count:} รายการ
		</a>
	</div>
</script>

<script type="html" id="commentListTemplate">
	<!-- 评论列表模板 -->
	<span id="{:key:}_form_{:doid:}_0"></span>
	<div class="doing-card-comments">
		{:comment_items:}
	</div>
	<!--{:load_more_html:}-->
	<div class="comment-triangle"></div>
</script>

<script type="html" id="noCommentsTemplate">
	<!-- 简化的无评论模板 -->
	<span id="{:key:}_form_{:doid:}_0"></span>
	<div class="no-comments">ยังไม่มีความคิดเห็น มาเป็นคนแรกที่เจิมเลย!</div>
	<div class="comment-triangle"></div>
</script>

<script type="html" id="toggleOpenTemplate">
	<!-- 评论展开按钮模板 -->
	<span class="toggle-icon">-</span> พับเก็บความคิดเห็น
</script>

<script type="html" id="toggleCloseTemplate">
	<!-- 评论折叠按钮模板 -->
	<span class="toggle-icon">+</span> ดูความคิดเห็นเพิ่มเติมอีก {:hide_count:} รายการ
</script>

<script type="html" id="loadMoreBtnTemplate">
	<!-- 加载更多按钮模板 -->
	<a href="javascript:;" onclick="loadMoreComments({:doid:}, '{:key:}', {:next_page:});">โหลดความคิดเห็นเพิ่มเติม......</a>
</script>

<script type="text/javascript">
// ตัวแปร Global สำหรับเก็บเลขหน้าปัจจุบัน เพื่อให้สอดคล้องกับเวอร์ชัน PC
var current_comment_pages = {};

// สร้าง HTML สำหรับรายการความคิดเห็นเดี่ยว - ใช้เทมเพลต <script type="html">
function generateCommentItemHTML(comment, doid, key) {
	// หากเป็นปุ่มขยาย/พับเก็บ ให้ใช้เทมเพลต toggle
	if (comment.is_toggle) {
		var template = document.getElementById('commentToggleTemplate').innerHTML;
		var data = {
			root_id: comment.root_id || 0,
			hide_count: comment.hide_count || 0
		};
		return template.replace(/{:root_id:}/g, data.root_id)
			.replace(/{:hide_count:}/g, data.hide_count);
	}
	
	// ความคิดเห็นทั่วไป ให้ใช้เทมเพลต commentItem
	var template = document.getElementById('commentItemTemplate').innerHTML;
	var commentClass = 'comment-item';
	if (comment.layer > 0) commentClass += ' comment-item-child';
	if (comment.is_hidden) commentClass += ' comment-item-hidden';
	
	// สร้าง HTML รูปโปรไฟล์
	var avatarHtml = '';
	if (comment.layer == 0) {
		avatarHtml = '<div class="comment-avatar"><a href="home.php?mod=space&uid=' + comment.uid + '"><img src="' + comment.avatar + '" alt="' + comment.username + '" class="avatar-small"></a></div>';
	}
	
	// สร้าง HTML ข้อมูลการตอบกลับ
	var replyToUserHtml = '';
	if (comment.reply_to_user) {
		replyToUserHtml = '<span class="comment-reply-to">ตอบกลับ <a href="home.php?mod=space&uid=' + comment.reply_uid + '">' + comment.reply_to_user + '</a></span>';
	}
	
	// สร้าง HTML ปุ่มลบ
	var deleteBtnHtml = '';
	if (comment.can_delete) {
		deleteBtnHtml = '<a href="home.php?mod=spacecp&ac=doing&op=delete&doid=' + comment.doid + '&docid=' + comment.id + '&handlekey=doinghk_' + comment.doid + '_' + comment.id + '" id="' + key + '_doing_delete_' + comment.doid + '_' + comment.id + '" class="comment-action comment-action-delete dialog">ลบ</a>';
	}
	
	// แทนที่ตัวแปรในเทมเพลต
	var html = template;
	html = html.replace(/{:id:}/g, comment.id);
	html = html.replace(/{:uid:}/g, comment.uid);
	html = html.replace(/{:username:}/g, comment.username);
	html = html.replace(/{:avatar:}/g, comment.avatar);
	html = html.replace(/{:message:}/g, comment.message);
	html = html.replace(/{:dateline_formatted:}/g, comment.dateline_formatted);
	html = html.replace(/{:doid:}/g, comment.doid);
	html = html.replace(/{:docid:}/g, comment.id);
	html = html.replace(/{:key:}/g, key);
	html = html.replace(/{:root_id:}/g, comment.root_id || 0);
	html = html.replace(/{:layer:}/g, comment.layer);
	html = html.replace(/{:comment_class:}/g, commentClass);
	html = html.replace(/{:avatar_html:}/g, avatarHtml);
	html = html.replace(/{:reply_to_user:}/g, replyToUserHtml);
	html = html.replace(/{:delete_btn:}/g, deleteBtnHtml);
	
	// ลบแท็กคอมเมนต์ที่ไม่มีเนื้อหาออกทั้งหมด
	html = html.replace(/<!--\s*-->\s*/g, '');
	
	return html;
}

// สร้าง HTML รายการความคิดเห็น - ใช้เทมเพลต <script type="html">
function generateCommentHTML(data, doid, key) {
	// หากไม่มีความคิดเห็น ให้ใช้เทมเพลต noComments
	if (!data.list || data.list.length === 0) {
		var template = document.getElementById('noCommentsTemplate').innerHTML;
		return template.replace(/{:key:}/g, key)
			.replace(/{:doid:}/g, doid);
	}
	
	// หากมีแสดงความคิดเห็น ให้ใช้เทมเพลต commentList
	var template = document.getElementById('commentListTemplate').innerHTML;
	
	// สร้าง HTML สำหรับรายการความคิดเห็นทั้งหมด
	var commentItemsHTML = '';
	for (var i = 0; i < data.list.length; i++) {
		commentItemsHTML += generateCommentItemHTML(data.list[i], doid, key);
	}
	
	// สร้าง HTML สำหรับปุ่มโหลดเพิ่มเติม
	var loadMoreHtml = '';
	var hasMore = data.total_pages > 1 && data.page < data.total_pages;
	if (hasMore) {
		var nextPage = data.page + 1;
		loadMoreHtml = '<div class="comment-load-more" data-doid="' + doid + '" data-key="' + key + '" data-next-page="' + nextPage + '">';
		loadMoreHtml += '<a href="javascript:;" onclick="loadMoreComments(' + doid + ', \'' + key + '\', ' + nextPage + ');">โหลดความคิดเห็นเพิ่มเติม...</a>';
		loadMoreHtml += '</div>';
	}
	
	// แทนที่ตัวแปรในเทมเพลต
	var html = template;
	html = html.replace(/{:key:}/g, key);
	html = html.replace(/{:doid:}/g, doid);
	html = html.replace(/{:comment_items:}/g, commentItemsHTML);
	html = html.replace(/{:load_more_html:}/g, loadMoreHtml);
	
	// ลบแท็กคอมเมนต์ที่ไม่มีเนื้อหาออกทั้งหมด
	html = html.replace(/<!--\s*-->/g, '');
	
	return html;
}

	function toggle_child_comments(root_id) {
	var toggleComment = document.querySelector('.comment-toggle[data-root-id="' + root_id + '"]');
	var toggleBtn = toggleComment.querySelector('.toggle-comment-btn');
	var toggleIcon = toggleBtn.querySelector('.toggle-icon');
	var hiddenComments = document.querySelectorAll('.comment-item-hidden[data-root-id="' + root_id + '"]');
	
	if (toggleIcon.textContent === '+') {
		// ขยายความคิดเห็น
		var openTemplate = document.getElementById('toggleOpenTemplate').innerHTML;
		toggleBtn.innerHTML = openTemplate;
		for (var i = 0; i < hiddenComments.length; i++) {
			hiddenComments[i].style.display = '';
			hiddenComments[i].classList.remove('comment-item-hidden');
		}
	} else {
		// พับเก็บความคิดเห็น
		var closeTemplate = document.getElementById('toggleCloseTemplate').innerHTML;
		var data = {
			hide_count: hiddenComments.length
		};
		var html = closeTemplate.replace(/{:hide_count:}/g, data.hide_count);
		toggleBtn.innerHTML = html;
		for (var i = 0; i < hiddenComments.length; i++) {
			hiddenComments[i].style.display = 'none';
			hiddenComments[i].classList.add('comment-item-hidden');
		}
	}
}

	function docomment_form(doid, docid, key) {
		var formId = key + '_form_' + doid + '_' + docid;
		var formContainer = document.getElementById(formId);
		
		if (formContainer && formContainer.innerHTML === '') {
			// หากยังไม่มีแบบฟอร์ม ให้โหลดแบบฟอร์ม
			var url = 'home.php?mod=spacecp&ac=doing&op=docomment&handlekey=msg_' + doid + '&doid=' + doid + '&docid=' + docid + '&key=' + key;
			var xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function() {
				if (xhr.readyState === 4 && xhr.status === 200) {
					formContainer.innerHTML = xhr.responseText;
				}
			};
			xhr.open('GET', url, true);
			xhr.send();
		} else if (formContainer) {
			// หากมีแบบฟอร์มอยู่แล้ว ให้สลับการแสดงผล/ซ่อน
			if (formContainer.style.display === 'none') {
				formContainer.style.display = '';
			} else {
				formContainer.style.display = 'none';
			}
		}
	}

	// ฟังก์ชันโหลดความคิดเห็นเฉพาะสำหรับมือถือ ร้องขอข้อมูล JSON และนำมาแทรก - กำหนดไว้ใน Global Scope
	function docomment_get(doid, key, page, append) {
		var showid = key + '_' + doid;
		var opid = key + '_do_a_op_' + doid;
		var commentContainerId = key + 'dl' + doid;
		
		// สร้าง URL คำร้องขอ Discuz! จะตรวจหาอุปกรณ์เคลื่อนที่โดยอัตโนมัติและส่งข้อมูลกลับเป็น JSON
		var url = 'home.php?mod=spacecp&ac=doing&op=getcomment&handlekey=msg_' + doid + '&doid=' + doid + '&key=' + key;
		
		// จัดการตรรกะเลขหน้า
		var current_page = page || current_comment_pages[doid] || 1;
		current_comment_pages[doid] = current_page;
		
		// ตรวจสอบว่า URL ปัจจุบันมีพารามิเตอร์ doid (หน้าสเตตัสเดี่ยว) หรือบันทึกเลขหน้าไว้แล้วหรือไม่
		var currentUrl = window.location.href;
		if (currentUrl.indexOf('doid=') > -1 || typeof current_comment_pages[doid] !== 'undefined') {
			// หน้าสเตตัสเดี่ยวหรือบันทึกเลขหน้าแล้ว ให้เพิ่มพารามิเตอร์ page_c
			url += '&page_c=' + current_page;
		}
		
		// ร้องขอรายการความคิดเห็นแบบ Asynchronous (รูปแบบ JSON)
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function() {
			if (xhr.readyState === 4 && xhr.status === 200) {
				try {
					// วิเคราะห์ข้อมูล JSON
					var response = JSON.parse(xhr.responseText);
					
					// แทรกผลลัพธ์ลงในคอนเทนเนอร์ {$key}dl{$doid}
					var commentContainer = document.getElementById(commentContainerId);
					if (commentContainer) {
						if (append && response.list.length > 0) {
							// เพิ่มความคิดเห็นต่อท้ายรายการที่มีอยู่
							var commentsHtml = '';
							for (var i = 0; i < response.list.length; i++) {
								commentsHtml += generateCommentItemHTML(response.list[i], doid, key);
							}
							
							// ค้นหาคอนเทนเนอร์รายการความคิดเห็น
							var commentList = commentContainer.querySelector('.doing-card-comments');
							if (commentList) {
								// ค้นหาปุ่มโหลดเพิ่มเติม และแทรกไว้ข้างหน้าปุ่มนั้น
								var loadMoreBtn = commentContainer.querySelector('.comment-load-more');
								if (loadMoreBtn) {
									loadMoreBtn.insertAdjacentHTML('beforebegin', commentsHtml);
								} else {
									// หากไม่มีปุ่มโหลดเพิ่มเติม ให้เพิ่มไว้ท้ายรายการโดยตรง
									commentList.insertAdjacentHTML('beforeend', commentsHtml);
								}
								
								// อัปเดตปุ่มโหลดเพิ่มเติม
			if (loadMoreBtn) {
				if (response.page < response.total_pages) {
					var nextPage = response.page + 1;
					loadMoreBtn.setAttribute('data-next-page', nextPage);
					var template = document.getElementById('loadMoreBtnTemplate').innerHTML;
					var data = {
						doid: doid,
						key: key,
						next_page: nextPage
					};
					var html = template.replace(/{:doid:}/g, data.doid)
						.replace(/{:key:}/g, data.key)
						.replace(/{:next_page:}/g, data.next_page);
					loadMoreBtn.innerHTML = html;
				} else {
					// ไม่มีเนื้อหาความคิดเห็นเพิ่มเติม ให้ลบปุ่มโหลดเพิ่มเติมออก
					loadMoreBtn.remove();
				}
			}
							}
						} else {
							// สร้าง HTML รายการความคิดเห็นและแทนที่เนื้อหาเดิม
							var html = generateCommentHTML(response, doid, key);
							commentContainer.innerHTML = html;
							// แสดงคอนเทนเนอร์ความคิดเห็น
							commentContainer.style.display = '';
						}
					}
				} catch (error) {
					console.error('JSON parse error:', error);
				}
			}
		};
		xhr.open('GET', url, true);
		xhr.send();
	}

	// ฟังก์ชันโหลดความคิดเห็นเพิ่มเติม
	function loadMoreComments(doid, key, page) {
		docomment_get(doid, key, page, true);
	}

	// ใช้สไตล์ดั้งเดิมของ Discuz แทบไม่มีการใช้ CSS ที่กำหนดเอง
var style = document.createElement('style');
style.textContent = `		/* คอนเทนเนอร์ความคิดเห็น - ใช้สไตล์ดั้งเดิม .do_comment ของ Discuz */
		.doing-card-comments {
			padding: 10px;
		}

		/* รายการความคิดเห็น - ใช้สไตล์ดั้งเดิม .imglist li ของ Discuz */
		.comment-item {
			display: flex;
			margin-bottom: 10px;
			padding: 10px 0;
			border-bottom: 1px solid var(--dz-BOR-ed);
		}

		/* ความคิดเห็นย่อย */
		.comment-item-child {
			margin-left: 40px;
			margin-top: 8px;
		}

		/* ความคิดเห็นที่ถูกซ่อน */
		.comment-item-hidden {
			display: none;
		}

		/* รูปโปรไฟล์ในความคิดเห็น - ใช้สไตล์ดั้งเดิม .imglist .mimg ของ Discuz */
		.comment-avatar {
			margin-right: 10px;
		}

		/* รูปโปรไฟล์ขนาดเล็ก - ใช้สไตล์รูปโปรไฟล์ดั้งเดิมของ Discuz */
		.comment-avatar img {
			width: 32px;
			height: 32px;
			border-radius: 50%;
		}

		/* เนื้อหาความคิดเห็น */
		.comment-content {
			flex: 1;
		}

		/* ส่วนหัวความคิดเห็น */
		.comment-header {
			margin-bottom: 5px;
		}

		/* ผู้เขียนความคิดเห็น - ใช้สไตล์ดั้งเดิมของ Discuz */
		.comment-author {
			font-weight: 700;
			color: var(--dz-FC-color);
			margin-right: 5px;
		}

		/* ข้อมูลการตอบกลับ */
		.comment-reply-to {
			color: var(--dz-FC-999);
			font-size: 12px;
		}

		/* การดำเนินการกับความคิดเห็น */
		.comment-actions {
			display: flex;
			gap: 10px;
			font-size: 12px;
		}

		/* ปุ่มดำเนินการกับความคิดเห็น - ใช้สไตล์ดั้งเดิมของ Discuz */
		.comment-action {
			color: var(--dz-FC-999);
			text-decoration: none;
		}

		/* ปุ่มตอบกลับ - ใช้สไตล์ดั้งเดิมของ Discuz */
		.comment-action-reply {
			color: var(--dz-FC-color);
		}

		/* ปุ่มลบ - ใช้สไตล์ดั้งเดิมของ Discuz */
		.comment-action-delete {
			color: var(--dz-FC-a);
		}

		/* ปุ่มขยาย/พับเก็บ */
		.comment-toggle {
			text-align: center;
			margin: 10px 0;
		}

		/* ปุ่มโหลดเพิ่มเติม - ใช้สไตล์ปุ่มดั้งเดิมของ Discuz */
		.comment-load-more {
			text-align: center;
			margin-top: 10px;
		}

		/* แจ้งเตือนเมื่อไม่มีความคิดเห็น - ใช้สไตล์สถานะว่างดั้งเดิมของ Discuz */
		.no-comments {
			text-align: center;
			color: var(--dz-FC-999);
			padding: 20px;
			background-color: var(--dz-BG-5);
			border-radius: 4px;
		}
	`;
document.head.appendChild(style);
	document.addEventListener('DOMContentLoaded', function() {
		// ฟีเจอร์กดถูกใจ
		var recommendBtns = document.querySelectorAll('.doing_recommend_btn');
		for (let i = 0; i < recommendBtns.length; i++) {
			recommendBtns[i].addEventListener('click', function() {
				if (this.classList.contains('disabled')) {
					return;
				}

				var doid = this.getAttribute('data-doid');
				var btn = this;
				var countElem = this.querySelector('.recommend_count');
				var iconElem = this.querySelector('i');

				btn.classList.add('disabled');
				fetch('home.php?mod=spacecp&ac=doing&op=recommend&doid=' + doid)
					.then(function(response) {
						return response.json();
					})
					.then(function(data) {
						btn.classList.remove('disabled');

						if (data && data.message === 'doing_recommend_success') {
							btn.setAttribute('data-status', data.status);
							countElem.innerHTML = data.count;

							if (parseInt(data.status) === 1) {
								iconElem.className = 'fico-thumbup fc-i';
							} else {
								iconElem.className = 'fico-thumbup fc-s';
							}
						} else {
							console.error('error:', data);
						}
					})
					.catch(function(error) {
								btn.classList.remove('disabled');
								console.error('error:', error);
						});
			});
		}

		// หน้าสเตตัสเดี่ยว โหลดรายการความคิดเห็นแบบ Asynchronous
		// ใช้ตัวแปรเทมเพลต PHP เพื่อตรวจสอบว่าเป็นหน้าสเตตัสเดี่ยวหรือไม่
		<!--{if count($dolist) == 1}-->
		// หากเป็นหน้าสเตตัสเดี่ยว ให้ดำเนินการโหลดรายการความคิดเห็นแบบ Asynchronous
		var doid = '<!--{$doid}-->';
		var key = '<!--{$key}-->';

		// เรียกใช้ฟังก์ชัน docomment_get ซึ่งเหมือนกับเวอร์ชัน PC
		docomment_get(doid, key, 1);

		// ฟีเจอร์โหลดความคิดเห็นเพิ่มเติมโดยอัตโนมัติ
		var autoLoadEnabled = true;
		var isLoading = false;

		// ตรวจจับเหตุการณ์การเลื่อน เพื่อโหลดข้อมูลอัตโนมัติเมื่อเลื่อนลงถึงด้านล่าง
		window.addEventListener('scroll', function() {
			if (!autoLoadEnabled || isLoading) {
				return;
			}

			// คำนวณตำแหน่งการเลื่อน
			var scrollTop = window.scrollY || document.documentElement.scrollTop;
			var scrollHeight = document.body.scrollHeight || document.documentElement.scrollHeight;
			var clientHeight = window.innerHeight || document.documentElement.clientHeight;
			var distanceToBottom = scrollHeight - (scrollTop + clientHeight);

			// ตรวจสอบว่าเลื่อนถึงด้านล่างหรือไม่ (ภายในระยะ 100px จากขอบล่าง)
			if (distanceToBottom <= 100) {
				// ดึงปุ่มโหลดเพิ่มเติมทั้งหมด
				var loadMoreBtns = document.querySelectorAll('.comment-load-more');
				if (loadMoreBtns.length > 0) {
					isLoading = true;
					// กระตุ้นเหตุการณ์การคลิกที่ปุ่มโหลดเพิ่มเติมปุ่มสุดท้าย (เพื่อรองรับกรณีที่มีหลายสเตตัส)
					var lastLoadMoreBtn = loadMoreBtns[loadMoreBtns.length - 1];
					// เรียกใช้ฟังก์ชัน loadMoreComments โดยตรง แทนที่จะพึ่งพาการคลิกปุ่ม
					var doid = lastLoadMoreBtn.getAttribute('data-doid');
					var key = lastLoadMoreBtn.getAttribute('data-key');
					var nextPage = lastLoadMoreBtn.getAttribute('data-next-page');
					if (doid && key && nextPage) {
						loadMoreComments(doid, key, nextPage);
					}
					// ตั้งค่าสถานะการโหลด เพื่อป้องกันการทำงานซ้ำซ้อน
					setTimeout(function() {
						isLoading = false;
					}, 1500); // กลับสู่สถานะพร้อมโหลดหลังจากผ่านไป 1.5 วินาที เพื่อให้เวลาในการประมวลผลคำร้องขออย่างเพียงพอ
				} else {
					// ไม่มีเนื้อหาเพิ่มเติม ให้ปิดการใช้งานการโหลดอัตโนมัติ
					autoLoadEnabled = false;
				}
			}
		});
		<!--{/if}-->
	});
</script>
<!--{template common/footer}-->