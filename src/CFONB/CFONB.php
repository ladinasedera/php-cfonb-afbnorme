<?php
	/**
	 * Copyright (c) - 2020 : Ladina Sedera
	 * http://ladina.fitiavana.mg
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
		protected $lines = [];
		protected $content = '';
		protected $emetteur = [];
		protected $destinataire = [];
		protected $data_emetteur = [];
		protected $data_destinataire = [];
		protected $total_amount = 0;
		protected $afb_norme = '320';

		/**
		 * CFONB constructor.
		 * @param $emetteur
		 * @param $destinataire
		 */
		public function __construct ( $emetteur, $destinataire,$afb_norme='320' )
		{
			$this->setAfbNorme($afb_norme);
			$this->setEmetteur( $emetteur );
			$this->setDestinataire( $destinataire );
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
		public function setContent ( string $content )
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
		 * Setter for $emetteur
		 * @param array $emetteur
		 */
		public function setEmetteur ( array $emetteur )
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

				$this->content = implode( PHP_EOL, $this->lines );
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
		 * Abstract method to build each lines for each NORME (AFB320/AFB160)
		 * @return mixed
		 */
		abstract protected function buildLines ();

		/**
		 * Internal method
		 */

		/**
		 * Validate emetteur data
		 * @param $emetteur
		 * @throws InvalidArgumentException
		 */
		private function validateEmetteurFields ( $emetteur )
		{
			foreach ( $this->emetteur_fields as $field => $prop )
			{
				if ( $prop[ 'mandat' ] )
				{
					if ( !array_key_exists( $field, $emetteur ) )
					{
						throw new InvalidArgumentException( "You must provid this mandatory field ${field}" );
					}
					else
					{
						if
						(
							isset( $prop[ 'default' ] ) &&
							isset( $emetteur[ $field ] ) &&
							$emetteur[ $field ] == 'useDefault'
						)
						{
							$this->data_emetteur[ $field ] = $prop[ 'default' ];
						}
						else
						{
							if ( $prop[ 'length' ] < strlen( $emetteur[ $field ] ) )
							{
								$emetteur[ $field ] = substr( $emetteur[ $field ], 0, $prop[ 'length' ] );
							}
							$this->data_emetteur[ $field ] = $emetteur[ $field ];
						}
					}
				}
				else
				{
					if
					(
						isset( $prop[ 'default' ] ) &&
						( !isset( $emetteur[ $field ] ) ||
							$emetteur[ $field ] == 'useDefault'
						)
					)
					{
						$this->data_emetteur[ $field ] = $prop[ 'default' ];
					}
					else if ( !empty( $emetteur[ $field ] ) )
					{
						if ( $prop[ 'length' ] < strlen( $emetteur[ $field ] ) )
						{
							$emetteur[ $field ] = substr( $emetteur[ $field ], 0, $prop[ 'length' ] );
						}
						$this->data_emetteur[ $field ] = $emetteur[ $field ];
					}
					else
					{
						$this->data_emetteur[ $field ] = '';
					}
				}
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
					if ( $prop[ 'mandat' ] )
					{
						if ( !array_key_exists( $field, $desti ) )
						{
							throw new InvalidArgumentException( "You must provid this mandatory field ${field}" );
						}
						else
						{
							if
							(
								isset( $prop[ 'default' ] ) &&
								isset( $desti[ $field ] ) &&
								$desti[ $field ] == 'useDefault'
							)
							{
								$this->data_destinataire[ $key ][ $field ] = $prop[ 'default' ];
							}
							else
							{
								if ( $prop[ 'length' ] < strlen( $desti[ $field ] ) )
								{
									$desti[ $field ] = substr( $desti[ $field ], 0, $prop[ 'length' ] );
								}
								$this->data_destinataire[ $key ][ $field ] = $desti[ $field ];
							}
						}
					}
					else
					{
						if
						(
							isset( $prop[ 'default' ] ) &&
							(
								!isset( $desti[ $field ] ) || $desti[ $field ] == 'useDefault'
							)
						)
						{
							$this->data_destinataire[ $key ][ $field ] = $prop[ 'default' ];
						}
						else if ( !empty( $desti[ $field ] ) )
						{
							if ( $prop[ 'length' ] < mb_strlen( $desti[ $field ] ) )
							{
								$desti[ $field ] = substr( $desti[ $field ], 0, $prop[ 'length' ] );
							}
							$this->data_destinataire[ $key ][ $field ] = $desti[ $field ];
						}
						else
						{
							$this->data_destinataire[ $key ][ $field ] = '';
						}
					}
				}
			}

			$this->sanitize_string( $this->data_destinataire );
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
				$filename = "CFONB_AFB".$this->getAfbNorme()."_" . $this->getMY();
			}

			header( 'Content-Type: text/plain' );
			header( 'Content-Description: File Transfer' );
			header( "Content-Disposition: attachment; filename=\"{$filename}.txt\"" );
			header( "Content-Transfer-Encoding: binary" );
			header( "Expires: 0" );
			header( "Pragma: public" );
			header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );

			ob_clean();
			flush();
			echo $files_contents;
			exit;
		}

	}
