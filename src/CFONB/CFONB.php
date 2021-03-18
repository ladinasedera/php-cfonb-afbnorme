<?php
	/**
	 * Copyright (c) - 2020 : Ladina Sedera
	 * http://ladina.fitiavana.mg
	 */

    /**
     * Document officielle V4 :
     * https://www.cfonb.org/fichiers/20171002171114_Brochure_Rem_inf_ordres_paiement_international_320C_V4.pdf
     *
     * https://fr.wikipedia.org/wiki/AFB320
     *
     * http://segs.free.fr/Fichiers/CFONB%20-%20Structure%20des%20fichiers%20ETEBAC3.pdf
     *
     * http://documentation.sepamail.org/images/f/fa/EBICS_IG_V1_3_Annexe_2_Nommage_Fichiers_VF_24092010CLEAN-CYV.pdf
     */
	namespace Ladina\CFONB;

	use Library\Exception\InvalidArgumentException;
	use Tools\Str;
	use Tools\TimeZone;

	/**
	 * Abstract Class CFONB
	 * To Generate CFONB File
	 * @package Ladina\CFONB
	 */
	abstract class CFONB
	{
		/**
		 * @var array
		 */
		protected $lines = [];
		/**
		 * @var string
		 */
		protected $content = '';
		/**
		 * @var array
		 */
		protected $emetteur = [];
		/**
		 * @var array
		 */
		protected $destinataire = [];
		/**
		 * @var array
		 */
		protected $intermediaire = [];
		/**
		 * @var array
		 */
		protected $data_emetteur = [];
		/**
		 * @var array
		 */
		protected $data_destinataire = [];

		protected $data_intermediaire = [];

		/**
		 * @var int
		 */
		protected $total_amount = 0;
		/**
		 * @var string
		 */
		protected $afb_norme = '320';

        /**
         * CFONB constructor.
         * @param string $afb_norme
         * @param array $data
         * @throws InvalidArgumentException
         */
		public function __construct ( $afb_norme = '320',$data = []  )
		{
			$required = [ 'emetteur', 'destinataires' ];

			foreach ( $required as $item )
			{
				if ( !isset( $data[ $item ] ) || empty( $data[ $item ] ) )
				{
					throw new InvalidArgumentException( "$item data is required" );
				}
			}

			$this->setAfbNorme( $afb_norme );
			$this->setEmetteur( $data[ 'emetteur' ] );
			$this->setDestinataire( $data[ 'destinataires' ] );

			if (isset($data['intermediaire']) && !empty($data['intermediaire']))
			{
				$this->setIntermediaire( $data[ 'intermediaire' ]);
			}

			$this->validateData();
		}

		/**
		 * Getter for content property
		 * @return string
		 */
		public function getContent ()
		{
			return $this->content;
		}

		/**
		 * Getter for emetteur property
		 * @return array
		 */
		public function getEmetteur ()
		{
			return $this->emetteur;
		}

		/**
		 * Getter for lines
		 * @return array
		 */
		public function getLines ()
		{
			return $this->lines;
		}

		/**
		 * Getter for destinataire
		 * @return array
		 */
		public function getDestinataire ()
		{
			return $this->destinataire;
		}

		/**
		 * @return array
		 */
		public function getIntermediaire ()
		{
			return $this->intermediaire;
		}

		/**
		 * Getter for total_amount
		 * @return int
		 */
		public function getTotalAmount ()
		{
			return $this->total_amount;
		}

		/**
		 * Getter for afbnorme
		 * @return string
		 */
		public function getAfbNorme ()
		{
			return $this->afb_norme;
		}

		/**
		 * Setter for content
		 * @param string $content
		 */
		public function setContent ( $content )
		{
			$this->content = $content;
		}

		/**
		 *  Setter for $destinataire
		 * @param array $destinataire
		 */
		public function setDestinataire ( $destinataire )
		{
			$this->destinataire = $destinataire;
		}

		/**
		 * @param array $intermediaire
		 */
		public function setIntermediaire ( $intermediaire )
		{
			$this->intermediaire = $intermediaire;
		}

		/**
		 * Setter for $emetteur
		 * @param array $emetteur
		 */
		public function setEmetteur ( $emetteur )
		{
			$this->emetteur = $emetteur;
		}

		/**
		 * Setter for total_amount
		 * @param int $total_amount
		 */
		public function setTotalAmount ( $total_amount )
		{
			$this->total_amount += (int) $total_amount;
		}

		/**
		 * Setter for lines
		 * @param array $lines
		 */
		public function setLines ( $lines )
		{
			$this->lines = $lines;
		}

		/**
		 * Setter for afb_norme
		 * @param string $afb_norme
		 */
		public function setAfbNorme ( $afb_norme )
		{
			$this->afb_norme = $afb_norme;
		}

		/**
		 * Used to validate emetteur and destinataire data
		 * @throws InvalidArgumentException
		 */
		public function validateData ()
		{
			$this->validateEmetteurFields( $this->emetteur );
			$this->validateDestinataireFields( $this->destinataire );

			if (!empty($this->intermediaire))
			{
				$this->validateIntermediaireFields( $this->intermediaire );
			}
		}

		/**
		 * Convert array of lines to string to build file content
		 * @param bool $add_empty_end_line
		 * @throws InvalidArgumentException
		 */
		protected function buildFileContent ( $add_empty_end_line = true )
		{
			if ( !empty( $this->lines ) )
			{
				if ( $add_empty_end_line )
				{
					end( $this->lines );
					if ( !empty( $this->lines[ key( $this->lines ) ] ) )
					{
						$this->lines[] = '';
					}
				}

				$this->content = implode( "\r\n", $this->lines );
			}
			else
			{
				throw new InvalidArgumentException( 'No lines created' );
			}
		}

		/**
		 * Build File
		 * @param bool $get_content (false by default)
		 * $get_content = true (get the file content)
		 * @return $this|string
		 */
		public function build ( $get_content = false )
		{
			$this->buildLines();

			if ( $get_content )
			{
				return $this->getContent();
			}

			return $this;
		}

		/**
		 * To Build File Content and download file
		 * @param string $filename (generate auto by default)
		 * The name of file to download
		 */
		public function downloadFile ( $filename = '' )
		{
			$this->build();
			$this->sendFile( $this->getContent(), $filename );
		}

		/**
		 * Abstract method to build each lines for each NORM (AFB320/AFB160)
		 * @return mixed
		 */
		abstract protected function buildLines ();

		/**
		 * Internal method
		 */

        /**
         * @param $prop
         * @param $field
         * @param $to_validate
         * @return false|mixed|string
         * @throws InvalidArgumentException
         */
		private function validateProps ( $prop,$field, $to_validate )
		{
			$field_value = '';
			if ( $prop[ 'required' ] )
			{
				if ( !array_key_exists( $field, $to_validate ) )
				{
					throw new InvalidArgumentException( "You must provid this mandatory field ${field}" );
				}
				else
				{
					if
					(
						isset( $prop[ 'default' ] ) &&
						isset( $to_validate[ $field ] ) && $to_validate[ $field ] === 'useDefault'
					)
					{
						$field_value = $prop[ 'default' ];
					}
					else
					{
						if ( $prop[ 'length' ] < strlen( $to_validate[ $field ] ) )
						{
							$to_validate[ $field ] = substr( $to_validate[ $field ], 0, $prop[ 'length' ] );
						}
						$field_value = $to_validate[ $field ];
					}
				}
			}
			else
			{
				if
				(
					isset( $prop[ 'default' ] ) &&
					( !isset( $to_validate[ $field ] ) || $to_validate[ $field ] === 'useDefault' )
				)
				{
					$field_value = $prop[ 'default' ];
				}
				else if ( isset( $to_validate[ $field ] ) )
				{
					if ( $prop[ 'length' ] < strlen( $to_validate[ $field ] ) )
					{
						$to_validate[ $field ] = substr( $to_validate[ $field ], 0, $prop[ 'length' ] );
					}
					$field_value = $to_validate[ $field ];
				}
				else
				{
					$field_value = '';
				}
			}

			return $field_value;
		}

		/**
		 * Validate emetteur data
		 * @param $emetteur
		 * @throws InvalidArgumentException
		 */
		private function validateEmetteurFields ( $emetteur )
		{
			foreach ( $this->emetteur_fields as $field => $prop )
			{
				$this->data_emetteur[$field] = $this->validateProps ( $prop,$field, $emetteur );
			}

			$this->sanitize_string( $this->data_emetteur );
		}

		/**
		 * Validate destinataire data
		 * @param $destinataire
		 * @throws InvalidArgumentException
		 */
		private function validateDestinataireFields ( $destinataire )
		{
			$destinataire = isset( $destinataire[ 0 ] ) ? $destinataire : [ $destinataire ];

			foreach ( $this->destinataire_fields as $field => $prop )
			{
				foreach ( $destinataire as $key => $desti )
				{
					$this->data_destinataire[ $key ][ $field ] = $this->validateProps ( $prop,$field, $desti );
				}
			}

			$this->sanitize_string( $this->data_destinataire );
		}

		private function validateIntermediaireFields ( $intermediaire )
		{
			foreach ( $this->intermediaire_fields as $field => $prop )
			{
				$this->data_intermediaire[$field] = $this->validateProps ( $prop,$field, $intermediaire );
			}

			$this->sanitize_string( $this->data_intermediaire );
		}

		/**
		 * Remove all special char from string
		 * @param $array
		 */
		private function sanitize_string ( &$array )
		{
			array_walk_recursive( $array, [ $this, 'sanitize_it' ] );
		}

		/**
		 * Remove special char
		 * @param $item
		 */
		private function sanitize_it ( &$item )
		{
			$item = Str::sanitize( $item );
		}

		/**
		 * Get month and year (french)
		 * @return string
		 */
		private function getMY ()
		{
			TimeZone::setTimeZone( 'Europe/Paris' );
			$month = date( 'n' );
			return TimeZone::getFrMonthStr( $month ) . '_' . date( 'Y' );
		}

		/**
		 * Send the file to browser as attachment
		 * @param $files_contents
		 * @param $filename
		 */
		private function sendFile ( $files_contents, $filename = '' )
		{
			if ( !$filename )
			{
				$filename = "CFONB_AFB" . $this->getAfbNorme() . "_" . $this->getMY();
			}

			header( 'Content-Type: text/plain' );
			header( 'Content-Description: File Transfer' );
			header( "Content-Disposition: attachment; filename=\"{$filename}.txt\"" );
			header( "Content-Transfer-Encoding: binary" );
			header( "Expires: 0" );
			header( "Pragma: public" );
			header( "Cache-Controlq: must-revalidate, post-check=0, pre-check=0" );

			ob_clean();
			flush();
			echo $files_contents;
			exit;
		}

	}
