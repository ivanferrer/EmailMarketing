<?php
require_once(dirname(__FILE__).'/GenericController.php');

class MensagemController extends GenericController
{

    private $action;

    public function init()
    {
        $this->action = Zend_Controller_Front::getInstance()
            ->getRequest()
            ->getActionName();

    }

    public function indexAction()
    {
        //acesse o endereço da template através do caminho: http://osite.com.br/mensagem/index/id/nome-da-template
        $template = $this->_getParam('id');

        if ($template != null) {
            $this->renderScript('templates/'.preg_replace('/-/','_',$template).'.phtml');
        }
    }

}