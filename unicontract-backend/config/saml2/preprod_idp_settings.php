<?php

// If you choose to use ENV vars to define these values, give this IdP its own env var names
// so you can define different values for each IdP, all starting with 'SAML2_'.$this_idp_env_id
$this_idp_env_id = 'PREPROD';

//This is variable is for simplesaml example only.
// For real IdP, you must set the url values in the 'idp' config to conform to the IdP's real urls.
$idp_host = env('SAML2_'.$this_idp_env_id.'_IDP_HOST', 'https://idptest.uniurb.it/');

return $settings = array(

    /*****
     * One Login Settings
     */

    // If 'strict' is True, then the PHP Toolkit will reject unsigned
    // or unencrypted messages if it expects them signed or encrypted
    // Also will reject the messages if not strictly follow the SAML
    // standard: Destination, NameId, Conditions ... are validated too.
    //'strict' => true, //@todo: make this depend on laravel config

    // Enable debug mode (to print errors)
    'debug' => env('APP_DEBUG', true),


    // Service Provider Data that we are deploying
    'sp' => array(

        // Specifies constraints on the name identifier to be used to
        // represent the requested subject.
        // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',

        // Usually x509cert and privateKey of the SP are provided by files placed at
        // the certs folder. But we can also provide them with the following parameters
        'x509cert' => file_exists(env('SAML2_SP_x509_PATH', '')) ? file_get_contents(env('SAML2_SP_x509_PATH', '')) : '',
        'privateKey' => file_exists(env('SAML2_SP_PRIVATEKEY_PATH', '')) ? file_get_contents(env('SAML2_SP_PRIVATEKEY_PATH', '')) : '',

        // Identifier (URI) of the SP entity.
        // Leave blank to use the 'saml_metadata' route.
        'entityId' => env('SAML2_SP_ENTITYID',''),

        // Specifies info about where and how the <AuthnResponse> message MUST be
        // returned to the requester, in this case our SP.
        'assertionConsumerService' => array(
            // URL Location where the <Response> from the IdP will be returned,
            // using HTTP-POST binding.
            // Leave blank to use the 'saml_acs' route
            'url' => '',
        ),
        // Specifies info about where and how the <Logout Response> message MUST be
        // returned to the requester, in this case our SP.
        // Remove this part to not include any URL Location in the metadata.
        'singleLogoutService' => array(
            // URL Location where the <Response> from the IdP will be returned,
            // using HTTP-Redirect binding.
            // Leave blank to use the 'saml_sls' route
            'url' => '',
        ),
    ),

    // Identity Provider Data that we want connect with our SP
    'idp' => array(
        // Identifier of the IdP entity  (must be a URI)
        //https://idptest.uniurb.it/idp/shibboleth
        'entityId' => env('SAML2_'.$this_idp_env_id.'_IDP_ENTITYID', $idp_host.'idp/shibboleth'), //. 'idp/shibboleth'
        // SSO endpoint info of the IdP. (Authentication Request protocol)
        'singleSignOnService' => array(
            // URL Target of the IdP where the SP will send the Authentication Request Message,
            // using HTTP-Redirect binding.
            'url' => $idp_host.'idp/profile/SAML2/Redirect/SSO',

        ),
        // SLO endpoint info of the IdP.
        'singleLogoutService' => array(
            // URL Location of the IdP where the SP will send the SLO Request,
            // using HTTP-Redirect binding.
            //https://ds90p01.bib.uniurb.it/Shibboleth.sso/Logout?return=https://idp.uniurb.it/idp/profile/Logout
            'url' => $idp_host.'idp/profile/SAML2/Redirect/SLO',
        ),
        // Public x509 certificate of the IdP
        'x509cert' => env('SAML2_IDP_x509', 'MIIDCTCCAfGgAwIBAgIJAJb36sy/0qwIMA0GCSqGSIb3DQEBCwUAMBsxGTAXBgNV
        BAMMEHNzby5wcC51bml2cG0uaXQwHhcNMTgwOTEwMTMzMDA1WhcNMzgwOTA5MTMz
        MDA1WjAbMRkwFwYDVQQDDBBzc28ucHAudW5pdnBtLml0MIIBIjANBgkqhkiG9w0B
        AQEFAAOCAQ8AMIIBCgKCAQEA40prKyVtJj2s598Uz2jJfvw7JT4dQcIYSWTEZm+I
        VvuaFMaW5vOEsZroR8zchETnP0YFlbaSSeiISA5v9EL+9gOIPi1EukECIhq7RGFx
        cSUkzzYCCiyiSYJIRi18Y1ic5uyH2ju737mbIfzjA0MJCrfneJ89MQiryUhwMvpl
        ve9lR594GL9A+krsECJ3jpwPyMwT7O8ktO2IQCacgCw2m2apgmYclIiuVtBsNwZW
        uBsG5zJqTJ6FUKW7sJDqA3m8ug43W6IAIJ+0vU4xNmnyzluP5QX6gftfE3YUlbfd
        C+Fe/wUcqVScGDuFOly6wE1/t73cfN0n5RA7/+Np5PFcMwIDAQABo1AwTjAdBgNV
        HQ4EFgQUNI4DH29gzZehjWDl7WgeXamDqBIwHwYDVR0jBBgwFoAUNI4DH29gzZeh
        jWDl7WgeXamDqBIwDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQsFAAOCAQEAdq+a
        hbcxyQH+iCVrbD6qG0RpnAEj54DfKATx0k+SISoHQHHW7jg+4QhdXtiOjyydmG1d
        S/WWqcLAdJgATMgjMInwoGQ78LaOczcJAZ75lBxhzUdavBdCxwSTaxZQXcuVKLbw
        IrbsdJpQvt0zCN8uRJyUCypsT7o41cfCGr0YCVGJx6oVRyppypIMoS2ekxThzzhX
        4H7+peWEp/JK8G62lUofSW73DLRJPNHwC7zFzzY++9Yx6l4sOWV1KK5Y7dvsl81Q
        JD8gcC2awRYZlOao/eFOctNOoQpWwgyEnUvX41Sbzv+j4y6D0cHNFHnmdM7I1uh5
        OouYVCUFUt6+5hx+7g=='),
        /*
         *  Instead of use the whole x509cert you can use a fingerprint
         *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it)
         */
        // 'certFingerprint' => '',
    ),



    /***
     *
     *  OneLogin advanced settings
     *
     *
     */
    // Security settings
    'security' => array(

        /** signatures and encryptions offered */

        // Indicates that the nameID of the <samlp:logoutRequest> sent by this SP
        // will be encrypted.
        'nameIdEncrypted' => false,

        // Indicates whether the <samlp:AuthnRequest> messages sent by this SP
        // will be signed.              [The Metadata of the SP will offer this info]
        'authnRequestsSigned' => false,

        // Indicates whether the <samlp:logoutRequest> messages sent by this SP
        // will be signed.
        'logoutRequestSigned' => false,

        // Indicates whether the <samlp:logoutResponse> messages sent by this SP
        // will be signed.
        'logoutResponseSigned' => false,

        /* Sign the Metadata
         False || True (use sp certs) || array (
                                                    keyFileName => 'metadata.key',
                                                    certFileName => 'metadata.crt'
                                                )
        */
        'signMetadata' => false,


        /** signatures and encryptions required **/

        // Indicates a requirement for the <samlp:Response>, <samlp:LogoutRequest> and
        // <samlp:LogoutResponse> elements received by this SP to be signed.
        'wantMessagesSigned' => false,

        // Indicates a requirement for the <saml:Assertion> elements received by
        // this SP to be signed.        [The Metadata of the SP will offer this info]
        'wantAssertionsSigned' => false,

        // Indicates a requirement for the NameID received by
        // this SP to be encrypted.
        'wantNameIdEncrypted' => false,

        'wantAssertionsEncrypted' => false,
        // Authentication context.
        // Set to false and no AuthContext will be sent in the AuthNRequest,
        // Set true or don't present thi parameter and you will get an AuthContext 'exact' 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport'
        // Set an array with the possible auth context values: array ('urn:oasis:names:tc:SAML:2.0:ac:classes:Password', 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509'),
        'requestedAuthnContext' => false,

          //'signatureAlgorithm' => 'http://www.w3.org/2000/09/xmldsig#rsa-sha1',
        'signatureAlgorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',

        //'digestAlgorithm' =>  'http://www.w3.org/2000/09/xmldsig#sha1',
        'digestAlgorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',
    ),

   // Contact information template, it is recommended to suply a technical and support contacts
   'contactPerson' => array(
        'technical' => array(
            'givenName' => env('SUPPORT_NAME', ''),
            'emailAddress' => env('SUPPORT_EMAIL', '')
        ),
        'support' => array(
            'givenName' => env('SUPPORT_NAME', ''),
            'emailAddress' => env('SUPPORT_EMAIL', '')
        ),
    ),

    // Organization information template, the info in en_US lang is recomended, add more if required
    'organization' => array(
        'it-IT' => array(
            'name' => env('ATENEO_NAME', ''),
            'displayname' => env('ATENEO_DISPLAYNAME', ''),
            'url' => env('ATENEO_HOME_URL', '')
        ),
    ),

/* Interoperable SAML 2.0 Web Browser SSO Profile [saml2int]   http://saml2int.org/profile/current
*/
  // 'authnRequestsSigned' => false,    // SP SHOULD NOT sign the <samlp:AuthnRequest>,
                                      // MUST NOT assume that the IdP validates the sign
  // 'wantAssertionsSigned' => true,
  // 'wantAssertionsEncrypted' => true, // MUST be enabled if SSL/HTTPs is disabled
  // 'wantNameIdEncrypted' => false,


);

