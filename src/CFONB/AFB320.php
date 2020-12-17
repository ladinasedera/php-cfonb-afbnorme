<?php
	/**
	 * Copyright (c) - 2020 : Ladina Sedera
	 * http://ladina.fitiavana.mg
	 */

	namespace Ladina\CFONB;

	/**
	 * Class AFB320
	 * @package Ladina\CFONB
	 */
	class AFB320 extends CFONB
	{
		/**
		 * @var array[]
		 */
		protected $emetteur_fields = [
			'date_creation' => [ 'length' => 10, 'mandat' => true, 'default' => 'now' ],
			'raison_sociale' => [ 'length' => 35, 'mandat' => true ],
			'address1' => [ 'length' => 35, 'mandat' => true ],
			'address2' => [ 'length' => 35, 'mandat' => false ],
			'address3' => [ 'length' => 35, 'mandat' => false ],
			'siret' => [ 'length' => 14, 'mandat' => false, 'default' => 'xxxxxxxxxxxx' ],
			'reference_virement' => [ 'length' => 16, 'mandat' => true ],
			'bic' => [ 'length' => 11, 'mandat' => false, 'default' => 'xxxxxxxx' ],
			'type_num_compte' => [ 'length' => 1, 'mandat' => false, 'default' => 1 ],
			'numero_compte' => [ 'length' => 34, 'mandat' => true ],
			'devise_compte' => [ 'length' => 3, 'mandat' => false, 'default' => 'EUR' ],
			'numero_emetteur' => [ 'length' => 16, 'mandat' => false ],
			'type_num_compte_frais' => [ 'length' => 1, 'mandat' => false, 'default' => 1 ],
			'numero_compte_frais' => [ 'length' => 34, 'mandat' => false ],
			'devise_compte_frais' => [ 'length' => 3, 'mandat' => false, 'default' => 'EUR' ],
			'type_debit' => [ 'length' => 1, 'mandat' => false, 'default' => 2 ],
			'type_remise' => [ 'length' => 1, 'mandat' => false, 'default' => 1 ],
			'date_execution' => [ 'length' => 10, 'mandat' => false, 'default' => 'now' ],
			'devise' => [ 'length' => 3, 'mandat' => false, 'default' => 'EUR' ]
		];

		/**
		 * @var array[]
		 */
		protected $destinataire_fields = [
			'type_num_compte' => [ 'length' => 1, 'mandat' => false, 'default' => 1 ],
			'numero_compte' => [ 'length' => 34, 'mandat' => false ],
			'raison_sociale' => [ 'length' => 35, 'mandat' => true ],
			'address1' => [ 'length' => 35, 'mandat' => false ],
			'address2' => [ 'length' => 35, 'mandat' => false ],
			'address3' => [ 'length' => 35, 'mandat' => false ],
			'id_nationale' => [ 'length' => 17, 'mandat' => false ],
			'pays' => [ 'length' => 2, 'mandat' => true, 'default' => 'FR' ],
			'reference' => [ 'length' => 16, 'mandat' => true ],
			'qualifiant' => [ 'length' => 1, 'mandat' => false, 'default' => 'D' ],
			'montant' => [ 'length' => 14, 'mandat' => true ],
			'decimales' => [ 'length' => 1, 'mandat' => false, 'default' => 2 ],
			'code_eco' => [ 'length' => 3, 'mandat' => false, 'default' => '010' ],
			'pays_BDF' => [ 'length' => 2, 'mandat' => false, 'default' => 'FR' ],
			'mode_reglement' => [ 'length' => 1, 'mandat' => false, 'default' => 0 ],
			'frais' => [ 'length' => 2, 'mandat' => false, 'default' => '13' ],
			'type_num_compte_frais' => [ 'length' => 1, 'mandat' => false, 'default' => 1 ],
			'numero_compte_frais' => [ 'length' => 34, 'mandat' => false ],
			'devise_compte_frais' => [ 'length' => 3, 'mandat' => false, 'default' => 'USD' ],
			'date_execution' => [ 'length' => 10, 'mandat' => false, 'default' => 'now' ],
			'devise' => [ 'length' => 3, 'mandat' => false, 'default' => 'USD' ]
		];

		/**
		 * @var int
		 */
		private $number_of_sequence = 1;

		/**
		 * Getter for number_of_sequence
		 * @return int
		 */
		public function getNumberOfSequence ()
		{
			return $this->number_of_sequence;
		}

		/**
		 * Setter for number_of_sequence
		 * @param int $number_of_sequence
		 */
		public function setNumberOfSequence ()
		{
			$this->number_of_sequence++;
		}

		/**
		 * Build line for AFB320
		 * @return mixed|void
		 * @throws \Library\Exception\InvalidArgumentException
		 */
		public function buildLines ()
		{
			$this->lines[] = $this->getHeaderLine();
			$this->setNumberOfSequence();
			foreach ( $this->data_destinataire as $desti )
			{
				$this->lines[] = $this->getDestinataireLine( $desti );
			}
			$this->lines[] = $this->getFooterLine();
			$this->buildFileContent();
		}

		/**
		 * Generate header for the file
		 * @return string
		 */
		public function getHeaderLine ()
		{
			$date_creation = $this->data_emetteur[ 'date_creation' ] == 'NOW'
				? time() : $this->data_emetteur[ 'date_creation' ];

			$date_execution = $this->data_emetteur[ 'date_execution' ] == 'NOW'
				? time() : $this->data_emetteur[ 'date_execution' ];

			$numero_compte_frais = empty( $this->data_emetteur[ 'numero_compte_frais' ] )
				? null : $this->data_emetteur[ 'type_num_compte_frais' ];

			$remise_type = $this->data_emetteur[ 'type_remise' ];

			$type_remise = ( $remise_type == 1 || $remise_type == 2 ) ? date( 'Ymd', $date_execution ) : null;

			$devise = $this->data_emetteur[ 'devise' ];

			return '03' . 'PI' .
				sprintf( '%06s', $this->number_of_sequence ) .
				date( 'Ymd', $date_creation ) .
				sprintf( '%-35s', $this->data_emetteur[ 'raison_sociale' ] ) .
				sprintf( '%-35s', $this->data_emetteur[ 'address1' ] ) .
				sprintf( '%-35s', $this->data_emetteur[ 'address2' ] ) .
				sprintf( '%-35s', $this->data_emetteur[ 'address3' ] ) .
				sprintf( '%-14s', $this->data_emetteur[ 'siret' ] ) .
				sprintf( '%-16s', $this->data_emetteur[ 'reference_virement' ] ) .
				sprintf( '%-11s', $this->data_emetteur[ 'bic' ] ) .
				sprintf( '%-1s', $this->data_emetteur[ 'type_num_compte' ] ) .
				sprintf( '%-34s', $this->data_emetteur[ 'numero_compte' ] ) .
				sprintf( '%-3s', $this->data_emetteur[ 'devise_compte' ] ) .
				sprintf( '%-16s', $this->data_emetteur[ 'numero_emetteur' ] ) .
				sprintf( '%-1s', $numero_compte_frais ) .
				sprintf( '%-34s', $this->data_emetteur[ 'numero_compte_frais' ] ) .
				sprintf( '%-3s', $this->data_emetteur[ 'devise_compte_frais' ] ) .
				sprintf( '%16s', null ) .
				sprintf( '%-1s', $this->data_emetteur[ 'type_debit' ] ) .
				sprintf( '%-1s', $this->data_emetteur[ 'type_remise' ] ) .
				sprintf( '%-8s', $type_remise ) .
				sprintf( '%-3s', ( $remise_type == 1 || $remise_type == 3 ) ? $devise : null );
		}

		/**
		 * Generate all destinataire for the file
		 * @param $desti
		 * @return string
		 */
		public function getDestinataireLine ( $desti )
		{
			$remise_type = $this->data_emetteur[ 'type_remise' ];
			$date_execution = $desti[ 'date_execution' ] == 'NOW' ? time() : $desti[ 'date_execution' ];
			$numero_compte_frais = $desti[ 'numero_compte_frais' ];

			$destiLines = '04' . 'PI' .
				sprintf( '%06s', $this->number_of_sequence ) .
				sprintf( '%-1s', $desti[ 'type_num_compte' ] ) .
				sprintf( '%-34s', $desti[ 'numero_compte' ] ) .
				sprintf( '%-35s', $desti[ 'raison_sociale' ] ) .
				sprintf( '%-35s', $desti[ 'address1' ] ) .
				sprintf( '%-35s', $desti[ 'address2' ] ) .
				sprintf( '%-35s', $desti[ 'address3' ] ) .
				sprintf( '%-17s', $desti[ 'id_nationale' ] ) .
				sprintf( '%-2s', $desti[ 'pays' ] ) .
				sprintf( '%-16s', $desti[ 'reference' ] ) .
				sprintf( '%-1s', $desti[ 'qualifiant' ] ) .
				sprintf( '%4s', null ) .
				sprintf( '%014s', $desti[ 'montant' ] ) .
				sprintf( '%1s', $desti[ 'decimales' ] ) .
				sprintf( '%1s', null ) .
				sprintf( '%-3s', $desti[ 'code_eco' ] ) .
				sprintf( '%-2s', $desti[ 'pays_BDF' ] ) .
				sprintf( '%-1s', $desti[ 'mode_reglement' ] ) .
				sprintf( '%-2s', $desti[ 'frais' ] ) .
				sprintf( '%-1s', empty( $numero_compte_frais ) ? null : $numero_compte_frais ) .
				sprintf( '%-34s', $desti[ 'numero_compte_frais' ] ) .
				sprintf( '%-3s', $desti[ 'devise_compte_frais' ] ) .
				sprintf( '%22s', null ) .
				sprintf( '%-8s', ( $remise_type == 3 || $remise_type == 4 ) ? date( 'Ymd', $date_execution ) : null ) .
				sprintf( '%-3s', ( $remise_type == 2 || $remise_type == 4 ) ? $desti[ 'devise' ] : null );

			$this->setNumberOfSequence();
			$this->setTotalAmount( $desti[ 'montant' ] );

			return $destiLines;
		}

		/**
		 * Generate footer for the file
		 * @return string
		 */
		public function getFooterLine ()
		{
			$totalAmount = $this->getTotalAmount();
			$date_creation = $this->data_emetteur[ 'date_creation' ] == 'NOW'
				? time() : (int) $this->data_emetteur[ 'date_creation' ];

			return '08' . 'PI' .
				sprintf( '%06s', $this->number_of_sequence ) .
				date( 'Ymd', $date_creation ) .
				sprintf( '%140s', null ) .
				sprintf( '%-14s', $this->data_emetteur[ 'siret' ] ) .
				sprintf( '%-16s', $this->data_emetteur[ 'reference_virement' ] ) .
				sprintf( '%11s', null ) .
				sprintf( '%-1s', $this->data_emetteur[ 'type_num_compte' ] ) .
				sprintf( '%-34s', $this->data_emetteur[ 'numero_compte' ] ) .
				sprintf( '%-3s', $this->data_emetteur[ 'devise_compte' ] ) .
				sprintf( '%-16s', $this->data_emetteur[ 'numero_emetteur' ] ) .
				sprintf( '%018s', $totalAmount ) .
				sprintf( '%49s', null );
		}
	}