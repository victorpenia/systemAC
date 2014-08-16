<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Sacraments\Form\ConfirmationsFilter;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class Confirmations extends TableGateway {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginated = false){
        if($paginated){
             $select = new Select();
             $select->from('confirmations')
                    ->join('person', 'confirmations.idPerson = person.id', array('firstName', 'firstSurname', 'secondSurname', 'ci', 'birthDate'))
                    ->join('bookofsacraments', 'bookofsacraments.id = confirmations.idBookofsacraments', array('code', 'book'))
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
             $select->from('confirmations')
                    ->join('person', 'confirmations.idPerson = person.id', array('firstName', 'firstSurname', 'secondSurname', 'ci', 'birthDate'))
                    ->join('bookofsacraments', 'bookofsacraments.id = confirmations.idBookofsacraments', array('code', 'book'))
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
    
    public function getOneConfirmation($id) {
        $id = (int) $id;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('confirmations')
               ->join('person', 'confirmations.idPerson = person.id', array('firstName', 'firstSurname', 'secondSurname', 'birthDate', 'fatherName', 'fatherFirstSurname', 'fatherSecondSurname','matherName', 'matherFirstSurname', 'matherSecondSurname', 'ci'))
               ->join('bookofsacraments', 'bookofsacraments.id = confirmations.idBookofsacraments', array('code', 'book', 'idParishes'))
               ->join('parishes', 'bookofsacraments.idParishes = parishes.id', array('parishName'))
               ->join('Users', 'confirmations.idUserCertificate = users.id', array('idRoles'), 'left')
               ->join('certificates', 'certificates.idUsers = users.id', array('certificateName', 'privateKey'), 'left') 
               ->where(array('confirmations.id' => $id));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            throw new \Exception("Could not find row $id");
        }
        return $results;        
    }
    
    public function getOneConfirmationEdit($id) {
        $id = (int) $id;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('confirmations')
//               ->join('person', 'confirmations.idPerson = person.id', array('firstName', 'firstSurname', 'secondSurname'))
//               ->join('bookofsacraments', 'bookofsacraments.id = confirmations.idBookofsacraments', array('code', 'book', 'idParishes'))
//               ->join('parishes', 'bookofsacraments.idParishes = parishes.id', array('parishName')) 
               ->where(array('confirmations.id' => $id));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            error_log('modelllllll');
            throw new \Exception("Could not find row $id");
        }
        error_log('return .....');
        return $results;        
    }
    
//    public function getOneConfirmationEdit($id) {
//        $id = (int) $id;
//        $rowset = $this->tableGateway->select(array('id' => $id));
//        $row = $rowset->current();
//        if (!$row) {
//            throw new \Exception("Could not find row $id");
//        }
//        return $row;
//    }
    
    public function getOneConfirmationByPerson($CI){
        error_log('logM. Ci='.$CI);
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('confirmations')
               ->join('person', 'confirmations.idPerson = person.id', array('ci')) 
               ->where(array('person.ci' => $CI));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            return true;
        }
        return false;        
    }
    
    public function getOneConfirmationByPersonName($firstName, $firstSurname, $secondSurname){
        error_log('logM. name person='.$firstName);
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('confirmations')
               ->join('person', 'confirmations.idPerson = person.id', array('ci')) 
               ->where(array('person.firstName' => $firstName, 'person.firstSurname' => $firstSurname, 'person.secondSurname' => $secondSurname));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            return true;
        }
        return false;        
    }
    
    public function fetchOneConfirmations($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No hay registros asociados al valor $id");
        }
        return $row;
    }   

    public function addConfirmations(ConfirmationsFilter $confirmationsFilter, $idPerson, $idUser, $idParish) {
        if(empty($confirmationsFilter->observation)){
            $confirmationsFilter->observation ='Ninguna';
        }
//        $confirmationPriest = '';
//        if($confirmationsFilter->attestPriest == 'Otros')
//            $confirmationPriest = $confirmationsFilter->attestPriestOthers;
//        else
//            $confirmationPriest = $confirmationsFilter->attestPriest;
//        $baptismParish = '';
//        if($confirmationsFilter->baptismParish == 'Otros')
//            $baptismParish = $confirmationsFilter->baptismParishOthers;
//        else
//            $baptismParish = $confirmationsFilter->baptismParish;
        $values = array(
            'page' => $confirmationsFilter->page,
            'item' => $confirmationsFilter->item,
            'confirmationDate' => $confirmationsFilter->confirmationDate,
            'baptismParish' => $confirmationsFilter->baptismParish,
            'baptismParishOthers' => $confirmationsFilter->baptismParishOthers,
            'godfatherNameOne' => $confirmationsFilter->godfatherNameOne,
            'godfatherSurnameOne' => $confirmationsFilter->godfatherSurnameOne,
            'godfatherNameTwo' => $confirmationsFilter->godfatherNameTwo,
            'godfatherSurnameTwo' => $confirmationsFilter->godfatherSurnameTwo,
            'attestPriest' => $confirmationsFilter->attestPriest,
            'attestPriestOthers' => $confirmationsFilter->attestPriestOthers,
            'observation' => $confirmationsFilter->observation,
            'idBookofsacraments' => $confirmationsFilter->idBookofsacraments,
            'idPerson' => $idPerson,
            'idUserConfirmation' => $idUser,
            'idParish' => $idParish,
        );
        $this->tableGateway->insert($values);
    }

    public function updateConfirmations(ConfirmationsFilter $confirmationsFilter) {
        $values = array(
            'page' => $confirmationsFilter->page,
            'item' => $confirmationsFilter->item,
            'baptismPriest' => $confirmationsFilter->baptismPriest,
//            'baptismDate' => $baptismsFilter->baptismDate,
//            'congregation' => $baptismsFilter->congregation,
//            'godfatherOne' => $baptismsFilter->godfatherOne,
//            'godfatherTwo' => $baptismsFilter->godfatherTwo,
//            'oficialiaRC' => $baptismsFilter->oficialiaRC,
//            'bookLN' => $baptismsFilter->bookLN,
//            'departure' => $baptismsFilter->departure,
//            'folioFs' => $baptismsFilter->folioFS,
//            'year' => $baptismsFilter->year,
//            'attestPriest' => $baptismsFilter->attestPriest,
//            'observation' => $baptismsFilter->observation,
        );
        $id = (int) $confirmationsFilter->id;
        $this->tableGateway->update($values, array('id' => $id));
    }
    
    public function signConfirmation($idConfirmation, $idUser) {
        $values = array(
            'idUserCertificate' => $idUser,
        );
        $idConfirmation = (int) $idConfirmation;
        $this->tableGateway->update($values, array('id' => $idConfirmation));
    }

    public function deleteConfirmations($id) {
        $this->tableGateway->delete(array('id' => $id));
    }

}

