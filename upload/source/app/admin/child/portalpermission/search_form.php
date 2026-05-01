<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

echo <<<SEARCH
<form method="post" autocomplete="off" action="$adminscript" id="tb_search">
	<table class="tb tb2" cellspacing="3" cellpadding="3">
		<tr>
			<td>{$searchlang['uid']}</td><td><input type="text" class="txt" name="uid" value="{$_GET['uid']}"></td>
			<td>{$searchlang['username']}*</td><td><input type="text" class="txt" name="username" value="{$_GET['username']}"> *{$searchlang['likesupport']}</td>
		</tr>
		<tr>
			<td>{$searchlang['resultsort']}</td>
			<td>
				<select name="ordersc">
				<option value="desc"{$ordersc['desc']}>{$searchlang['orderdesc']}</option>
				<option value="asc"{$ordersc['asc']}>{$searchlang['orderasc']}</option>
				</select>
				<select name="perpage">
				<option value="10"{$perpages[10]}>{$searchlang['perpage_10']}</option>
				<option value="20"{$perpages[20]}>{$searchlang['perpage_20']}</option>
				<option value="50"{$perpages[50]}>{$searchlang['perpage_50']}</option>
				<option value="100"{$perpages[100]}>{$searchlang['perpage_100']}</option>
				</select>
			</td>
			<td><label for="inherited">{$searchlang['portalpermission_no_inherited']}</label></td>
			<td>
				<input type="checkbox" value=1 name="inherited" id="inherited" $inherited/>
				<input type="hidden" name="action" value="portalpermission">
				<input type="hidden" name="operation" value="$operation">
				<input type="submit" name="searchsubmit" value="{$searchlang['search']}" class="btn">
			</td>
		</tr>
	</table>
</form>
SEARCH;
