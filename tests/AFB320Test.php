<?php

declare(strict_types=1);

namespace Ladina\CFONB\Tests;

use Ladina\CFONB\AFB320;
use Ladina\CFONB\Exception\InvalidArgumentException;
use Ladina\CFONB\Factory\CFONBFactory;
use PHPUnit\Framework\TestCase;

final class AFB320Test extends TestCase
{
    protected function setUp(): void
    {
        date_default_timezone_set('UTC');
    }

    private function emetteur(): array
    {
        return [
            'date_creation'      => 1750000000,
            'date_execution'     => 1750086400,
            'address1'           => 'quartier',
            'address2'           => 'ville',
            'numero_emetteur'    => 'xxxxx',
            'raison_sociale'     => 'FITIA',
            'reference_virement' => 'V 01/01/26',
            'numero_compte'      => 'xxxxxxxxxxxxxxxxxxxxxxxxx',
            'siret'              => 'xxxxxxxxxxxxxxxx',
            'bic'                => 'PSSTFRPPMON',
        ];
    }

    private function destinataires(): array
    {
        return [
            [
                'reference'      => 'VIR.1',
                'raison_sociale' => 'Nom 1',
                'address1'       => 'society_address',
                'nom_banque'     => 'bank_name',
                'bank_address'   => 'bank_address',
                'type_num_compte' => 2,
                'numero_compte'  => '1111111111',
                'bic'            => 'BMOIMGMG',
                'montant'        => 10000,
                'devise'         => 'EUR',
                'pays'           => 'FR',
                'motif'          => '/INV/raison//RFB/raison',
                'frais'          => 14,
                'code_eco'       => 'E01',
                'pays_BDF'       => '',
            ],
            [
                'reference'      => 'VIR.2',
                'raison_sociale' => 'Nom 2',
                'address1'       => 'society_address 2',
                'nom_banque'     => 'bank_name',
                'bank_address'   => 'bank_address',
                'type_num_compte' => 1,
                'numero_compte'  => 'xxxxxxxxxxxxxxxxxxxxxxxxx',
                'bic'            => 'BMOIMGMG',
                'montant'        => 25000,
                'devise'         => 'EUR',
                'pays'           => 'FR',
                'motif'          => '/INV/raison 2//RFB/raison 2',
                'frais'          => 14,
                'code_eco'       => 'E01',
                'pays_BDF'       => '',
            ],
        ];
    }

    public function testItGeneratesTheExpectedFile(): void
    {
        $content = CFONBFactory::afb320([
            'emetteur'      => $this->emetteur(),
            'intermediaire' => ['bic' => 'UBHKHKHH'],
            'destinataires' => $this->destinataires(),
        ])->build(true);

        self::assertSame(
            file_get_contents(__DIR__ . '/fixtures/afb320.txt'),
            $content
        );
    }

    public function testEveryRecordIsExactly320CharactersWide(): void
    {
        $cfonb = CFONBFactory::afb320([
            'emetteur'      => $this->emetteur(),
            'intermediaire' => ['bic' => 'UBHKHKHH'],
            'destinataires' => $this->destinataires(),
        ]);
        $cfonb->build();

        foreach ($cfonb->getLines() as $line) {
            if ($line === '') {
                continue; // trailing empty line
            }
            self::assertSame(320, strlen($line));
        }
    }

    public function testRecordSequenceIsContiguous(): void
    {
        $cfonb = CFONBFactory::afb320([
            'emetteur'      => $this->emetteur(),
            'intermediaire' => ['bic' => 'UBHKHKHH'],
            'destinataires' => $this->destinataires(),
        ]);
        $cfonb->build();

        $types = [];
        foreach ($cfonb->getLines() as $line) {
            if ($line !== '') {
                $types[] = substr($line, 0, 2);
            }
        }

        self::assertSame(['03', '04', '05', '07', '04', '05', '07', '06', '08'], $types);
        self::assertSame(9, $cfonb->getNumberOfSequence());
    }

    public function testBeneficiaryBankRequiresNameOrBic(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $destinataires = $this->destinataires();
        $destinataires[0]['nom_banque'] = '';
        $destinataires[0]['bic'] = '';

        CFONBFactory::afb320([
            'emetteur'      => $this->emetteur(),
            'destinataires' => $destinataires,
        ])->build();
    }

    public function testWorksWithoutIntermediary(): void
    {
        $cfonb = CFONBFactory::afb320([
            'emetteur'      => $this->emetteur(),
            'destinataires' => $this->destinataires(),
        ]);
        $cfonb->build();

        $types = array_map(
            static fn (string $line): string => substr($line, 0, 2),
            array_filter($cfonb->getLines(), static fn (string $line): bool => $line !== '')
        );

        self::assertNotContains('06', $types);
        self::assertContains('08', $types);
    }
}
