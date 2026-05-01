<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

shownav('group', 'nav_group_type');
showsubmenu('nav_group_type');

if(!submitcheck('editsubmit')) {
	?>
	<script type="text/JavaScript">
		var rowtypedata = [
			[[1, '<input type="text" class="txt" name="newcatorder[]" value="0" />', 'td25'], [3, '<input name="newcat[]" value="<?php echo $lang['groups_type_level_1'];?>" size="20" type="text" class="txt" /> <?php echo cplang('groups_type_show_rows');?><input type="text" name="newforumcolumns[]" value="0" class="txt" style="width: 30px;" />']],
			[[1, '<input type="text" class="txt" name="neworder[{1}][]" value="0" />', 'td25'], [3, '<div class="board"><input name="newforum[{1}][]" value="<?php echo $lang['groups_type_sub_new'];?>" size="20" type="text" class="txt" /><?php echo cplang('groups_type_show_rows');?><input type="text" name="newforumcolumns[{1}][]" value="0" class="txt" style="width: 30px;" /></div>']],
			[[1, '<input type="text" class="txt" name="neworder[{1}][]" value="0" />', 'td25'], [3, '<div class="childboard"><input name="newforum[{1}][]" value="<?php echo $lang['groups_type_sub_new'];?>" size="20" type="text" class="txt" /><?php echo cplang('groups_type_show_rows');?><input type="text" name="newforumcolumns[{1}][]" value="0" class="txt" style="width: 30px;" /></div>']],
		];
	</script>
	<?php
	showformheader('group&operation=type');
	showtableheader();
	showsubtitle(['display_order', 'groups_type_name', 'groups_type_count', 'groups_type_operation']);

	$forums = $showedforums = [];
	$query = table_forum_forum::t()->fetch_all_group_type();
	$groups = $forums = $subs = $fids = $showed = [];
	foreach($query as $forum) {
		if($forum['type'] == 'group') {
			$groups[$forum['fid']] = $forum;
		} else {
			$forums[$forum['fup']][] = $forum;
		}
		$fids[] = $forum['fid'];
	}

	foreach($groups as $id => $gforum) {
		$showed[] = showgroup($gforum, 'group');
		if(!empty($forums[$id])) {
			foreach($forums[$id] as $forum) {
				$showed[] = showgroup($forum);
				$lastfid = 0;
				if(!empty($subs[$forum['fid']])) {//群组不展示了  废弃代码
					foreach($subs[$forum['fid']] as $sub) {
						$showed[] = showgroup($sub, 'sub');
						$lastfid = $sub['fid'];
					}
				}
				showgroup($forum, $lastfid, 'lastchildboard');
			}
		}
		showgroup($gforum, '', 'lastboard');
	}

	if(count($fids) != count($showed)) {
		foreach($fids as $fid) {
			if(!in_array($fid, $showed)) {
				table_forum_forum::t()->update($fid, ['fup' => '0', 'type' => 'forum']);
			}
		}
	}

	showgroup($gforum, '', 'last');

	showsubmit('editsubmit');
	showtablefooter();
	showformfooter();

} else {
	$order = $_GET['order'];
	$name = $_GET['name'];
	$newforum = $_GET['newforum'];
	$newcat = $_GET['newcat'];
	$newcatorder = $_GET['newcatorder'];
	$neworder = $_GET['neworder'];
	$newforumcolumns = $_GET['newforumcolumns'];
	$forumcolumnsnew = $_GET['forumcolumnsnew'];
	if(is_array($order)) {
		foreach($order as $fid => $value) {
			if(empty($name[$fid])) {
				continue;
			}
			table_forum_forum::t()->update($fid, ['name' => $name[$fid], 'displayorder' => $order[$fid], 'forumcolumns' => $forumcolumnsnew[$fid]]);
		}
	}

	if(is_array($newcat)) {
		foreach($newcat as $key => $forumname) {
			if(empty($forumname)) {
				continue;
			}
			$fid = table_forum_forum::t()->insert(['type' => 'group', 'name' => $forumname, 'status' => 3, 'displayorder' => $newcatorder[$key], 'forumcolumns' => $newforumcolumns[$key]], 1);
			table_forum_forumfield::t()->insert(['fid' => $fid]);
		}
	}

	$table_forum_columns = ['fup', 'type', 'name', 'status', 'displayorder', 'styleid', 'allowsmilies', 'allowhtml', 'allowbbcode', 'allowimgcode', 'allowanonymous', 'allowpostspecial', 'alloweditrules', 'alloweditpost', 'modnewposts', 'recyclebin', 'jammer', 'forumcolumns', 'threadcaches', 'disablewatermark', 'autoclose', 'simple'];
	$table_forumfield_columns = ['fid', 'attachextensions', 'threadtypes', 'creditspolicy', 'viewperm', 'postperm', 'replyperm', 'getattachperm', 'postattachperm'];
	$projectdata = [];

	if(is_array($newforum)) {
		foreach($newforum as $fup => $forums) {
			$forum = table_forum_forum::t()->fetch($fup);
			foreach($forums as $key => $forumname) {
				if(empty($forumname)) {
					continue;
				}
				$forumfields = [];

				$forumfields['allowsmilies'] = $forumfields['allowbbcode'] = $forumfields['allowimgcode'] = 1;
				$forumfields['allowpostspecial'] = 127;


				$forumfields['fup'] = $forum ? $fup : 0;
				$forumfields['type'] = 'forum';
				$forumfields['name'] = $forumname;
				$forumfields['status'] = 3;
				$forumfields['displayorder'] = $neworder[$fup][$key];
				$forumfields['forumcolumns'] = $newforumcolumns[$fup][$key];

				$data = [];
				foreach($table_forum_columns as $field) {
					if(isset($forumfields[$field])) {
						$data[$field] = $forumfields[$field];
					}
				}

				$forumfields['fid'] = $fid = table_forum_forum::t()->insert($data, 1);

				$data = [];
				foreach($table_forumfield_columns as $field) {
					if(isset($forumfields[$field])) {
						$data[$field] = $forumfields[$field];
					}
				}
				table_forum_forumfield::t()->insert($data);
			}
		}
	}
	updatecache('grouptype');
	cpmsg('group_update_succeed', 'action=group&operation=type', 'succeed');
}
	