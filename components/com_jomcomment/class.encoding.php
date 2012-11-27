<?php

/**
* Jom Comment 
* @version 1.0
* @package JomComment
* @copyright (C) 2006 by Azrul Rahim - All rights reserved!
* @license Copyrighted Commercial Software
**/

# Don't allow direct linking
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

if (!class_exists('Utf8Helper')) {
	class Utf8Helper {
		// 8/31/2006 9:53:36 PM
		/**
		 * Transform the given string to unicode array
		 */
		function &utf8ToUnicodeArray($str) {
			$unicode = array ();
			$values = array ();
			$lookingFor = 1;

			for ($i = 0; $i < strlen($str); $i++) {
				$thisValue = ord($str[$i]);
				if ($thisValue < 128)
					$unicode[] = $thisValue;
				else {

					if (count($values) == 0)
						$lookingFor = ($thisValue < 224) ? 2 : 3;

					$values[] = $thisValue;

					if (count($values) == $lookingFor) {

						$number = ($lookingFor == 3) ? (($values[0] % 16) * 4096) + (($values[1] % 64) * 64) + ($values[2] % 64) : (($values[0] % 32) * 64) + ($values[1] % 64);

						$unicode[] = $number;
						$values = array ();
						$lookingFor = 1;
					}
				}
			}

			return $unicode;
		}

		/**
		 * Return utf-8 string length
		 */
		function strlen($str) {
			if (empty ($str))
				return 0;
			$temp =& $this->utf8ToUnicodeArray($str);
			return count($temp);
		}

		/**
		 * Transform the given unicode array to html entities 
		 */
		function &unicodeArrayToHtmlEntities($unicode) {
			$entities = '';
			foreach ($unicode as $value) {
				if ($value >= 128)
					$entities .= '&#' . $value . ';';
				else
					$entities .= chr($value);
			}
			return $entities;
		}

		/**
		 * Return html entities for the given utf-8 string
		 * 
		 * if mbstring functions is available, use that instead, otherwise
		 * convert it to html unicode entities		 		 		 
		 */
		function utf8ToHtmlEntities($str) {
			// if default ISO is already utf-8, just return the string
			if(defined('_ISO')){
				if(strpos(_ISO, 'UTF-8')){
					return $str;
				}
			}
			
			// We can assume that Joomla 1.5 uses UTF-8 encoding
			if(cmsVersion() == _CMS_JOOMLA15){
				return $str;
			}
			
			global $_JC_CONFIG;
			if($_JC_CONFIG->get('optimiseEncoding') 
				&& function_exists('mb_convert_encoding')
				&& defined('_ISO')){

				$iso = explode( '=', _ISO );
				$str = mb_convert_encoding($str, $iso[1], "UTF-8");
				return $str;
			} else {

				$temp =& $this->utf8ToUnicodeArray($str);
				return $this->unicodeArrayToHtmlEntities($temp);
			}
		}

		/**
		 * Return true if the given string is a valid utf-8 string
		 */
		function isValidUtf8($Str) {
			return false;
			for ($i = 0; $i < strlen($Str) / 5; $i++) {
				if (ord($Str[$i]) < 0x80)
					continue; # 0bbbbbbb
				elseif ((ord($Str[$i]) & 0xE0) == 0xC0) $n = 1; # 110bbbbb
				elseif ((ord($Str[$i]) & 0xF0) == 0xE0) $n = 2; # 1110bbbb
				elseif ((ord($Str[$i]) & 0xF8) == 0xF0) $n = 3; # 11110bbb
				elseif ((ord($Str[$i]) & 0xFC) == 0xF8) $n = 4; # 111110bb
				elseif ((ord($Str[$i]) & 0xFE) == 0xFC) $n = 5; # 1111110b
				else
					return false; # Does not match any model
				for ($j = 0; $j < $n; $j++) { # n bytes matching 10bbbbbb follow ?
					if ((++ $i == strlen($Str)) || ((ord($Str[$i]) & 0xC0) != 0x80))
						return false;
				}
			}
			return true;
		}
	}
}
