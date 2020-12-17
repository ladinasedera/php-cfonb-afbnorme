<?php
	/**
	 * Copyright (c) - 2020 : Ladina Sedera
	 */

	namespace Tools;

	class TimeZone
	{
		/**
		 * @param string $timezone
		 */
		public static function setTimeZone ( $timezone = "UTC" )
		{
			$timezones = self::getAllTimezones();
			if ( $timezone != 'UTC' && in_array( $timezone, $timezones ) )
			{
				date_default_timezone_set( $timezone );
			}
		}

		/**
		 * @param $month
		 * @return string
		 */
		public static function getFrMonthStr ( $month )
		{
			switch ( $month )
			{
				case 1:
					return "Janvier";
					break;

				case 2:
					return "Février";
					break;

				case 3:
					return "Mars";
					break;

				case 4:
					return "Avril";
					break;

				case 5:
					return "Mai";
					break;

				case 6:
					return "Juin";
					break;

				case 7:
					return "Juillet";
					break;

				case 8:
					return "Août";
					break;

				case 9:
					return "Septembre";
					break;

				case 10:
					return "Octobre";
					break;

				case 11:
					return "Novembre";
					break;

				case 12:
					return "Décembre";
					break;
			}

			return '';
		}

		/**
		 * @return array|false
		 */
		private static function getAllTimezones ()
		{
			return timezone_identifiers_list();
		}

	}