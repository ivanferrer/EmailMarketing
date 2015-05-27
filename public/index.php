<?php
header('Content-Type:text/html; charset=utf-8', true);

//http://localhost/EmailMarketing

define('SITE_SUBFOLDER', 'http://'.$_SERVER['SERVER_NAME']);

//configuração para entrar no sistema
define('USERNAME_EMKT', 'admin');
define('PASSWORD_EMKT', 'admin');

//configuração para e-mail
define('NAME_SENDER_CLIENT', 'Nome Sender');
define('EMAIL_SENDER_CLIENT', 'emailresposta@servidor.com.br'); //email para a resposta
define('EMAIL_SENDER_SIS', 'seuemail@servidor.com'); //email do sistema
define('PASS_SENDER_SIS', '123'); //senha do email do sistema // AK$3k_2@1LK&DMO)A*1953%
define('PORT_MAILER',587); //PORTA DE ENVIO - define('PORT_MAILER', 465);
define('TYPE_AUTH_SIS', 'login'); //TIPO DE AUTENTICAÇÃO (OPÇÕES: 'login', '')
define('SSL_PROTOCOL', 'tls'); //PORTA DE ENVIO - define('SSL_PROTOCOL', ' ssl');
define('SMTP_ACCOUNT', 'smtp.servidor.com'); //PORTA DE ENVIO
define('SHORTURL', false); //true or false para links incurtados;
define('VERSAO_SHORTURL', '2.0.1');
define('FORMAT_SHORTURL', 'xml');
define('LOGIN_SHORTURL',  'o_8bq8799qj');
define('KEY_SHORTURL', 'R_a55c50ad5d094443941a03b55b64ea9c');



// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

//define a url de remoção de email...
//define('URL_SITE', $_SERVER['SERVER_NAME']);



/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()
            ->run();

