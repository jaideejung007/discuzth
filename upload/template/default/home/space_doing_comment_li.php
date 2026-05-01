<?php echo ''; ?>
<!--{template common/header_ajax}-->
<div class="doing_form" id="{$_GET['key']}_form_{$doid}_0"></div>
<!--{if $comment_multi[$doid]}-->
<script reload="1">docomment_form({$doid}, 0, '{$_GET['key']}');</script>
<!--{/if}-->

<!--{if $list}-->

<!--{loop $list $value}-->
	<!--{if $value['is_toggle']}-->
	<!-- 展开/折叠按钮 -->
	<div id="comment_{$value['id']}_li" class="doing_comment_list cl{$value['class']}" style="{$value['style']}">
		<div class="ptm" style="margin-left: 10px;">
			<div>
				<p>
					<a href="javascript:;" class="toggle-comment-btn" onclick="toggle_child_comments({$value['root_id']});" data-root-id="{$value['root_id']}">
						<span class="toggle-icon">+</span> {lang viewmore}({$value['hide_count']})
					</a>
				</p>
			</div>
		</div>
	</div>
	<!--{else}-->
	<!-- 普通评论 -->
	 <div id="comment_{$value['id']}_li" class="doing_list cl cl{$value['class']} <!--{if $value['is_hidden']}--> hidden-comment <!--{/if}-->" style="{$value['style']}" <!--{if $value['root_id']}--> data-root-id="{$value['root_id']}" <!--{/if}--> >
		<div class="doing_list_item">
			<div class="doing_avatar">
				<a href="home.php?mod=space&uid=$value[uid]" c="1"><!--{avatar($value['uid'], 'small')}--></a>
			</div>
			<div class="item_right">
				<div class="item_author_box">
					<!--{if $value['username']}-->
					<a href="home.php?mod=space&uid={$value['uid']}" c="1" class="author_name">{$value['username']}</a>
					<!--{/if}-->
				</div>
				<div class="item_info_date">
					<span ><!--{date($value['dateline'], 'Y-n-j H:i')}--></span>
					<span >{lang comefrom} {$value['iplocation']}</span>
				</div>
				<div class="item_content">
					<div class="item_content_box mbm">
						<!--{if $value['reply_to_user']}-->
						<span class="">{lang reply} <a href="home.php?mod=space&uid=$value['reply_uid']" c="1" class="author_name">$value['reply_to_user']</a>: </span>
						<!--{/if}-->
						$value['message']
					</div>
				</div>
				<div class="item_info">
					<div class="item_info_interactions">
						<div class="interactions_left">
							<!--{if $_G['uid'] && helper_access::check_module('doing')}-->
							<a href="javascript:;" onclick="docomment_form($value['doid'], $value['id'], '$_GET['key']');" class="icon_box"><i class="fico-comment"></i><span>{lang reply}</span></a>
							<!--{/if}-->
							<!--{if $value['uid'] == $_G['uid'] || $dv['uid'] == $_G['uid'] || checkperm('managedoing')}-->
							<a href="home.php?mod=spacecp&ac=doing&op=delete&doid=$value['doid']&docid=$value['id']&handlekey=doinghk_{$value['doid']}_$value['id']" id="{$_GET['key']}_doing_delete_{$value['doid']}_{$value['id']}" onclick="showWindow(this.id, this.href, 'get', 0);" class="icon_box"><i class="fico-delete"></i><span>{lang delete}</span></a>
							<!--{/if}-->
						</div>
					</div>
				</div>
				<div class="doingreply_form" id="{$_GET['key']}_form_{$value['doid']}_{$value['id']}"></div>
			</div>
		</div>
	</div>

	<!--{/if}-->
<!--{/loop}-->
<!--{if $has_more}-->
<div class="more_comments xg1">
	<a href="home.php?mod=space&do=doing&doid={$doid}">{lang click_view_all}</a>
</div>
<!--{/if}-->

<style>
.toggle-comment-btn {
	color: #6c757d;
	cursor: pointer;
	font-size: 12px;
	font-weight: normal;
	padding: 4px 8px;
	background-color: #f8f9fa;
	border: 1px solid #dee2e6;
	border-radius: 3px;
	display: inline-block;
	transition: all 0.2s ease;
	margin: 2px 0;
}
.toggle-comment-btn:hover {
	color: #2196F3;
	text-decoration: none;
	background-color: #e9ecef;
	border-color: #adb5bd;
	transform: translateY(-1px);
}
.toggle-comment-btn .toggle-icon {
	font-weight: bold;
	margin-right: 4px;
	font-size: 10px;
	display: inline-block;
	width: 12px;
	text-align: center;
}
.hidden-comment {
	display: none;
}
/* 优化加载更多评论链接样式 */
.more_comments {
	text-align: center;
	padding: 10px 0;
	font-size: 12px;
}
.more_comments a {
	color: #2196F3;
	font-weight: bold;
	padding: 6px 12px;
	display: inline-block;
	border-radius: 3px;
	transition: all 0.2s ease;
}
</style>
<script>
// 子评论展开/折叠功能
function toggle_child_comments(root_id) {
	// 获取所有带有指定root_id的评论
	var all_comments = document.querySelectorAll('[data-root-id="' + root_id + '"]');
	// 过滤出隐藏的评论
	var hidden_comments = [];
	for (var i = 0; i < all_comments.length; i++) {
		var comment = all_comments[i];
		if (comment.classList.contains('hidden-comment')) {
			hidden_comments.push(comment);
		}
	}
	
	// 获取切换按钮
	var toggle_btns = document.querySelectorAll('.toggle-comment-btn');
	var toggle_btn = null;
	for (var i = 0; i < toggle_btns.length; i++) {
		if (toggle_btns[i].getAttribute('data-root-id') == root_id) {
			toggle_btn = toggle_btns[i];
			break;
		}
	}
	
	if (!toggle_btn) {
		console.error('Toggle button not found for root_id:', root_id);
		return;
	}
	
	var toggle_icon = toggle_btn.querySelector('.toggle-icon');
	
	if (toggle_icon.textContent === '+') {
		// 展开评论
		toggle_icon.textContent = '-';
		// 更新按钮文本
		var btn_text = toggle_btn.textContent;
		btn_text = btn_text.replace('查看更多', '收起');
		toggle_btn.innerHTML = toggle_btn.innerHTML.replace('查看更多', '收起');
		// 显示所有隐藏的评论
		for (var i = 0; i < hidden_comments.length; i++) {
			hidden_comments[i].style.display = 'block';
		}
	} else {
		// 折叠评论
		toggle_icon.textContent = '+';
		// 更新按钮文本
		var btn_text = toggle_btn.textContent;
		btn_text = btn_text.replace('收起', '查看更多');
		toggle_btn.innerHTML = toggle_btn.innerHTML.replace('收起', '查看更多');
		// 隐藏所有评论
		for (var i = 0; i < hidden_comments.length; i++) {
			hidden_comments[i].style.display = 'none';
		}
	}
}

// 评论分页加载功能
function docomment_get_page(doid, key, page) {
	var showid = key + '_' + doid;
	var url = 'home.php?mod=spacecp&ac=doing&op=getcomment&handlekey=msg_'+doid+'&doid='+doid+'&key='+key+'&page_c='+page;
	if (typeof current_comment_pages === 'undefined') {
		current_comment_pages = {};
	}
	current_comment_pages[doid] = page;
	ajaxget(url, showid);
}
</script>
<!--{else}-->
<div class="doing_empty">{lang doing_no_replay}</div>
<!--{/if}-->
<!--{if $comment_multi[$doid] && !$is_ajax_list}-->
<div class="pgs cl mtm">{$comment_multi[$doid]}</div>
<!--{/if}-->

<!--{template common/footer_ajax}-->