<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use App\User;
use App\Ruolo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use App\Service\EmailHelper;
use App\Service\LoginService;
use Exception;
use App\Exceptions\Handler;
use Illuminate\Container\Container;

class LoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Saml2LoginEvent $event)
    {
        $messageId = $event->getSaml2Auth()->getLastMessageId();
        Log::info('messageId [' . $messageId . ']');

        $attributesName = [
            'uid' => 'urn:oid:0.9.2342.19200300.100.1.1',
            'givenName' => 'urn:oid:2.5.4.42',
            'surname' => 'urn:oid:2.5.4.4',
            'codiceFiscale' => 'urn:oid:1.3.6.1.4.1.25178.1.2.15',
            'email' => 'urn:oid:0.9.2342.19200300.100.1.3',
            'eduPersonAffiliation' => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.1',
            'eduPersonScopedAffiliation' => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.9',
            'ruolo' => 'ateneoRuolo',
            'matricola' => 'urn:oid:2.5.4.3'
        ];

        $user = $event->getSaml2User();
        Log::info('user [' . $user->getUserId() . ']');
        $user->parseAttributes($attributesName);

        $userData = new \App\User;
        try{
            $userData->id = $user->getUserId();
            $userData->attributes = $user->getAttributes();
            $userData->name = $user->givenName[0] . ' ' . $user->surname[0];
            $userData->email = $user->email[0];
            Log::info('email [' . $userData->email . ']');
            // Check if the email domain is unauthorized
            if (strpos($userData->email, '@studenti.univpm.it') !== false) {
                // If the email domain is unauthorized, log it and return an error
                Log::info('Unauthorized user with email: ' . $userData->email);
                abort(401, trans('global.utente_non_autorizzato'));
            }

            // Check if the "ruolo" attribute exists and is not empty
            if (isset($user->ruolo[0]) && !empty($user->ruolo[0])) {
                $userData->ruolo = $user->ruolo[0];
                Log::info('ruolo [' . $userData->ruolo . ']');
            } else {
                // If "ruolo" is missing or empty, return an error
                throw new Exception('Ruolo non presente o vuoto');
            }

            $userData->eduPersonScopedAffiliation = $user->eduPersonScopedAffiliation;
            $userData->password =Hash::make($user->codiceFiscale[0]);
            $userData->assertion = $user->getRawSamlAssertion();
            $userData->cf = $user->codiceFiscale[0];
        }catch(Exception $e){
            Log::info('Errore metadati utente passati dall\'idp');
            $handler = new Handler(Container::getInstance());
            $handler->report($e);
            Log::info('Utente non autorizzato: '.$userData->email.' '.$userData->ruolo);
            abort(401,  trans('global.utente_non_autorizzato'));
        }

        //check if email already exists and fetch user
        $laravelUser = \App\User::where('email', $userData['email'])->first();
        Log::info('laravel user [' . $laravelUser . ']');
        //ulteriore verifica attraverso il codice fiscale
        if ($laravelUser===null){
            $laravelUser = \App\User::where('cf', $userData['cf'])->first();
            if ($laravelUser !== null ){
                //aggiornare email
                if (EmailHelper::hasAllowedDomain($userData['email'])){
                    $laravelUser->email = $userData['email'];
                    $laravelUser->save();
                    Log::info('Aggiornata email laravel user [' . $laravelUser->name . ']');
                }
            }
        }

        try{
            //if email doesn't exist, create new user
            if($laravelUser === null)
            {
                Log::info('inserisci utente [' . $userData->name . ' '. $userData->email . ' '.$user->codiceFiscale[0].' ]');
                $laravelUser = new \App\User;
                $laravelUser->name = $userData['name'];
                $laravelUser->email = $userData['email'];
                $laravelUser->password = Hash::make($user->codiceFiscale[0]);
                //Per ulteriore controllo memorizza anche il codice fiscale
                $laravelUser->cf = $user->codiceFiscale[0];

                Log::info('istanza utente [' . $laravelUser->name . ' '. $laravelUser->email . ' ]');
                //va determinato il ruolo da assegnare 1) leggerlo da file id configurazione, 2) ruolo di default
                $service = new LoginService();
                Log::info('istanza service');
                $data = null;
                if ($userData['ruolo'] && Ruolo::isRuoloDocente($userData['ruolo'])){
                    $data = $service->findDocenteData($userData->email);
                }else{
                    $data = $service->findUserRoleAndData($userData->email);
                }

                Log::info('ruoli [' . implode(';',$data['ruoli']) . ']');

                if ($data){
                    $laravelUser->v_ie_ru_personale_id_ab = $data['id_ab'];
                    $laravelUser->save();
                    $laravelUser->assignRole($data['ruoli']);
                }

            }

        } catch (\Exception $e) {
            Log::info('Errore nuovo utente non creato');
            $handler = new Handler(Container::getInstance());
            $handler->report($e);

            Log::info('Utente non autorizzato: '.$userData->email.' '.$userData->ruolo);
            abort(401,  trans('global.utente_non_autorizzato'));
        }

        // Here we save the received nameId and sessionIndex needed later for the LogoutRequest
        session()->put('nameId', $user->getNameId());
        session()->put('sessionIndex', $user->getSessionIndex());

        Log::info('login [' . $laravelUser->name . ']');
        Auth::login($laravelUser);
    }
}
