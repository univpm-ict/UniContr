<?php
/**
 * uniurb/unidem package configuration file.
 */
return [

    /**
     * Configurazione data e ora:
     */
    'date_format'        => 'd-m-Y',
    'time_format'        => 'H:i:s',
    'datetime_format' => 'd-m-Y H:i:s',
    'timezone' => 'Europe/Rome',

    'date_format_contratto' => 'd/m/Y',


    'route'              => '',
    /**
     * URL target per l'applicazione client
     */
    'client_url' => env('CLIENT_URL', 'http://localhost:4200/'),

    //Configurazioni per dati di seed

    /**
     * Codici unità organizzativa associati al ruolo super-admin
     */
    'unitaSuperAdmin' => ['000052'], //Ufficio Gestione e integrazione applicativi di Amministrazione
    /**
     * Codici unità organizzativa associati al ruolo admin
     */
    'unitaAdmin' => explode(',',env('UFF_ADMIN','092130')), //Servizio Personale Docente e di Ricerca
    /**
     * Codici unità organizzativa associati al ruolo op_approvazione_amm
     */
    'ufficiPerValidazioneAmm' =>  explode(',',env('UFF_VALIDAZIONE_AMM', '000066')), //Ufficio Contratti e Supplenze
    /**
     * Codici unità organizzativa associati al ruolo op_approvazione_economica
     */
    'ufficiPerValidazioneEconomica' =>  explode(',',env('UFF_VALIDAZIONE_ECONOMICA', '000073')), //Ufficio Compnesi
    /**
     * Codici unità organizzativa associati al ruolo op_dipartimentale
     */
    'ufficiOpDipartimentale' =>  [                 //Nuclei didattici
        '092510',                                   //Nucleo didattico Agraria
        '092711',                                   //Nucleo didattico Economia
        '092710',                                   //Nucleo didattico Ingegneria
        '092330',                                   //Nucleo didattico Medicina e Chirurgia
        '092651'                                    //Nucleo didattico Scienze
    ],
    /**
     * Codici unità organizzativa associati al ruolo op_approvazione
     */
    'ufficiPerValidazione' =>  explode(',',env('UFF_VALIDAZIONE', '')), //Non utilizzato
    /**

    /**
     * Tipologie di corsi per didattica ufficiale.
     */
    'corsiUfficiali' => array_map('trim', explode(',', env('TIPI_CONTRATTO_DIDATTICA_UFFICIALE', 'CONTC,CONTU,CONTR'))),

    /**
     * Tipologie di corsi per didattica integrativa.
     */
    'corsiIntegrativi' => array_map('trim', explode(',', env('TIPI_CONTRATTO_DIDATTICA_INTEGRATIVA', 'INTC,INTU,INTXU,INTXC'))),

    /**
     * Tipologie di corsi di supporto.
     */
    'corsiSupporto' => array_map('trim', explode(',', env('TIPI_CONTRATTO_SUPPORTO_DIDATTICA', 'SUPPU,SUPPC'))),

    /**
     * Tipologie di corsi di alta qualificazione.
     */
    'corsiAltaQualificazione' => array_map('trim', explode(',', env('TIPI_CONTRATTO_ALTA_QUALIFICAZIONE', 'ALTQG,ALTQC,ALTQU'))),

    //configurazione email

    /**
     * Ufficio per validazione contratti ALD
     */
    'ufficio_stipendi' => ['stipendi@univpm.it'],

    /**
     * Lista email separate da , per amministratori di sistema
     */
    'administrator_email' =>  explode(',',env('ADMINISTRATOR_EMAIL', 'f.a.pileo@univpm.it,d.attanasio@univpm.it')),

    /**
     * Lista email separate da , per spedizione report alle segreterie marco.bernardo@uniurb.it
     */
    'cc_report_segreterie' =>  explode(',',env('CC_REPORT_SEGRETERIE', 'gianluca.antonelli@uniurb.it,antonio.micheli@uniurb.it,luca.zigoli@uniurb.it,francesca.fumelli@uniurb.it,pierangela.donnanno@uniurb.it')),

    'dispea_report_segreterie' =>  array_map('trim',explode(',',env('DISPEA_REPORT_SEGRETERIE', 'direttore.dispea@uniurb.it, segreteria.dispea@uniurb.it,scuola.restauro@uniurb.it,scuola.geologia@uniurb.it,scuola.stefi@uniurb.it, amministrazione.reclutamento.pdoc@uniurb.it'))),
    'disb_report_segreterie' =>  array_map('trim',explode(',',env('DISB_REPORT_SEGRETERIE', 'direttore.disb@uniurb.it, segreteria.disb@uniurb.it, scuola.farmacia@uniurb.it, scuola.sbb@uniurb.it, scuola.motorie@uniurb.it, amministrazione.reclutamento.pdoc@uniurb.it'))),
    'digiur_report_segreterie' =>  array_map('trim',explode(',',env('DIGIUR_REPORT_SEGRETERIE', 'direttore.digiur@uniurb.it, segreteria.digiur@uniurb.it, scuola.giurisprudenza@uniurb.it, amministrazione.reclutamento.pdoc@uniurb.it'))),
    'discui_report_segreterie' =>  array_map('trim',explode(',',env('DISCUI_REPORT_SEGRETERIE', 'direttore.discui@uniurb.it, segreteria.discui@uniurb.it, scuola.lingue@uniurb.it, scuola.comunicazione@uniurb.it, amministrazione.reclutamento.pdoc@uniurb.it'))),
    'desp_report_segreterie' =>  array_map('trim',explode(',',env('DESP_REPORT_SEGRETERIE', 'direttore.desp@uniurb.it, segreteria.desp@uniurb.it, scuola.economia@uniurb.it, scuola.scienzepolitichesociali@uniurb.it, amministrazione.reclutamento.pdoc@uniurb.it'))),
    'distum_report_segreterie' =>  array_map('trim',explode(',',env('DISTUM_REPORT_SEGRETERIE', 'direttore.distum@uniurb.it, segreteria.distum@uniurb.it, scuola.lettere@uniurb.it, scuola.formazione@uniurb.it, amministrazione.reclutamento.pdoc@uniurb.it'))),

    /**
     * Lista email separate da , per notifica di visione accettazione da parte del docente
     */
    'firma_direttore_email' => explode(',',env('FIRMA_DIRETTORE_EMAIL',  'catia.rossi@uniurb.it')),

     /**
     * Lista email separate da , per notifica compilazione terminata da parte del docente
     */
    'cmu_email' => explode(',',env('CMU_EMAIL',  'unicontract@uniurb.it,amministrazione.reclutamento.pdoc@uniurb.it')),

    /**
     * Lista domini email istituzionali accettati.
     */
    'allowed_email_domains' => array_values(array_filter(array_map(
        static fn ($domain) => strtolower(trim(ltrim($domain, '@'))),
        explode(',', env('ALLOWED_EMAIL_DOMAINS', 'univpm.it'))
    ))),

    /**
     * Inserire nuovi IBAN in Ugov
     */
    'ins_iban_ugov' => env('INS_IBAN_UGOV',false),

    'tipi_firma_attivi' =>  array_map('trim',explode(',',env('TIPI_FIRMA_ATTIVI', 'FIRMAIO, USIGN, FIRMA_GRAFOMETRICA'))),  // , PRESA_VISIONE
];
