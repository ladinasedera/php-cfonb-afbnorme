# php-cfonb-afbnorme

[![Packagist](https://img.shields.io/packagist/v/ladina/php-cfonb-afbnorme.svg)](https://packagist.org/packages/ladina/php-cfonb-afbnorme)
[![PHP](https://img.shields.io/packagist/php-v/ladina/php-cfonb-afbnorme.svg)](https://packagist.org/packages/ladina/php-cfonb-afbnorme)
[![License](https://img.shields.io/packagist/l/ladina/php-cfonb-afbnorme.svg)](LICENSE)

PHP library to generate **CFONB / AFB** bank transfer files:

- **AFB160 / CFONB160** — domestic transfers (*virement domestique*) — [spec](https://fr.wikipedia.org/wiki/AFB160)
- **AFB320 / CFONB320** — international transfers (*virement étranger*) — [spec](https://fr.wikipedia.org/wiki/AFB320) · [official brochure (PDF)](https://www.cfonb.org/fichiers/20171002171114_Brochure_Rem_inf_ordres_paiement_international_320C_V4.pdf)

> The CFONB norm is a French banking standard defined by the *Comité français
> d'organisation et de normalisation bancaires* for the *Association française
> des banques* (AFB). Official site: [cfonb.org](https://www.cfonb.org/).

## Requirements

- PHP **8.1+** (tested up to PHP 8.4)

## Installation

```bash
composer require ladina/php-cfonb-afbnorme
```

## Usage

The library follows the same shape for both norms: give it an `emetteur`
(sender) and a list of `destinataires` (recipients), then build the file.

### AFB160 (domestic)

```php
use Ladina\CFONB\Factory\CFONBFactory;

$cfonb = CFONBFactory::afb160([
    'emetteur' => [
        'numero_emetteur'      => '123456',
        'date_de_valeur'       => '15JAN',
        'raison_sociale'       => 'DUPOND',
        'reference_virement'   => 'V 01/01/26',
        'numero_guichet'       => '00001',
        'numero_compte'        => '00000000001',
        'numero_etablissement' => '00001',
    ],
    'destinataires' => [
        [
            'reference_ligne'      => 'VIR.1',
            'raison_sociale'       => 'DUPONT',
            'banque'               => 'MA BANQUE',
            'numero_guichet'       => '00001',
            'numero_compte'        => '00000000001',
            'montant'              => 10000,
            'label'                => 'Retrait untel',
            'numero_etablissement' => '00001',
        ],
    ],
]);

// Get the content as a string...
$content = $cfonb->build(true);

// ...or stream it to the browser as a download:
// $cfonb->downloadFile();
```

### AFB320 (international)

```php
use Ladina\CFONB\Factory\CFONBFactory;

$cfonb = CFONBFactory::afb320([
    'emetteur' => [
        'date_creation'      => time(),
        'raison_sociale'     => 'FITIA',
        'address1'           => 'quartier',
        'reference_virement' => 'V 01/01/26',
        'numero_compte'      => 'FR7600001000010000000000123',
        'bic'                => 'PSSTFRPPMON',
    ],
    'destinataires' => [
        [
            'reference'      => 'VIR.1',
            'raison_sociale' => 'Nom 1',
            'nom_banque'     => 'bank name',
            'bic'            => 'BMOIMGMG',
            'numero_compte'  => '1111111111',
            'montant'        => 10000,
            'devise'         => 'EUR',
            'pays'           => 'FR',
            'motif'          => '/INV/raison//RFB/raison',
        ],
    ],
    // 'intermediaire' => ['bic' => 'UBHKHKHH'], // optional intermediary bank
]);

echo $cfonb->build(true);
```

Full, runnable examples live in [`examples/`](examples).

## API at a glance

```php
use Ladina\CFONB\Factory\CFONBFactory;

CFONBFactory::afb160(array $data): \Ladina\CFONB\AFB160;
CFONBFactory::afb320(array $data): \Ladina\CFONB\AFB320;
CFONBFactory::generateAFB(string $norme, array $data): \Ladina\CFONB\CFONB; // '160' | '320'

$cfonb->build();             // build and return the generator (fluent)
$cfonb->build(true);         // build and return the file content (string)
$cfonb->getContent();        // the built content
$cfonb->getLines();          // the built records as an array
$cfonb->getTotalAmount();    // sum of all recipient amounts
$cfonb->downloadFile($name); // stream the file to the browser
```

Every field is validated against the norm: mandatory fields raise an
`Ladina\CFONB\Exception\InvalidArgumentException`, values are folded to
upper-case ASCII and truncated to the norm's fixed widths. Catch any library
error with the shared `Ladina\CFONB\Exception\ExceptionInterface`.

## Field reference

The available fields (with their lengths and defaults) are declared in
[`src/AFB160.php`](src/AFB160.php) and [`src/AFB320.php`](src/AFB320.php).
Pass the string `'useDefault'` as a value to explicitly request a field's
default.

## Tests

```bash
composer install
composer test
```

## License

[MIT](LICENSE) © Ladina Sedera
