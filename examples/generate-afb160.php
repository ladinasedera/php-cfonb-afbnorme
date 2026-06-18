<?php

declare(strict_types=1);

/*
 * AFB160 / CFONB160 — domestic transfer ("virement domestique").
 *
 * Run from the project root:  php examples/generate-afb160.php
 */

require __DIR__ . '/../vendor/autoload.php';

use Ladina\CFONB\Exception\ExceptionInterface;
use Ladina\CFONB\Factory\CFONBFactory;

// Sender ("emetteur").
$emetteur = [
    'numero_emetteur'      => 'xxxxxx',
    'date_de_valeur'       => '15JAN',
    'raison_sociale'       => 'DUPOND',
    'reference_virement'   => 'V ' . date('d/m/y'),
    'numero_guichet'       => 'xxxxx',
    'numero_compte'        => 'xxxxxxxxxxx',
    'numero_etablissement' => 'xxxxx',
];

// Recipients ("destinataires").
$destinataires = [
    [
        'reference_ligne'      => 'VIR.1',
        'raison_sociale'       => 'DUPONT',
        'banque'               => 'XXXX',
        'numero_guichet'       => 'xxxxx',
        'numero_compte'        => 'xxxxxxxxxxx',
        'montant'              => 10000,
        'label'                => 'Retrait untel',
        'numero_etablissement' => 'xxxxx',
    ],
    [
        'reference_ligne'      => 'VIR.2',
        'raison_sociale'       => 'TOURNESOL',
        'banque'               => 'XXXXXX',
        'numero_guichet'       => 'xxxxx',
        'numero_compte'        => 'xxxxxxxxxxxx',
        'montant'              => 25000,
        'label'                => 'Autre Retrait',
        'numero_etablissement' => 'xxxxx',
    ],
];

try {
    $cfonb = CFONBFactory::afb160([
        'emetteur'      => $emetteur,
        'destinataires' => $destinataires,
    ]);

    // Get the file content as a string...
    echo $cfonb->build(true);

    // ...or stream it to the browser as a download:
    // $cfonb->downloadFile();
} catch (ExceptionInterface $e) {
    echo $e->getMessage();
}
