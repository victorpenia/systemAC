<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Archdiocese\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Archdiocese\Form\ParishesFilter;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class Parishes extends TableGateway {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginated = false){
        if($paginated){
             $select = new Select();
             $select->from('parishes')
                    ->join('vicarious', 'parishes.idVicarious = vicarious.id', array('vicariousName'));
             $paginatorAdapter = new DbSelect(
                 $select,
                 $this->tableGateway->getAdapter()
             );
             $paginator = new Paginator($paginatorAdapter);
             return $paginator;
        }
        return $this->tableGateway->select();
    }
    
    public function fetchOneParish($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No hay registros asociados al valor $id");
        }
        return $row;
    }
    
    public function fetchAllParishesByIdVicarious($idVicarious) {
        $idVicarious = (int) $idVicarious;
        $rowset = $this->tableGateway->select(array('idVicarious' => $idVicarious));
        return $rowset;
//        $row = $rowset->current();
//        if (!$row) {
//            throw new \Exception("No hay registros asociados al valor $id");
//        }
//        return $row;
    }
    
    public function getOneParish($id) {
        $id = (int) $id;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('parishes')
               ->join('vicarious', 'parishes.idVicarious = vicarious.id', array('vicariousName'))
               ->where(array('parishes.id' => $id));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            throw new \Exception("Could not find row $id");
        }
        return $results;        
    }
    
    public function fetchAllUsers($idParish){
        $idParish = (int) $idParish;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('parishes')
               ->columns(array('parishName')) 
               ->join('users', 'parishes.id = users.idParishes', array('id', 'charge', 'firstName', 'lastName', 'email', 'cellPhone'))
               ->join('roles', 'roles.id = users.idRoles', array('nameRole')) 
               ->where(array('users.idParishes' => $idParish));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
//        $results = $resultSet->current();
        return $resultSet; 
    }

    public function addParish(ParishesFilter $parishesFilter) {
        $values = array(
            'parishName' => $parishesFilter->parishName,
            'address' => $parishesFilter->address,
            'phone' => $parishesFilter->phone,
//            'cellPhone' => $parishesFilter->cellPhone,
            'idVicarious' => $parishesFilter->idVicarious,
        );
        $this->tableGateway->insert($values);
    }

    public function updateParish(ParishesFilter $parishesFilter) {
        $values = array(
            'parishName' => $parishesFilter->parishName,
            'address' => $parishesFilter->address,
            'phone' => $parishesFilter->phone,
//            'cellPhone' => $parishesFilter->cellPhone,
            'idVicarious' => $parishesFilter->idVicarious,
        );
        $id = (int) $parishesFilter->id;
        $this->tableGateway->update($values, array('id' => $id));
    }

    public function deleteParish($id) {
        $this->tableGateway->delete(array('id' => $id));
    }

}

