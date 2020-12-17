<?php
	ini_set( 'error_reporting', E_ALL );
	ini_set( 'display_errors', 1 );
	ini_set( 'log_errors', 1 );

	require './vendor/autoload.php';

	use Ladina\Factory\CFONBFactory;

	// CFONB320 ou AFB320
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
		$cfonbafb320 = CFONBFactory::generateAFB('320',[
					'emetteur' => $emetteur,
					'destinataires' => $destinataires
				]);

		/**
		 * To Build File Content and download file
		 * @param string $filename (The name of file to download)
		 */
//		$cfonbafb320->downloadFile();

		/**
		 * Build File
		 * @param bool $get_content (false by default)
		 * $get_content = true (get the file content)
		 * @return $this|string
		 */
		$cfonbafb320->build();

		/**
		 * After build file you can get it's content
		 * OR you can this by using $cfonbafb320->build(true);
		 * @return string
		 */
		$content = $cfonbafb320->getContent();
		echo "<pre>$content</pre>";
	}
	catch ( \Exception $e )
	{
		echo $e->getMessage();
	}