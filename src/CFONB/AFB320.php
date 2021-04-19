<?php
    /**
     * Copyright (c) - 2020 : Ladina Sedera
     * http://ladina.fitiavana.mg
     */

    /**
     * Document officielle V4 :
     * https://www.cfonb.org/fichiers/20171002171114_Brochure_Rem_inf_ordres_paiement_international_320C_V4.pdf
     *
     * https://fr.wikipedia.org/wiki/AFB320
     *
     * http://segs.free.fr/Fichiers/CFONB%20-%20Structure%20des%20fichiers%20ETEBAC3.pdf
     *
     * http://documentation.sepamail.org/images/f/fa/EBICS_IG_V1_3_Annexe_2_Nommage_Fichiers_VF_24092010CLEAN-CYV.pdf
     */

    namespace Ladina\CFONB;

    use Library\Exception\InvalidArgumentException;
    use Tools\Str;

    /**
     * Class AFB320
     *
     * @package Ladina\CFONB
     */
    class AFB320 extends CFONB
    {
        /**
         * @var array[]
         */
        protected $emetteur_fields = [
            'date_creation' => [
                'length' => 10,
                'required' => true,
                'default' => 'now'
            ],
            'raison_sociale' => [
                'length' => 35,
                'required' => true
            ],
            'address1' => [
                'length' => 35,
                'required' => true
            ],
            'address2' => [
                'length' => 35,
                'required' => false
            ],
            'address3' => [
                'length' => 35,
                'required' => false
            ],
            'qualifiant_address' => [
                'length' => 3,
                'required' => false
            ],
            'siret' => [
                'length' => 14,
                'required' => false,
                'default' => 'xxxxxxxxxxxx'
            ],
            'reference_virement' => [
                'length' => 16,
                'required' => true
            ],
            'bic' => [
                'length' => 11,
                'required' => false,
                'default' => 'xxxxxxxx'
            ],
            'type_num_compte' => [
                'length' => 1,
                'required' => false,
                'default' => 1
            ],
            'numero_compte' => [
                'length' => 34,
                'required' => true
            ],
            'devise_compte' => [
                'length' => 3,
                'required' => false,
                'default' => 'EUR'
            ],
            'numero_emetteur' => [
                'length' => 16,
                'required' => false
            ],
            'type_num_compte_frais' => [
                'length' => 1,
                'required' => false,
                'default' => 1
            ],
            'numero_compte_frais' => [
                'length' => 34,
                'required' => false
            ],
            'devise_compte_frais' => [
                'length' => 3,
                'required' => false,
                'default' => ''
            ],
            'type_debit' => [
                'length' => 1,
                'required' => false,
                'default' => 2
            ],
            'type_remise' => [
                'length' => 1,
                'required' => false,
                'default' => 1
            ],
            'date_execution' => [
                'length' => 10,
                'required' => false,
                'default' => 'now'
            ],
            'devise' => [
                'length' => 3,
                'required' => false,
                'default' => ''
            ]
        ];

        /**
         * @var array[]
         */
        protected $destinataire_fields = [
            'type_num_compte' => [
                'length' => 1,
                'required' => false,
                'default' => 1
            ],
            'nom_banque' => [
                'length' => 140,
                'required' => false
            ],
            'bank_address' => [
                'length' => 105,
                'required' => false
            ],
            'numero_compte' => [
                'length' => 34,
                'required' => false
            ],
            'raison_sociale' => [
                'length' => 35,
                'required' => true
            ],
            'address1' => [
                'length' => 35,
                'required' => false
            ],
            'address2' => [
                'length' => 35,
                'required' => false
            ],
            'address3' => [
                'length' => 35,
                'required' => false
            ],
            'id_nationale' => [
                'length' => 17,
                'required' => false
            ],
            // RIB
            'bic' => [
                'length' => 11,
                'required' => false
            ],
            // BIC
            'qualifiant_address' => [
                'length' => 3,
                'required' => false
            ],
            'pays' => [
                'length' => 2,
                'required' => true,
                'default' => 'FR'
            ],
            'reference' => [
                'length' => 16,
                'required' => true
            ],
            'qualifiant' => [
                'length' => 1,
                'required' => false,
                'default' => 'D'
            ],
            'montant' => [
                'length' => 14,
                'required' => true
            ],
            'decimales' => [
                'length' => 1,
                'required' => false,
                'default' => 2
            ],
            'code_eco' => [
                'length' => 3,
                'required' => false,
                'default' => '010'
            ],
            'pays_BDF' => [
                'length' => 2,
                'required' => false,
                'default' => 'FR'
            ],
            'mode_reglement' => [
                'length' => 1,
                'required' => false,
                'default' => 0
            ],
            'frais' => [
                'length' => 2,
                'required' => false,
                'default' => '13'
            ],
            'type_num_compte_frais' => [
                'length' => 1,
                'required' => false,
                'default' => 1
            ],
            'numero_compte_frais' => [
                'length' => 34,
                'required' => false
            ],
            'devise_compte_frais' => [
                'length' => 3,
                'required' => false,
                'default' => ''
            ],
            'date_execution' => [
                'length' => 10,
                'required' => false,
                'default' => 'now'
            ],
            'devise' => [
                'length' => 3,
                'required' => false,
                'default' => 'USD'
            ],
            'motif' => [
                'length' => 140,
                'required' => false
            ],
            'instruction_particulier' => [
                'length' => 105,
                'required' => false,
                'default' => 'BONL'
            ],
        ];

        protected $intermediaire_fields = [
            'nom_banque' => [
                'length' => 140,
                'required' => false
            ],
            'bic' => [
                'length' => 11,
                'required' => false
            ],
            // BIC
            'pays' => [
                'length' => 2,
                'required' => false
            ],
        ];

        /**
         * @var int
         */
        private $number_of_sequence = 1;

        /**
         * Getter for number_of_sequence
         *
         * @return int
         */
        public function getNumberOfSequence ()
        {
            return $this->number_of_sequence;
        }

        /**
         * Setter for number_of_sequence
         *
         * @param int $number_of_sequence
         */
        public function setNumberOfSequence ()
        {
            $this->number_of_sequence++;
        }

        /**
         * Build line for AFB320
         *
         * @return mixed|void
         * @throws \Library\Exception\InvalidArgumentException
         */
        public function buildLines ()
        {
            $this->lines[] = $this->getHeaderLine ();
            foreach ( $this->data_destinataire as $desti )
            {
                $this->lines[] = $this->getDestinataireLine ( $desti );
                $this->lines[] = $this->getBeneficiaryBank ( $desti );
                $this->lines[] = $this->getComplementaryPayementInfo ( $desti );
            }
            if ( !empty( $this->data_intermediaire ) )
            {
                $this->lines[] = $this->getIntermediaryBank ( $this->data_intermediaire );
            }
            $this->lines[] = $this->getFooterLine ();
            $this->buildFileContent ();
        }

        /**
         * Generate header for the file
         *
         * @return string
         */
        public function getHeaderLine ()
        {
            $date_creation       = $this->data_emetteur[ 'date_creation' ] == 'NOW'
                ? time () : $this->data_emetteur[ 'date_creation' ];
            $date_execution      = $this->data_emetteur[ 'date_execution' ] == 'NOW'
                ? time () : $this->data_emetteur[ 'date_execution' ];
            $numero_compte_frais = empty( $this->data_emetteur[ 'numero_compte_frais' ] )
                ? null : $this->data_emetteur[ 'type_num_compte_frais' ];
            $remise_type         = $this->data_emetteur[ 'type_remise' ];
            $date                = ( $remise_type == 1 || $remise_type == 2 ) ? date ( 'Ymd', $date_execution ) : null;
            $devise              = $this->data_emetteur[ 'devise' ];
            return '03' . 'PI' .
                sprintf ( '%06s', $this->number_of_sequence ) .
                date ( 'Ymd', $date_creation ) .
                sprintf ( '%-35s', $this->data_emetteur[ 'raison_sociale' ] ) .
                sprintf ( '%-35s', $this->data_emetteur[ 'address1' ] ) .
                sprintf ( '%-35s', $this->data_emetteur[ 'address2' ] ) .
                sprintf ( '%-35s', $this->data_emetteur[ 'address3' ] ) .
                sprintf ( '%-14s', $this->data_emetteur[ 'siret' ] ) .
                sprintf ( '%-16s', $this->data_emetteur[ 'reference_virement' ] ) .
                sprintf ( '%-11s', $this->data_emetteur[ 'bic' ] ) .
                sprintf ( '%-1s', $this->data_emetteur[ 'type_num_compte' ] ) .
                sprintf ( '%-34s', $this->data_emetteur[ 'numero_compte' ] ) .
                sprintf ( '%-3s', $this->data_emetteur[ 'devise_compte' ] ) .
                sprintf ( '%-16s', $this->data_emetteur[ 'numero_emetteur' ] ) .
                sprintf ( '%-1s', $numero_compte_frais ) .
                sprintf ( '%-34s', $this->data_emetteur[ 'numero_compte_frais' ] ) .
                sprintf ( '%-3s', $this->data_emetteur[ 'devise_compte_frais' ] ) .
                sprintf ( '%-4s', null ) .
                sprintf ( '%-1s', null ) .
                sprintf ( '%-3s', null ) .
                sprintf ( '%-3s', $this->data_emetteur[ 'qualifiant_address' ] ) .
                sprintf ( '%-5s', null ) .
                sprintf ( '%-1s', $this->data_emetteur[ 'type_debit' ] ) .
                sprintf ( '%-1s', $this->data_emetteur[ 'type_remise' ] ) .
                sprintf ( '%-8s', $date ) .
                sprintf ( '%-3s', ( $remise_type == 1 || $remise_type == 3 ) ? $devise : null );
        }

        /**
         * Generate destinataire for the file
         *
         * @param $desti
         *
         * @return string
         */
        public function getDestinataireLine ( $desti )
        {
            $this->setNumberOfSequence ();
            $remise_type         = $this->data_emetteur[ 'type_remise' ];
            $date_execution      = $desti[ 'date_execution' ] == 'NOW' ? time () : $desti[ 'date_execution' ];
            $numero_compte_frais = $desti[ 'numero_compte_frais' ];
            $decimales           = $desti[ 'decimales' ];
            $remap_amount        = Str::remapAmount ( $desti[ 'montant' ], $decimales );
            $type_num_compte     = $desti[ 'type_num_compte' ];
            $count_numcompte     = 34;
            $blank_4             = '';
//            if ( $type_num_compte == 2 )
//            {
//                $blank_4         = sprintf ( '%-4s', null );
//                $count_numcompte -= 4;
//            }
            $destiLines = '04' . 'PI' .
                sprintf ( '%06s', $this->number_of_sequence ) .
                sprintf ( '%-1s', $type_num_compte ) . $blank_4 .
                sprintf ( '%-' . $count_numcompte . 's', $desti[ 'numero_compte' ] ) .
                sprintf ( '%-35s', $desti[ 'raison_sociale' ] ) .
                sprintf ( '%-35s', $desti[ 'address1' ] ) .
                sprintf ( '%-35s', $desti[ 'address2' ] ) .
                sprintf ( '%-35s', $desti[ 'address3' ] ) .
                sprintf ( '%-9s', $desti[ 'id_nationale' ] ) .
                sprintf ( '%-3s', $desti[ 'qualifiant_address' ] ) .
                sprintf ( '%-5s', null ) .
                sprintf ( '%-2s', $desti[ 'pays' ] ) .
                sprintf ( '%-16s', $desti[ 'reference' ] ) .
                sprintf ( '%-1s', $desti[ 'qualifiant' ] ) .
                sprintf ( '%4s', null ) .
                sprintf ( '%014s', $remap_amount ) .
                sprintf ( '%1s', $decimales ) .
                sprintf ( '%1s', null ) .
                sprintf ( '%-3s', $desti[ 'code_eco' ] ) .
                sprintf ( '%-2s', $desti[ 'pays_BDF' ] ) .
                sprintf ( '%-1s', $desti[ 'mode_reglement' ] ) .
                sprintf ( '%-2s', $desti[ 'frais' ] ) .
                sprintf ( '%-1s', empty( $numero_compte_frais ) ? null : $numero_compte_frais ) .
                sprintf ( '%-34s', $desti[ 'numero_compte_frais' ] ) .
                sprintf ( '%-3s', $desti[ 'devise_compte_frais' ] ) .
                sprintf ( '%19s', null ) .
                sprintf ( '%3s', null ) .
                sprintf ( '%-8s', ( $remise_type == 3 || $remise_type == 4 ) ? date ( 'Ymd', $date_execution ) : null ) .
                sprintf ( '%-3s', ( $remise_type == 2 || $remise_type == 4 ) ? $desti[ 'devise' ] : null );
            $this->setTotalAmount ( $remap_amount );
            return $destiLines;
        }

        /**
         * Generate destinataire complementary for the file
         *
         * @param $desti
         *
         * @return string
         * @throws InvalidArgumentException
         */
        public function getBeneficiaryBank ( $desti )
        {
            $this->setNumberOfSequence ();
            $nom_banque = '';
            if ( $desti[ 'bic' ] === '' && $desti[ 'nom_banque' ] === '' )
            {
                throw new InvalidArgumentException( "You must provid beneficiary bank name OR BIC code" );
            }
            if ( $desti[ 'nom_banque' ] !== '' )
            {
                $nom_banque = $desti[ 'nom_banque' ];
            }
            $nom_banque = strlen ( $nom_banque ) > 35 ? sprintf ( '%-70s', $nom_banque ) : sprintf ( '%-35s', $nom_banque );
            if ( $desti[ 'bank_address' ] !== '' )
            {
                $nom_banque .= $desti[ 'bank_address' ];
            }
            return '05' . 'PI' .
                sprintf ( '%06s', $this->number_of_sequence ) .
                sprintf ( '%-140s', $nom_banque ) .
                sprintf ( '%-11s', $desti[ 'bic' ] ) .
                sprintf ( '%-2s', $desti[ 'pays' ] ) .
                sprintf ( '%157s', null );
        }

        /**
         * @param $intermediaire
         *
         * @return string
         * @throws InvalidArgumentException
         */
        public function getIntermediaryBank ( $intermediaire )
        {
            $this->setNumberOfSequence ();
            $nom_banque = '';
            if ( $intermediaire[ 'bic' ] === '' )
            {
                if ( $intermediaire[ 'nom_banque' ] === '' )
                {
                    throw new InvalidArgumentException( "You must provid beneficiary bank name OR BIC code" );
                }
                $nom_banque = $intermediaire[ 'nom_banque' ];
            }
            return '06' . 'PI' .
                sprintf ( '%06s', $this->number_of_sequence ) .
                sprintf ( '%140s', $nom_banque ) .
                sprintf ( '%-11s', $intermediaire[ 'bic' ] ) .
                sprintf ( '%-2s', $intermediaire[ 'pays' ] ) .
                sprintf ( '%157s', null );
        }

        public function getComplementaryPayementInfo ( $desti )
        {
            $this->setNumberOfSequence ();
            return '07' . 'PI' .
                sprintf ( '%06s', $this->number_of_sequence ) .
                sprintf ( '%-140s', $desti[ 'motif' ] ) .
                sprintf ( '%-1s', null ) .
                sprintf ( '%-16s', null ) .
                sprintf ( '%-8s', null ) .
                sprintf ( '%-12s', null ) .
                sprintf ( '%-105s', $desti[ 'instruction_particulier' ] ) .
                sprintf ( '%28s', null );
        }

        /**
         * Generate footer for the file
         *
         * @return string
         */
        public function getFooterLine ()
        {
            $this->setNumberOfSequence ();
            $totalAmount   = $this->getTotalAmount ();
            $date_creation = $this->data_emetteur[ 'date_creation' ] == 'NOW'
                ? time () : (int) $this->data_emetteur[ 'date_creation' ];
            return '08' . 'PI' .
                sprintf ( '%06s', $this->number_of_sequence ) .
                date ( 'Ymd', $date_creation ) .
                sprintf ( '%140s', null ) .
                sprintf ( '%-14s', $this->data_emetteur[ 'siret' ] ) .
                sprintf ( '%-16s', $this->data_emetteur[ 'reference_virement' ] ) .
                sprintf ( '%11s', null ) .
                sprintf ( '%-1s', $this->data_emetteur[ 'type_num_compte' ] ) .
                sprintf ( '%-34s', $this->data_emetteur[ 'numero_compte' ] ) .
                sprintf ( '%-3s', $this->data_emetteur[ 'devise_compte' ] ) .
                sprintf ( '%-16s', $this->data_emetteur[ 'numero_emetteur' ] ) .
                sprintf ( '%018s', $totalAmount ) .
                sprintf ( '%49s', null );
        }
    }