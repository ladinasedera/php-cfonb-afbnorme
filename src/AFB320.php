<?php

declare(strict_types=1);

namespace Ladina\CFONB;

use Ladina\CFONB\Exception\InvalidArgumentException;
use Ladina\CFONB\Support\Str;

/**
 * AFB320 / CFONB320 generator (international transfers — "virement étranger").
 *
 * @see https://fr.wikipedia.org/wiki/AFB320
 */
class AFB320 extends CFONB
{
    /** @var array<string, array{length:int, required:bool, default?:mixed}> */
    protected array $emetteurFields = [
        'date_creation'         => ['length' => 10, 'required' => true,  'default' => 'now'],
        'raison_sociale'        => ['length' => 35, 'required' => true],
        'address1'              => ['length' => 35, 'required' => true],
        'address2'              => ['length' => 35, 'required' => false],
        'address3'              => ['length' => 35, 'required' => false],
        'qualifiant_address'    => ['length' => 3,  'required' => false],
        'siret'                 => ['length' => 14, 'required' => false, 'default' => 'xxxxxxxxxxxx'],
        'reference_virement'    => ['length' => 16, 'required' => true],
        'bic'                   => ['length' => 11, 'required' => false, 'default' => 'xxxxxxxx'],
        'type_num_compte'       => ['length' => 1,  'required' => false, 'default' => 1],
        'numero_compte'         => ['length' => 34, 'required' => true],
        'devise_compte'         => ['length' => 3,  'required' => false, 'default' => 'EUR'],
        'numero_emetteur'       => ['length' => 16, 'required' => false],
        'type_num_compte_frais' => ['length' => 1,  'required' => false, 'default' => 1],
        'numero_compte_frais'   => ['length' => 34, 'required' => false],
        'devise_compte_frais'   => ['length' => 3,  'required' => false, 'default' => ''],
        'type_debit'            => ['length' => 1,  'required' => false, 'default' => 2],
        'type_remise'           => ['length' => 1,  'required' => false, 'default' => 1],
        'date_execution'        => ['length' => 10, 'required' => false, 'default' => 'now'],
        'devise'                => ['length' => 3,  'required' => false, 'default' => ''],
    ];

    /** @var array<string, array{length:int, required:bool, default?:mixed}> */
    protected array $destinataireFields = [
        'type_num_compte'       => ['length' => 1,   'required' => false, 'default' => 1],
        'nom_banque'            => ['length' => 140, 'required' => false],
        'bank_address'          => ['length' => 105, 'required' => false],
        'numero_compte'         => ['length' => 34,  'required' => false],
        'raison_sociale'        => ['length' => 35,  'required' => true],
        'address1'              => ['length' => 35,  'required' => false],
        'address2'              => ['length' => 35,  'required' => false],
        'address3'              => ['length' => 35,  'required' => false],
        'id_nationale'          => ['length' => 17,  'required' => false],
        'bic'                   => ['length' => 11,  'required' => false], // BIC
        'qualifiant_address'    => ['length' => 3,   'required' => false],
        'pays'                  => ['length' => 2,   'required' => true,  'default' => 'FR'],
        'reference'             => ['length' => 16,  'required' => true],
        'qualifiant'            => ['length' => 1,   'required' => false, 'default' => 'D'],
        'montant'               => ['length' => 14,  'required' => true],
        'decimales'             => ['length' => 1,   'required' => false, 'default' => 2],
        'code_eco'              => ['length' => 3,   'required' => false, 'default' => '010'],
        'pays_BDF'              => ['length' => 2,   'required' => false, 'default' => 'FR'],
        'mode_reglement'        => ['length' => 1,   'required' => false, 'default' => 0],
        'frais'                 => ['length' => 2,   'required' => false, 'default' => '13'],
        'type_num_compte_frais' => ['length' => 1,   'required' => false, 'default' => 1],
        'numero_compte_frais'   => ['length' => 34,  'required' => false],
        'devise_compte_frais'   => ['length' => 3,   'required' => false, 'default' => ''],
        'date_execution'        => ['length' => 10,  'required' => false, 'default' => 'now'],
        'devise'                => ['length' => 3,   'required' => false, 'default' => 'USD'],
        'motif'                 => ['length' => 140, 'required' => false],
        'instruction_particulier' => ['length' => 105, 'required' => false, 'default' => 'BONL'],
    ];

    /** @var array<string, array{length:int, required:bool, default?:mixed}> */
    protected array $intermediaireFields = [
        'nom_banque' => ['length' => 140, 'required' => false],
        'bic'        => ['length' => 11,  'required' => false], // BIC
        'pays'       => ['length' => 2,   'required' => false],
    ];

    /** Record sequence counter (incremented for each record after the header). */
    private int $sequence = 1;

    public function getNumberOfSequence(): int
    {
        return $this->sequence;
    }

    protected function buildLines(): void
    {
        $this->lines[] = $this->getHeaderLine();

        foreach ($this->dataDestinataire as $desti) {
            $this->lines[] = $this->getDestinataireLine($desti);
            $this->lines[] = $this->getBeneficiaryBank($desti);
            $this->lines[] = $this->getComplementaryPayementInfo($desti);
        }

        if (!empty($this->dataIntermediaire)) {
            $this->lines[] = $this->getIntermediaryBank($this->dataIntermediaire);
        }

        $this->lines[] = $this->getFooterLine();
        $this->buildFileContent();
    }

    /**
     * Generate the header record ("03PI").
     */
    public function getHeaderLine(): string
    {
        $dateCreation = $this->dataEmetteur['date_creation'] == 'NOW'
            ? time() : (int) $this->dataEmetteur['date_creation'];
        $dateExecution = $this->dataEmetteur['date_execution'] == 'NOW'
            ? time() : (int) $this->dataEmetteur['date_execution'];
        $numeroCompteFrais = empty($this->dataEmetteur['numero_compte_frais'])
            ? '' : $this->dataEmetteur['type_num_compte_frais'];
        $remiseType = $this->dataEmetteur['type_remise'];
        $date = ($remiseType == 1 || $remiseType == 2) ? date('Ymd', $dateExecution) : '';
        $devise = $this->dataEmetteur['devise'];

        return '03' . 'PI' .
            sprintf('%06s', $this->sequence) .
            date('Ymd', $dateCreation) .
            sprintf('%-35s', $this->dataEmetteur['raison_sociale']) .
            sprintf('%-35s', $this->dataEmetteur['address1']) .
            sprintf('%-35s', $this->dataEmetteur['address2']) .
            sprintf('%-35s', $this->dataEmetteur['address3']) .
            sprintf('%-14s', $this->dataEmetteur['siret']) .
            sprintf('%-16s', $this->dataEmetteur['reference_virement']) .
            sprintf('%-11s', $this->dataEmetteur['bic']) .
            sprintf('%-1s', $this->dataEmetteur['type_num_compte']) .
            sprintf('%-34s', $this->dataEmetteur['numero_compte']) .
            sprintf('%-3s', $this->dataEmetteur['devise_compte']) .
            sprintf('%-16s', $this->dataEmetteur['numero_emetteur']) .
            sprintf('%-1s', $numeroCompteFrais) .
            sprintf('%-34s', $this->dataEmetteur['numero_compte_frais']) .
            sprintf('%-3s', $this->dataEmetteur['devise_compte_frais']) .
            sprintf('%-4s', '') .
            sprintf('%-1s', '') .
            sprintf('%-3s', '') .
            sprintf('%-3s', $this->dataEmetteur['qualifiant_address']) .
            sprintf('%-5s', '') .
            sprintf('%-1s', $this->dataEmetteur['type_debit']) .
            sprintf('%-1s', $this->dataEmetteur['type_remise']) .
            sprintf('%-8s', $date) .
            sprintf('%-3s', ($remiseType == 1 || $remiseType == 3) ? $devise : '');
    }

    /**
     * Generate a recipient record ("04PI") and add its amount to the total.
     */
    public function getDestinataireLine(array $desti): string
    {
        $this->incrementSequence();

        $remiseType = $this->dataEmetteur['type_remise'];
        $dateExecution = $desti['date_execution'] == 'NOW' ? time() : (int) $desti['date_execution'];
        $numeroCompteFrais = $desti['numero_compte_frais'];
        $decimales = $desti['decimales'];
        $remapAmount = Str::remapAmount($desti['montant'], (int) $decimales);
        $typeNumCompte = $desti['type_num_compte'];
        $countNumCompte = 34;
        $blank4 = '';

        if ($typeNumCompte == 2) {
            $blank4 = sprintf('%-4s', '');
            $countNumCompte -= 4;
        }

        $destiLine = '04' . 'PI' .
            sprintf('%06s', $this->sequence) .
            sprintf('%-1s', $typeNumCompte) . $blank4 .
            sprintf('%-' . $countNumCompte . 's', $desti['numero_compte']) .
            sprintf('%-35s', $desti['raison_sociale']) .
            sprintf('%-35s', $desti['address1']) .
            sprintf('%-35s', $desti['address2']) .
            sprintf('%-35s', $desti['address3']) .
            sprintf('%-9s', $desti['id_nationale']) .
            sprintf('%-3s', $desti['qualifiant_address']) .
            sprintf('%-5s', '') .
            sprintf('%-2s', $desti['pays']) .
            sprintf('%-16s', $desti['reference']) .
            sprintf('%-1s', $desti['qualifiant']) .
            sprintf('%4s', '') .
            sprintf('%014s', $remapAmount) .
            sprintf('%1s', $decimales) .
            sprintf('%1s', '') .
            sprintf('%-3s', $desti['code_eco']) .
            sprintf('%-2s', $desti['pays_BDF']) .
            sprintf('%-1s', $desti['mode_reglement']) .
            sprintf('%-2s', $desti['frais']) .
            sprintf('%-1s', empty($numeroCompteFrais) ? '' : $numeroCompteFrais) .
            sprintf('%-34s', $desti['numero_compte_frais']) .
            sprintf('%-3s', $desti['devise_compte_frais']) .
            sprintf('%19s', '') .
            sprintf('%3s', '') .
            sprintf('%-8s', ($remiseType == 3 || $remiseType == 4) ? date('Ymd', $dateExecution) : '') .
            sprintf('%-3s', ($remiseType == 2 || $remiseType == 4) ? $desti['devise'] : '');

        $this->setTotalAmount($remapAmount);

        return $destiLine;
    }

    /**
     * Generate the beneficiary bank record ("05PI").
     *
     * @throws InvalidArgumentException When neither a bank name nor a BIC is given.
     */
    public function getBeneficiaryBank(array $desti): string
    {
        $this->incrementSequence();

        if ($desti['bic'] === '' && $desti['nom_banque'] === '') {
            throw new InvalidArgumentException('You must provide beneficiary bank name OR BIC code');
        }

        $nomBanque = $desti['nom_banque'] !== '' ? $desti['nom_banque'] : '';
        $nomBanque = strlen($nomBanque) > 35 ? sprintf('%-70s', $nomBanque) : sprintf('%-35s', $nomBanque);

        if ($desti['bank_address'] !== '') {
            $nomBanque .= $desti['bank_address'];
        }

        return '05' . 'PI' .
            sprintf('%06s', $this->sequence) .
            sprintf('%-140s', $nomBanque) .
            sprintf('%-11s', $desti['bic']) .
            sprintf('%-2s', $desti['pays']) .
            sprintf('%157s', '');
    }

    /**
     * Generate the intermediary bank record ("06PI").
     *
     * @throws InvalidArgumentException When neither a bank name nor a BIC is given.
     */
    public function getIntermediaryBank(array $intermediaire): string
    {
        $this->incrementSequence();

        $nomBanque = '';
        if ($intermediaire['bic'] === '') {
            if ($intermediaire['nom_banque'] === '') {
                throw new InvalidArgumentException('You must provide beneficiary bank name OR BIC code');
            }
            $nomBanque = $intermediaire['nom_banque'];
        }

        return '06' . 'PI' .
            sprintf('%06s', $this->sequence) .
            sprintf('%140s', $nomBanque) .
            sprintf('%-11s', $intermediaire['bic']) .
            sprintf('%-2s', $intermediaire['pays']) .
            sprintf('%157s', '');
    }

    /**
     * Generate the complementary payment information record ("07PI").
     */
    public function getComplementaryPayementInfo(array $desti): string
    {
        $this->incrementSequence();

        return '07' . 'PI' .
            sprintf('%06s', $this->sequence) .
            sprintf('%-140s', $desti['motif']) .
            sprintf('%-1s', '') .
            sprintf('%-16s', '') .
            sprintf('%-8s', '') .
            sprintf('%-12s', '') .
            sprintf('%-105s', $desti['instruction_particulier']) .
            sprintf('%28s', '');
    }

    /**
     * Generate the footer record ("08PI") with the grand total.
     */
    public function getFooterLine(): string
    {
        $this->incrementSequence();

        $totalAmount = $this->getTotalAmount();
        $dateCreation = $this->dataEmetteur['date_creation'] == 'NOW'
            ? time() : (int) $this->dataEmetteur['date_creation'];

        return '08' . 'PI' .
            sprintf('%06s', $this->sequence) .
            date('Ymd', $dateCreation) .
            sprintf('%140s', '') .
            sprintf('%-14s', $this->dataEmetteur['siret']) .
            sprintf('%-16s', $this->dataEmetteur['reference_virement']) .
            sprintf('%11s', '') .
            sprintf('%-1s', $this->dataEmetteur['type_num_compte']) .
            sprintf('%-34s', $this->dataEmetteur['numero_compte']) .
            sprintf('%-3s', $this->dataEmetteur['devise_compte']) .
            sprintf('%-16s', $this->dataEmetteur['numero_emetteur']) .
            sprintf('%018s', $totalAmount) .
            sprintf('%49s', '');
    }

    private function incrementSequence(): void
    {
        $this->sequence++;
    }
}
