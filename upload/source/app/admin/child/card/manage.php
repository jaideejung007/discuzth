<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('cardsubmit')) {
	if(is_array($_POST['delete'])) {
		$delnum = table_common_card::t()->delete($_POST['delete']);
		$card_info = serialize(['num' => ($delnum ? $delnum : 0)]);
		$cardlog = [
			'uid' => $_G['uid'],
			'cardrule' => '',
			'info' => $card_info,
			'dateline' => $_G['timestamp'],
			'operation' => 3,
			'username' => $_G['member']['username']
		];
		table_common_card_log::t()->insert($cardlog);
	}
}
$sqladd = cardsql();
foreach($_GET as $key => $val) {
	if(str_contains($key, 'srch_') && $val) {
		if($key == 'srch_username') {
			$val = rawurlencode($val);
		}
		$export_url[] = $key.'='.$val;
	}
}

$perpage = max(20, empty($_GET['perpage']) ? 20 : intval($_GET['perpage']));
echo '<script type="text/javascript" src="'.STATICURL.'js/calendar.js"></script>';

/*search={"card_manage_tips":"action=card&operation=manage"}*/
showtips('card_manage_tips');
/*search*/
$card_type_option = '';
foreach(table_common_card_type::t()->range(0, 0, 'ASC') as $result) {
	$card_type[$result['id']] = $result;
	$card_type_option .= "<option value=\"{$result['id']}\" ".($_GET['srch_card_type'] == $result['id'] ? 'selected' : '').">{$result['typename']}</option>";
}
showformheader('card');
showtableheader('', 'fixpadding');
showtablerow('', ['width="80"', 'width="160"', 'width=100'],
	[
		cplang('card_number'), '<input type="text" name="srch_id" class="txt" value="'.$_GET['srch_id'].'" />',
		cplang('card_log_price').cplang('between'), '<input type="text" name="srch_price_min" class="txt" value="'.($_GET['srch_price_min'] ? $_GET['srch_price_min'] : '').'" />- &nbsp;<input type="text" name="srch_price_max" class="txt" value="'.($_GET['srch_price_max'] ? $_GET['srch_price_max'] : '').'" />',
	]
);

echo "<input type='hidden' name='action' value='card'><input type='hidden' name='operation' value='manage'>";
$extcredits_option = "<option value=''>".cplang('nolimit').'</option>';
foreach($_G['setting']['extcredits'] as $key => $val) {
	$extcredits_option .= "<option value='$key' ".($_GET['srch_extcredits'] == $key ? 'selected' : '').">{$val['title']}</option>";
}
foreach(['1' => cplang('card_manage_status_1'), '2' => cplang('card_manage_status_2'), '9' => cplang('card_manage_status_9')] as $key => $val) {
	$status_option .= "<option value='{$key}' ".($_GET['srch_card_status'] == $key ? 'selected' : '').">{$val}</option>";
}
showtablerow('', [],
	[
		cplang('card_extcreditsval'), '<input type="text" name="srch_extcreditsval" class="txt" style="width:42px;" value="'.$_GET['srch_extcreditsval'].'" /><select name="srch_extcredits">'.$extcredits_option.'</select>',
		cplang('card_status'), "<select name='srch_card_status'><option value=''>".cplang('nolimit').'</option>'.$status_option.'</select>',
	]
);
showtablerow('', ['class="td23"', 'class="td23"'],
	[
		cplang('card_log_used_user'), '<input type="text" name="srch_username" class="txt" value="'.$_GET['srch_username'].'" />',
		cplang('card_used_dateline'), '<input type="text" name="srch_useddateline_start" class="txt" value="'.$_GET['srch_useddateline_start'].'" onclick="showcalendar(event, this);" />- &nbsp;<input type="text" name="srch_useddateline_end" class="txt" value="'.$_GET['srch_useddateline_end'].'" onclick="showcalendar(event, this)" />',
	]
);

$perpage_selected[$perpage] = 'selected=selected';
showtablerow('', [],
	[
		cplang('card_type'), '<select name="srch_card_type"><option value="">'.cplang('nolimit').'</option><option value="0" '.($_GET['srch_card_type'] != '' && $_GET['srch_card_type'] == 0 ? 'selected' : '').'>'.cplang('card_type_default').'</option>'.$card_type_option.'</select>',
		cplang('card_search_perpage'), '<select name="perpage" class="ps" onchange="this.form.submit();" ><option value="20" '.$perpage_selected[20].'>'.cplang('perpage_20').'</option><option value="50" '.$perpage_selected[50].'>'.cplang('perpage_50').'</option><option value="100" '.$perpage_selected[100].'>'.cplang('perpage_100').'</option></select>',
	]
);

showtablerow('', ['width="40"', 'width="100"', 'width=50', 'width="260"'],
	[
		'<input type="submit" name="srchbtn" class="btn" value="'.$lang['search'].'" />', ''
	]
);
showtablefooter();
showformfooter();

showformheader('card&operation=manage&');
showtableheader('card_manage_title');
showsubtitle(['', cplang('card_number'), cplang('card_log_price'), cplang('card_extcreditsval'), cplang('card_type'), cplang('card_status'), cplang('card_log_used_user'), cplang('card_used_dateline'), cplang('card_make_cleardateline')/*, cplang('card_maketype')*/, cplang('card_maketime'), cplang('card_log_maker')]);


$start_limit = ($page - 1) * $perpage;
$export_url[] = 'start='.$start_limit;
foreach($_GET as $key => $val) {
	if(str_contains($key, 'srch_')) {
		$url_add .= '&'.$key.'='.$val;
	}
}
$url = ADMINSCRIPT.'?action=card&operation=manage&page='.$page.'&perpage='.$perpage.$url_add;
$count = $sqladd ? table_common_card::t()->count_by_where($sqladd) : table_common_card::t()->count();
if($count) {
	$multipage = multi($count, $perpage, $page, $url, 0, 3);
	foreach(table_common_card::t()->fetch_all_by_where($sqladd, $start_limit, $perpage) as $result) {
		$userlist[$result['makeruid']] = $result['makeruid'];
		$userlist[$result['uid']] = $result['uid'];
		$cardlist[] = $result;
	}
	if($userlist) {
		$members = table_common_member::t()->fetch_all($userlist);
		unset($userlist);
	}

	foreach($cardlist as $key => $val) {
		showtablerow('', ['class="smallefont"', '', '', '', '', '', '', '', '', '', '', ''], [
			'<input class="checkbox" type="checkbox" name="delete[]" value="'.$val['id'].'">',
			$val['id'],
			$val['price'].cplang('card_make_price_unit'),
			$val['extcreditsval'].$_G['setting']['extcredits'][$val['extcreditskey']]['title'],
			$card_type[$val['typeid']]['typename'] ? $card_type[$val['typeid']]['typename'] : cplang('card_type_default'),
			cplang('card_manage_status_'.$val['status']),
			$val['uid'] ? "<a href='home.php?mod=space&uid={$val['uid']}' target='_blank'>".$members[$val['uid']]['username'] : ' -- ',
			$val['useddateline'] ? dgmdate($val['useddateline']) : ' -- ',
			$val['cleardateline'] ? dgmdate($val['cleardateline'], 'Y-m-d') : cplang('card_make_cleardateline_none'),
			dgmdate($val['dateline'], 'u'),
			"<a href='home.php?mod=space&uid={$val['makeruid']}' target='_blank'>".$members[$val['makeruid']]['username'].'</a>'
		]);
	}
	echo '<input type="hidden" name="perpage" value="'.$perpage.'">';
	showsubmit('cardsubmit', 'submit', 'del', '<a href="'.ADMINSCRIPT.'?action=card&operation=export&'.implode('&', $export_url).'" title="'.$lang['card_export_title'].'">'.$lang['card_export'].'</a>', $multipage, false);
}

showtablefooter();
showformfooter();
	