<?php

class Application_Model_Agendador
{

    private $dbTable;

    public function __construct()
    {

        $this->dbTable = new Application_Model_DbTable_Agendador();
        $this->dbTable->getDefaultAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
    }

    public function getEnvio($status_envio)
    {
        $select = $this->dbTable->select()->from('tab_agendador', array(
                    "agn_id",
                    "DATE_FORMAT(agn_data_disparo,'%d/%m/%Y %H:%i') AS agn_data_disparo",
                    "agn_template",
                    "agn_status",
                    "agn_assunto"
                ))
                ->where("agn_status = ? ", $status_envio);
        return $this->getAllDados($select);
    }

    public function getAllEnvios()
    {
        $select = $this->dbTable->select()->from('tab_agendador', array(
            "agn_id",
            "DATE_FORMAT(agn_data_disparo,'%d/%m/%Y %H:%i') AS agn_data_disparo",
            "agn_template",
            "agn_status",
            "agn_assunto"
        ))->order(array('agn_id DESC', 'agn_data_disparo DESC'));
        return $this->getAllDados($select);
    }

    public function getEnvioToDate($data_atual)
    {
        $select = $this->dbTable->select()->from('tab_agendador', array(
            'agn_id',
            'agn_data_disparo',
            'agn_template',
            'agn_status',
            'agn_assunto'
        ))
            ->where('agn_data_disparo <= ?',$data_atual)
            ->where('agn_status = ? ', 'nao_enviado');
        return $this->getDados($select);
    }

    public function addEnvio($data)
    {
        $add = $this->dbTable->insert(array(
            'agn_data_disparo' => $data['data'],
            'agn_template' => $data['template'],
            'agn_status' => 'nao_enviado',
            'agn_assunto'=>$data['assunto']
        ));
        return $add;

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
