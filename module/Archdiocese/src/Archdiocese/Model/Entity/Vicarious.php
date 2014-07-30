<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Archdiocese\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Archdiocese\Form\VicariousFilter;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class Vicarious {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
//        $select = new Select();
//        $select->from('vicarious')
//                ->join('users', 'users.idVicarious = vicarious.id', array('firstName', 'lastName'), 'left');
//        $paginatorAdapter = new DbSelect(
//                $select, 
//                $this->tableGateway->getAdapter()
//        );
//        $paginator = new Paginator($paginatorAdapter);
//        return $paginator;
        
//        $sql = new Sql($this->tableGateway->getAdapter());
//        $select = $sql->select();
//        $select->from('vicarious')
//                ->columns(array('id', 'vicariousName', 'location'))
//                ->join('users', 'users.idVicarious = vicarious.id', array('firstName', 'lastName'), 'left');
//        $resultSet = $this->tableGateway->selectWith($select);
//        return $resultSet;
    }
    
    public function fetchOneVicariou($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $resultSet = $rowset->current();
        if (!$resultSet) {
            throw new \Exception("Could not find row $id");
        }
        return $resultSet;
    }
    
    public function fetchAllParishes($idVicarious){
        $idVicarious = (int) $idVicarious;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('vicarious')
               ->columns(array('vicariousName')) 
               ->join('parishes', 'vicarious.id = parishes.idVicarious', array('id','parishName', 'address', 'phone'))
               ->where(array('parishes.idVicarious' => $idVicarious));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
//        $results = $resultSet->current();
        return $resultSet; 
    }

    public function addVicariou(VicariousFilter $vicariosFilter) {
        $values = array(
            'vicariousName' => $vicariosFilter->vicariousName,
            'location' => $vicariosFilter->location,
        );
        $this->tableGateway->insert($values);
    }

    public function updateVicariou(VicariousFilter $vicariousFilter) {
        $values = array(
            'vicariousName' => $vicariousFilter->vicariousName,
            'location' => $vicariousFilter->location,
        );
        $id = (int) $vicariousFilter->id;
        $this->tableGateway->update($values, array('id' => $id));
    }

    public function deleteVicariou($id) {
        $this->tableGateway->delete(array('id' => $id));
    }

}

