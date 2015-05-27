<?php

require_once(dirname(__FILE__).'/GenericController.php');

class CronController extends GenericController
{

    private $sessao_agenda = null;

    public function init()
    {
        /* Initialize action controller here */
        $this->sessao_agenda = new Zend_Session_Namespace('sessao_agenda');

    }

    public function indexAction()
    {
    //no use
    }

    public function agendarEnvioAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $post = $this->getRequest();
        $params = $post->getParams();
        $add = false;

        if ($post->isPost()) {
            $data = $params['data_envio'];
            list($date, $time) = explode('T', $data);
            $tituloMsg = $params['titulo'];
            $template = $params['template'];

            $collection = array(
                'data' => $date.' '.$time.':00',
                'template' => $template,
                'assunto' => $tituloMsg
            );
            $mCron = new Application_Model_Agendador();
            $add = $mCron->addEnvio($collection);

        }
        if ($add) {
            echo Zend_Json::encode(array('success' => '1', 'msg' => "O email foi agendado com sucesso! Aguarde o redirecionamento...",'classe'=>'success'));
        } else {
            echo Zend_Json::encode(array('success' => '0', 'msg' => "Não foi possível agendar a mensagem!",'classe'=>'danger'));
        }

    }

    public function envioProgramadoAction()
    {

        //executar este script a cada 20 minutos ....

        $mCron = new Application_Model_Agendador();
        $mSend = new Application_Model_SendMail();
        $dados = new Application_Model_Contatos();

        $date = date('Y-m-d H:i:s');
        $data = $mCron->getEnvioToDate($date);

        if ( count($data) ) {


            $this->sessao_agenda->layout = $data->agn_template;
            $this->sessao_agenda->titulo = $data->agn_assunto;
            $mCron->setStatus('enviado', $data->agn_id);


            $layout = (isset($this->sessao_agenda->layout)) ? $this->sessao_agenda->layout : $data->agn_template;
            $titulo =  (isset($this->sessao_agenda->titulo)) ? $this->sessao_agenda->titulo : $data->agn_assunto;


            $total_por_vez = 8;
            $collection = $dados->getListContatosNotSend('inativo', $total_por_vez); //ENVIA 8 POR VEZ

            if (!empty($collection)) {
                foreach ($collection as $contato) {

                    $nome_usuario = $contato['nome'];
                    $usuario_email = $contato['email'];
                    $usuario_id = $contato['id_contato'];

                    $nome_usuario = ($nome_usuario != "") ? $nome_usuario : 'Usuário';
                    $dinamic_content = $contato['dinamic_content'];

                    $arguments = array(
                        'template' => $layout,
                        'email_recebe' => $usuario_email,
                        'nome_recebe' => $nome_usuario,
                        'dinamic_content' => $dinamic_content
                    );

                    $mSend->setBody($arguments, $titulo);
                    $enviou = $mSend->sendEmail();
                    $dados->setStatusEnviou($usuario_id);
                    //sleep(5);

                }
                $this->view->enviando = 'Esta página é executada através do agendador cron, executando a coleção de envio: <br><pre> '.print_r((array) $collection, true).'</pre>';
            } else {
                $dados->setStatusEnviouTudo();
                unset($this->sessao_agenda->layout);
                unset($this->sessao_agenda->titulo);
                $this->view->enviando = 'Esta página é executada através do agendador cron - fim de envio de emails.';
            }

        } else{
            $this->view->enviando = 'Esta página é executada através do agendador cron - Não há envios agendados para '.date('d/m/Y H:i:s');
        }
    }

    public function exclusaoMassaAgendaAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $post = $this->getRequest();

        if ($post->isPost()) {
            $lista = $post->getParam('ids');
            //$ids = explode(',',$lista);
            $delAgenda = new Application_Model_Agendador();

            $deletou = $delAgenda->deleteEnvio("agn_id IN ( $lista )");

            $msgSuccess = preg_match('/,/',$lista) ? "Todos os agendamentos selecionados foram deletados!" : "O agendamento foi deletado com sucesso!";

            if ($deletou) {
                echo Zend_Json::encode(array('success' => '1', 'msg' => $msgSuccess));
            } else {
                echo Zend_Json::encode(array('success' => '0', 'msg' => "Não foi possível deletar os agendamentos selecionados!"));
            }
        }
    }




}

