<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$rule = [];
$rewritedata = rewritedata();
$rule['{apache1}'] = $rule['{apache2}'] = $rule['{iis}'] = $rule['{iis7}'] = $rule['{nginx}'] = $rule['{lighttpd}'] = $rule['{caddy}'] = '';
foreach($rewritedata['rulesearch'] as $k => $v) {
	if(!is_array($_G['setting']['rewritestatus']) || !in_array($k, $_G['setting']['rewritestatus'])) {
		continue;
	}
	$v = empty($_G['setting']['rewriterule'][$k]) ? $v : $_G['setting']['rewriterule'][$k];
	$pvmaxv = count($rewritedata['rulevars'][$k]) + 2;
	$vkeys = array_keys($rewritedata['rulevars'][$k]);
	$rewritedata['rulereplace'][$k] = pvsort($vkeys, $v, $rewritedata['rulereplace'][$k]);
	$v = str_replace($vkeys, $rewritedata['rulevars'][$k], addcslashes($v, '?*+^$.[]()|'));
	$rulepath = $k != 'forum_archiver' ? '' : 'archiver/';
	$rule['{apache1}'] .= "\t".'RewriteCond %{QUERY_STRING} ^(.*)$'."\n\t".'RewriteRule ^(.*)/'.$rulepath.$v.'$ $1/'.$rulepath.pvadd($rewritedata['rulereplace'][$k])."&%1\n";
	$rule['{apache2}'] .= 'RewriteCond %{QUERY_STRING} ^(.*)$'."\n".'RewriteRule ^'.$rulepath.$v.'$ '.$rulepath.$rewritedata['rulereplace'][$k]."&%1\n";
	$rule['{iis}'] .= 'RewriteRule ^(.*)/'.$rulepath.$v.'(\?(.*))*$ $1/'.$rulepath.addcslashes(pvadd($rewritedata['rulereplace'][$k]).'&$'.($pvmaxv + 1), '.?')."\n";
	$rule['{iis7}'] .= "\t\t".'&lt;rule name="'.$k.'"&gt;'."\n\t\t\t".'&lt;match url="^(.*/)*'.$rulepath.str_replace('\.', '.', $v).'\?*(.*)$" /&gt;'."\n\t\t\t".'&lt;action type="Rewrite" url="{R:1}/'.str_replace(['&', 'page\%3D'], ['&amp;amp;', 'page%3D'], $rulepath.addcslashes(pvadd($rewritedata['rulereplace'][$k], ['{R:', '}']).'&{R:'.$pvmaxv.'}', '?')).'" /&gt;'."\n\t\t".'&lt;/rule&gt;'."\n";
	$rule['{nginx}'] .= 'rewrite ^([^\.]*)/'.$rulepath.$v.'$ $1/'.$rulepath.stripslashes(pvadd($rewritedata['rulereplace'][$k]))." last;\n";
	$rule['{lighttpd}'] .= '"(.*)/'.$rulepath.$v.'\?*(.*)$" =&gt; "$1/'.$rulepath.pvadd($rewritedata['rulereplace'][$k]).'&$'.$pvmaxv.'",'."\n";
	$rule['{caddy}'] .= '@'.$k.' path_regexp '.$k.' ^(.*)/'.$rulepath.$v."$\n".'rewrite @'.$k.' {re.'.$k.'.1}/'.$rulepath.pvadd($rewritedata['rulereplace'][$k], ['{re.'.$k.'.', '}']).'&{query}'."\n";
}
$rule['{nginx}'] .= "if (!-e \$request_filename) {\n\treturn 404;\n}";
$rule['{siteroot}'] = !empty($_G['siteroot']) ? $_G['siteroot'] : '/';
echo str_replace(array_keys($rule), $rule, cplang('rewrite_message'));
	