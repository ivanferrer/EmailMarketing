<?php

class Application_Model_Contatos
{

    private $dbTable;

    public function __construct()
    {

        $this->dbTable = new Application_Model_DbTable_Contatos();
        $this->dbTable->getDefaultAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
    }

    public function getListContatosNotSend($status_envio, $limit)
    {
        $select = $this->dbTable->select()->from('tab_contatos', array(
                    'id_contato',
                    'email',
                    'nome',
                    'status_permissao_envio',
                    'status_envio',
                    'dinamic_content'
                ))
                ->where('status_permissao_envio = ? ', 'ativo')
                ->where('status_envio = ? ', $status_envio)
                ->limit($limit);
        return $this->getAllDados($select);
    }

    public function getTotalContatosAtivos()
    {
        $select = $this->dbTable->select()->from('tab_contatos', array(
            'COUNT(*) as total'
        ))
            ->where('status_permissao_envio = ? ', 'ativo');
        $total = $this->getDados($select);
        return $total->total;
    }

    public function getTotalContatosEnviados()
    {
        $select = $this->dbTable->select()->from('tab_contatos', array(
            'COUNT(*) as total'
        ))
            ->where('status_envio = ? ', 'ativo')
            ->where('status_permissao_envio = ? ', 'ativo');
        $total = $this->getDados($select);
        return $total->total;
    }
    
     public function getAllContatos()
    {
        $select = $this->dbTable->select()->from('tab_contatos', array(
                    'id_contato',
                    'email',
                    'nome',
                    'status_permissao_envio',
                    'status_envio',
                    'dinamic_content'
                ));
        
        return $this->getAllDados($select);
    }

    public function setStatusEnviou($id)
    {
        $this->dbTable->update(array('status_envio' => 'ativo'), array('id_contato = ? ' => (int) $id));
    }

    public function getContatoByEmail($email)
    {

        $select = $this->dbTable->select()->from('tab_contatos', array(
                    'id_contato',
                    'email',
                    'nome',
                    'status_permissao_envio',
                    'status_envio',
                    'dinamic_content'

                ))->where('email = ? ', $email);
        return $this->getDados($select);
    }

    public function addContato($data)
    {
        $exists = $this->getContatoByEmail($data['email']);
        if(!count($exists)){
                $this->dbTable->insert(array('email' => $data['email'], 'nome' => $data['nome'], 'status_permissao_envio' => 'ativo', 'status_envio' => 'inativo', 'dinamic_content'=>$data['dinamic_content']));
        }
    }
    
    public function deleteContato($where)
    {
      return $this->dbTable->delete($where);  
    }

    public function setStatusEnviouTudo()
    {
        $this->dbTable->update(array('status_envio' => 'inativo'), array('status_envio =?' => 'ativo'));
    }

     public function setEnableEmail($email,$msg)
    {
        $set = $this->dbTable->update(array('status_permissao_envio' => 'inativo','motivo_remocao'=>$msg), array('email = ? ' => $email, 'status_permissao_envio =? ' => 'ativo'));
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
