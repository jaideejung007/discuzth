<?php exit('Access Denied');?>
<!--{template common/header}-->
<!--{eval $adveditor = $isfirstpost && $special || $special == 2 && ($_GET['action'] == 'newthread' || $_GET['action'] == 'reply' && !empty($_GET['addtrade']) || $_GET['action'] == 'edit' && $thread['special'] == 2);}-->
<form method="post" id="postform" 
			{if $_GET['action'] == 'newthread'}action="forum.php?mod=post&action={if $special != 2}newthread{else}newtrade{/if}&fid=$_G['fid']&extra=$extra&topicsubmit=yes&mobile=2"
			{elseif $_GET['action'] == 'reply'}action="forum.php?mod=post&action=reply&fid=$_G['fid']&tid=$_G['tid']&extra=$extra&replysubmit=yes&mobile=2"
			{elseif $_GET['action'] == 'edit'}action="forum.php?mod=post&action=edit&extra=$extra&editsubmit=yes&mobile=2" $enctype
			{/if}>
	<input type="hidden" name="formhash" id="formhash" value="{FORMHASH}" />
	<input type="hidden" name="posttime" id="posttime" value="{TIMESTAMP}" />
<!--{if !empty($_GET['modthreadkey'])}--><input type="hidden" name="modthreadkey" id="modthreadkey" value="$_GET['modthreadkey']" /><!--{/if}-->
<!--{if $_GET['action'] == 'reply'}-->
	<input type="hidden" name="noticeauthor" value="$noticeauthor" />
	<input type="hidden" name="noticetrimstr" value="$noticetrimstr" />
	<input type="hidden" name="noticeauthormsg" value="$noticeauthormsg" />
	<!--{if $reppid}-->
		<input type="hidden" name="reppid" value="$reppid" />
	<!--{/if}-->
	<!--{if $_GET['reppost']}-->
		<input type="hidden" name="reppost" value="$_GET['reppost']" />
	<!--{elseif $_GET['repquote']}-->
		<input type="hidden" name="reppost" value="$_GET['repquote']" />
	<!--{/if}-->
<!--{/if}-->
<!--{if $_GET['action'] == 'edit'}-->
	<input type="hidden" name="fid" id="fid" value="$_G['fid']" />
	<input type="hidden" name="tid" value="$_G['tid']" />
	<input type="hidden" name="pid" value="$pid" />
	<input type="hidden" name="page" value="$_GET['page']" />
<!--{/if}-->
<!--{if $special}-->
	<input type="hidden" name="special" value="$special" />
<!--{/if}-->
<!--{if $specialextra}-->
	<input type="hidden" name="specialextra" value="$specialextra" />
<!--{/if}-->
<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2><!--{if $_GET['action'] == 'edit'}-->{lang edit}<!--{else}-->{lang send_threads}<!--{/if}--></h2>
	<div class="my"></div>
</div>
<!--{if $_GET['action'] == 'newthread' && ($_G['group']['allowpostpoll'] || $_G['group']['allowpostreward'] || $_G['group']['allowpostdebate'] || $_G['group']['allowpostactivity'] || $_G['group']['allowposttrade'] || $_G['setting']['threadplugins'] || $_G['forum']['threadsorts']['types'])}-->
<div class="dhnavs_box">
	<div id="dhnavs">
		<div id="dhnavs_li">
			<ul class="swiper-wrapper">
				<!--{if empty($_G['forum']['threadsorts']['required']) && !$_G['forum']['allowspecialonly']}-->
				<li class="swiper-slide {if $postspecialcheck[0]}mon{/if}"><a href="forum.php?mod=post&action=newthread&fid=$_G['fid']&cedit=yes<!--{if !empty($_G['tid'])}-->&tid=$_G['tid']<!--{/if}--><!--{if !empty($modelid)}-->&modelid=$modelid<!--{/if}-->&extra=$extra">{lang post_newthread}</a></li>
				<!--{/if}-->
				<!--{loop $_G['forum']['threadsorts']['types'] $tsortid $name}-->
					<li class="swiper-slide {if $sortid == $tsortid}mon{/if}"><a href="forum.php?mod=post&action=newthread&sortid=$tsortid&fid=$_G['fid']&cedit=yes<!--{if !empty($_G['tid'])}-->&tid=$_G['tid']<!--{/if}--><!--{if !empty($modelid)}-->&modelid=$modelid<!--{/if}-->&extra=$extra"><!--{echo strip_tags($name);}--></a></li>
				<!--{/loop}-->
				<!--{if $_G['group']['allowpostpoll']}--><li class="swiper-slide {if $_GET['special'] == 1}mon{/if}"><a href="forum.php?mod=post&action=newthread&special=1&fid=$_G['fid']&cedit=yes<!--{if !empty($_G['tid'])}-->&tid=$_G['tid']<!--{/if}--><!--{if !empty($modelid)}-->&modelid=$modelid<!--{/if}-->&extra=$extra">{lang post_newthreadpoll}</a></li><!--{/if}-->
				<!--{if $_G['group']['allowpostreward']}--><li class="swiper-slide {if $_GET['special'] == 3}mon{/if}"><a href="forum.php?mod=post&action=newthread&special=3&fid=$_G['fid']&cedit=yes<!--{if !empty($_G['tid'])}-->&tid=$_G['tid']<!--{/if}--><!--{if !empty($modelid)}-->&modelid=$modelid<!--{/if}-->&extra=$extra">{lang post_newthreadreward}</a></li><!--{/if}-->
				<!--{if $_G['group']['allowpostdebate']}--><li class="swiper-slide {if $_GET['special'] == 5}mon{/if}"><a href="forum.php?mod=post&action=newthread&special=5&fid=$_G['fid']&cedit=yes<!--{if !empty($_G['tid'])}-->&tid=$_G['tid']<!--{/if}--><!--{if !empty($modelid)}-->&modelid=$modelid<!--{/if}-->&extra=$extra">{lang post_newthreaddebate}</a></li><!--{/if}-->
				<!--{if $_G['group']['allowpostactivity']}--><li class="swiper-slide {if $_GET['special'] == 4}mon{/if}"><a href="forum.php?mod=post&action=newthread&special=4&fid=$_G['fid']&cedit=yes<!--{if !empty($_G['tid'])}-->&tid=$_G['tid']<!--{/if}--><!--{if !empty($modelid)}-->&modelid=$modelid<!--{/if}-->&extra=$extra">{lang post_newthreadactivity}</a></li><!--{/if}-->
				<!--{if $_G['group']['allowposttrade']}--><li class="swiper-slide {if $_GET['special'] == 2}mon{/if}"><a href="forum.php?mod=post&action=newthread&special=2&fid=$_G['fid']&cedit=yes<!--{if !empty($_G['tid'])}-->&tid=$_G['tid']<!--{/if}--><!--{if !empty($modelid)}-->&modelid=$modelid<!--{/if}-->&extra=$extra">{lang post_newthreadtrade}</a></li><!--{/if}-->
				<!--{if $_G['setting']['threadplugins']}-->
					<!--{loop $_G['forum']['threadplugin'] $tpid}-->
						<!--{if array_key_exists($tpid, $_G['setting']['threadplugins']) && is_array($_G['group']['allowthreadplugin']) && in_array($tpid, $_G['group']['allowthreadplugin'])}-->
							<li class="swiper-slide {if $specialextra==$tpid}mon{/if}"><a href="forum.php?mod=post&action=newthread&specialextra=$tpid&fid=$_G['fid']&cedit=yes<!--{if !empty($_G['tid'])}-->&tid=$_G['tid']<!--{/if}--><!--{if !empty($modelid)}-->&modelid=$modelid<!--{/if}-->&extra=$extra">{$_G['setting']['threadplugins'][$tpid]['name']}</a></li>
						<!--{/if}-->
					<!--{/loop}-->
				<!--{/if}-->
			</ul>
		</div>
	</div>
</div>
<script>
	if($("#dhnavs_li .mon").length > 0) {
		var discuz_nav = $("#dhnavs_li .mon").offset().left + $("#dhnavs_li .mon").width() >= $(window).width() ? $("#dhnavs_li .mon").index() : 0;
	}else{
		var discuz_nav = 0;
	}	
	new Swiper('#dhnavs_li', {
		freeMode : true,
		slidesPerView : 'auto',
		initialSlide : discuz_nav,
		onTouchMove: function(swiper){
			Discuz_Touch_on = 0;
		},
		onTouchEnd: function(swiper){
			Discuz_Touch_on = 1;
		},
	});
</script>
<!--{/if}-->
<div class="post_from post_box">
	<!--{hook/post_top_mobile}-->
	<ul class="cl">
		<!--{if $_GET['action'] == 'reply' && !empty($_GET['addtrade']) || $_GET['action'] == 'edit' && $thread['special'] == 2 && !$postinfo['first']}-->
		<input name="subject" type="hidden" value="" />
		<!--{elseif $_GET['action'] != 'reply'}-->
		<li class="mli"><input type="text" class="px pl5" id="needsubject" autocomplete="off" value="$postinfo['subject']" name="subject" placeholder="{lang posts}{lang thread_subject}"></li>
		<!--{else}-->
		<li class="mtext">
			RE: $thread['subject']
			<!--{if $quotemessage}-->$quotemessage<!--{/if}-->
		</li>
		<!--{/if}-->
		<!--{if $isfirstpost && !empty($_G['forum']['threadtypes']['types'])}-->
		<li class="mli">
			<select id="typeid" name="typeid" class="sort_sel pl5">	
				<i class="dm-c-down sort_jt"></i>
				<option value="0" selected="selected">{lang threadtype}</option>
				<!--{loop $_G['forum']['threadtypes']['types'] $typeid $name}-->
				<!--{if empty($_G['forum']['threadtypes']['moderators'][$typeid]) || $_G['forum']['ismoderator']}-->
				<option value="$typeid"{if $thread['typeid'] == $typeid || $_GET['typeid'] == $typeid} selected="selected"{/if}><!--{echo strip_tags($name);}--></option>
				<!--{/if}-->
				<!--{/loop}-->
			</select>
		</li>
		<!--{/if}-->
	</ul>
	<!--{template forum/post_editor_extra}-->

	<ul class="cl">
		<!--{if (!empty($_G['setting']['editormodetype']) && $_GET[action] != 'edit') || ($_GET[action] == 'edit' && $isJsonContent)}-->
			<!--{subtemplate forum/jsoneditor_content}-->
		<!--{else}-->
			<!--{subtemplate forum/post_body}-->
		<!--{/if}-->
		
		<!--{if $_GET['action'] == 'edit' && $isorigauthor && ($isfirstpost && $thread['replies'] < 1 || !$isfirstpost) && !$rushreply && $_G['setting']['editperdel']}-->
		<label>
		<li class="flex-box mli">
			<div class="flex-3 xg1"><span class="z">{lang post_delpost}</span></div>
			<div class="flex"><span class="y"><input type="checkbox" name="delete" id="delete" class="pc" value="1"></span></div>
		</li>
		</label>
		<!--{/if}-->
		<!--{hook/post_middle_mobile}-->
		<!--{subtemplate forum/post_editor_attribute}-->
	</ul>
	<!--{if $_GET['action'] != 'edit' && ($secqaacheck || $seccodecheck)}-->
	<ul class="cl">
		<!--{subtemplate common/seccheck}-->
	</ul>
	<!--{/if}-->
	<!--{hook/post_bottom_mobile}-->
</div>
<div class="post_btn">
	<button id="postsubmit" class="btn_pn <!--{if $_GET['action'] == 'edit'}-->btn_pn_blue" disable="false"<!--{else}-->btn_pn_grey" disable="true"<!--{/if}-->>
	<!--{if $_GET['action'] == 'newthread'}-->
		<!--{if $special == 0}-->{lang send_thread}
		<!--{elseif $special == 1}-->{lang post_newthreadpoll}
		<!--{elseif $special == 2}-->{lang post_newthreadtrade}
		<!--{elseif $special == 3}-->{lang post_newthreadreward}
		<!--{elseif $special == 4}-->{lang post_newthreadactivity}
		<!--{elseif $special == 5}-->{lang post_newthreaddebate}
		<!--{elseif $special == 127}-->
			<!--{if $buttontext}-->$buttontext<!--{else}-->{lang post_newthread}<!--{/if}-->
		<!--{/if}-->
	<!--{elseif $_GET['action'] == 'reply' && !empty($_GET['addtrade'])}-->{lang trade_add_post}
	<!--{elseif $_GET['action'] == 'reply'}-->{lang join_thread}
	<!--{elseif $_GET['action'] == 'edit' && $isfirstpost && $thread['displayorder'] == -4}-->{lang post_newthread}
	<!--{elseif $_GET['action'] == 'edit'}-->{lang edit_save}
	<!--{/if}-->
	</button>
</div>
<!--{hook/post_btn_extra_mobile}-->
<input type="hidden" name="{if $_GET['action'] == 'newthread'}topicsubmit{elseif $_GET['action'] == 'reply'}replysubmit{elseif $_GET['action'] == 'edit'}editsubmit{/if}" value="yes">
</form>
<script type="text/javascript">
(function($) {
	let needsubject = false;
	let needmessage = false;
	<!--{if $_GET['action'] == 'reply'}-->
		needsubject = true;
	<!--{elseif $_GET['action'] == 'edit'}-->
		needsubject = needmessage = true;
	<!--{/if}-->
	<!--{if $_GET['action'] == 'newthread' || ($_GET['action'] == 'edit' && $isfirstpost)}-->
	$('#needsubject').on('keyup input', function() {
		var obj = $(this);
		if(obj.val()) {
			needsubject = true;
			if(needmessage == true) {
				$('.btn_pn').removeClass('btn_pn_grey').addClass('btn_pn_blue');
				$('.btn_pn').attr('disable', 'false');
			}
		} else {
			needsubject = false;
			$('.btn_pn').removeClass('btn_pn_blue').addClass('btn_pn_grey');
			$('.btn_pn').attr('disable', 'true');
		}
	});
	<!--{/if}-->
	$('#needmessage').on('keyup input', function() {
		var obj = $(this);
		if(obj.val()) {
			
			if(needsubject == true) {
				$('.btn_pn').removeClass('btn_pn_grey').addClass('btn_pn_blue');
				$('.btn_pn').attr('disable', 'false');
			}
		} else {
			needmessage = false;
			$('.btn_pn').removeClass('btn_pn_blue').addClass('btn_pn_grey');
			$('.btn_pn').attr('disable', 'true');
		}
	});
})(jQuery);
</script>
<script type="text/javascript">
	$('#postsubmit').on('click', function() {
		var obj = $(this);
		if(obj.attr('disable') == 'true') {
			return false;
		}
		obj.attr('disable', 'true').removeClass('btn_pn_blue').addClass('btn_pn_grey');
		popup.open('<img src="' + IMGDIR + '/imageloading.gif">');

		<!--{if (!empty($_G['setting']['editormodetype']) && $_GET[action] != 'edit') || ($_GET[action] == 'edit' && $isJsonContent)}-->
		saveJsonContent();
		var _needsubject = document.getElementById('needsubject');
		var _content = document.getElementById('content');
		var post_content = '';
		<!--{if $_GET[action] == 'edit' && $isJsonContent}-->
		post_content = `{$postinfo['content']}`;
		<!--{/if}-->
		if (!post_content) {
			post_content = _content;
		}
		<!--{if $postinfo['first']}-->
		if(_needsubject.value.trim() === '' || _needsubject.value === null ||
			post_content.trim() === '') {
			popup.open('{lang post_sm_isnull}', 'alert');
			return false;
		}
		<!--{/if}-->
		<!--{/if}-->

		var postlocation = '';
		if(typeof geo !== 'undefined' && geo.errmsg === '' && geo.loc) {
			postlocation = geo.longitude + '|' + geo.latitude + '|' + geo.loc;
		}
		var myform = document.getElementById('postform');
		var formdata = new FormData(myform);
		$.ajax({
			type:'POST',
			url:form.attr('action') + '&geoloc=' + postlocation + '&handlekey='+form.attr('id')+'&inajax=1',
			data:formdata,
			cache:false,
			contentType:false,
			processData:false,
			dataType:'xml'
		})
		.success(function(s) {
			popup.open(s.lastChild.firstChild.nodeValue);
			obj.attr('disable','false');
			obj.removeClass('btn_pn_grey').addClass('btn_pn_blue');
		})
		.error(function() {
			popup.open('{lang networkerror}', 'alert');
		});
		return false;
	});
	
</script>
<!--{eval $nofooter = true;}-->
<!--{template common/footer}-->