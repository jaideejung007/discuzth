<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2>{lang send_posts}</h2>
	<div class="my">
		<a href="search.php?mod=forum"><i class="dm-search"></i></a>
	</div>
</div>
<div style="display:none">
	<ul id="fs_group">$grouplist</ul>
	<ul id="fs_forum_common">$commonlist</ul>
	<!--{loop $forumlist $forumid $forum}-->
	<ul id="fs_forum_$forumid">$forum</ul>
	<!--{/loop}-->
	<!--{loop $subforumlist $forumid $forum}-->
	<ul id="fs_subforum_$forumid">$forum</ul>
	<!--{/loop}-->
</div>
<div class="pblbox cl">
	<ul class="pbl cl">
		<li id="block_group"></li>
		<li id="block_forum"></li>
		<li id="block_subforum"></li>
	</ul>
</div>
<!--{if $_G['group']['allowpost'] || !$_G['uid']}-->
<div class="post_btn cl">
	<button id="postbtn" class="pn" onclick="window.location.href='forum.php?mod=post&action=newthread&fid=' + selectfid" disabled="disabled">{lang send_posts}</button>
</div>
<!--{/if}-->

<script type="text/javascript" reload="1">
	var s = '<!--{if $commonfids}--><p><a id="commonforum" href="javascript:;" onclick="switchforums(this, 1, \'common\')" class="pbsb lightlink">{lang nav_forum_frequently}</a></p><!--{/if}-->';
	var lis = document.getElementById('fs_group').getElementsByTagName('LI');
	for(i = 0;i < lis.length;i++) {
		var gid = lis[i].getAttribute('fid');
		if(document.getElementById('fs_forum_' + gid)) {
			s += '<p><a href="javascript:;" ondblclick="locationforums(1, ' + gid + ')" onclick="switchforums(this, 1, ' + gid + ')" class="pbsb">' + lis[i].innerHTML + '</a></p>';
		}
	}

	document.getElementById('block_group').innerHTML = '<div class="forum-level-title">{lang choose_please}{lang forumlist}</div>' + s;
	var lastswitchobj = null;
	var selectfid = 0;
	var switchforum = switchsubforum = '';

	if(document.getElementById('commonforum')) {
		switchforums(document.getElementById('commonforum'), 1, 'common');
	} else {
		// 如果没有常用版块，尝试切换到第一个可用版块
		var firstLink = document.querySelector('#block_group a');
		if(firstLink) {
			var fid = firstLink.getAttribute('onclick').match(/\d+/)[0];
			switchforums(firstLink, 1, fid);
		}
	}
	function switchforums(obj, block, fid) {
		if(lastswitchobj != obj) {
			if(lastswitchobj) {
				lastswitchobj.parentNode.className = '';
			}
			obj.parentNode.className = 'pbls';
		}
		var s = '';
		if(block == 1) {
			if(fid == 'common') {
				var lis = document.getElementById('fs_forum_common').getElementsByTagName('LI');
			} else {
				var lis = document.getElementById('fs_forum_' + fid).getElementsByTagName('LI');
			}
			for(i = 0;i < lis.length;i++) {
				fid = lis[i].getAttribute('fid');
				if(fid != '') {
					s += '<p><a href="javascript:;" ondblclick="locationforums(2, ' + fid + ')" onclick="switchforums(this, 2, ' + fid + ')"' + (document.getElementById('fs_subforum_' + fid) ?  ' class="pbsb"' : '') + '>' + lis[i].innerHTML + '</a></p>';
				}
			}
			document.getElementById('block_forum').innerHTML = '<div class="forum-level-title">{lang choose_please}{lang forum}</div>' + s;
			document.getElementById('block_subforum').innerHTML = '<div class="forum-level-title">{lang choose_please}{lang forum_subforums}</div>';
			// 当block为1时，隐藏block_subforum
			document.getElementById('block_subforum').style.display = 'none';
			switchforum = switchsubforum = '';
			selectfid = 0;
			document.getElementById('postbtn').setAttribute("disabled", "disabled");
			document.getElementById('postbtn').className = 'pn xg1 y';
		} else if(block == 2) {
			selectfid = fid;
			if(document.getElementById('fs_subforum_' + fid)) {
				var lis = document.getElementById('fs_subforum_' + fid).getElementsByTagName('LI');
				for(i = 0;i < lis.length;i++) {
					fid = lis[i].getAttribute('fid');
					s += '<p><a href="javascript:;" ondblclick="locationforums(3, ' + fid + ')" onclick="switchforums(this, 3, ' + fid + ')">' + lis[i].innerHTML + '</a></p>';
				}
				document.getElementById('block_subforum').innerHTML = '<div class="forum-level-title">{lang choose_please}{lang forum_subforums}</div>' + s;
				// 如果子版块内容不为空，则显示block_subforum，否则隐藏
				if(s.trim() !== '') {
					document.getElementById('block_subforum').style.display = 'block';
				} else {
					document.getElementById('block_subforum').style.display = 'none';
				}
			} else {
				document.getElementById('block_subforum').innerHTML = '<div class="forum-level-title">{lang choose_please}{lang forum_subforums}</div>';
				document.getElementById('block_subforum').style.display = 'none';
			}
			switchforum = obj.innerHTML;
			switchsubforum = '';
			document.getElementById('postbtn').removeAttribute("disabled");
			document.getElementById('postbtn').className = 'pn pnc y';
		} else {
			selectfid = fid;
			switchsubforum = obj.innerHTML;
			document.getElementById('postbtn').removeAttribute("disabled");
			document.getElementById('postbtn').className = 'pn pnc y';
		}
		lastswitchobj = obj;
	}

	function locationforums(block, fid) {
		location.href = block == 1 ? 'forum.php?gid=' + fid : 'forum.php?mod=forumdisplay&fid=' + fid;
	}

</script>
<!--{template common/footer}-->