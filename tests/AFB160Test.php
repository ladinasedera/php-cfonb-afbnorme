<?php

declare(strict_types=1);

namespace Ladina\CFONB\Tests;

use Ladina\CFONB\AFB160;
use Ladina\CFONB\Exception\InvalidArgumentException;
use Ladina\CFONB\Factory\CFONBFactory;
use PHPUnit\Framework\TestCase;

final class AFB160Test extends TestCase
{
    protected function setUp(): void
    {
        // The generated content embeds dates: keep them deterministic.
        date_default_timezone_set('UTC');
    }

    private function emetteur(): array
    {
        return [
            'numero_emetteur'      => 'xxx',
            'date_de_valeur'       => 1750000000,
            'raison_sociale'       => 'DUPOND',
            'reference_virement'   => 'V 01/01/26',
            'numero_guichet'       => 'xxxxxxx',
            'numero_compte'        => 'xxxxxxxxxxx',
            'numero_etablissement' => 'xxxx',
        ];
    }

    private function destinataires(): array
    {
        return [
            [
                'reference_ligne'      => 'VIR.1',
                'raison_sociale'       => 'DUPONT',
                'banque'               => 'XXXX',
                'numero_guichet'       => 'xxxx',
                'numero_compte'        => 'xxxxxxxxxxx',
                'montant'              => 10000,
                'label'                => 'Retrait untel',
                'numero_etablissement' => 'xxxxx',
            ],
            [
                'reference_ligne'      => 'VIR.2',
                'raison_sociale'       => 'TOURNESOL',
                'banque'               => 'XXXXXX',
                'numero_guichet'       => 'xxxxxxx',
                'numero_compte'        => 'xxxxxxxxxxxx',
                'montant'              => 25000,
                'label'                => 'Autre Retrait',
                'numero_etablissement' => 'xxxxx',
            ],
        ];
    }

    public function testItGeneratesTheExpectedFile(): void
    {
        $content = CFONBFactory::afb160([
            'emetteur'      => $this->emetteur(),
            'destinataires' => $this->destinataires(),
        ])->build(true);

        self::assertSame(
            file_get_contents(__DIR__ . '/fixtures/afb160.txt'),
            $content
        );
    }

    public function testRecordTypesAndTotalAmount(): void
    {
        $cfonb = CFONBFactory::afb160([
            'emetteur'      => $this->emetteur(),
            'destinataires' => $this->destinataires(),
        ]);
        $cfonb->build();

        $lines = $cfonb->getLines();

        self::assertStringStartsWith('0302', $lines[0]);
        self::assertStringStartsWith('0602', $lines[1]);
        self::assertStringStartsWith('0602', $lines[2]);
        self::assertStringStartsWith('0802', $lines[3]);
        self::assertSame(35000, $cfonb->getTotalAmount());
    }

    public function testMissingEmetteurThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AFB160('160', ['destinataires' => $this->destinataires()]);
    }

    public function testMissingMandatoryFieldThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $emetteur = $this->emetteur();
        unset($emetteur['raison_sociale']);

        CFONBFactory::afb160([
            'emetteur'      => $emetteur,
            'destinataires' => $this->destinataires(),
        ])->build();
    }
}
