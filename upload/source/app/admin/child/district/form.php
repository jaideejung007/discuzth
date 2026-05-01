<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

/*search={"district":"action=district"}*/
showtips('district_tips');
/*search*/

showformheader('district&countryid='.$values[0].'&pid='.$values[1].'&cid='.$values[2].'&did='.$values[3]);
showboxheader();
showtableheader();

$options = [0 => [], 1 => [], 2 => [], 3 => []];
$thevalues = [];
foreach(table_common_district::t()->fetch_all_by_upid($upids) as $value) {
	$options[$value['level']][] = [$value['id'], $value['name']];
	if($value['upid'] == $theid) {
		$thevalues[] = [$value['id'], $value['name'], $value['displayorder'], $value['usetype']];
	}
}

$names = ['country', 'province', 'city', 'district'];
for($i = 0; $i < 4; $i++) {
	$elems[$i] = !empty($elems[$i]) ? $elems[$i] : $names[$i];
}
$html = '';
for($i = 0; $i < 4; $i++) {
	$jscall = ($i == 0 ? 'this.form.province.value=\'\';this.form.city.value=\'\';this.form.district.value=\'\';' : '')."refreshdistrict('$elems[0]', '$elems[1]', '$elems[2]', '$elems[3]')";
	$html .= '<select name="'.$elems[$i].'" id="'.$elems[$i].'" onchange="'.$jscall.'">';
	$html .= '<option value="">'.lang('spacecp', 'district_level_'.$i).'</option>';
	foreach($options[$i] as $option) {
		$selected = $option[0] == $values[$i] ? ' selected="selected"' : '';
		$html .= '<option value="'.$option[0].'"'.$selected.'>'.$option[1].'</option>';
	}
	$html .= '</select>&nbsp;&nbsp;';
}
echo cplang('district_choose').' &nbsp; '.$html;
showsubtitle($values[0] ? ['', 'display_order', 'name', 'operation'] : ['', 'display_order', 'name', 'district_birthcity', 'district_residecity', 'operation']);
foreach($thevalues as $value) {
	$valarr = [];
	$valarr[] = '';
	$valarr[] = '<input type="text" id="displayorder_'.$value[0].'" class="txt" name="displayorder['.$value[0].']" value="'.$value[2].'"/>';
	$valarr[] = '<p id="p_'.$value[0].'"><input type="text" id="input_'.$value[0].'" class="txt" name="district['.$value[0].']" value="'.$value[1].'"/></p>';
	if(!$values[0]) {
		$valarr[] = '<input type="checkbox" name="birthcity['.$value[0].']" value="1" class="checkbox"'.($value[3] && in_array($value[3], [1, 3]) ? ' checked="checked" ' : '').' />';
		$valarr[] = '<input type="checkbox" name="residecity['.$value[0].']" value="1" class="checkbox"'.($value[3] && in_array($value[3], [2, 3]) ? ' checked="checked" ' : '').' />';
	}
	$valarr[] = '<a href="javascript:;" onclick="deletedistrict('.$value[0].');return false;">'.cplang('delete').'</a>';
	showtablerow('id="td_'.$value[0].'"', ['', 'class="td25"', '', '', '', ''], $valarr);
}
showtablerow('', ['colspan=2'], [
	'<div><a href="javascript:;" onclick="addrow(this, 0, 1);return false;" class="addtr">'.cplang('add').'</a></div>'
]);
showsubmit('editsubmit', 'submit');
$adminurl = ADMINSCRIPT.'?action=district';
echo <<<SCRIPT
<script type="text/javascript">
var rowtypedata = [
	[[1,'', ''],[1,'<input type="text" class="txt" name="districtnew_order[]" value="0" />', 'td25'],[2,'<input type="text" class="txt" name="districtnew[]" value="" />', '']],
];

function refreshdistrict(country, province, city, district) {
	location.href = "$adminurl"
		+"&country="+country+"&province="+province+"&city="+city+"&district="+district
		+"&countryid="+$(country).value+"&pid="+$(province).value + "&cid="+$(city).value+"&did="+$(district).value;
}

function editdistrict(did) {
	$('input_' + did).style.display = "block";
	$('span_' + did).style.display = "none";
}

function deletedistrict(did) {
	var elem = $('p_' + did);
	elem.parentNode.removeChild(elem);
	var elem = $('td_' + did);
	elem.parentNode.removeChild(elem);
}
</script>
SCRIPT;
showtablefooter();
showboxfooter();
showformfooter();

	