<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace admin;

use table_common_admincp_member;
use table_common_admincp_perm;
use table_common_admincp_session;
use table_common_plugin;
use table_common_member;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class class_core {
	var $core = null;
	var $script = null;

	var $userlogin = false;
	var $adminsession = [];
	var $adminuser = [];
	var $perms = null;

	var $panel = 1;

	var $isfounder = false;

	var $cpsetting = [];

	var $cpaccess = 0;

	var $sessionlife = 1800;
	var $sessionlimit = 0;

	public static function &instance() {
		static $object;
		if(empty($object)) {
			$object = new core();
		}
		return $object;
	}

	function __construct() {
		;
	}

	private function _validate() {
		global $_G;

		if($_G['config']['admincp']['validate']['method'] == 'default') {
			$realm = 'Discuz! Admincp';
			$nonce = authcode(md5($_SERVER['HTTP_USER_AGENT'].$_G['cookie']['saltkey']),
				'ENCODE', $_G['cookie']['saltkey'].substr(time(), 0, 6), 0, 0);

			if(empty($_SERVER['PHP_AUTH_DIGEST'])) {
				header('HTTP/1.1 401 Unauthorized');
				header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.$nonce.'",opaque="'.uniqid().'"');
				exit;
			}

			if(!($data = $this->_http_digest_parse($_SERVER['PHP_AUTH_DIGEST']))) {
				header('HTTP/1.1 401 Unauthorized');
				exit;
			}

			$ha1 = md5($_G['config']['admincp']['validate']['user'].':'.$realm.':'.$_G['config']['admincp']['validate']['pass']);
			$ha2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
			$valid_response = md5($ha1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$ha2);

			if($data['response'] != $valid_response || $data['nonce'] != $nonce) {
				header('HTTP/1.1 401 Unauthorized');
				exit;
			}
		} elseif($f = childfile('adminvalidate/'.$_G['config']['admincp']['validate']['method'], 'global')) {
			require_once $f;
		} else {
			if(basename($_SERVER['PHP_SELF']) == 'index.php') {
				$this->_isIllegal = true;
			}
		}
	}

	private function _http_digest_parse($txt) {
		$parts = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
		$data = array();
		$keys = implode('|', array_keys($parts));

		preg_match_all('@('.$keys.')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

		foreach($matches as $m) {
			$data[$m[1]] = $m[3] ? $m[3] : $m[4];
			unset($parts[$m[1]]);
		}

		return $parts ? false : $data;
	}

	function init() {

		if(empty($this->core) || !is_object($this->core)) {
			exit('No Discuz core found');
		}

		if(!is_dir(DISCUZ_DATA.'./template')) {
			dmkdir(DISCUZ_DATA.'./template');
		}

		if(!file_exists(DISCUZ_ROOT.'./admin.php')) {
			$this->_validate();
		}

		$this->cpsetting = $this->core->config['admincp'];
		$this->adminuser = &$this->core->var['member'];
		$this->core->var['setting']['jscachepath'] = $this->core->var['setting']['jspath'];
		$this->core->var['setting']['jspath'] = 'static/js/';

		if(!empty($_GET['qrcodeReturnCode']) && empty($this->core->var['setting']['admin_qrlogin_close'])) {
			$this->qrcodelogin();
		}

		$this->isfounder = $this->checkfounder($this->adminuser);

		$this->sessionlimit = TIMESTAMP - $this->sessionlife;

		$this->check_cpaccess();

		try {
			$this->writecplog();
		} catch (Exception $e) {
		}
	}

	function writecplog() {
		global $_G;
		$extralog = implodearray(['GET' => $_GET, 'POST' => $_POST], ['formhash', 'submit', 'addsubmit', 'admin_password', 'sid', 'action']);
		
		
		if(!empty($_G['setting']['log']['cp'])) {
			$errorlog = [
				'timestamp' => TIMESTAMP,
				'operator_username' => $_G['username'],
				'operator_adminid' => $_G['adminid'],
				'clientip' => $_G['clientip'],
				'action' => getgpc('action'),
				'extralog' => clearlogstring($extralog),
			];
			$member_log = $_G['member'];
			logger('cp', $member_log, $_G['member']['uid'], $errorlog);
		}
		
	}

	function check_cpaccess() {

		global $_G;
		$session = [];

		if(!$this->adminuser['uid']) {
			$this->cpaccess = getglobal('config/admincp/mustlogin') ? -5 : 0;
		} else {

			if(!$this->isfounder) {
				$session = table_common_admincp_member::t()->fetch($this->adminuser['uid']);
				if($session) {
					$session = array_merge($session, table_common_admincp_session::t()->fetch_session($this->adminuser['uid'], $this->panel));
				}
			} else {
				$session = table_common_admincp_session::t()->fetch_session($this->adminuser['uid'], $this->panel);
			}

			if(empty($session)) {
				$this->cpaccess = $this->isfounder ? 1 : -2;

			} elseif($_G['setting']['adminipaccess'] && !ipaccess($_G['clientip'], $_G['setting']['adminipaccess'])) {
				$this->do_user_login();

			} elseif($session && empty($session['uid'])) {
				$this->cpaccess = 1;

			} elseif($session['dateline'] < $this->sessionlimit) {
				$this->cpaccess = 1;

			} elseif($this->cpsetting['checkip'] && ($session['ip'] != $this->core->var['clientip'])) {
				$this->cpaccess = 1;
				$_G['admincp_checkip_noaccess'] = 1;

			} elseif($session['errorcount'] >= 0 && $session['errorcount'] <= 3) {
				$this->cpaccess = 2;

			} elseif($session['errorcount'] == -1) {
				$this->cpaccess = 3;

			} else {
				$this->cpaccess = -1;
			}
		}

		if($this->cpaccess == 2 || $this->cpaccess == 3) {
			if(!empty($session['customperm'])) {
				$session['customperm'] = dunserialize($session['customperm']);
			}
		}

		$this->adminsession = $session;

		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_password']) && empty($_G['config']['admincp']['qrcode_only'])) {
			if($this->cpaccess == 2) {
				$this->check_admin_login();
			} elseif($this->cpaccess == 0) {
				$this->check_user_login();
			}
		}

		if($this->cpaccess == 1) {
			table_common_admincp_session::t()->delete_session($this->adminuser['uid'], $this->panel, $this->sessionlife);
			table_common_admincp_session::t()->insert([
				'uid' => $this->adminuser['uid'],
				'adminid' => $this->adminuser['adminid'],
				'panel' => $this->panel,
				'ip' => $this->core->var['clientip'],
				'dateline' => TIMESTAMP,
				'errorcount' => 0,
				'storage' => '',
			]);
		} elseif($this->cpaccess == 3) {
			$this->load_admin_perms();
			table_common_admincp_session::t()->update_session($this->adminuser['uid'], $this->panel, ['dateline' => TIMESTAMP, 'ip' => $this->core->var['clientip'], 'errorcount' => -1]);
		}

		if($this->cpaccess != 3) {
			$this->do_user_login();
		}

	}

	function location() {
		$platform = !empty($_POST['admin_platform']) ? $_POST['admin_platform'] : PLATFORM;
		if(basename($_SERVER['PHP_SELF']) == 'index.php') {
			dheader('Location: ?app=admin&platform='.$platform.'?'.cpurl('url', ['sid']));
		} else {
			dheader('Location: '.getglobal('scheme').'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?platform='.$platform.'?'.cpurl('url', ['sid']));
		}
	}

	function check_admin_login() {
		global $_G;
		if((empty($_POST['admin_questionid']) || empty($_POST['admin_answer'])) && ($_G['config']['admincp']['forcesecques'] || $_G['group']['forcesecques'])) {
			$this->do_user_login();
		}
		loaducenter();
		$ucresult = uc_user_login($this->adminuser['uid'], $_POST['admin_password'], 1, 1, $_POST['admin_questionid'], $_POST['admin_answer'], $this->core->var['clientip']);
		if($ucresult[0] > 0) {
			table_common_admincp_session::t()->update_session($this->adminuser['uid'], $this->panel, ['dateline' => TIMESTAMP, 'ip' => $this->core->var['clientip'], 'errorcount' => -1]);
			$this->location();
		} else {
			$errorcount = $this->adminsession['errorcount'] + 1;
			table_common_admincp_session::t()->update_session($this->adminuser['uid'], $this->panel, ['dateline' => TIMESTAMP, 'ip' => $this->core->var['clientip'], 'errorcount' => $errorcount]);
		}
	}

	function check_user_login() {
		global $_G;
		if(!getglobal('config/admincp/mustlogin') && $_POST['admin_type'] > 0) {
			$user = site_userinfo();
			if($user['uid'] == $_POST['admin_type']) {
				$_POST['admin_username'] = $user['loginname'];
			}
		}

		$admin_username = isset($_POST['admin_username']) ? trim($_POST['admin_username']) : '';
		if($admin_username != '') {
			require_once libfile('function/member');
			if(logincheck($admin_username)) {
				if((empty($_POST['admin_questionid']) || empty($_POST['admin_answer'])) && ($_G['config']['admincp']['forcesecques'] || $_G['group']['forcesecques'])) {
					$this->do_user_login();
				}
				$result = userlogin($admin_username, $_POST['admin_password'], $_POST['admin_questionid'], $_POST['admin_answer'], 'username', $this->core->var['clientip']);
				if($result['status'] == 1) {
					$cpgroupid = table_common_admincp_member::t()->fetch($result['member']['uid']);
					$cpgroupid = $cpgroupid['uid'];
					if($cpgroupid || $this->checkfounder($result['member'])) {
						table_common_admincp_session::t()->insert([
							'uid' => $result['member']['uid'],
							'adminid' => $result['member']['adminid'],
							'panel' => $this->panel,
							'dateline' => TIMESTAMP,
							'ip' => $this->core->var['clientip'],
							'errorcount' => -1], false, true);

						$this->setloginstatus($result['member'], 0);
						if(getglobal('config/admincp/synclogin_front') || empty($_G['cookie']['auth'])) {
							setloginstatus($result['member'], 0);
						}
						$this->location();
					} else {
						$this->cpaccess = -2;
					}
				} else {
					loginfailed($_POST['admin_username']);
				}
			} else {
				$this->cpaccess = -4;
			}
		}
	}

	function allow($action, $operation, $do) {
		if($this->perms === null) {
			$this->load_admin_perms();
		}

		if(isset($this->perms['all'])) {
			return $this->perms['all'];
		}

		if(!empty($_POST) && !array_key_exists('_allowpost', $this->perms) && $action.'_'.$operation != 'misc_custommenu') {
			return false;
		}
		$this->perms['misc_custommenu'] = 1;

		$key = $action;
		if(isset($this->perms[$key])) {
			return $this->perms[$key];
		}
		$noplugin = $action == 'plugins' && $operation == 'config' && is_numeric($do);
		if($action == 'plugins' && !$noplugin) {
			$identifier = $_GET['identifier'] ? $_GET['identifier'] : '';
			if(!$identifier) {
				$do = $_GET['do'] ? $_GET['do'] : 0;
				if($do) {
					$plugin = table_common_plugin::t()->fetch($do);
					if($plugin) {
						$identifier = $plugin['identifier'];
					}
				}
			}
			$pmod = $_GET['pmod'] ? $_GET['pmod'] : '';
			if(isset($this->perms[$key])) {
				return $this->perms[$key];
			}
			$key = 'plugin_'.$identifier.($pmod ? ':'.$pmod : '');
			if(isset($this->perms[$key])) {
				return $this->perms[$key];
			}
			$do = $_GET['do'] ? $_GET['do'] : '';
			$key .= $do ? ':'.$do : '';
			if(isset($this->perms[$key])) {
				return $this->perms[$key];
			}
		} else {
			$opkey = $action.'_'.$operation;
			if(isset($this->perms[$opkey])) {
				return $this->perms[$opkey];
			}
			if($do != '') {
				$dokey = $action.'_'.$operation.'_'.$do;
				if(isset($this->perms[$dokey])) {
					return $this->perms[$dokey];
				}
			}
			global $_G;

			foreach([$key, $opkey, $dokey] as $k) {
				if(!empty($_G['cache']['admin']['subperms'][$k])) {
					$key = $_G['cache']['admin']['subperms'][$k];
					if(isset($this->perms[$key])) {
						return $this->perms[$key];
					}
				}
			}
		}
		return false;
	}

	function load_admin_perms() {

		$this->perms = [];
		if(!$this->isfounder) {
			if($this->adminsession['cpgroupid']) {
				foreach(table_common_admincp_perm::t()->fetch_all_by_cpgroupid($this->adminsession['cpgroupid']) as $perm) {
					if(empty($this->adminsession['customperm'])) {
						$this->perms[$perm['perm']] = true;
					} elseif(!in_array($perm['perm'], (array)$this->adminsession['customperm'])) {
						$this->perms[$perm['perm']] = true;
					}
				}
			} else {
				$this->perms['all'] = true;
			}
		} else {
			$this->perms['all'] = true;
		}
	}

	function checkfounder($user) {

		$founders = str_replace(' ', '', $this->cpsetting['founder']);
		if(!$user['uid'] || $user['groupid'] != 1 || $user['adminid'] != 1) {
			return false;
		} elseif(empty($founders)) {
			return true;
		} elseif(strexists(",$founders,", ",{$user['uid']},")) {
			return true;
		} elseif(!is_numeric($user['username']) && strexists(",$founders,", ",{$user['username']},")) {
			return true;
		} else {
			return FALSE;
		}
	}

	function do_user_login() {
		if(!empty($this->_isIllegal)) {
			header('HTTP/1.1 404 Not Found');
			exit;
		}
		require $this->admincpfile('login');
	}

	function do_admin_logout() {
		dsetcookie('adminauth');
		table_common_admincp_session::t()->delete_session($this->adminuser['uid'], $this->panel, $this->sessionlife);
	}

	function admincpfile($action) {
		$f = appfile('module/'.$action);
		return file_exists($f) ? $f : '';
	}

	function show_admincp_main() {
		$this->do_request('main');
	}

	function show_no_access() {
		cpheader();
		cpmsg('action_noaccess', '', 'error');
		cpfooter();
	}

	function do_request($action) {

		global $_G;

		$lang = lang('admincp');
		$title = 'cplog_'.getgpc('action').(getgpc('operation') ? '_'.getgpc('operation') : '');
		$operation = getgpc('operation');
		$do = getgpc('do');
		$sid = $_G['sid'];
		$isfounder = $this->isfounder;
		if($action == 'main' || $this->allow($action, $operation, $do)) {
			require_once appfile('module/'.$action);
		} else {
			cpheader();
			cpmsg('action_noaccess', '', 'error');
		}
	}

	function setloginstatus($member, $cookietime) {
		global $_G;
		$_G['uid'] = intval($member['uid']);
		$_G['username'] = $member['username'];
		$_G['adminid'] = $member['adminid'];
		$_G['groupid'] = $member['groupid'];
		$_G['formhash'] = formhash();
		$_G['session']['invisible'] = getuserprofile('invisible');
		$_G['member'] = $member;
		loadcache('usergroup_'.$_G['groupid']);

		$_authkey = getglobal('config/admincp/mustlogin') || !defined('IN_ADMINCP') ? 'auth' : 'adminauth';
		dsetcookie($_authkey, authcode("{$member['password']}\t{$member['uid']}", 'ENCODE'), $cookietime, 1, true);

		include_once libfile('function/stat');
		updatestat('login', 1);
		if(defined('IN_MOBILE')) {
			updatestat('mobilelogin', 1);
		}

		$this->qrcodenotify();
	}

	function qrcodelogin() {
		if(strlen($_GET['qrcodeReturnCode']) != 19) {
			return;
		}
		$v = \admin\class_qrcodelogin::login($_GET['qrcodeReturnCode']);
		if($v['data']['siteuniqueid'] == getglobal('setting/siteuniqueid') &&
			$v['data']['siteurl'] == getglobal('siteurl') && $v['data']['status'] >= 0) {
			$this->adminuser = table_common_member::t()->fetch($v['data']['adminUid']);
			if(!empty($this->adminuser) && $v['data']['pwdmd5'] == md5($this->adminuser['password'])) {
				$_POST['admin_username'] = $this->adminuser['username'];

				table_common_admincp_session::t()->insert([
					'uid' => $this->adminuser['uid'],
					'adminid' => $this->adminuser['uid'],
					'panel' => $this->panel,
					'dateline' => TIMESTAMP,
					'ip' => $this->core->var['clientip'],
					'errorcount' => -1], false, true);

				$this->setloginstatus($this->adminuser, 0);
			}
		}
	}

	function qrcodenotify() {
		if(empty($this->core->var['setting']['admin_qrlogin_notify'])) {
			return;
		}
		$param = [
			'adminuser' => $this->adminuser['username'],
			'adminid' => $this->adminuser['uid'],
			'siteuniqueid' => getglobal('setting/siteuniqueid'),
			'siteurl' => getglobal('siteurl'),
			'ip' => $this->core->var['clientip'],
		];
		\admin\class_qrcodelogin::notify($param);
	}
}