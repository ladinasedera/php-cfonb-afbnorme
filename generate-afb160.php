<?php
	ini_set( 'error_reporting', E_ALL );
	ini_set( 'display_errors', 1 );
	ini_set( 'log_errors', 1 );

	require './vendor/autoload.php';

	use Ladina\Factory\CFONBFactory;

	// CFONB160 OU AFB160
	$emetteur = [
		'numero_emetteur' => 'xxx',
		'date_de_valeur' => strtotime( '+1 day' ),
		'raison_sociale' => 'DUPOND',
		'reference_virement' => 'V ' . date( 'd/m/y' ),
		'numero_guichet' => 'xxxxxxx',
		'numero_compte' => 'xxxxxxxxxxx',
		'numero_etablissement' => 'xxxx'
	];

	$destinataires = [
		[
			'reference_ligne' => 'VIR.1',
			'raison_sociale' => 'DUPONT',
			'banque' => 'XXXX',
			'numero_guichet' => 'xxxx',
			'numero_compte' => 'xxxxxxxxxxx',
			'montant' => 10000,
			'label' => 'Retrait untel',
			'numero_etablissement' => 'xxxxx'
		],
		[
			'reference_ligne' => 'VIR.2',
			'raison_sociale' => 'TOURNESOL',
			'banque' => 'XXXXXX',
			'numero_guichet' => 'xxxxxxx',
			'numero_compte' => 'xxxxxxxxxxxx',
			'montant' => 25000,
			'label' => 'Autre Retrait',
			'numero_etablissement' => 'xxxxx'
		]
	];

	try
	{
		$cfonbafb160 = CFONBFactory::generateAFB( '160', [
			'emetteur' => $emetteur,
			'destinataires' => $destinataires
		] );

		/**
		 * To Build File Content and download file
		 * @param string $filename (The name of file to download)
		 */
//		$cfonbafb160->downloadFile();

		/**
		 * Build File
		 * @param bool $get_content (false by default)
		 * $get_content = true (get the file content)
		 * @return $this|string
		 */
		$cfonbafb160->build();

		/**
		 * After build file you can get it's content
		 * OR you can this by using $cfonbafb160->build(true);
		 * @return string
		 */
		$content = $cfonbafb160->getContent();
		echo "<pre>$content</pre>";
	}
	catch ( \Exception $e )
	{
		echo $e->getMessage();
	}