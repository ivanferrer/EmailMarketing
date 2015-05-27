<?php

class GenericController extends Zend_Controller_Action
{

    private $sessao = null;

    public function init(){
        $this->sessao_agenda = new Zend_Session_Namespace('sessao_agenda');

        $modelBanco = new Application_Model_ConfigSendMail();
        $dataBanco = $modelBanco->getConfig(1);

        if(count($dataBanco)) {
            define('EMAIL_SENDER_SIS',$dataBanco->snd_login);
            define('PASS_SENDER_SIS',$dataBanco->snd_senha);
            define('PORT_MAILER',$dataBanco->snd_port);
            define('SSL_PROTOCOL',$dataBanco->snd_ssl_protocolo);
            define('NAME_SENDER_CLIENT',$dataBanco->snd_nome);
            define('EMAIL_SENDER_CLIENT', $dataBanco->snd_email_envio); //email para a resposta
            define('SMTP_ACCOUNT',$dataBanco->snd_smtp);

        }

    }



    public function indexAction()
    {
    //no use
    }


}

