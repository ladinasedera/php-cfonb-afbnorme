<?php
	/**
	 * Copyright (c) - 2020 : Ladina Sedera
	 * http://ladina.fitiavana.mg
	 */

	namespace Ladina\Factory;

	use Ladina\CFONB\AFB160;
	use Ladina\CFONB\AFB320;
	use Library\Exception\InvalidArgumentException;

	/**
	 * Class CFONBFactory
	 * @package Ladina\Factory
	 */
	class CFONBFactory
	{
		/**
		 * Generate CONFB AFB
		 * @param string $afb_norme
		 * @param array $data
		 * @return AFB160|AFB320
		 * @throws InvalidArgumentException
		 */
		public static function generateAFB ( $afb_norme = '320', $data = [] )
		{
			$required = [ 'emetteur', 'destinataires' ];

			foreach ( $required as $item )
			{
				if ( !isset( $data[ $item ] ) || empty( $data[ $item ] ) )
				{
					throw new InvalidArgumentException( "$item data is required" );
				}
			}

			if ( $afb_norme === '320' )
			{
				return self::buildAFB320($afb_norme,$data );
			}

			return self::buildAFB160( $afb_norme,$data );
		}

		/**
		 * Build CFONB AFB320
		 * @param $emetteur
		 * @param $destinataires
		 * @return AFB320
		 */
		public static function buildAFB320 ( $afb_norme,$data )
		{
			return new AFB320( $afb_norme,$data );
		}

		/**
		 * Build CFONB AFB160
		 * @param $emetteur
		 * @param $destinataires
		 * @return AFB160
		 */
		public static function buildAFB160 ( $afb_norme,$data )
		{
			return new AFB160( $afb_norme,$data );
		}
	}