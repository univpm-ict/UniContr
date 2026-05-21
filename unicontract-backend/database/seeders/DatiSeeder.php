<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Convenzione;
use App\MappingRuolo;
use App\Role;
use App\Personale;
use App\Precontrattuale;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

//php artisan db:seed --class=DatiSeeder
//composer dump-autoload -o

////php artisan migrate:fresh --seed
class DatiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->attachmenttypes();
        $this->mappingtable();
        $this->mappingruoli();
    }



    private function onlyFirstUpper($value){
        return ucwords(mb_strtolower($value, 'UTF-8'));
    }


    private function insertOffice(Array $offices, $rolename){
        $role = Role::where('name', $rolename)->first();
        $useOracle = !app()->environment('testing') && $this->oracleConnected();

        foreach ($offices as $office) {
            $mp = new MappingRuolo();
            $mp->unitaorganizzativa_uo = $office;
            $mp->descrizione_uo = "Fake descr for $office";

            if ($useOracle) {
                try {
                    $uo = $mp->unitaorganizzativa()->first();
                    if ($uo && !empty($uo->descr)) {
                        $mp->descrizione_uo = $uo->descr;
                    }
                } catch (\Exception $e) {
                    // Keep the fake description when Oracle is unavailable.
                }
            }

            $mp->role_id = $role->id;
            $mp->save();
        }
    }

    /**
     * Check if Oracle connection is alive
     */
    private function oracleConnected(): bool
    {
        try {
            DB::connection('oracle')->getPdo();
            return true;
        } catch (\Exception $e) {
            // Could not connect
            return false;
        }
    }

    /**
     * Tabella di corrispondenza tra unità organizzativa e ruolo
     *
     * @return void
     */
    public function mappingruoli(){

        $this->insertOffice(config('unidem.unitaSuperAdmin'), 'super-admin');
        $this->insertOffice(config('unidem.unitaAdmin'), 'admin');
        $this->insertOffice(config('unidem.ufficiPerValidazione'), 'op_approvazione');

        $this->insertOffice(config('unidem.ufficiPerValidazioneAmm'), 'op_approvazione_amm');
        $this->insertOffice(config('unidem.ufficiPerValidazioneEconomica'), 'op_approvazione_economica');

        $this->insertOffice(config('unidem.ufficiOpDipartimentale'), 'op_dipartimentale');
    }


    /**
     * Tabella di corrispondenza unità organizzativa e struttura interna
     *
     * @return void
     */
    public function mappingtable(){

        //Ufficio Contratti e Supplenze
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '000066',
            'descrizione_uo' => 'Ufficio Contratti e Supplenze',
            'strutturainterna_cod_uff' => '000066',
            'descrizione_uff' => 'Ufficio Contratti e Supplenze',
        ]);

        //Servizio Personale Docente e di Ricerca
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '092130',
            'descrizione_uo' => 'Servizio Personale Docente e di Ricerca',
            'strutturainterna_cod_uff' => 'SI000183',
            'descrizione_uff' => 'Servizio Personale Docente e di Ricerca',
        ]);

        //Ufficio Compensi
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '000073',
            'descrizione_uo' => 'Ufficio Compensi',
            'strutturainterna_cod_uff' => '000073',
            'descrizione_uff' => 'Ufficio Compensi',
        ]);

        // Nuclei didattici
        //092510 Nucleo didattico Agraria
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '092510',
            'descrizione_uo' => 'Ufficio Nucleo didattico Agraria',
            'strutturainterna_cod_uff' => 'SI000238',
            'descrizione_uff' => 'Ufficio Nucleo didattico Agraria',
        ]);

        //092711 Nucleo didattico Economia
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '092711',
            'descrizione_uo' => 'Ufficio Nucleo didattico Economia',
            'strutturainterna_cod_uff' => 'SI000250',
            'descrizione_uff' => 'Ufficio Nucleo didattico Economia',
        ]);

        //092710 Nucleo didattico Ingegneria
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '092710',
            'descrizione_uo' => 'Ufficio Nucleo didattico Ingegneria',
            'strutturainterna_cod_uff' => 'SI000251',
            'descrizione_uff' => 'Ufficio Nucleo didattico Ingegneria',
        ]);

        //092330 Nucleo didattico Medicina e Chirurgia
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '092330',
            'descrizione_uo' => 'Ufficio Nucleo didattico Medicina',
            'strutturainterna_cod_uff' => 'SI000273',
            'descrizione_uff' => 'Ufficio Nucleo didattico Medicina',
        ]);

        //092651 Nucleo didattico Scienze
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '092651',
            'descrizione_uo' => 'Ufficio Nucleo didattico Scienze',
            'strutturainterna_cod_uff' => 'SI000249',
            'descrizione_uff' => 'Ufficio Nucleo didattico Scienze',
        ]);

        // Mapping dipartimento -> ufficio nucleo didattico
        // D3A
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '040027',
            'descrizione_uo' => 'Dipartimento D3A',
            'strutturainterna_cod_uff' => 'SI000238',
            'descrizione_uff' => 'Ufficio Nucleo didattico Agraria',
        ]);

        // DIMA
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '040018',
            'descrizione_uo' => 'Dipartimento DIMA',
            'strutturainterna_cod_uff' => 'SI000250',
            'descrizione_uff' => 'Ufficio Nucleo didattico Economia',
        ]);

        // DISES
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '040002',
            'descrizione_uo' => 'Dipartimento DISES',
            'strutturainterna_cod_uff' => 'SI000250',
            'descrizione_uff' => 'Ufficio Nucleo didattico Economia',
        ]);

        // DICEA
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '040042',
            'descrizione_uo' => 'Dipartimento DICEA',
            'strutturainterna_cod_uff' => 'SI000251',
            'descrizione_uff' => 'Ufficio Nucleo didattico Ingegneria',
        ]);

        // DII
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '040040',
            'descrizione_uo' => 'Dipartimento DII',
            'strutturainterna_cod_uff' => 'SI000251',
            'descrizione_uff' => 'Ufficio Nucleo didattico Ingegneria',
        ]);

        // DIISM
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '040004',
            'descrizione_uo' => 'Dipartimento DIISM',
            'strutturainterna_cod_uff' => 'SI000251',
            'descrizione_uff' => 'Ufficio Nucleo didattico Ingegneria',
        ]);

        // SIMAU
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '040008',
            'descrizione_uo' => 'Dipartimento SIMAU',
            'strutturainterna_cod_uff' => 'SI000251',
            'descrizione_uff' => 'Ufficio Nucleo didattico Ingegneria',
        ]);

        // DIMSC
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '040046',
            'descrizione_uo' => 'Dipartimento DIMSC',
            'strutturainterna_cod_uff' => 'SI000273',
            'descrizione_uff' => 'Ufficio Nucleo didattico Medicina',
        ]);

        // DISCO
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '040054',
            'descrizione_uo' => 'Dipartimento DISCO',
            'strutturainterna_cod_uff' => 'SI000273',
            'descrizione_uff' => 'Ufficio Nucleo didattico Medicina',
        ]);

        // DISCLIMO
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '040020',
            'descrizione_uo' => 'Dipartimento DISCLIMO',
            'strutturainterna_cod_uff' => 'SI000273',
            'descrizione_uff' => 'Ufficio Nucleo didattico Medicina',
        ]);

        // DISBSP
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '040024',
            'descrizione_uo' => 'Dipartimento DISBSP',
            'strutturainterna_cod_uff' => 'SI000273',
            'descrizione_uff' => 'Ufficio Nucleo didattico Medicina',
        ]);

        // DISVA
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '040017',
            'descrizione_uo' => 'Dipartimento DISVA',
            'strutturainterna_cod_uff' => 'SI000249',
            'descrizione_uff' => 'Ufficio Nucleo didattico Scienze',
        ]);

    }

    /**
     * Tipi di allegato
     *
     * @return void
     */
    public function attachmenttypes(){
        DB::table('attachmenttypes')->insert([
            'codice' => 'DOC_CV',
            'gruppo' => 'anagrafica',
            'descrizione' => 'Curriculum',
            'descrizione_compl' => 'Curriculum',
            'parent_type' => User::class,
        ]);

        DB::table('attachmenttypes')->insert([
            'codice' => 'DOC_CI',
            'gruppo' => 'anagrafica',
            'descrizione' => 'Carta di identità',
            'descrizione_compl' => 'Carta di identità',
            'parent_type' => User::class,
        ]);

        DB::table('attachmenttypes')->insert([
            'codice' => 'AUT_PA',
            'gruppo' => 'B4RapportoPA',
            'descrizione' => 'Autorizzazione PA',
            'descrizione_compl' => 'Autorizzazione Pubblica Amministrazione',
            'parent_type' => B4RapportoPA::class,
        ]);

        DB::table('attachmenttypes')->insert([
            'codice' => 'CONTR_BOZZA',
            'gruppo' => 'Precontrattuale',
            'descrizione' => 'Contratto bozza',
            'descrizione_compl' => 'Contratto in stato di bozza',
            'parent_type' => Precontrattuale::class,
        ]);
        DB::table('attachmenttypes')->insert([
            'codice' => 'CONTR_FIRMA',
            'gruppo' => 'Precontrattuale',
            'descrizione' => 'Contratto',
            'descrizione_compl' => 'Contratto',
            'parent_type' => Precontrattuale::class,
        ]);

        DB::table('attachmenttypes')->insert([
            'codice' => 'DOM_GS',
            'gruppo' => 'D1_Inps',
            'descrizione' => 'Iscrizione GS',
            'descrizione_compl' => 'Domanda di iscrizione alla Gestione Separata',
            'parent_type' => D1_Inps::class,
        ]);

    }
}
