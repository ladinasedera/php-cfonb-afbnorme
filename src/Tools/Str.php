<?php
	/**
	 * Copyright (c) - 2020 : Ladina Sedera
	 */

	namespace Tools;

	class Str
	{
		/**
		 * @param $string
		 * @return string
		 */
		public static function sanitize ( $string )
		{
			$map = [
				// German
				'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
				// others
				'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A', 'Ă' => 'A', 'Æ' => 'A',
				'Þ' => 'B', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
				'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
				'Ñ' => 'N', 'Ń' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ø' => 'O',
				'Š' => 'S', 'Ș' => 'S', 'Ț' => 'T',
				'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ý' => 'Y',
				'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a', 'ă' => 'a', 'æ' => 'a',
				'þ' => 'b', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ƒ' => 'f',
				'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
				'ñ' => 'n', 'ń' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ø' => 'o', 'ð' => 'o',
				'ș' => 's', 'š' => 's', 'ț' => 't',
				'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ÿ' => 'y',
				'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z',
			];
			$tmp = strtoupper( strtr( $string, $map ) );
			return preg_replace( '#[^A-Z0-9"\.\)\(/ -]#', '', $tmp );
		}

		public static function remapAmount ( $amount,$decimal=2 )
		{
			$amount = (float) $amount;
			return str_replace( [ '.', ',' ], '', number_format( $amount, $decimal ) );
		}
	}