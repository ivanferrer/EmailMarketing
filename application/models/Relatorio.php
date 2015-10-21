<?php

class Application_Model_Relatorio
{

    private $dbTable;

    public function __construct()
    {

        $this->dbTable = new Application_Model_DbTable_Relatorio();
        $this->dbTable->getDefaultAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
    }

    public function getRelatorio($id)
    {
        $select = $this->dbTable->select()->from('tab_relatorio', array(
                    'id_relatorio',
                    'fkid_contato',
                    'status'
                ))
                ->where('fkid_contato = ? ', $id);
        return $this->getDados($select);
    }

    public function setStatus($id = 0, $status)
    {
        if ($id != 0) {
            $act = $this->dbTable->insert(array('fkid_contato' => $id, 'status' => $status, 'data_status'=>date('Y-m-d H:i:s')));
        }
        return $act;
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
