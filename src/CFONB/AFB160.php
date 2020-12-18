<?php
	/**
	 * Copyright (c) - 2020 : Ladina Sedera
	 * http://ladina.fitiavana.mg
	 */

	namespace Ladina\CFONB;

	/**
	 * Class AFB160
	 * @package Ladina\CFONB
	 */
	class AFB160 extends CFONB
	{
		/**
		 * @var array[]
		 */
		protected $emetteur_fields = [
			'numero_emetteur' => [ 'length' => 6, 'required' => true ],
			'date_de_valeur' => [ 'length' => 5, 'required' => true, 'default' => 'now' ],
			'raison_sociale' => [ 'length' => 24, 'required' => true ],
			'reference_virement' => [ 'length' => 11, 'required' => true ],
			'numero_guichet' => [ 'length' => 5, 'required' => true ],
			'numero_compte' => [ 'length' => 11, 'required' => true ],
			'numero_etablissement' => [ 'length' => 5, 'required' => true ]
		];

		/**
		 * @var array[]
		 */
		protected $destinataire_fields = [
			'reference_ligne' => [ 'length' => 12, 'required' => true ],
			'raison_sociale' => [ 'length' => 24, 'required' => true ],
			'banque' => [ 'length' => 20, 'required' => true ],
			'numero_guichet' => [ 'length' => 5, 'required' => true ],
			'numero_compte' => [ 'length' => 11, 'required' => true ],
			'montant' => [ 'length' => 16, 'required' => true ],
			'label' => [ 'length' => 31, 'required' => true ],
			'numero_etablissement' => [ 'length' => 5, 'required' => true ]
		];

		/**
		 * Build line for AFB160
		 * @return mixed|void
		 * @throws \Library\Exception\InvalidArgumentException
		 */
		public function buildLines ()
		{
			$this->lines[] = $this->getHeaderLine();
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
			return '0302' .
				sprintf( '%8s', null ) .
				$this->data_emetteur[ 'numero_emetteur' ] .
				sprintf( '%7s', null ) .
				$this->data_emetteur[ 'date_de_valeur' ] .
				sprintf( '%-24s', $this->data_emetteur[ 'raison_sociale' ] ) .
				sprintf( '%-11s', $this->data_emetteur[ 'reference_virement' ] ) .
				sprintf( '%15s', null ) .
				'E' .
				sprintf( '%5s', null ) .
				$this->data_emetteur[ 'numero_guichet' ] .
				$this->data_emetteur[ 'numero_compte' ] .
				sprintf( '%47s', null ) .
				$this->data_emetteur[ 'numero_etablissement' ] .
				sprintf( '%5s', null );
		}

		/**
		 * Generate all destinataire for the file
		 * @param $desti
		 * @return string
		 */
		public function getDestinataireLine ( $desti )
		{
			$destinataireLines = '0602' .
				sprintf( '%8s', null ) .
				$this->data_emetteur[ 'numero_emetteur' ] .
				sprintf( '%-12s', $desti[ 'reference_ligne' ] ) .
				sprintf( '%-24s', $desti[ 'raison_sociale' ] ) .
				sprintf( '%-20s', $desti[ 'banque' ] ) .
				sprintf( '%12s', null ) .
				$desti[ 'numero_guichet' ] .
				$desti[ 'numero_compte' ] .
				sprintf( '%016s', $desti[ 'montant' ] ) .
				sprintf( '%-31s', $desti[ 'label' ] ) .
				$desti[ 'numero_etablissement' ] .
				sprintf( '%5s', null );

			$this->setTotalAmount( $desti[ 'montant' ] );
			return $destinataireLines;
		}

		/**
		 *  Generate footer for the file
		 * @return string
		 */
		public function getFooterLine ()
		{
			$totalAmount = $this->getTotalAmount();
			return '0802' .
				sprintf( '%8s', null ) .
				$this->data_emetteur[ 'numero_emetteur' ] .
				sprintf( '%84s', null ) .
				sprintf( '%016s', $totalAmount ) .
				sprintf( '%42s', null );
		}

	}

