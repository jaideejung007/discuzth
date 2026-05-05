<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 *
 * Custom GeoLite2 IP Database Integration
 *
 * @package     Discuz!
 * @subpackage  IP
 * @author      jaideejung007 (Discuz! TH)
 * @version     v1.1 (R20260430)
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

// โหลดไฟล์ geoip2.phar เพียงครั้งเดียวเมื่อมีการเรียกใช้คลาสนี้
require_once DISCUZ_ROOT . './source/data/ip/geoip2.phar';

use GeoIp2\Database\Reader;

class ip_geoip2 {

	private static $instance = null;
	private $reader = null;
	private $langDict = [];
	private $currentLang = '';

	public function __construct() {
		$dbPath = DISCUZ_ROOT . './source/data/ip/GeoLite2-City.mmdb';
		if (file_exists($dbPath)) {
			// Initialize MaxMind Reader
			$this->reader = new Reader($dbPath);
		}
	}

	public function __destruct() {
		if ($this->reader !== null) {
			// ฟังก์ชันปิดการเชื่อมต่อไม่ได้ถูกกำหนดบังคับใน PHP API ของ GeoIP2 (เวอร์ชันใหม่จะจัดการทรัพยากรเอง)
			$this->reader = null;
		}
	}

	public static function getInstance() {
		if(!self::$instance) {
			try {
				self::$instance = new ip_geoip2();
			} catch (Exception $e) {
				return null;
			}
		}
		return self::$instance;
	}

	private function loadLanguage() {
		// ป้องกันกรณีที่ระบบเรียกใช้คลาสก่อนที่ฟังก์ชันของระบบหลักจะถูกโหลด
		if (!function_exists('getglobal')) {
			return;
		}

		$sys_lang = getglobal('i18n');

		// หากค่าไดนามิกว่างเปล่า ให้ Fallback กลับไปใช้ภาษาเริ่มต้นของระบบ
		if (empty($sys_lang)) {
			$sys_lang = function_exists('currentlang') ? currentlang() : '';
		}

		// หากยังว่างอยู่อีก ให้หยุดการทำงานเพื่อดึงภาษาอังกฤษดิบจากฐานข้อมูลมาแสดงแทน
		if (empty($sys_lang)) {
			return;
		}

		// ถ้าโหลดภาษาเดิมไว้แล้ว ให้ข้ามไปเลยเพื่อประหยัดทรัพยากร
		if ($this->currentLang === $sys_lang) {
			return;
		}

		$this->currentLang = $sys_lang;
		$this->langDict = []; // ล้างค่าเก่าทิ้ง

		$langPath = DISCUZ_ROOT . './source/i18n/' . $sys_lang . '/lang_geoip2.php';
		if (file_exists($langPath)) {
			include($langPath);
			// เปลี่ยนมารองรับตัวแปร $lang ตามมาตรฐาน v1.2
			if (isset($lang) && is_array($lang)) {
				$this->langDict = $lang;
			}
		}
	}

	private function translate($text) {
		return isset($this->langDict[$text]) ? $this->langDict[$text] : $text;
	}

	/**
	 * ฟังก์ชันหลักที่ระบบ Discuz! จะเรียกใช้เพื่อแปลง IP เป็นสถานที่
	 */
	public function convert($ip) {
		// --- ตรวจสอบว่า Reader พร้อมใช้งานหรือไม่ ---
		if (!$this->reader) {
			return 'Unknown';
		}

		// โหลดชุดภาษา (Lazy Loading) ก่อนทำการแปล
		$this->loadLanguage();

		try {
			$record = $this->reader->city($ip);

			// --- ดึงข้อมูลตำแหน่งในภาษาอังกฤษ (en) ---
			$cityName = $record->city->names['en'] ?? null;
			$subdivisionName = $record->mostSpecificSubdivision->names['en'] ?? null;
			$countryName = $record->country->names['en'] ?? null;

			$locationParts = [];
			
			// เรียงลำดับจาก ใหญ่ ไป เล็ก (Country, Subdivision, City)
			if ($countryName) {
				$locationParts[] = $this->translate($countryName);
			}
			if ($subdivisionName) {
				$locationParts[] = $this->translate($subdivisionName);
			}
			if ($cityName) {
				$locationParts[] = $this->translate($cityName);
			}

			$return = implode(', ', $locationParts);

		} catch (\GeoIp2\Exception\AddressNotFoundException $e) {
			// IP address not found in the database.
			$return = 'Private Network';
		} catch (Exception $e) {
			// Other errors (e.g., invalid IP address).
			$return = 'ERR';
		}

		if (empty($return)) {
			return '??';
		}

		return $return;
	}
}