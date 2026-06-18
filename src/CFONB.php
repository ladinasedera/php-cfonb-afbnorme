<?php

declare(strict_types=1);

/*
 * Official documentation (V4):
 * https://www.cfonb.org/fichiers/20171002171114_Brochure_Rem_inf_ordres_paiement_international_320C_V4.pdf
 * https://fr.wikipedia.org/wiki/AFB320
 */

namespace Ladina\CFONB;

use Ladina\CFONB\Exception\InvalidArgumentException;
use Ladina\CFONB\Support\Str;
use Ladina\CFONB\Support\TimeZone;

/**
 * Base class shared by every CFONB / AFB file generator (AFB160, AFB320).
 *
 * A concrete generator only has to declare its field definitions
 * ({@see $emetteurFields}, {@see $destinataireFields}, …) and implement
 * {@see buildLines()}; this class takes care of validation, sanitising,
 * assembling the file content and streaming it to the browser.
 *
 * @phpstan-type FieldDefinition array{length:int, required:bool, default?:mixed}
 */
abstract class CFONB
{
    /** @var list<string> The assembled file lines. */
    protected array $lines = [];

    /** Final, ready-to-write file content. */
    protected string $content = '';

    /** Raw "emetteur" (sender) input as provided by the caller. */
    protected array $emetteur = [];

    /** Raw "destinataire" (recipient) input as provided by the caller. */
    protected array $destinataire = [];

    /** Raw "intermediaire" (intermediary bank) input as provided by the caller. */
    protected array $intermediaire = [];

    /** Validated & sanitised sender data, keyed by field name. */
    protected array $dataEmetteur = [];

    /** Validated & sanitised recipients, a list of field-keyed arrays. */
    protected array $dataDestinataire = [];

    /** Validated & sanitised intermediary bank data, keyed by field name. */
    protected array $dataIntermediaire = [];

    /** Running total of every recipient amount. */
    protected int $totalAmount = 0;

    /** The AFB norm handled by this generator ("160" or "320"). */
    protected string $afbNorme = '320';

    /**
     * Field definitions for the sender.
     *
     * @var array<string, FieldDefinition>
     */
    protected array $emetteurFields = [];

    /**
     * Field definitions for a recipient.
     *
     * @var array<string, FieldDefinition>
     */
    protected array $destinataireFields = [];

    /**
     * Field definitions for the (optional) intermediary bank.
     *
     * @var array<string, FieldDefinition>
     */
    protected array $intermediaireFields = [];

    /**
     * @param string $afbNorme The AFB norm ("160" or "320").
     * @param array  $data     Must contain "emetteur" and "destinataires";
     *                         "intermediaire" is optional.
     *
     * @throws InvalidArgumentException When mandatory data is missing.
     */
    public function __construct(string $afbNorme = '320', array $data = [])
    {
        foreach (['emetteur', 'destinataires'] as $item) {
            if (empty($data[$item])) {
                throw new InvalidArgumentException("$item data is required");
            }
        }

        $this->setAfbNorme($afbNorme);
        $this->setEmetteur($data['emetteur']);
        $this->setDestinataire($data['destinataires']);

        if (!empty($data['intermediaire'])) {
            $this->setIntermediaire($data['intermediaire']);
        }

        $this->validateData();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getEmetteur(): array
    {
        return $this->emetteur;
    }

    /** @return list<string> */
    public function getLines(): array
    {
        return $this->lines;
    }

    public function getDestinataire(): array
    {
        return $this->destinataire;
    }

    public function getIntermediaire(): array
    {
        return $this->intermediaire;
    }

    public function getTotalAmount(): int
    {
        return $this->totalAmount;
    }

    public function getAfbNorme(): string
    {
        return $this->afbNorme;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setDestinataire(array $destinataire): void
    {
        $this->destinataire = $destinataire;
    }

    public function setIntermediaire(array $intermediaire): void
    {
        $this->intermediaire = $intermediaire;
    }

    public function setEmetteur(array $emetteur): void
    {
        $this->emetteur = $emetteur;
    }

    public function setTotalAmount(int|string $amount): void
    {
        $this->totalAmount += (int) $amount;
    }

    /** @param list<string> $lines */
    public function setLines(array $lines): void
    {
        $this->lines = $lines;
    }

    public function setAfbNorme(string $afbNorme): void
    {
        $this->afbNorme = $afbNorme;
    }

    /**
     * Validate the sender, recipients and (optional) intermediary bank.
     *
     * @throws InvalidArgumentException
     */
    public function validateData(): void
    {
        $this->validateEmetteurFields($this->emetteur);
        $this->validateDestinataireFields($this->destinataire);

        if (!empty($this->intermediaire)) {
            $this->validateIntermediaireFields($this->intermediaire);
        }
    }

    /**
     * Build the file lines and either return their content or the generator.
     */
    public function build(bool $getContent = false): static|string
    {
        $this->buildLines();

        return $getContent ? $this->getContent() : $this;
    }

    /**
     * Build the file content and stream it to the browser as an attachment.
     */
    public function downloadFile(string $filename = ''): void
    {
        $this->build();
        $this->sendFile($this->getContent(), $filename);
    }

    /**
     * Build each line of the file for the implemented norm (AFB160 / AFB320).
     */
    abstract protected function buildLines(): void;

    /**
     * Convert the array of lines into the final file content (CRLF separated).
     *
     * @throws InvalidArgumentException When no line has been produced.
     */
    protected function buildFileContent(bool $addEmptyEndLine = true): void
    {
        if ($this->lines === []) {
            throw new InvalidArgumentException('No lines created');
        }

        if ($addEmptyEndLine && end($this->lines) !== '') {
            $this->lines[] = '';
        }

        $this->content = implode("\r\n", $this->lines);
    }

    /**
     * Validate a single field against its definition and return its final,
     * sanitised, fixed-width-safe value.
     *
     * @param FieldDefinition $prop
     *
     * @throws InvalidArgumentException When a required field is missing.
     */
    protected function validateProps(array $prop, string $field, array $data): string
    {
        $present = array_key_exists($field, $data);

        if ($prop['required'] && !$present) {
            throw new InvalidArgumentException("You must provide this mandatory field $field");
        }

        $value = $present ? $data[$field] : null;
        $hasDefault = array_key_exists('default', $prop);

        // A default is applied when the field is unset (null) or explicitly
        // marked as "useDefault" by the caller.
        if ($hasDefault && (!isset($data[$field]) || $value === 'useDefault')) {
            $value = $prop['default'];
        }

        // Sanitise first, then enforce the fixed width: this guarantees the
        // declared length is never exceeded, even when a transliteration grows
        // the string (e.g. "Ä" -> "AE").
        $value = Str::sanitize($value);

        if (strlen($value) > $prop['length']) {
            $value = substr($value, 0, $prop['length']);
        }

        return $value;
    }

    /**
     * Send the file to the browser as a download and stop execution.
     */
    protected function sendFile(string $contents, string $filename = ''): void
    {
        if ($filename === '') {
            $filename = 'CFONB_AFB' . $this->getAfbNorme() . '_' . $this->getDownloadPeriod();
        }

        header('Content-Type: text/plain');
        header('Content-Description: File Transfer');
        header(sprintf('Content-Disposition: attachment; filename="%s.txt"', $filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Pragma: public');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        if (ob_get_level() > 0) {
            ob_clean();
        }
        flush();

        echo $contents;
        exit;
    }

    /**
     * Validate the sender data against {@see $emetteurFields}.
     *
     * @throws InvalidArgumentException
     */
    private function validateEmetteurFields(array $emetteur): void
    {
        foreach ($this->emetteurFields as $field => $prop) {
            $this->dataEmetteur[$field] = $this->validateProps($prop, $field, $emetteur);
        }
    }

    /**
     * Validate the recipient(s) against {@see $destinataireFields}.
     *
     * Accepts either a single recipient or a list of recipients.
     *
     * @throws InvalidArgumentException
     */
    private function validateDestinataireFields(array $destinataire): void
    {
        $destinataires = isset($destinataire[0]) ? $destinataire : [$destinataire];

        foreach ($this->destinataireFields as $field => $prop) {
            foreach ($destinataires as $key => $desti) {
                $this->dataDestinataire[$key][$field] = $this->validateProps($prop, $field, $desti);
            }
        }
    }

    /**
     * Validate the intermediary bank against {@see $intermediaireFields}.
     *
     * @throws InvalidArgumentException
     */
    private function validateIntermediaireFields(array $intermediaire): void
    {
        foreach ($this->intermediaireFields as $field => $prop) {
            $this->dataIntermediaire[$field] = $this->validateProps($prop, $field, $intermediaire);
        }
    }

    /**
     * Build the "Month_Year" (French) suffix used for default download names.
     */
    private function getDownloadPeriod(): string
    {
        TimeZone::setTimeZone('Europe/Paris');

        return TimeZone::getFrMonthStr((int) date('n')) . '_' . date('Y');
    }
}
