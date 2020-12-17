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
	 *    'date_creation' => [ 'length' => 10, 'mandat' => true, 'default' => 'now' ],
	 *    'raison_sociale' => [ 'length' => 35, 'mandat' => true ],
	 *    'address1' => [ 'length' => 35, 'mandat' => true ],
	 *    'address2' => [ 'length' => 35, 'mandat' => false ],
	 *    'address3' => [ 'length' => 35, 'mandat' => false ],
	 *    'siret' => [ 'length' => 14, 'mandat' => false, 'default' => 'xxxxxxxxxxxx' ],
	 *    'reference_virement' => [ 'length' => 16, 'mandat' => true ],
	 *    'bic' => [ 'length' => 11, 'mandat' => false, 'default' => 'xxxxxxxx' ],
	 *    'type_num_compte' => [ 'length' => 1, 'mandat' => false, 'default' => 1 ],
	 *    'numero_compte' => [ 'length' => 34, 'mandat' => true ],
	 *    'devise_compte' => [ 'length' => 3, 'mandat' => false, 'default' => 'EUR' ],
	 *    'numero_emetteur' => [ 'length' => 16, 'mandat' => false ],
	 *    'type_num_compte_frais' => [ 'length' => 1, 'mandat' => false, 'default' => 1 ],
	 *    'numero_compte_frais' => [ 'length' => 34, 'mandat' => false ],
	 *    'devise_compte_frais' => [ 'length' => 3, 'mandat' => false, 'default' => 'EUR' ],
	 *    'type_debit' => [ 'length' => 1, 'mandat' => false, 'default' => 2 ],
	 *    'type_remise' => [ 'length' => 1, 'mandat' => false, 'default' => 1 ],
	 *    'date_execution' => [ 'length' => 10, 'mandat' => false, 'default' => 'now' ],
	 *    'devise' => [ 'length' => 3, 'mandat' => false, 'default' => 'EUR' ]
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
	 *      'type_num_compte' => [ 'length' => 1, 'mandat' => false, 'default' => 1 ],
	 *      'numero_compte' => [ 'length' => 34, 'mandat' => false ],
	 *      'raison_sociale' => [ 'length' => 35, 'mandat' => true ],
	 *      'address1' => [ 'length' => 35, 'mandat' => false ],
	 *      'address2' => [ 'length' => 35, 'mandat' => false ],
	 *      'address3' => [ 'length' => 35, 'mandat' => false ],
	 *      'id_nationale' => [ 'length' => 17, 'mandat' => false ],
	 *      'pays' => [ 'length' => 2, 'mandat' => true, 'default' => 'FR' ],
	 *      'reference' => [ 'length' => 16, 'mandat' => true ],
	 *      'qualifiant' => [ 'length' => 1, 'mandat' => false, 'default' => 'D' ],
	 *      'montant' => [ 'length' => 14, 'mandat' => true ],
	 *      'decimales' => [ 'length' => 1, 'mandat' => false, 'default' => 2 ],
	 *      'code_eco' => [ 'length' => 3, 'mandat' => false, 'default' => '010' ],
	 *      'pays_BDF' => [ 'length' => 2, 'mandat' => false, 'default' => 'FR' ],
	 *      'mode_reglement' => [ 'length' => 1, 'mandat' => false, 'default' => 0 ],
	 *      'frais' => [ 'length' => 2, 'mandat' => false, 'default' => '13' ],
	 *      'type_num_compte_frais' => [ 'length' => 1, 'mandat' => false, 'default' => 1 ],
	 *      'numero_compte_frais' => [ 'length' => 34, 'mandat' => false ],
	 *      'devise_compte_frais' => [ 'length' => 3, 'mandat' => false, 'default' => 'USD' ],
	 *      'date_execution' => [ 'length' => 10, 'mandat' => false, 'default' => 'now' ],
	 *      'devise' => [ 'length' => 3, 'mandat' => false, 'default' => 'USD' ]
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

	try
	{
		$cfonbafb320 = CFONBFactory::generateAFB( '320', [
			'emetteur' => $emetteur,
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