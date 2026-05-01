<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

class rp {

	public static function showMenu($operation) {
		showsubmenu('menu_founder_restful', [
			['restful_api_list', 'restful&operation=list', $operation == 'list'],
			['restful_api_add', 'restful&operation=add', $operation == 'add'],
			['restful_app_list', 'restful&operation=appList', $operation == 'appList'],
		]);
	}

	public static function list() {
		if(!submitcheck('submit')) {
			self::_checkRedis();
			showtips('restful_api_tips');
			showformheader('restful&operation=list');
			self::_showList();
			showformfooter();
		} else {
			self::_listSubmit($_GET['list']);
			cpmsg('setting_update_succeed', 'action=restful', 'succeed');
		}
	}

	private static function _checkRedis() {
		global $_G;

		if(!$_G['config']['memory']['redis']['server']) {
			cpmsg('restful_need_redis', '', 'error');
		}

		$m = new memory_driver_redis();
		$m->init($_G['config']['memory']['redis']);
		if(!$m->enable) {
			cpmsg('restful_need_redis', '', 'error');
		}
	}

	private static function _cache($type, $op, $id) {
		if(!restful::cache($type, $op, $id)) {
			cpmsg('restful_need_redis', '', 'error');
		}
	}

	public static function appList() {
		if(!submitcheck('submit')) {
			showtips('restful_api_tips');
			showformheader('restful&operation=appList');
			self::_showAppList();
			showformfooter();
		} else {
			self::_appListSubmit($_GET['list']);
			cpmsg('setting_update_succeed', 'action=restful&operation=appList', 'succeed');
		}
	}

	private static function _showAppList() {
		$data = table_restful_app::t()->fetch_all_data();
		showtableheader('', '');
		showsubtitle(['del', 'available', 'appid', 'name', 'restful_dateline']);
		foreach($data as $row) {
			showtablerow('header', ['width="30"', 'width="30"', 'class="td25"', '', ''], [
				'<input name="list[del][]" type="checkbox" class="checkbox" value="'.$row['appid'].'">',
				'<input name="list[status]['.$row['appid'].']" type="checkbox" class="checkbox" value="1"'.($row['status'] ? ' checked' : '').'>',
				'<a href="'.ADMINSCRIPT.'?action=restful&operation=app&id='.$row['appid'].'">'.$row['appid'].'</a>',
				'<a href="'.ADMINSCRIPT.'?action=restful&operation=app&id='.$row['appid'].'">'.$row['name'].'</a>',
				dgmdate($row['dateline']),
			]);
		}
		showsubmit('submit', 'submit', '',
			'<input type="button" class="btn" value="'.cplang('add').'" onclick="location.href=\''.ADMINSCRIPT.'?action=restful&operation=appAdd\'"/>');
		showtablefooter();
	}

	private static function _appListSubmit($data) {
		if($data['del']) {
			foreach($data['del'] as $appid) {
				table_restful_app::t()->delete($appid);
				self::_cache('app', 'del', $appid);
			}
		}
		$oldData = table_restful_app::t()->fetch_all_data();
		if($oldData) {
			foreach($oldData as $row) {
				if($row['status'] != $data['status'][$row['appid']]) {
					table_restful_app::t()->update($row['appid'], [
						'status' => $data['status'][$row['appid']] ? 1 : 0
					]);
					if($data['status'][$row['appid']]) {
						self::_cache('app', 'add', $row['appid']);
					} else {
						self::_cache('app', 'del', $row['appid']);
					}
				}
			}
		}

	}

	public static function appAdd() {
		if(!submitcheck('submit')) {
			showchildmenu([['menu_founder_restful', 'restful&operation=list'], ['restful_app_list', 'restful&operation=appList']], cplang('restful_app_add'));

			showformheader('restful&operation=appAdd');
			showtableheader('restful_app_add', 'tb2');
			/*search={"founder":"action=restful&operation=appAdd"}*/
			showsetting('restful_app_name', 'name', '', 'text');
			showsubmit('submit');
			/*search*/
			showtablefooter();
			showformfooter();
		} else {
			$appid = '1'.sprintf('%07d', random(7, true));
			$secret = strtoupper(random(16));
			$name = !empty($_GET['name']) ? $_GET['name'] : $appid;
			table_restful_app::t()->insert([
				'appid' => $appid,
				'secret' => $secret,
				'name' => $name,
				'status' => 1,
				'dateline' => TIMESTAMP,
			]);
			self::_cache('app', 'add', $appid);
			cpmsg('setting_update_succeed', 'action=restful&operation=appList', 'succeed');
		}
	}

	public static function app() {
		$appid = dintval($_GET['id']);
		$app = table_restful_app::t()->fetch($appid);
		if(!$app) {
			cpmsg('restful_app_not_found', '', 'error');
		}
		$app['data'] = json_decode($app['data'], true);
		if(submitcheck('submit')) {
			foreach($_GET['datanew'] as $k => $v) {
				$app['data'][$k] = $v;
			}
			table_restful_app::t()->update($appid, [
				'name' => $_GET['name'],
				'data' => json_encode($app['data']),
			]);
			self::_cache('app', 'add', $appid);
			cpmsg('setting_update_succeed', 'action=restful&operation=appList', 'succeed');
		} elseif(submitcheck('permsubmit')) {
			table_restful_permission::t()->delete_by_appid($appid);
			$perm = !empty($_GET['perm']) ? $_GET['perm'] : [];
			$freq = !empty($_GET['freq']) ? $_GET['freq'] : [];
			foreach($perm as $key => $v) {
				if(!$v) {
					continue;
				}
				list($uri, $ver) = explode('|', $key);
				table_restful_permission::t()->insert([
					'appid' => $appid,
					'uri' => $uri,
					'ver' => $ver,
					'isbase' => 0,
					'freq' => $freq[$key],
					'dateline' => TIMESTAMP,
				]);
			}
			self::_cache('app', 'add', $appid);
			cpmsg('setting_update_succeed', 'action=restful&operation=app&id='.$appid, 'succeed');
		} elseif($_GET['do'] == 'resetSecret') {
			if(submitcheck('resetsubmit')) {
				$secret = strtoupper(random(16));
				table_restful_app::t()->update($appid, [
					'secret' => $secret,
				]);
				self::_cache('app', 'add', $appid);
				cpmsg('setting_update_succeed', 'action=restful&operation=app&id='.$appid, 'succeed');
			}
			cpmsg('restful_reset_secret_confirm', 'action=restful&operation=app&id='.$appid.'&do=resetSecret&resetsubmit=yes', 'form');
		} else {
			showchildmenu([['menu_founder_restful', 'restful&operation=list'], ['restful_app_list', 'restful&operation=appList']], $app['name']);

			showformheader('restful&operation=app&id='.$appid);
			showtableheader('restful_app_edit', 'tb2');
			showsetting('appid', '', '', $app['appid']);
			showsetting('secret', '', '', $app['secret'].'<a href="'.ADMINSCRIPT.'?action=restful&operation=app&id='.$appid.'&do=resetSecret" class="lightnum marginleft10">['.cplang('restful_reset_secret').']</a>');
			showsetting('restful_app_name', 'name', $app['name'], 'text');
			showsetting('restful_app_seccheck', 'datanew[seccheck]', $app['data']['seccheck'] ?? 0, 'radio');
			showsetting('restful_app_log', 'datanew[log]', $app['data']['log'] ?? 0, 'radio');
			showsetting('restful_app_tokenTTL', 'datanew[tokenTTL]', $app['data']['tokenTTL'] ?? 0, 'text');
			showsetting('restful_app_refreshTokenTTL', 'datanew[refreshTokenTTL]', $app['data']['refreshTokenTTL'] ?? 0, 'text');
			showsubmit('submit');
			showtablefooter();
			showformfooter();

			$apisindex = [];
			$apis = table_restful_api::t()->fetch_all_data();
			foreach($apis as $api) {
				$apisindex[$api['baseuri']] = $api['copyright'];
			}
			$permList = table_restful_permission::t()->fetch_all_by_appid(0);
			$appPerm = table_restful_permission::t()->fetch_all_by_appid($appid);
			$list = $values = [];
			foreach($appPerm as $row) {
				$values[$row['uri'].'|'.$row['ver']] = $row;
			}
			$selectAll = empty($appPerm);
			foreach($permList as $row) {
				if($row['isbase']) {
					$copyright = $apisindex[$row['uri']];
					$list[$copyright][$row['ver']][$row['uri']] = $row;
				} else {
					list(, $uri) = explode('/', $row['uri']);
					$copyright = $apisindex['/'.$uri];
					$list[$copyright][$row['ver']]['/'.$uri]['sub'][] = $row;
				}
			}
			$start = dgmdate(TIMESTAMP - 30 * 86400, 'Ymd');
			$end = dgmdate(TIMESTAMP, 'Ymd');
			$allstats = table_restful_stat::t()->fetch_all_stat($appid, '', $start, $end);
			$statdata = [];
			foreach($allstats as $api => $stats) {
				foreach($stats as $date => $count) {
					$statdata['all'] += $count;
					$statdata[$api] += $count;
				}
			}

			echo '<style>.c1 { width: 70px;text-align: right !important;padding-left: 0 !important; }</style>';
			showformheader('restful&operation=app&id='.$appid);
			showtableheader('restful_app_perm', 'tb2');
			showsubtitle(['available', 'name', 'restful_freq',
				cplang('restful_stats', [
					'count' => '<a href="'.ADMINSCRIPT.'?action=restful&operation=stat&id='.$appid.'&api=all">'.$statdata['all'].'</a>'
				])], 'header', ['class="c1"']);

			$apis = table_restful_api::t()->fetch_all_data(true);
			$names = [];
			foreach($apis as $api) {
				$api['data'] = json_decode($api['data'], true);
				foreach($api['data'] as $k => $v) {
					$names[$api['ver']][$k] = $v['name'];
				}
			}
			foreach([
				        '/token' => cplang('restful_token'),
				        '/authtoken' => cplang('restful_authtoken'),
				        '/deltoken' => cplang('restful_deltoken'),
			        ] as $api => $name) {
				showtablerow('header', ['class="c1"', 'width="30%"', ''], [
					'<input type="checkbox" class="checkbox" checked readonly disabled />',
					$name.'('.$api.')',
					'<input type="text" class="txt" value="0" readonly isabled />',
					'<a href="'.ADMINSCRIPT.'?action=restful&operation=stat&id='.$appid.'&api='.$api.'">'.($statdata[$api] ?? 0).'</a>',
				]);
			}
			$allcount = [];
			foreach($list as $copyright => $row1) {
				foreach($row1 as $ver => $row2) {
					$gkey = substr(md5($copyright.'.'.$ver), 0, 16);
					showtablerow('class="header"', ['class="c1"', 'width="30%"', ''], [
						'<a id="a_group_v'.$gkey.'_body" class="marginleft10" href="javascript:;" onclick="toggle_group(\'group_v'.$gkey.'_body\')">[-]</a>'.
						'<input type="checkbox" class="checkbox" name="chkall'.$gkey.'" id="chkall'.$gkey.'" onclick="checkAll(\'value\', this.form, \''.$gkey.'\', \'chkall'.$gkey.'\')" name="perm['.$key.']" />',
						$copyright.' - v'.$ver,
						'',
						'',
					]);
					showtagheader('tbody', 'group_v'.$gkey.'_body', TRUE);
					foreach($row2 as $row) {
						$key = $row['uri'].'|'.$row['ver'];
						$check = $selectAll || !empty($values[$key]) ? ' checked' : '';
						if(!$row['sub']) {
							if(empty($names[$row['ver']][$row['uri']])) {
								continue;
							}
							$api = $row['uri'].'/v'.$row['ver'];
							$freq = !empty($values[$key]['freq']) ? $values[$key]['freq'] : 0;
							showtablerow('header', ['class="c1"', 'width="30%"', ''], [
								'<input type="checkbox" class="checkbox" chkvalue="'.$gkey.'" name="perm['.$key.']"'.$check.'/>',
								$names[$row['ver']][$row['uri']].'('.$api.')',
								'<input type="text" class="txt" name="freq['.$key.']" value="'.$freq.'" />',
								'<a href="'.ADMINSCRIPT.'?action=restful&operation=stat&id='.$appid.'&api='.$api.'">'.($statdata[$api] ?? 0).'</a>',
							]);

							if($check) {
								$allcount[$gkey][1]++;
							}
							$allcount[$gkey][0]++;
						}

						foreach($row['sub'] as $sub) {
							if(empty($names[$sub['ver']][$sub['uri']])) {
								continue;
							}
							$api = $sub['uri'].'/v'.$sub['ver'];
							$key = $sub['uri'].'|'.$sub['ver'];
							$check = $selectAll || !empty($values[$key]) ? ' checked' : '';
							$freq = !empty($values[$key]['freq']) ? $values[$key]['freq'] : 0;
							showtablerow('header', ['class="c1"', 'width="30%"', ''], [
								'<input type="checkbox" class="checkbox" chkvalue="'.$gkey.'" name="perm['.$key.']"'.$check.'/>',
								$names[$sub['ver']][$sub['uri']].'('.$api.')',
								'<input type="text" class="txt" name="freq['.$key.']" value="'.$freq.'" />',
								'<a href="'.ADMINSCRIPT.'?action=restful&operation=stat&id='.$appid.'&api='.$api.'">'.($statdata[$api] ?? 0).'</a>',
							]);

							if($check) {
								$allcount[$gkey][1]++;
							}
							$allcount[$gkey][0]++;
						}
					}
					showtagfooter('tbody');
				}
			}
			showsubmit('permsubmit', 'submit', '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'perm\')" /><label for="chkall">'.cplang('select_all').'</label>');
			showtablefooter();
			showformfooter();

			foreach($allcount as $key => $value) {
				if($value[0] == $value[1]) {
					echo '<script type="text/javascript">$(\'chkall'.$key.'\').checked = true;</script>';
				}
			}
		}
	}

	public static function stat() {
		global $_G;

		$appid = dintval($_GET['id']);
		$api = $_GET['api'];

		$app = table_restful_app::t()->fetch($appid);
		if(!$app) {
			cpmsg('restful_app_not_found', '', 'error');
		}

		if(empty($_GET['data'])) {
			showchildmenu([['menu_founder_restful', 'restful&operation=list'], ['restful_app_list', 'restful&operation=appList'], [$app['name'], 'restful&operation=app&id='.$appid]], $api);

			showtableheader('restful_chart_title');
			echo '<tr><td><div class="charts">
			<script src="'.STATICURL.'js/echarts/echarts.common.min.js"></script>
			<script src="'.$_G['setting']['jspath'].'stat.js"></script>
			<div id="statchart"></div>
			<script type="text/javascript">
				drawstatchart(\''.ADMINSCRIPT.'?action=restful&operation=stat&id='.$appid.'&api='.$api.'&data=yes\', 300);
			</script></div></td></tr>';
			showtablefooter();
			exit;
		}
		define('FOOTERDISABLED', true);
		ob_end_clean();

		$start = dgmdate(TIMESTAMP - 30 * 86400, 'Ymd');
		$end = dgmdate(TIMESTAMP, 'Ymd');
		$allstats = table_restful_stat::t()->fetch_all_stat($appid, $api != 'all' ? $api : '', $start, $end);
		$statdata = [];
		foreach($allstats as $stats) {
			foreach($stats as $date => $count) {
				$statdata[$date] += $count;
			}
		}

		$xaxis = '';
		$count = 0;
		$graph = [];
		for($i = TIMESTAMP - 30 * 86400; $i <= TIMESTAMP; $i += 86400) {
			$xaxis .= "<value xid='$count'>".dgmdate($i, 'md').'</value>';
			$graph['request'] .= "<value xid='$count'>".($statdata[dgmdate($i, 'Ymd')] + 0).'</value>';
			$count++;
		}

		$xml = '';
		$xml .= '<'."?xml version=\"1.0\" encoding=\"utf-8\"?>";
		$xml .= '<chart><xaxis>';
		$xml .= $xaxis;
		$xml .= '</xaxis><graphs>';
		$count = 0;
		foreach($graph as $key => $value) {
			$title = cplang('restful_graph_'.$key, [
				'appid' => $appid,
				'api' => $api == 'all' ? cplang('restful_graph_all') : dhtmlspecialchars($api)
			]);
			$xml .= "<graph gid='$count' title='".$title."'>";
			$xml .= $value;
			$xml .= '</graph>';
			$count++;
		}
		$xml .= '</graphs></chart>';

		@header('Expires: -1');
		@header('Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0', FALSE);
		@header('Pragma: no-cache');
		@header('Content-type: application/xml; charset=utf-8');
		echo $xml;
		exit();
	}

	private static function _showList() {
		echo '<style>.c1 { width: 70px;text-align: right !important;padding-left: 0 !important; }</style>';
		$data = table_restful_api::t()->fetch_all_data();
		showtableheader('', '');
		showsubtitle(['del', 'available', 'name', 'version', 'restful_copyright', 'restful_import_time'], 'header', ['', 'class="c1"']);
		showtagheader('tbody', '', TRUE);
		if($data) {
			showtablerow('header', ['width="20"', 'class="c1"', '', 'width="30"', '', ''], [
				'<input type="checkbox" class="checkbox" disabled>',
				'<input type="checkbox" class="checkbox" disabled checked>',
				'<a href="'.ADMINSCRIPT.'?action=restful&operation=view&id=system:global">'.cplang('restful_global').'</a>',
				'-',
				'Discuz! Team',
				'-',
			]);
			showtablerow('header', ['width="20"', 'class="c1"', '', 'width="30"', '', ''], [
				'<input type="checkbox" class="checkbox" disabled>',
				'<input type="checkbox" class="checkbox" disabled checked>',
				'<a href="'.ADMINSCRIPT.'?action=restful&operation=view&id=system:/token">'.cplang('restful_token').'(/token)</a>',
				'-',
				'Discuz! Team',
				'-',
			]);
			showtablerow('header', ['width="20"', 'class="c1"', '', 'width="30"', '', ''], [
				'<input type="checkbox" class="checkbox" disabled>',
				'<input type="checkbox" class="checkbox" disabled checked>',
				'<a href="'.ADMINSCRIPT.'?action=restful&operation=view&id=system:/authtoken">'.cplang('restful_authtoken').'(/authtoken)</a>',
				'-',
				'Discuz! Team',
				'-',
			]);
			showtablerow('header', ['width="20"', 'class="c1"', '', 'width="30"', '', ''], [
				'<input type="checkbox" class="checkbox" disabled>',
				'<input type="checkbox" class="checkbox" disabled checked>',
				'<a href="'.ADMINSCRIPT.'?action=restful&operation=view&id=system:/deltoken">'.cplang('restful_deltoken').'(/deltoken)</a>',
				'-',
				'Discuz! Team',
				'-',
			]);
		}
		showtagfooter('tbody');

		$uniqueData = array_reduce($data, function($carry, $item) {
			if(!isset($carry[$item['ver']])) {
				$carry[$item['copyright'].'.'.$item['ver']] = [
					'ver' => $item['ver'],
					'copyright' => $item['copyright']
				];
			}
			return $carry;
		}, []);
		uksort($uniqueData, function($a, $b) {
			if($a == $b) return 0;
			return ($a < $b) ? -1 : 1;
		});

		$uniqueData = array_values($uniqueData);

		$allcount = [];
		foreach($uniqueData as $key => $ud) {
			showtablerow('class="header"', ['width="20"', 'class="c1"', '', 'width="30"', '', ''], [
				'',
				'<a id="a_group_v'.$key.'_body" class="marginleft10" href="javascript:;" onclick="toggle_group(\'group_v'.$key.'_body\')">[-]</a>'.
				'<input type="checkbox" class="checkbox" name="chkall'.$key.'" id="chkall'.$key.'" onclick="checkAll(\'value\', this.form, \''.$key.'\', \'chkall'.$key.'\')" name="perm['.$key.']"'.$check.'/>',
				$ud['copyright'].' - v'.$ud['ver'],
				'',
				'',
				'',
			]);
			showtagheader('tbody', 'group_v'.$key.'_body', TRUE);
			foreach($data as $row) {
				if($ud['copyright'] != $row['copyright'] || $ud['ver'] != $row['ver']) {
					continue;
				}
				$id = $row['baseuri'].'|'.$row['ver'];
				$name = $row['name'] ? $row['name'].'('.$row['baseuri'].')' : $row['baseuri'];
				showtablerow('', ['width="20"', 'class="c1"', '', 'width="30"', '', ''], [
					'<input name="list[del][]" type="checkbox" class="checkbox" value="'.$id.'">',
					'<input name="list[status]['.$id.']" type="checkbox" chkvalue="'.$key.'" class="checkbox" value="1"'.($row['status'] ? ' checked' : '').'>',
					'<a href="'.ADMINSCRIPT.'?action=restful&operation=view&id='.$id.'">'.$name.'</a>',
					'v'.$row['ver'],
					$row['copyright'],
					dgmdate($row['dateline']),
				]);
				if($row['status']) {
					$allcount[$key][1]++;
				}
				$allcount[$key][0]++;
			}
			showtagfooter('tbody');
		}

		showsubmit('submit');
		showtablefooter();
		foreach($allcount as $key => $value) {
			if($value[0] == $value[1]) {
				echo '<script type="text/javascript">$(\'chkall'.$key.'\').checked = true;</script>';
			}
		}
	}

	private static function _listSubmit($data) {
		if($data['del']) {
			foreach($data['del'] as $row) {
				list($baseuri, $ver) = explode('|', $row);
				table_restful_api::t()->delete_by_baseuri_ver($baseuri, $ver);
				table_restful_permission::t()->delete_by_baseuri_ver($baseuri, $ver);
				self::_cache('api', 'del', $baseuri.'|'.$ver);
			}
		}
		$oldData = table_restful_api::t()->fetch_all_data();
		if($oldData) {
			foreach($oldData as $row) {
				$key = $row['baseuri'].'|'.$row['ver'];
				if($row['status'] != $data['status'][$key]) {
					table_restful_api::t()->update_by_baseuri_ver([
						'status' => $data['status'][$key] ? 1 : 0
					], $row['baseuri'], $row['ver']);
					if($data['status'][$key]) {
						self::_cache('api', 'add', $key);
					} else {
						self::_cache('api', 'del', $key);
					}
				}
			}
		}
	}

	public static function add() {
		if(!submitcheck('importsubmit')) {
			$sources = table_restful_source::t()->fetch_all_data();
			$importtyps = [
				cplang('restful_import_local'),
				['file', cplang('import_type_file'), ['importfile' => '', 'importtxt' => 'none']],
				['txt', cplang('import_type_txt'), ['importfile' => 'none', 'importtxt' => '']]
			];
			if($sources) {
				$importtyps[] = cplang('restful_import_online');
				foreach($sources as $source) {
					$importtyps[] = [$source['sourceid'], $source['name'], ['importfile' => 'none', 'importtxt' => 'none']];
				}
			}
			showformheader('restful&operation=add', 'enctype');
			showtableheader('');
			showsetting('import_type', ['importtype', $importtyps], 'file', 'mradio');
			showtagheader('tbody', 'importfile', TRUE);
			showsetting('import_file', 'importfile', '', 'file');
			showtagfooter('tbody');
			showtagheader('tbody', 'importtxt');
			showsetting('import_txt', 'importtxt', '', 'textarea');
			showtagfooter('tbody');
			showsubmit('importsubmit');
			showtablefooter();
			showformfooter();
		} else {
			self::_addSubmit();
			cpmsg('setting_update_succeed', 'action=restful', 'succeed');
		}
	}

	public static function _addSubmit() {
		if(is_numeric($_GET['importtype'])) {
			$siteuniqueid = table_common_setting::t()->fetch_setting('siteuniqueid');
			$source = table_restful_source::t()->fetch($_GET['importtype']);
			$_GET['importtxt'] = dfsockopen($source['url'].'?siteuniqueid='.$siteuniqueid);
			$_GET['importtype'] = 'txt';
		} elseif($_GET['importtype'] == 'file' && empty($_FILES['importfile']['tmp_name'])) {
			cpmsg(sprintf(cplang('restful_xml_error'), 'empty'), '', 'error');
		} elseif($_GET['importtype'] == 'txt' && empty($_GET['importtxt'])) {
			cpmsg(sprintf(cplang('restful_xml_error'), 'empty'), '', 'error');
		}
		$data = getimportdata('Discuz! RESTful');
		if(empty($data['copyright'])) {
			cpmsg(sprintf(cplang('restful_xml_error'), 'copyright'), '', 'error');
		}
		if(empty($data['api']) || !is_array($data['api'])) {
			cpmsg(sprintf(cplang('restful_xml_error'), 'api'), '', 'error');
		}
		foreach($data['api'] as $ver => $apis) {
			if(!preg_match('/^v(\d+)$/', $ver, $r)) {
				cpmsg(sprintf(cplang('restful_xml_error'), 'api->v*'), '', 'error');
			}
			self::parseApis($apis);
			foreach($_ENV['api'] as $baseuri => $value) {
				$_api = table_restful_api::t()->fetch_by_baseuri_ver($baseuri, $r[1]);
				if($_api && $_api['copyright'] != $data['copyright']) {
					cpmsg(sprintf(cplang('restful_api_exists'), $baseuri.'/v'.$r[1]), '', 'error');
				}
			}
			foreach($_ENV['api'] as $baseuri => $value) {
				table_restful_api::t()->insert([
					'baseuri' => $baseuri,
					'ver' => $r[1],
					'name' => $_ENV['apiName'][$baseuri] ?? '',
					'copyright' => $data['copyright'],
					'data' => json_encode($value),
					'status' => 1,
					'dateline' => TIMESTAMP,
				], false, true);
				table_restful_permission::t()->insert([
					'appid' => 0,
					'uri' => $baseuri,
					'ver' => $r[1],
					'isbase' => 1,
					'dateline' => TIMESTAMP,
				], false, true);
				foreach($value as $uri => $_tmp) {
					if($uri !== $baseuri) {
						table_restful_permission::t()->insert([
							'appid' => 0,
							'uri' => $uri,
							'ver' => $r[1],
							'isbase' => 0,
							'dateline' => TIMESTAMP,
						], false, true);
					}
				}
				self::_cache('api', 'add', $baseuri.'|'.$r[1]);
			}
		}
		cpmsg('setting_update_succeed', 'action=restful', 'succeed');
	}

	private static function parseApis($apis, $uriPre = '/') {
		foreach($apis as $uri => $api) {
			$k = $uriPre.$uri;
			list(, $baseuri) = explode('/', $k);
			$baseuri = '/'.$baseuri;
			if($uriPre == '/') {
				$_ENV['apiName'][$baseuri] = $api['name'] ?? '';
			}
			if(!empty($api['script']) && is_string($api['script'])) {
				$_ENV['api'][$baseuri][$k] = [];
				$_ENV['api'][$baseuri][$k] = $api;
			} else {
				self::parseApis($api, $k.'/');
			}
		}
	}

	public static function view() {
		global $_G;

		list($baseuri, $ver) = explode('|', $_GET['id']);
		$data = table_restful_api::t()->fetch_by_baseuri_ver($baseuri, $ver);
		if(!$data) {
			cpmsg('restful_api_not_found', '', 'error');
		}
		$data['data'] = json_decode($data['data'], true);
		showchildmenu([['menu_founder_restful', 'restful&operation=list']], $data['name'] ?: $data['baseuri']);
		showtableheader($data['name'] ?: $data['baseuri'], 'nobottom nobdb');
		foreach($data['data'] as $uri => $row) {
			$url = $_G['siteurl'].'api/restful/?'.$uri;
			if($data['ver'] > 1) {
				$url .= '/v'.$data['ver'];
			}
			echo '<tr><td class="td27" colspan="2">'.$row['name'].'</td></tr>';
			echo '<tr><td class="vtop" style="width: 100px !important;">'.cplang('restful_url').'</td><td><input type="text" class="txt" style="width:350px" value="'.$url.'"></td>';
			if($row['usage']) {
				echo '<tr><td class="vtop">'.cplang('restful_request_param').'</td><td class="vtop tips2">';
				foreach($row['usage'] as $key => $desc) {
					echo '<p class="mbm"><font class="highlight">'.$key.'</font>: '.$desc.'</p>';
				}
			}
			echo '</td></tr>';
		}
		showtablefooter();
	}

	public static function viewSystem() {
		global $_G;

		$baseuri = substr($_GET['id'], 7);
		switch($baseuri) {
			case 'global':
				showchildmenu([['menu_founder_restful', 'restful&operation=list']], cplang('restful_global'));
				showtableheader(cplang('restful_global'), 'nobottom nobdb');
				echo '<tr><td class="vtop" style="width: 100px !important;">'.cplang('restful_request_header').'</td><td class="vtop tips2">'.cplang('restful_global_detail').'</td></tr>';
				break;
			case '/token':
				showchildmenu([['menu_founder_restful', 'restful&operation=list']], cplang('restful_token'));
				$url = $_G['siteurl'].'api/restful/?'.$baseuri;
				showtableheader(cplang('restful_token'), 'nobottom nobdb');
				echo '<tr><td class="td27" colspan="2">'.cplang('restful_token').'</td></tr>'.
					'<tr><td class="vtop" style="width: 100px !important;">'.cplang('restful_url').'</td><td><input type="text" class="txt" style="width:350px" value="'.$url.'"></td>'.
					'<tr><td class="vtop">'.cplang('restful_request_param').'</td><td class="vtop tips2">'.cplang('restful_token_detail').'</td></tr>';
				break;
			case '/authtoken':
				showchildmenu([['menu_founder_restful', 'restful&operation=list']], cplang('restful_authtoken'));
				$url = $_G['siteurl'].'api/restful/?'.$baseuri;
				showtableheader(cplang('restful_authtoken'), 'nobottom nobdb');
				echo '<tr><td class="td27" colspan="2">'.cplang('restful_authtoken').'</td></tr>'.
					'<tr><td class="vtop" style="width: 100px !important;">'.cplang('restful_url').'</td><td><input type="text" class="txt" style="width:350px" value="'.$url.'"></td>'.
					'<tr><td class="vtop">'.cplang('restful_request_param').'</td><td class="vtop tips2">'.cplang('restful_authtoken_detail').'</td></tr>';
				break;
			case '/deltoken':
				showchildmenu([['menu_founder_restful', 'restful&operation=list']], cplang('restful_deltoken'));
				$url = $_G['siteurl'].'api/restful/?'.$baseuri;
				showtableheader(cplang('restful_deltoken'), 'nobottom nobdb');
				echo '<tr><td class="td27" colspan="2">'.cplang('restful_deltoken').'</td></tr>'.
					'<tr><td class="vtop" style="width: 100px !important;">'.cplang('restful_url').'</td><td><input type="text" class="txt" style="width:350px" value="'.$url.'"></td>'.
					'<tr><td class="vtop">'.cplang('restful_request_param').'</td><td class="vtop tips2">'.cplang('restful_deltoken_detail').'</td></tr>';
				break;
		}
		showtablefooter();

	}

}
