<?php

require_once(dirname(__FILE__).'/GenericController.php');

class IndexController extends GenericController
{

    private $sessao = null;

    public function init()
    {
        /* Initialize action controller here */
        $this->sessao = new Zend_Session_Namespace('sessao');
    }

    public function preDispatch()
    {
        $this->view->site = SITE_SUBFOLDER;

        $action = $this->getFrontController()->getRequest()->getActionName();
        if ($this->sessao->userLogin != md5(USERNAME_EMKT) && $this->sessao->userActive != 1 && $action != 'index' && $action != 'login' && $action != 'remover-email' && $action != 'link-invalido' && $action != 'click') {
            $this->redirect($this->view->site . '/index');
        } else {
            $this->view->sair = '<a href="' . $this->view->site . '/index/sair">Sair</a>';
        }
    }

    public function sairAction($param)
    {

        $this->sessao->userLogin = null;
        $this->sessao->userActive = 0;
        unset($this->sessao->userLogin);
        unset($this->sessao->userActive);
        $this->redirect($this->view->site . '/index');
    }

    public function indexAction()
    {
        if ((!isset($this->sessao->userLogin) || !isset($this->sessao->userActive)) || ($this->sessao->userActive == 0)) {
            $this->render('login');
        }
        // action body
    }

    public function loginAction()
    {
        $this->view->msg = '';
        if ($this->getRequest()->isPost()) {
            $usr = $this->_getParam('usr');
            $pas = $this->_getParam('pas');

            if ($usr == '') {
                $this->view->msg .= '<p>- Preencha o nome de Usuário</p>';
            }
            if ($pas == '') {
                $this->view->msg .= '<p>- Preencha a Senha</p>';
            }

            if ($usr == USERNAME_EMKT && $pas == PASSWORD_EMKT) {
                $this->sessao->userLogin = md5(USERNAME_EMKT);
                $this->sessao->userActive = 1;
                $this->redirect($this->view->site . '/index');
            } else {
                if ($usr != USERNAME_EMKT && $usr != '') {
                    $this->view->msg .= '<p>- Preencha o nome de Usuário correto</p>';
                }
                if ($pas != PASSWORD_EMKT && $pas != '') {
                    $this->view->msg .= '<p>- Preencha a senha correta</p>';
                }
            }
        } else {
            $this->view->msg = '- Preencha usuário / senha para fazer o login';
        }
    }

    private function setSlug($titulo)
    {
        $acentos = array(" ", "À", "Á", "Â", "Ã", "Ä", "Å", "à", "á", "â", "ã", "ä", "å", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "ò", "ó", "ô", "õ", "ö", "ø", "È", "É", "Ê", "Ë", "è", "é", "ê", "ë", "Ç", "ç", "Ì", "Í", "Î", "Ï", "ì", "í", "î", "ï", "Ù", "Ú", "Û", "Ü", "ù", "ú", "û", "ü", "ÿ", "Ñ", "ñ", "&aacute;", "&agrave;", "&acirc;", "&atilde;", "&eacute;", "&egrave;", "&ecirc;", "&iacute;", "&igrave;", "&icirc;", "&oacute;", "&ograve;", "&ocirc;", "&otilde;", "&uacute;", "&ugrave;", "&uuml;", "&ucirc;", "&ccedil;", "&ordm;", "&ordf;", "&nbsp;", "&#8212;", "&#8211;");
        $sem_acentos = array("_", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "e", "e", "e", "e", "e", "e", "e", "e", "c", "c", "i", "i", "i", "i", "i", "i", "i", "i", "u", "u", "u", "u", "u", "u", "u", "u", "y", "n", "n", "a", "a", "a", "a", "e", "e", "e", "i", "i", "i", "o", "o", "o", "o", "u", "u", "u", "u", "c", "o", "a", "_", "_", "_");
        $titulo = str_replace($acentos, $sem_acentos, $titulo);
        $titulo = preg_replace('/[`^~\'"]/', null, iconv('UTF-8', 'ASCII//TRANSLIT', $titulo));
        $titulo = preg_replace("/[^a-z0-9\/_|+ -]/i", '', $titulo);
        $titulo = strtolower(trim($titulo, '-'));
        $titulo = preg_replace("/[\/_|+ -]+/", '-', $titulo);
        return $titulo;
    }

    private function setSlugName($param)
    {
        $param = preg_replace('/.phtml/', '', $param);
        return preg_replace('/-/', '_', $this->setSlug($param)) . '.phtml';
    }

    public function addTemplateAction()
    {
        $post = $this->getRequest();
        $params = $post->getParams();
        if ($params['nome'] != "" && $params['texto'] != "") {

            $diretorio = realpath(APPLICATION_PATH) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

            $myFile = $diretorio . $this->setSlugName($params['nome']);
            if (!file_exists($myFile)) {
                $fh = fopen($myFile, 'a+') or die("Erro ao criar arquivo");
                $stringData = $params['texto'];
                fwrite($fh, $stringData);
                fclose($fh);

                $this->view->msg = '<div class="alert alert-success alert-dismissible" role="alert">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                    . '<p>A template foi salva com sucesso!</p>'
                    . '<p><a href="' . $this->view->site . '/index/send-mail/">Clique aqui para escolher o Layout Criado e Enviar.</a></p>'
                    . '</div>';
            } else {
                $this->view->msg = '<div class="alert alert-danger alert-dismissible" role="alert">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                    . 'O arquivo já existe, experimente outro nome!'
                    . '<p><a href="' . $this->view->site . '/index/cadastrar-template">Clique aqui para Voltar.</a></p>'
                    . '</div>';
            }
        } else {
            $this->view->msg = '<div class="alert alert-danger alert-dismissible" role="alert">'
                . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                . 'Todos os campos precisam ser preenchidos!'
                . '<p><a href="' . $this->view->site . '/index/cadastrar-template">Clique aqui para Voltar.</a></p>'
                . '</div>';
        }
    }

    public function gerenciarEmailsAction()
    {
        $contatos = New Application_Model_Contatos();
        $collection = $contatos->getAllContatos();
        $this->view->emails = $collection;
    }

    public function updateTemplateAction()
    {
        $post = $this->getRequest();
        $params = $post->getParams();
        if ($params['nome'] != "" && $params['texto'] != "") {

            $diretorio = realpath(APPLICATION_PATH) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

            $myFile = $diretorio . $params['nome'] . '.phtml';


            if (file_exists($myFile)) {
                $fh = fopen($myFile, 'w+') or die("Erro ao atualizar arquivo");
                $stringData = $params['texto'];
                fwrite($fh, $stringData);
                fclose($fh);

                $this->view->msg = '<div class="alert alert-success alert-dismissible" role="alert">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                    . '<p>A template foi atualizada com sucesso!</p>'
                    . '<p><a href="' . $this->view->site . '/index/send-mail/">Clique aqui para escolher o Layout Editado e Enviar.</a></p>'
                    . '</div>';
            } else {
                $this->view->msg = '<div class="alert alert-danger alert-dismissible" role="alert">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                    . 'O arquivo foi removido do sistema!'
                    . '<p><a href="' . $this->view->site . '/index/cadastrar-template">Clique aqui para Voltar.</a></p>'
                    . '</div>';
            }
        } else {
            $this->view->msg = '<div class="alert alert-danger alert-dismissible" role="alert">'
                . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                . 'A HTML da template precisa ser preenchida!'
                . '<p><a href="' . $this->view->site . '/index/editar-template/tmp/' . $params['nome'] . '">Clique aqui para Voltar.</a></p>'
                . '</div>';
        }
    }

    public function cadastrarTemplateAction()
    {

    }

    public function editarTemplateAction()
    {
        $get = $this->getRequest();
        $template = $get->getParam('tmp');

        $diretorio = realpath(APPLICATION_PATH) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

        $myFile = $diretorio . $template . '.phtml';
        $fh = file_get_contents($myFile) or die("Erro ao abrir arquivo");
        $this->view->content = $fh;
        $this->view->template = $template;
    }

    public function gerenciarTemplatesAction()
    {


        $files = glob(APPLICATION_PATH . "/views/scripts/templates/*.phtml");

        $templates = array();
        foreach ($files as $template) {
            $template = str_replace(APPLICATION_PATH . "/views/scripts/templates/", "", $template);
            $template = preg_replace('/.phtml/', '', $template);
            $templates[] = $template;
        }

        $this->view->templates = $templates;
    }

    public function deletarTemplateAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $post = $this->getRequest();

        if ($post->isPost()) {
            $template = $post->getParam('template');

            if ($template) {
                if (unlink(APPLICATION_PATH . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "scripts" . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . $template . '.phtml')) {
                    echo Zend_Json::encode(array('success' => '1', 'msg' => "Template: \"{$template}\" removida com sucesso!", 'classe' => 'success'));
                } else {
                    echo Zend_Json::encode(array('success' => '0', 'msg' => "Esta template não pode ser excluída!", 'classe' => 'danger'));
                }
            }
        }
    }

    public function exclusaoMassaEmailAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $post = $this->getRequest();

        if ($post->isPost()) {
            $lista = $post->getParam('ids');
            //$ids = explode(',',$lista);
            $delEmails = new Application_Model_Contatos();

            $deletou = $delEmails->deleteContato("id_contato IN ( $lista )");

            $msgSuccess = preg_match('/,/',$lista) ? "Todos os contados selecionados foram deletados!" : "O email foi deletado com sucesso!";

            if ($deletou) {
                echo Zend_Json::encode(array('success' => '1', 'msg' => $msgSuccess));
            } else {
                echo Zend_Json::encode(array('success' => '0', 'msg' => "Não foi possível deletar os contatos selecionados!"));
            }
        }
    }

    private function setEmail($stringMail)
    {
        $nstringMail = preg_replace('/\;/',',', $stringMail);
        $nstringMail = preg_replace('/\v+/',',', $nstringMail);
        $nstringMail = preg_replace('/\t+/',',',  $nstringMail);
        $nstringMail = preg_replace('/\n+/',',', $nstringMail);
        $nstringMail = preg_replace('/\,\,/',',', $nstringMail);

        if ( !preg_match('/</',  $nstringMail) ) {

            $dataEmailFormat = explode(',', $nstringMail);
            $listarEmail = array();
            $listarContent = array();
            $listarNome = array();
            $newEmails = array();

            if(!empty($dataEmailFormat)) {
                foreach ($dataEmailFormat as $email) {
                    if (preg_match('/([^ ]+(.+))@([^ ]+(.+))/', $email)) {
                        $listarEmail[] = '<' . $email . '>';
                    } else if (preg_match('/{texto_dinamico: (.*)}/', $email, $return)) {
                        $listarContent[] = $return[1];
                    } else {
                        $listarNome[] = $email;
                    }
                }
            }



            if(!empty($listarEmail)) {


                $i = 0;
                foreach ($listarEmail as $email) {
                    $elementMail  = (isset($listarNome[$i])) ?  $listarNome[$i] . ' ' : 'Usuário ';
                    $elementMail .= $email;
                    $elementMail .= (isset($listarContent[$i])) ?  $listarContent[$i] : '';
                    $newEmails[] = $elementMail;
                    $i++;
                }
            }
            $nstringMail = implode(',',$newEmails);
        }


        $dataEmail = explode(',', $nstringMail);

        $nome = array();
        $email = array();
        $conteudo = array();

        if(!empty($dataEmail)) {
            foreach ($dataEmail as $stringValue) {
                if (preg_match('/(.*)?(\s)<([^ ]+(.+))@([^ ]+(.+))>/', $stringValue, $return)) {
                    if (preg_match('/\./', $return[5])) {
                        $posivelMail = trim(strtolower($return[3] . '@' . $return[5]));
                        if (filter_var($posivelMail, FILTER_VALIDATE_EMAIL)) {
                            $email[] = $posivelMail;
                        }
                    }
                    $nome[] = trim($return[1]);
                }
                if (preg_match('/{texto_dinamico: (.*)}/', $stringValue, $return)) {
                    $conteudo[] = trim($return[1]);
                } else {
                    $conteudo[] = null;
                }
            }
        }
        array_unique($email);
        $emails = array('nomes' => $nome, 'emails' => $email, 'contents' => $conteudo);

        return $emails;
    }

//    public function testeMailAction()
//    {
//        $stringMail = '
// assaod@hotmail.com,
// betula1363@gmail.com,
// ';
//        $this->setEmail($stringMail);
//    }

    private function sendMailTestNow($collection)
    {


        $mSend = new Application_Model_SendMail();
        $enviou = false;
        if (!empty($collection)) {
            $usuario_email = $collection['email'];
            $layout = $collection['layout'];
            $titulo = $collection['titulo'];
            $arguments = array(
                'template' => $layout,
                'email_recebe' => $usuario_email,
                'nome_recebe' => '[Usuário]',
                'dinamic_content'=> '[texto_dinamico]'
            );

            $mSend->setBody($arguments, $titulo);
            return $mSend->sendEmail();
        }
    }

    function sendTestEmailAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);


        $post = $this->getRequest();
        $params = $post->getParams();

        $enviou = false;

        if ($post->isPost()) {
            $emails = $params['emails'];
            $listEmails = $this->setEmail($emails);

            $tituloMsg = $params['titulo'];
            $template = $params['template'];
            $collection = array();
            if (!empty($listEmails['emails'])) {
                foreach ($listEmails['emails'] as $key => $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $collection = array(
                            'email' => $email,
                            'layout' => $template,
                            'titulo' => $tituloMsg
                        );
                        $enviou = $this->sendMailTestNow($collection);
                    }
                }
            }
        }
        if ($enviou) {
            echo Zend_Json::encode(array('success' => '1', 'msg' => "O teste foi enviado com sucesso! Verifique a seu e-mail!",'classe'=>'success'));
        } else {
            echo Zend_Json::encode(array('success' => '0', 'msg' => "Não foi possível enviar a mensagem, os emails não são válidos!",'classe'=>'danger'));
        }
    }

    function sendMailAction()
    {


        $postMsg = ($this->_getParam('titulo')) ? $this->_getParam('titulo') : 'MENSAGEM PARA VOCÊ';
        if( $this->_getParam('lay') ) {
            $this->sessao->layout = $this->_getParam('lay');
        }
        if( $this->_getParam('titulo') ) {
            $this->sessao->titulo = $this->_getParam('titulo');
        }

        $frase = (isset($this->sessao->frase)) ? $this->sessao->frase : 'Iniciando envio de mensagem';
        $layout = (isset($this->sessao->layout)) ? $this->sessao->layout : $this->_getParam('lay');
        $titulo =  (isset($this->sessao->titulo)) ? $this->sessao->titulo : $this->_getParam('titulo');

        $send = $this->_getParam('enviar');
        $dados = new Application_Model_Contatos();
        $relatorio = new Application_Model_Relatorio();

        if ($send != '' && $layout != "" && $titulo != "") {

            //agenda o envio para garantir o processo até o final


            set_time_limit(0);
            ignore_user_abort(true);
            $total_por_vez = 8;
            $collection = $dados->getListContatosNotSend('inativo', $total_por_vez); //ENVIA 8 POR VEZ

            $mSend = new Application_Model_SendMail();
            $processado = $dados->getTotalContatosEnviados();
            $total = $dados->getTotalContatosAtivos();

            if($frase == 'Iniciando envio de mensagem'){
                $processado = $processado - $total_por_vez;

                $collectionAgenda = array(
                    'data' => date("Y-m-d H:i:s",strtotime(date('Y-m-d H:i:s')." +2 minutes")),
                    'template' => $layout,
                    'assunto' => $titulo
                );

                $mCron = new Application_Model_Agendador();
                $add = $mCron->addEnvio($collectionAgenda);

            }
            if (!empty($collection)) {


                $emails = '<h1>'.$frase.'</h1>';

                foreach ($collection as $contato) {

                    $this->view->porcentagem = floor(( $processado / $total) * 100);
                    $nome_usuario = $contato['nome'];
                    $usuario_email = $contato['email'];
                    $usuario_id = $contato['id_contato'];

                    $nome_usuario = ($nome_usuario != "") ? $nome_usuario : 'Usuário';
                    $dinamic_content = $contato['dinamic_content'];

                    $arguments = array(
                        'template' => $layout,
                        'email_recebe' => $usuario_email,
                        'nome_recebe' => $nome_usuario,
                        'dinamic_content'=> $dinamic_content
                    );

                    $mSend->setBody($arguments, $titulo);
                    $enviou = $mSend->sendEmail();
                    $dados->setStatusEnviou($usuario_id);
                    $RESP = ($enviou) ? 'ENVIADO' : 'NÃO ENVIADO';
                    $icone = ($enviou) ? '<img src="' . $this->view->site . '/img/ok.png" border="0">' : '<img src="' . $this->view->site . '/img/error.png" border="0">';

                    //   $relatorio->setStatus($usuario_id, $RESP);
                    $emails .= "<div class=\"send\">{$icone}&nbsp;{$RESP} ::::: Email: {$usuario_email}  </div>";
                }
                $this->view->msg = $emails
                    . '<meta http-equiv="refresh" content="5;url=' . $this->view->site . '/index/send-mail/enviar/go" />';
                $this->sessao->frase = 'Enviando mensagens. Se você não quiser acompanhar o processo, basta fechar essa janela.';
            } else {

                $dados->setStatusEnviouTudo();
                unset($this->sessao->layout);
                unset($this->sessao->titulo);
                unset($this->sessao->frase);
                $this->view->porcentagem = 100;
                $this->view->msg = '<div class=\"fim\"><h1>Todas as Mensagens foram enviadas com sucesso!</h1> <br> <p><a href="' . $this->view->site . '/index">Clique aqui</a> pare retornar.</p></div>';
            }
        } else {

            $this->view->msg = '<input type="button" class="btn btn-info" onclick="location.href=\'' . $this->view->site . '/index\';" value="Voltar"><br><br>';
            $selec = '';
            if ($layout != '') {
                $send = $this->_getParam('enviar');

                if ($layout == 'null') {
                    $this->view->msg = '';
                    $this->view->sendScript='';
                } else {
                    $this->view->sendScript = ' onclick="sendMail()"';
                    $this->view->msg = '';
                    $action = '';
                    $this->view->layout = "templates/" . $layout;
                    $this->view->msg .= '<form name="layout" action="' . $this->view->site . '/index/send-mail" method="post">' . "\n"
                        . '<input type="hidden" name="lay" value="' . $layout . '">' . "\n"
                        . '<input type="hidden" name="enviar" value="">' . "\n"
                        . '<br><input type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModalQuest" value="Enviar mensagem agora">'
                        . '&nbsp;<input type="button" class="btn btn-info" data-toggle="modal" data-target="#myModalAgend" value="Agendar envio">'
                        . '&nbsp;<input type="button" class="btn btn-warning" '
                        . 'onclick="location.href=\'' . $this->view->site . '/index/editar-template/tmp/' . preg_replace('/.phtml/', '', $layout) . '\';"'
                        . ' value="Editar esta template">&nbsp;<input type="button" class="btn btn-default"'
                        . ' data-toggle="modal" data-target="#myModal" value="Enviar um Email teste">'
                        . '&nbsp;<input type="button" class="btn btn-info"'
                        . ' onclick="location.href=\'' . $this->view->site . '/index\';" value="Voltar"><br><br>' . "\n";
                }
            }

            $dados->setStatusEnviouTudo();
            $html = "\n\n<script type=\"text/javascript\">\n"
                . "function sendForm(obj){\n"
                . "if (obj.options[obj.selectedIndex].value != ''){ \n"
                . "document.layout.submit();\n"
                . "  }\n"
                . "}\n"
                . "function sendMail(){\n "
                //  . "  if(confirm('tem certeza que deseja enviar sua mensagem agora?')){\n"
                . "    document.layout.enviar.value = 'go';\n"
                . "    document.layout.submit();\n"
                //    . "  }\n"
                . "}\n"
                . "</script>\n";

            $html .= '<form name="layout" action="' . $this->view->site . '/index/send-mail" method="post">' . "\n";
            $html .= '<div class="form-group">' . "\n";
            $html .= '<label for="inputTitle">Título da Mensagem:</label>' . "\n";
            $html .= '<input hidden name="tit" id="tit_temp">' . "\n";
            $html .= '<input type="text"  class="form-control" size="50" id="title_msg" name="titulo" value="'.$postMsg.'">' . "\n";
            $html .= '</div>' . "\n";
            $html .= '<div class="form-group">' . "\n";
            $html .= '<label for="inputLayout">Layout:</label>' . "\n";
            $html .= '<select name="lay" class="form-control" onChange="sendForm(this);">' . "\n";
            $html .= '<option value="null">Selecione o layout</option>' . "\n";
            $files = glob(APPLICATION_PATH . "/views/scripts/templates/*.phtml");
            foreach ($files as $template) {
                $template = (str_replace(APPLICATION_PATH . "/views/scripts/templates/", "", $template));
                $html .= "<option value=\"{$template
                        }\"";
                $html .= ($layout == $template) ? 'selected="selected"' : '';
                $tempname = preg_replace('/.phtml/', '', $template);
                $html .= ">{$tempname}</option>\n";
            }
            $html .= "</select>";
            $html .= '</div>' . "\n"
                . "</form>";

            $this->view->msg .= $html;
        }
    }

    public function cadastrarEmailsAction()
    {
        $this->view->classe = 'info';

        $post = $this->getRequest();
        if ($post->isPost()) {
            $emails = $post->getParam('emails');
            if ($emails != "") {
                $listEmails = $this->setEmail($emails);

                if (!empty($listEmails)) {


                    $addmails = array();
                    $email_collection = array();
                    if (count($listEmails['emails'])) {

                        foreach ($listEmails['emails'] as $key => $email) {

                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $email_collection = array(
                                    'nome' => $listEmails['nomes'][$key],
                                    'email' => $email,
                                    'dinamic_content'=>$listEmails['contents'][$key]
                                );
                                $addmails[] = $email_collection;
                            }
                            if (count($addmails)) {
                                $gravaEmail = new Application_Model_Contatos();
                                $gravaEmail->addContato($email_collection);
                            }
                        }
                        $this->view->classe = 'success';
                        $this->view->msg = (count($addmails)) ? "<p>&nbsp;</p><p>- E-mail(s) cadastrado(s) com sucesso!</p>" : "<p>&nbsp;</p><p>- Preencha algum e-mail para gravar!</p>";
                    }
                    $this->view->classe = 'success';
                    $this->view->msg = "E-mail(s) cadastrado(s) com sucesso!";
                }
            } else {
                $this->view->classe = 'danger';
                $this->view->msg = "<p>&nbsp;</p><p>- Preencha algum e-mail para gravar!</p>";
                // $this->render('cadastrar-emails');
            }
        }

        if ($post->isGet()) {
            $param = $post->getParam('data');

            if ($param == 'imported') {
                $this->view->msg = "<p>&nbsp;</p><p>- A planilha foi importada com sucesso e os emails foram cadastrados no sistema!</p>";
                $this->view->classe = 'success';
            } else if ($param == 'not-imported') {
                $this->view->classe = 'danger';
                $this->view->msg = "<p>&nbsp;</p><p>- A planilha enviada não contém informações de email!</p>";
            } else if ($param == 'not-read') {
                $this->view->classe = 'danger';
                $this->view->msg = "<p>&nbsp;</p><p>- A planilha precisa ser reenviada, certifique-se de que ela não esteja aberta!</p>";
            } else if ($param == 'not-file') {
                $this->view->classe = 'danger';
                $this->view->msg = "<p>&nbsp;</p><p>- Selecione uma planilha antes de processar a importação!</p>";
            }
        }
    }

    public function removerEmailAction()
    {
        $this->view->msg = '';
        $this->view->email = '';
        $request = $this->getRequest();
        $k = $request->getParam('k');
        $email = base64_decode(str_replace('::','/',$k));

        $modelMsg = new Application_Model_Contatos();
        $modelRel = new Application_Model_Relatorio();

        $this->view->email = $email;


        if ($request->isPost()) {
            $em = $request->getParam('email');
            $msg = $request->getParam('motivo');
            $motivo = strip_tags($msg);
            $set = $modelMsg->setEnableEmail($em, $motivo);
            $data = $modelMsg->getContatoByEmail($em);
            if (count($data)) {
                $setRelatorio = $modelRel->setStatus($data->id_contato, 'REMOVIDO CLIENTE');
            }
            $this->view->classe = 'danger';
            $this->view->msg = ($set) ? "Seu E-mail <strong>{$em}</strong> foi removido com sucesso!" : "O E-mail <strong>{$em}</strong> não existe em nosso sistema!";
        }
    }

    public function importarEmailsAction()
    {

        $post = $this->getRequest();

        $diretorio = preg_replace('/application/', '', realpath(APPLICATION_PATH)) . 'public' . DIRECTORY_SEPARATOR . 'excel' . DIRECTORY_SEPARATOR;

//          $req = $this->getRequest();
        $params = $post->getParams();

        $upload = new Zend_File_Transfer_Adapter_Http();

        $upload->setDestination($diretorio);
        $upload->addValidator('Size', false, 8000000);
        $upload->addValidator('Extension', false, array('extension1' => 'xlsx,xls,sxc,pdf,csv,dbf,dif,ods,pts,pxl,sdc,slk,stc,vor,xlt'));

        //  print_r($upload->getFileName(null, false)); die();

        if ($post->isPost() && !$upload->isValid()) {
            $this->redirect($this->view->site . '/index/cadastrar-emails/data/not-file');
        }
        if (!$upload->isValid()) {
            $this->view->classe = 'danger';
            // var_dump($upload->getMessages());
            $this->sessao->infoUpload = $this->treatMessagesUpload($upload->getErrors());
        }

//        if (file_exists($upload->getFileName())) {
//
//            $messages = array(0 => 'O arquivo ' . $upload->getFileName(null, false) . ' Já existe no diretório.');
//            $this->sessao->infoUpload = $this->treatMessagesUpload($messages);
//        }
//        $rename = substr(md5(rand(000, 999) . time()), 0, 5) . '_' . strtolower($upload->getFileName());
//        $upload->addFilter('Rename', $this->public_dir_upload, $rename);

        try {

            if (!$upload->isValid()) {
                $this->view->classe = 'danger';
                $this->sessao->infoUpload = $this->treatMessagesUpload($upload->getErrors());
            } else {
                $upload->receive();


                $this->sessao->infoUpload = $upload->getFileInfo();

                $arr = array(
                    'url_file' => $upload->getFileName(null, false),
                    'file' => $params['emails']
                );
                $file_to_include = $diretorio . $arr['url_file'];
                if (is_file($file_to_include)) {


                    //  print_r($file_to_include); die();
                    $identify = PHPExcel_IOFactory::identify($file_to_include);
                    $excelReader = PHPExcel_IOFactory::createReader($identify);
                    $reader = $excelReader->load($file_to_include);
                    $this->sessao->infoUpload = '';

                    $listEmails = array();
                    $email = array();
                    $nome = array();
                    $dinamic_text = array();
                    $addmails = array();
                    $email_collection = array();
                    if (!count($reader->getActiveSheet()->getRowIterator())) {
                        $this->redirect($this->view->site . '/index/cadastrar-emails/data/not-read');
                    } else {
                        foreach ($reader->getActiveSheet()->getRowIterator() as $rowKey => $rows) {

                            $cellIterator = $rows->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);

                            if (!count($cellIterator)) {
                                $this->redirect($this->view->site . '/index/cadastrar-emails/data/not-read');
                            } else {
                                foreach ($cellIterator as $cellIteratorIteratorKey => $cell) {
                                    if (preg_match('/@/', $cell->getValue())) {
                                        $email[] = $cell->getValue();
                                        $this->sessao->infoUpload .= "email: " . $email . "<br>";

                                    } else if (preg_match('/^\{(.*)\}$/', $cell->getValue(), $return)) {
                                        $dinamic_text[] =  $return[1];
                                        $this->sessao->infoUpload .= "email: " . $dinamic_text . "<br>";

                                    } else {
                                        $nome[] = $cell->getValue();
                                        $this->sessao->infoUpload .= "email: " . $nome . "<br>";
                                    }


                                    //  $data[$rowKey][$cell->getCoordinate()] = $cell->getValue();
                                    //$data[$rowKey][$cell->getCoordinate()] = $cell->getValue();
                                    // $data[$rowKey] = $cell->getValue();
                                }
                            }
                        }
                    }
                    if (!empty($email)) {
                        array_unique($email);
                        $listEmails = array('nomes' => $nome, 'emails' => $email, 'dinamic_contents'=> $dinamic_text);
                    } else {
                        $this->redirect($this->view->site . '/index/cadastrar-emails/data/not-imported');
                    }

                    if (count($listEmails['emails'])) {

                        foreach ($listEmails['emails'] as $key => $email) {

                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $email_collection = array(
                                    'nome' => $listEmails['nomes'][$key],
                                    'dinamic_content' => $listEmails['dinamic_contents'][$key],
                                    'email' => $email
                                );
                                $addmails[] = $email_collection;
                            }
                            if (count($addmails)) {
                                $gravaEmail = new Application_Model_Contatos();
                                $gravaEmail->addContato($email_collection);
                            } else {
                                $this->redirect($this->view->site . '/index/cadastrar-emails/data/not-imported');
                            }
                        }
                        $this->redirect($this->view->site . '/index/cadastrar-emails/data/imported');
                    } else {
                        $this->redirect($this->view->site . '/index/cadastrar-emails/data/not-imported');
                    }
                } else {
                    $this->redirect($this->view->site . '/index/cadastrar-emails/data/not-imported');
                }
            }
        } catch (Zend_File_Transfer_Exception $e) {
            $this->sessao->infoUpload = $e->getMessage();
        }
        $this->view->info = $this->sessao->infoUpload;
    }

    private function treatMessagesUpload($messages)
    {
        if (count($messages)) {
            switch ($messages[0]) {
                case 'fileSizeTooBig':
                    $messages[0] = 'O arquivo deve conter no máximo 8MB.';
                    break;
                case 'fileExtensionFalse': case 'fileUploadErrorIniSize':
                $messages[0] = 'O arquivo deve ser uma imagem no formato: excel.';
                break;
                case 'fileUploadErrorNoFile':
                    $messages[0] = 'Selecione um arquivo da sua máquina antes de enviar.';
                    break;
            }
        }
        return $messages;
    }

    public function exportarContatosAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $data = new Application_Model_Contatos();
        $collection = (array) $data->getAllContatos();

        $collection = $this->titulosPlanilha($collection);

        $export = new PHPExcel();

        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize ' => '8MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $export->setActiveSheetIndex(0)
            ->fromArray($collection, null, 'A1');


        $xmlWriter = new PHPExcel_Writer_Excel2007($export);

        header("Pragma: protected"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public", false); // required for certain browsers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8');
        header("Content-Disposition: attachment;filename='contatos.xlsx'");
        header("Content-Transfer-Encoding: binary");
        $xmlWriter->save("php://output");
        exit();
    }

    public function titulosPlanilha($value)
    {
        $return = null;

        if (is_array($value)) {

            $header = array_keys($value[0]);

            $return[0] = $header;

            foreach ($value as $key => $row) {
                foreach ($row as $columnKey => $column) {
                    $currentfindedKey = array_search($columnKey, $header);
                    $return[$key][$currentfindedKey] = $column;
                }
            }

            array_unshift($return, $header);
        }

        return $return;
    }

    public function clickAction() {
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        $k = $request->getParam('k');
        $url = $request->getParam('url');

        $email = base64_decode(str_replace('::','/',$k));
        $url = base64_decode(str_replace('::','/',$url));


        $modelMsg = new Application_Model_Contatos();
        $modelRel = new Application_Model_Relatorio();

        $data = $modelMsg->getContatoByEmail($email);
        if (count($data)) {
            $setRelatorio = $modelRel->setStatus($data->id_contato, 'CLICOU NO LINK DA MENSAGEM');
        }

        if(preg_match('/htt(.*)/', urldecode($url))) {
            $this->redirect(html_entity_decode(urldecode($url)));
        } else {
            $this->redirect('index/link-invalido');
        }


    }

    public function gerenciarMensagensAgendadasAction()
    {
        $modelAgendador = new Application_Model_Agendador();
        $collection = $modelAgendador->getAllEnvios();

        $this->view->mensagens = $collection;
    }
    public function testAction() {

    }

    public function configurarSmtpAction()
    {
        $post = $this->getRequest();
        $modelSMTP = new Application_Model_ConfigSendMail();
        $data = $modelSMTP->getConfig(1);
        if(count($data)) {
            $params_banco = array(
                'id'    => 1,
                'nome'  => $data->snd_nome,
                'email' => $data->snd_email_envio,
                'login' => $data->snd_login,
                'senha' => $data->snd_senha,
                'ssl'   => $data->snd_ssl_protocolo,
                'smtp'  => $data->snd_smtp,
                'port'  => $data->snd_port
            );
        } else{
            $params_banco = array(
                'id'    => 1,
                'nome'  =>null,
                'email' =>null,
                'login' =>null,
                'senha' =>null,
                'ssl'   =>null,
                'smtp'  =>null,
                'port'  =>null
            );
        }
        $this->view->params = $params_banco;
        if ($post->isPost()) {

            $params = $post->getParams();

            unset($params['controller']);
            unset($params['module']);

            if(array_count_values($params) >= count($params_banco) ) {


                $config = $modelSMTP->setConfig($params);

                $this->view->params = $params;

                if ($config) {
                    $this->view->message = "Configuração realizada com sucesso!";
                    $this->view->message_class = "success";
                } else {

                    $this->view->message = "Nenhum campo foi modificado!";
                    $this->view->message_class = "warning";
                }

            } else {
                $this->view->message = "Todos os campos precisam ser preenchidos!";
                $this->view->message_class = "danger";
            }


        }
    }

    public function linkInvalidoAction()
    {
        $this->view->message = "Desculpe-nos! Esta mensagem não se encontra mais disponível!";
    }


}
