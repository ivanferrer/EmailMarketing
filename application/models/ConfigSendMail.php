<?php

class Application_Model_ConfigSendMail
{

    private $dbTable;

    public function __construct()
    {

        $this->dbTable = new Application_Model_DbTable_ConfigSendMail();
        $this->dbTable->getDefaultAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
    }

    public function getConfig($id)
    {

        $select = $this->dbTable->select()->from('tab_sendmail', array(
            'snd_id',
            'snd_email_envio',
            'snd_nome',
            'snd_login',
            'snd_senha',
            'snd_ssl_protocolo', //ssl','tls','inativo'
            'snd_smtp',
            'snd_port'
        ))->where('snd_id =? ', (int) $id);
        return $this->getDados($select);
    }

    public function addConfig($data)
    {

        $add = $this->dbTable->insert(array(
            'snd_email_envio' => $data['email'],
            'snd_nome' => $data['nome'],
            'snd_login' => $data['login'],
            'snd_senha' => $data['senha'],
            'snd_ssl_protocolo' => $data['ssl'], //ssl','tls','inativo'
            'snd_smtp' => $data['smtp'],
            'snd_port' => $data['port']
        ));
        return $add;

    }

    public function setConfig($data)
    {
        if(!isset($data['id'])) {
            $data['id'] = 0;
        }
        $check = $this->getConfig($data['id']);

        if(count($check) &&  $data['id'] != 0) {
            $set = $this->dbTable->update(array(
                'snd_email_envio' => $data['email'],
                'snd_nome' => $data['nome'],
                'snd_login' => $data['login'],
                'snd_senha' => $data['senha'],
                'snd_ssl_protocolo' => $data['ssl'], //ssl','tls','inativo'
                'snd_smtp' => $data['smtp'],
                'snd_port' => $data['port']
            ), array('snd_id =? ' => (int) $data['id']));
            return $set;
        } else {
            return $this->addConfig($data);
        }

    }
    
    public function deleteEnvio($where)
    {
      return $this->dbTable->delete($where);  
    }

    public function setStatus($status_envio, $id)
    {
        $set = $this->dbTable->update(array('agn_status' => $status_envio), array('agn_id =? ' =>  (int)  $id));
        return $set;
    }


    /**
     * 
     * @param type query sql
     * @return 1 registro (objeto)
     */
    public function getDados($select)
    {

        $row = $this->dbTable->fetchRow($select);
        if ($row instanceof Zend_Db_Table_Row_Abstract) {
            $row = (object) $row->toArray();
        }
        return $row;
    }

    /**
     * 
     * @param type query sql
     * @return  coleção de registros: null ou objeto
     */
    public function getAllDados($select)
    {
        $rows = $this->dbTable->fetchAll($select);
        if ($rows instanceof Zend_Db_Table_Rowset_Abstract) {
            if (count($rows->toArray())) {
                return (object) $rows->toArray();
            } else
                return 0;
        } else
            return false;
    }

}
