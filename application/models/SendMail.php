<?php

class Application_Model_SendMail extends Zend_View_Helper_BaseUrl
{

    private $enviaEmail = array();
    private $recebeEmail = array();
    private $retornoEmail = array();
    private $html_body = array();
    public $_baseUrl = array();
    public $assunto = array();

    private $email_sender_sis;
    private $email_sender_client;
    private $name_sender_client;
    private $pass_sender_sis;
    private $port_mailer;
    private $ssl_protocol;
    private $smtp_account;

    public function setBody(array $arguments = array(), $assunto = 'MENSAGEM DE EMAIL')
    {
        //configuração servidor de envio
        $modelBanco = new Application_Model_ConfigSendMail();
        $dataBanco = $modelBanco->getConfig(1);

        if(count($dataBanco)) {

            $this->email_sender_sis = $dataBanco->snd_login;
            $this->pass_sender_sis = $dataBanco->snd_senha;
            $this->port_mailer = $dataBanco->snd_port;
            $this->ssl_protocol = $dataBanco->snd_ssl_protocolo;
            $this->smtp_account = $dataBanco->snd_smtp;
            $this->email_sender_client = $dataBanco->snd_email_envio;
            $this->name_sender_client = $dataBanco->snd_nome;

        } else {

            $this->email_sender_sis = EMAIL_SENDER_SIS;
            $this->pass_sender_sis = PASS_SENDER_SIS;
            $this->port_mailer =PORT_MAILER;
            $this->ssl_protocol = SSL_PROTOCOL;
            $this->smtp_account = SMTP_ACCOUNT;
            $this->email_sender_client = EMAIL_SENDER_CLIENT;
            $this->name_sender_client = NAME_SENDER_CLIENT;

        }


        $layout = ($arguments['template']) ? $arguments['template'] : 'template_emkt.phtml';
        $email_send = $this->email_sender_client;
        $email_receive = ($arguments['email_recebe']) ? $arguments['email_recebe'] :  $this->email_sender_client;
        $name_receive = ($arguments['nome_recebe']) ? $arguments['nome_recebe'] : 'Usuário';
        $text_receive = ($arguments['dinamic_content']) ? $arguments['dinamic_content'] : '-';

        $envia = array(
            'email' => $email_send,
            'nome' => $this->name_sender_client);
        $recebe = array(
            'email' => $email_receive,
            'nome' => $name_receive);

        $this->assunto = $assunto;
        $front_controller = Zend_Controller_Front::getInstance();
        $request = new Zend_Controller_Request_Http();
        $this->_baseUrl = $request->getBaseUrl();
        $myView = new Zend_View();

        $myView->addScriptPath(APPLICATION_PATH . '/views/scripts/templates/');
        //$myView->addScriptPath($url.'/views/scripts/templates/'.$layout;);
        $html_body = $myView->render($layout);

        //substituí as tags pelo original
        $html_body = preg_replace('/{email}/', $email_receive, $html_body);
        $html_body = preg_replace('/{nome}/', $name_receive, $html_body);
        $html_body = preg_replace('/{texto_dinamico}/', $text_receive, $html_body);

        $html_body = preg_replace('/^<a(.*?)href=("|\')(.+)("|\')(.*?)>$/','<a$1href=$2$3$2$5>',$html_body);

        preg_match_all('<a(.*?)href=("|\')([^"]+)("|\')(.*?)>', $html_body, $matches);


        if(count($matches)) {
            $arrayUrls = $matches[3];
            $urls = array_values(array_filter($arrayUrls));
        }

        if(count($urls) ) {
            foreach ($urls as $k => $url) {

                $urlString = SITE_SUBFOLDER.'/index/click/k/'.str_replace('/','::',base64_encode($email_receive)).'/url/'.str_replace('/','::',base64_encode(urlencode($url)));
                $urlString = $this->makeShortUrlBitly($urlString);
                $html_body = preg_replace('/<a(.*?)href=("|\')'.preg_quote($url, '/').'("|\')(.*?)>/', '<a$1href=$2'.$urlString.'$2$5 mc:disable-tracking>', $html_body);

            }
        }

//        if(count($urls) ) {
//            foreach ($urls as $k => $url) {
//
//                $urlString = SITE_SUBFOLDER.'/index/click/k/'.str_replace('/','::',base64_encode($email_receive)).'/url/'.str_replace('/','::',base64_encode(utf8_encode($url)));
//                $urlString = $this->makeShortUrlBitly($urlString);
//               // $html_body = preg_replace('/<a(.*?)href=("|\')'.preg_quote($url, '/').'("|\')(.*?)>/', '<a$1href=$2'.$urlString.'$2$5 mc:disable-tracking>', $html_body);
//
//                $html_body = str_replace($url, $urlString, $html_body);
//
//            }
//        }

        $html_body.='<p>Se não deseja receber mais mensagens, <a href="'.$this->makeShortUrlBitly(SITE_SUBFOLDER.'/index/remover-email/k/'.str_replace('/','::',base64_encode($email_receive))).'" mc:disable-tracking>clique aqui</a>.</p>';



        $this->enviaEmail = $envia;
        $this->recebeEmail = $recebe;
        $this->retornoEmail = $recebe;
        $this->html_body = $html_body;



    }

    public function sendEmail()
    {
        $return = false;

        $retorno_resposta = $this->retornoEmail['email'];
        $nome_resp = $this->retornoEmail['nome'];
        $emailFrom = $this->enviaEmail['email'];
        $nomeFrom = $this->enviaEmail['nome'];
        $nomeTo = $this->recebeEmail['nome'];
        $emailTo = $this->recebeEmail['email'];
        //  
        /*
         * $emailTo = 'joaopedro@progiro.com.br';


          $body.=$quem_recebe[0];
          $body.=$quem_recebe[1];
         */
        //  $emailTo = 'ivanferrer@progiro.com.br';
// ->addTo('infonatal@agenciaestacaobrasil.com.br', 'Nome') // para quem esta enviando
        $mail = new Zend_Mail();
        $mail->setFrom($emailFrom, utf8_decode($nomeFrom)) // Quem esta enviando
        ->addTo($emailTo, utf8_decode($nomeTo)) // para quem esta enviando
        ->setBodyText(strip_tags($this->html_body), 'UTF-8') // mensagem sem formatação
        ->setBodyHtml($this->html_body, 'UTF-8') // mensagem sem formata?
        ->setSubject(utf8_decode($this->assunto)) // Assunto
        ->setReturnPath($retorno_resposta, $nome_resp)
            ->setReplyTo($emailFrom, $nomeFrom);
        //->addTextHeader('X-MC-GoogleAnalytics', $emailFrom)
        //->addTextHeader('Sender', $emailFrom);

        try {
            $mail->send($this->getMailInstance()); // Enviar
            $return = true;
        } catch (Zend_Mail_Exception $e) {
            $return = $e->getMessage() . '\n' . $e->getTraceAsString();
            $this->gravarLogErroEmail($return);
        }
        return $return;
    }

    private function getMailInstance()
    {
        require_once 'Zend/Mail/Transport/Smtp.php';

        $config['auth'] = TYPE_AUTH_SIS;// tipo de autencicação: login
        $config['username'] = $this->email_sender_sis; // informa o login do E-mail
        $config['password'] = $this->pass_sender_sis; // senha
        $config['port'] = $this->port_mailer;
        $config['ssl'] = $this->ssl_protocol;;
        return new Zend_Mail_Transport_Smtp($this->smtp_account, $config);
    }

    private function gravarLogErroEmail($dados)
    {
        $diretorio_log = realpath(APPLICATION_PATH) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'logemail';
        $filename = $diretorio_log . DIRECTORY_SEPARATOR . 'LogEmail_' . date('YmdHis') . '_' . substr(md5(rand(000, 999)), 0, 5) . '.log';
        $dados = (string) print_r($dados, true);
        file_put_contents($filename, $dados);
    }

    public function makeShortUrlBitly($url)
    {

        $version = VERSAO_SHORTURL;
        $format = FORMAT_SHORTURL;
        $login = LOGIN_SHORTURL;
        $appkey = KEY_SHORTURL;

        if(SHORTURL == false) {
            return $url;
        }

        //create the URL
        $bitly = 'http://api.bit.ly/shorten?version=' . $version . '&longUrl=' . urlencode($url) . '&login=' . $login . '&apiKey=' . $appkey . '&format=' . $format;

        //get the url
        //could also use cURL here
        $response = file_get_contents($bitly);

        //parse depending on desired format
        if (strtolower($format) == 'json') {
            $json = @json_decode($response, true);
            return $json['results'][$url]['shortUrl'];
        } else //xml
        {
            $xml = simplexml_load_string($response);
            return 'http://bit.ly/' . $xml->results->nodeKeyVal->hash;
        }

    }

}
