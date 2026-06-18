<?php

declare(strict_types=1);

namespace Ladina\CFONB\Tests\Factory;

use Ladina\CFONB\AFB160;
use Ladina\CFONB\AFB320;
use Ladina\CFONB\Exception\InvalidArgumentException;
use Ladina\CFONB\Factory\CFONBFactory;
use PHPUnit\Framework\TestCase;

final class CFONBFactoryTest extends TestCase
{
    public function testGenerateAfb320ReturnsAFB320(): void
    {
        self::assertInstanceOf(AFB320::class, CFONBFactory::generateAFB('320', $this->buildableData('320')));
        self::assertInstanceOf(AFB320::class, CFONBFactory::afb320($this->buildableData('320')));
    }

    public function testGenerateAfb160ReturnsAFB160(): void
    {
        self::assertInstanceOf(AFB160::class, CFONBFactory::generateAFB('160', $this->buildableData('160')));
        self::assertInstanceOf(AFB160::class, CFONBFactory::afb160($this->buildableData('160')));
    }

    public function testUnknownNormThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        CFONBFactory::generateAFB('999', $this->buildableData('320'));
    }

    public function testMissingDataThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        CFONBFactory::afb320(['emetteur' => ['raison_sociale' => 'X']]);
    }

    private function buildableData(string $norme): array
    {
        if ($norme === '320') {
            return [
                'emetteur' => [
                    'raison_sociale'     => 'FITIA',
                    'address1'           => 'quartier',
                    'reference_virement' => 'V1',
                    'numero_compte'      => 'FR0001',
                    'date_creation'      => time(),
                ],
                'destinataires' => [[
                    'raison_sociale' => 'Nom',
                    'reference'      => 'VIR.1',
                    'pays'           => 'FR',
                    'montant'        => 1000,
                    'bic'            => 'BMOIMGMG',
                ]],
            ];
        }

        return [
            'emetteur' => [
                'numero_emetteur'      => '123456',
                'date_de_valeur'       => '15JAN',
                'raison_sociale'       => 'DUPOND',
                'reference_virement'   => 'V1',
                'numero_guichet'       => '00001',
                'numero_compte'        => '00000000001',
                'numero_etablissement' => '00001',
            ],
            'destinataires' => [[
                'reference_ligne'      => 'VIR.1',
                'raison_sociale'       => 'DUPONT',
                'banque'               => 'BANK',
                'numero_guichet'       => '00001',
                'numero_compte'        => '00000000001',
                'montant'              => 1000,
                'label'                => 'Label',
                'numero_etablissement' => '00001',
            ]],
        ];
    }
}
