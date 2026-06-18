<?php

declare(strict_types=1);

/*
 * AFB320 / CFONB320 — international transfer ("virement étranger").
 *
 * Run from the project root:  php examples/generate-afb320.php
 */

require __DIR__ . '/../vendor/autoload.php';

use Ladina\CFONB\Exception\ExceptionInterface;
use Ladina\CFONB\Factory\CFONBFactory;

// Sender ("emetteur").
$emetteur = [
    'date_creation'      => time(),
    'date_execution'     => strtotime('+1 day'),
    'raison_sociale'     => 'FITIA',
    'address1'           => 'quartier',
    'address2'           => 'ville',
    'numero_emetteur'    => 'xxxxx',
    'reference_virement' => 'V ' . date('d/m/y'),
    'numero_compte'      => 'xxxxxxxxxxxxxxxxxxxxxxxxx',
    'siret'              => 'xxxxxxxxxxxxxxxx',
    'bic'                => 'PSSTFRPPMON',
];

// Recipients ("destinataires").
$destinataires = [
    [
        'reference'       => 'VIR.1',
        'raison_sociale'  => 'Nom 1',
        'address1'        => 'society_address',
        'nom_banque'      => 'bank_name',
        'bank_address'    => 'bank_address',
        'type_num_compte' => 2,
        'numero_compte'   => '1111111111',
        'bic'             => 'BMOIMGMG',
        'montant'         => 10000,
        'devise'          => 'EUR',
        'pays'            => 'FR',
        'motif'           => '/INV/raison//RFB/raison',
        'frais'           => 14,
        'code_eco'        => 'E01',
        'pays_BDF'        => '',
    ],
    [
        'reference'       => 'VIR.2',
        'raison_sociale'  => 'Nom 2',
        'address1'        => 'society_address 2',
        'nom_banque'      => 'bank_name',
        'bank_address'    => 'bank_address',
        'type_num_compte' => 1,
        'numero_compte'   => 'xxxxxxxxxxxxxxxxxxxxxxxxx',
        'bic'             => 'BMOIMGMG',
        'montant'         => 25000,
        'devise'          => 'EUR',
        'pays'            => 'FR',
        'motif'           => '/INV/raison 2//RFB/raison 2',
        'frais'           => 14,
        'code_eco'        => 'E01',
        'pays_BDF'        => '',
    ],
];

// Optional intermediary bank ("intermediaire").
$intermediaire = [
    'bic' => 'UBHKHKHH',
];

try {
    $cfonb = CFONBFactory::afb320([
        'emetteur'      => $emetteur,
        // 'intermediaire' => $intermediaire, // optional
        'destinataires' => $destinataires,
    ]);

    // Get the file content as a string...
    echo $cfonb->build(true);

    // ...or stream it to the browser as a download:
    // $cfonb->downloadFile();
} catch (ExceptionInterface $e) {
    echo $e->getMessage();
}
