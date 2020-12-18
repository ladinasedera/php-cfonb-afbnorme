<?php
	ini_set( 'error_reporting', E_ALL );
	ini_set( 'display_errors', 1 );
	ini_set( 'log_errors', 1 );

	require './vendor/autoload.php';

	use Ladina\Factory\CFONBFactory;

	// CFONB320 ou AFB320

	/**
	 * Emetteur possible field
	 * protected $emetteur_fields = [
	 *    'date_creation' => [ 'length' => 10, 'required' => true, 'default' => 'now' ],
	 *    'raison_sociale' => [ 'length' => 35, 'required' => true ],
	 *    'address1' => [ 'length' => 35, 'required' => true ],
	 *    'address2' => [ 'length' => 35, 'required' => false ],
	 *    'address3' => [ 'length' => 35, 'required' => false ],
	 *    'siret' => [ 'length' => 14, 'required' => false, 'default' => 'xxxxxxxxxxxx' ],
	 *    'reference_virement' => [ 'length' => 16, 'required' => true ],
	 *    'bic' => [ 'length' => 11, 'required' => false, 'default' => 'xxxxxxxx' ],
	 *    'type_num_compte' => [ 'length' => 1, 'required' => false, 'default' => 1 ],
	 *    'numero_compte' => [ 'length' => 34, 'required' => true ],
	 *    'devise_compte' => [ 'length' => 3, 'required' => false, 'default' => 'EUR' ],
	 *    'numero_emetteur' => [ 'length' => 16, 'required' => false ],
	 *    'type_num_compte_frais' => [ 'length' => 1, 'required' => false, 'default' => 1 ],
	 *    'numero_compte_frais' => [ 'length' => 34, 'required' => false ],
	 *    'devise_compte_frais' => [ 'length' => 3, 'required' => false, 'default' => 'EUR' ],
	 *    'type_debit' => [ 'length' => 1, 'required' => false, 'default' => 2 ],
	 *    'type_remise' => [ 'length' => 1, 'required' => false, 'default' => 1 ],
	 *    'date_execution' => [ 'length' => 10, 'required' => false, 'default' => 'now' ],
	 *    'devise' => [ 'length' => 3, 'required' => false, 'default' => 'EUR' ]
	 * ];
	 */
	$emetteur = [
		'date_creation' => time(),
		'date_execution' => strtotime( '+1 day' ),
		'address1' => 'quartier',
		'address2' => 'ville',
		'numero_emetteur' => 'xxxxx',
		'raison_sociale' => 'FITIA',
		'reference_virement' => 'V ' . date( 'd/m/y' ),
		'numero_compte' => 'xxxxxxxxxxxxxxxxxxxxxxxxx',
		'siret' => 'xxxxxxxxxxxxxxxx',
		'bic' => 'PSSTFRPPMON'
	];

	/**
	 * Distinataire possible field
	 * protected $destinataire_fields = [
	 *      'type_num_compte' => [ 'length' => 1, 'required' => false, 'default' => 1 ],
	 *      'numero_compte' => [ 'length' => 34, 'required' => false ],
	 *      'nom_banque' => [ 'length' => 34, 'required' => false ],
	 *      'raison_sociale' => [ 'length' => 35, 'required' => true ],
	 *      'address1' => [ 'length' => 35, 'required' => false ],
	 *      'address2' => [ 'length' => 35, 'required' => false ],
	 *      'address3' => [ 'length' => 35, 'required' => false ],
	 *      'id_nationale' => [ 'length' => 9, 'required' => false ],
	 *      'qualifiant_address' => [ 'length' => 3, 'required' => false ],
	 *      'pays' => [ 'length' => 2, 'required' => true, 'default' => 'FR' ],
	 *      'reference' => [ 'length' => 16, 'required' => true ],
	 *      'qualifiant' => [ 'length' => 1, 'required' => false, 'default' => 'D' ],
	 *      'montant' => [ 'length' => 14, 'required' => true ],
	 *      'decimales' => [ 'length' => 1, 'required' => false, 'default' => 2 ],
	 *      'code_eco' => [ 'length' => 3, 'required' => false, 'default' => '010' ],
	 *      'pays_BDF' => [ 'length' => 2, 'required' => false, 'default' => 'FR' ],
	 *      'mode_reglement' => [ 'length' => 1, 'required' => false, 'default' => 0 ],
	 *      'frais' => [ 'length' => 2, 'required' => false, 'default' => '13' ],
	 *      'type_num_compte_frais' => [ 'length' => 1, 'required' => false, 'default' => 1 ],
	 *      'numero_compte_frais' => [ 'length' => 34, 'required' => false ],
	 *      'devise_compte_frais' => [ 'length' => 3, 'required' => false, 'default' => 'USD' ],
	 *      'date_execution' => [ 'length' => 10, 'required' => false, 'default' => 'now' ],
	 *      'devise' => [ 'length' => 3, 'required' => false, 'default' => 'USD' ]
	 * ];
	 */
	$destinataires = [
		[
			'reference' => 'VIR.1',
			'raison_sociale' => 'MILOU',
			'numero_compte' => 'xxxxxxxxxxxxxxxxxxxxxxxxx',
			'montant' => 10000,
			'pays' => 'FR'
		],
		[
			'reference' => 'VIR.2',
			'raison_sociale' => 'TINTIN',
			'numero_compte' => 'xxxxxxxxxxxxxxxxxxxxxxxxx',
			'montant' => 25000,
			'pays' => 'FR'
		]
	];

	/**
	 *protected $intermediaire_fields = [
	 *  'nom_banque' => [ 'length' => 140, 'required' => false ],
	 *  'bic' => [ 'length' => 11, 'required' => false ], // BIC
	 *  'pays' => [ 'length' => 2, 'required' => false ],
	 * ];
	 */

	$intermediaire = [
		'bic' => 'UBHKHKHH',
	];

	try
	{
		$cfonbafb320 = CFONBFactory::generateAFB( '320', [
			'emetteur' => $emetteur,
			'intermediaire' => $intermediaire, // Optional
			'destinataires' => $destinataires
		] );

		/**
		 * To Build File Content and download file
		 * @param string $filename (The name of file to download)
		 */
		$cfonbafb320->downloadFile();

		/**
		 * Build File
		 * @param bool $get_content (false by default)
		 * $get_content = true (get the file content)
		 * @return $this|string
		 */
//		$cfonbafb320->build();

		/**
		 * After build file you can get it's content
		 * OR you can this by using $cfonbafb320->build(true);
		 * @return string
		 */
//		$content = $cfonbafb320->getContent();
//		echo "<pre>$content</pre>";
	}
	catch ( \Exception $e )
	{
		echo $e->getMessage();
	}