<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Sacraments\Form\BaptismsFilter;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class Baptisms extends TableGateway {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginated = false){
        if($paginated){
             $select = new Select();
             $select->from('baptisms')
                    ->join('person', 'baptisms.idPerson = person.id', array('firstName', 'firstSurname', 'secondSurname'))
                    ->join('bookofsacraments', 'bookofsacraments.id = baptisms.idBookofsacraments', array('code', 'book'))
                    ->join('parishes', 'bookofsacraments.idParishes = parishes.id', array('parishName'));
             $paginatorAdapter = new DbSelect(
                 $select,
                 $this->tableGateway->getAdapter()
             );
             $paginator = new Paginator($paginatorAdapter);
             return $paginator;
        }
        return $this->tableGateway->select();
    }
    
    public function fetchAllByParish($paginated = false, $idParish){
        if($paginated){
             $select = new Select();
             $select->from('baptisms')
                    ->join('person', 'baptisms.idPerson = person.id', array('firstName', 'firstSurname', 'secondSurname'))
                    ->join('bookofsacraments', 'bookofsacraments.id = baptisms.idBookofsacraments', array('code', 'book'))
                    ->join('parishes', 'bookofsacraments.idParishes = parishes.id', array('parishName'))
                    ->where(array('parishes.id' => $idParish)); 
             $paginatorAdapter = new DbSelect(
                 $select,
                 $this->tableGateway->getAdapter()
             );
             $paginator = new Paginator($paginatorAdapter);
             return $paginator;
        }
        return $this->tableGateway->select();
    }
    
    public function getOneBaptisms($id) {
        $id = (int) $id;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('baptisms')
               ->join('person', 'baptisms.idPerson = person.id', array('firstName', 'firstSurname', 'secondSurname', 'bornIn', 'bornInProvince', 'birthDate', 'fatherName', 'matherName', 'fatherFirstSurname', 'matherFirstSurname', 'fatherSecondSurname', 'matherSecondSurname', 'ci'))
               ->join('bookofsacraments', 'bookofsacraments.id = baptisms.idBookofsacraments', array('code', 'book', 'idParishes'))
               ->join('parishes', 'bookofsacraments.idParishes = parishes.id', array('parishName'))
               ->join('Users', 'baptisms.idUserCertificate = users.id', array('idRoles'), 'left')
               ->join('certificates', 'certificates.idUsers = users.id', array('certificateName', 'privateKey'), 'left') 
               ->where(array('baptisms.id' => $id));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            throw new \Exception("Could not find row $id");
        }
        return $results;        
    }
    
    public function getOneBaptismsByParish($id, $idParish) {
        $id = (int) $id;
        $idParish = (int) $idParish;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('baptisms')
               ->join('person', 'baptisms.idPerson = person.id', array('firstName', 'firstSurname', 'secondSurname', 'bornIn', 'bornInProvince', 'birthDate', 'fatherName', 'matherName', 'fatherFirstSurname', 'fatherSecondSurname', 'matherFirstSurname', 'matherSecondSurname', 'ci'))
               ->join('bookofsacraments', 'bookofsacraments.id = baptisms.idBookofsacraments', array('code', 'book', 'idParishes'))
               ->join('parishes', 'bookofsacraments.idParishes = parishes.id', array('parishName'))
               ->join('Users', 'baptisms.idUserCertificate = users.id', array('idRoles'), 'left')
               ->join('certificates', 'certificates.idUsers = users.id', array('certificateName', 'privateKey'), 'left') 
               ->where(array('baptisms.id' => $id, 'bookofsacraments.idParishes' => $idParish));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            throw new \Exception("Could not find row $id");
        }
        return $results;        
    }
    
    public function getOneBaptismsByPerson($CI){
        error_log('logM. Ci='.$CI);
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('baptisms')
               ->join('person', 'baptisms.idPerson = person.id', array('ci')) 
               ->where(array('person.ci' => $CI));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            return true;
        }
        return false;        
    }
    
    public function getOneBaptismsByPersonName($firstName, $firstSurname, $secondSurname){
        error_log('logM. name person='.$firstName);
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('baptisms')
               ->join('person', 'baptisms.idPerson = person.id', array('ci')) 
               ->where(array('person.firstName' => $firstName, 'person.firstSurname' => $firstSurname, 'person.secondSurname' => $secondSurname));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            return true;
        }
        return false;        
    }
    
    public function fetchOneBaptism($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No hay registros asociados al valor $id");
        }
        return $row;
    }   

    public function addBaptism(BaptismsFilter $baptismsFilter, $idPerson, $idUser, $idParish) {
        if(empty($baptismsFilter->observation)){
            $baptismsFilter->observation ='Ninguna';
        }
        $baptismPriest = '';
        if($baptismsFilter->baptismPriest == 'Otros')
            $baptismPriest = $baptismsFilter->baptismPriestOthers;
        else
            $baptismPriest = $baptismsFilter->baptismPriest;
        
        $AttestPriest = '';
        if($baptismsFilter->attestPriest == 'Otros')
            $AttestPriest = $baptismsFilter->attestPriestOthers;
        else
            $AttestPriest = $baptismsFilter->attestPriest;
        
        $values = array(
            'page' => $baptismsFilter->page,
            'item' => $baptismsFilter->item,
            'baptismPriest' => $baptismPriest,
            'baptismDate' => $baptismsFilter->baptismDate,
            'congregation' => $baptismsFilter->congregation,
            'godfatherNameOne' => $baptismsFilter->godfatherNameOne,
            'godfatherSurnameOne' => $baptismsFilter->godfatherSurnameOne,
            'godfatherNameTwo' => $baptismsFilter->godfatherNameTwo,
            'godfatherSurnameTwo' => $baptismsFilter->godfatherSurnameTwo,
            'oficialiaRC' => $baptismsFilter->oficialiaRC,
            'bookLN' => $baptismsFilter->bookLN,
            'departure' => $baptismsFilter->departure,
            'folioFs' => $baptismsFilter->folioFS,
            'attestPriest' => $AttestPriest,
            'observation' => $baptismsFilter->observation,
            'idBookofsacraments' => $baptismsFilter->idBookofsacraments,
            'idPerson' => $idPerson,
            'idUserBaptism' => $idUser,
            'idParish' => $idParish,
        );
        $this->tableGateway->insert($values);
    }

    public function updateBaptism(BaptismsFilter $baptismsFilter) {
        $values = array(
            'page' => $baptismsFilter->page,
            'item' => $baptismsFilter->item,
            'baptismPriest' => $baptismsFilter->baptismPriest,
            'baptismDate' => $baptismsFilter->baptismDate,
            'congregation' => $baptismsFilter->congregation,
            'godfatherOne' => $baptismsFilter->godfatherOne,
            'godfatherTwo' => $baptismsFilter->godfatherTwo,
            'oficialiaRC' => $baptismsFilter->oficialiaRC,
            'bookLN' => $baptismsFilter->bookLN,
            'departure' => $baptismsFilter->departure,
            'folioFs' => $baptismsFilter->folioFS,
            'year' => $baptismsFilter->year,
            'attestPriest' => $baptismsFilter->attestPriest,
            'observation' => $baptismsFilter->observation,
        );
        $id = (int) $baptismsFilter->id;
        $this->tableGateway->update($values, array('id' => $id));
    }
    
    public function signBaptism($idBaptism, $idUser) {
        $values = array(
            'idUserCertificate' => $idUser,
        );
        $idBaptism = (int) $idBaptism;
        $this->tableGateway->update($values, array('id' => $idBaptism));
    }

    public function deleteBaptism($id) {
        $this->tableGateway->delete(array('id' => $id));
    }

}

