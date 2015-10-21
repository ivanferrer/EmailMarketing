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

        $template = $this->_getParam('id');

        if ($template != null) {
            $this->renderScript('templates/'.preg_replace('/-/','_',$template).'.phtml');
        }
    }

}