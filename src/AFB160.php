<?php

declare(strict_types=1);

namespace Ladina\CFONB;

/**
 * AFB160 / CFONB160 generator (domestic transfers — "virement domestique").
 *
 * @see https://fr.wikipedia.org/wiki/AFB160
 */
class AFB160 extends CFONB
{
    /** @var array<string, array{length:int, required:bool, default?:mixed}> */
    protected array $emetteurFields = [
        'numero_emetteur'      => ['length' => 6,  'required' => true],
        'date_de_valeur'       => ['length' => 5,  'required' => true, 'default' => 'now'],
        'raison_sociale'       => ['length' => 24, 'required' => true],
        'reference_virement'   => ['length' => 11, 'required' => true],
        'numero_guichet'       => ['length' => 5,  'required' => true],
        'numero_compte'        => ['length' => 11, 'required' => true],
        'numero_etablissement' => ['length' => 5,  'required' => true],
    ];

    /** @var array<string, array{length:int, required:bool, default?:mixed}> */
    protected array $destinataireFields = [
        'reference_ligne'      => ['length' => 12, 'required' => true],
        'raison_sociale'       => ['length' => 24, 'required' => true],
        'banque'               => ['length' => 20, 'required' => true],
        'numero_guichet'       => ['length' => 5,  'required' => true],
        'numero_compte'        => ['length' => 11, 'required' => true],
        'montant'              => ['length' => 16, 'required' => true],
        'label'                => ['length' => 31, 'required' => true],
        'numero_etablissement' => ['length' => 5,  'required' => true],
    ];

    protected function buildLines(): void
    {
        $this->lines[] = $this->getHeaderLine();

        foreach ($this->dataDestinataire as $desti) {
            $this->lines[] = $this->getDestinataireLine($desti);
        }

        $this->lines[] = $this->getFooterLine();
        $this->buildFileContent();
    }

    /**
     * Generate the header record ("0302").
     */
    public function getHeaderLine(): string
    {
        return '0302' .
            sprintf('%8s', '') .
            $this->dataEmetteur['numero_emetteur'] .
            sprintf('%7s', '') .
            $this->dataEmetteur['date_de_valeur'] .
            sprintf('%-24s', $this->dataEmetteur['raison_sociale']) .
            sprintf('%-11s', $this->dataEmetteur['reference_virement']) .
            sprintf('%15s', '') .
            'E' .
            sprintf('%5s', '') .
            $this->dataEmetteur['numero_guichet'] .
            $this->dataEmetteur['numero_compte'] .
            sprintf('%47s', '') .
            $this->dataEmetteur['numero_etablissement'] .
            sprintf('%5s', '');
    }

    /**
     * Generate a recipient record ("0602") and add its amount to the total.
     */
    public function getDestinataireLine(array $desti): string
    {
        $destinataireLine = '0602' .
            sprintf('%8s', '') .
            $this->dataEmetteur['numero_emetteur'] .
            sprintf('%-12s', $desti['reference_ligne']) .
            sprintf('%-24s', $desti['raison_sociale']) .
            sprintf('%-20s', $desti['banque']) .
            sprintf('%12s', '') .
            $desti['numero_guichet'] .
            $desti['numero_compte'] .
            sprintf('%016s', $desti['montant']) .
            sprintf('%-31s', $desti['label']) .
            $desti['numero_etablissement'] .
            sprintf('%5s', '');

        $this->setTotalAmount($desti['montant']);

        return $destinataireLine;
    }

    /**
     * Generate the footer record ("0802") with the grand total.
     */
    public function getFooterLine(): string
    {
        return '0802' .
            sprintf('%8s', '') .
            $this->dataEmetteur['numero_emetteur'] .
            sprintf('%84s', '') .
            sprintf('%016s', $this->getTotalAmount()) .
            sprintf('%42s', '');
    }
}
