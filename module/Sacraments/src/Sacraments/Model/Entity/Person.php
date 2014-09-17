<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Sacraments\Form\BaptismsFilter;
use Sacraments\Form\ConfirmationsFilter;
use Sacraments\Form\MarriagesFilter;
use Sacraments\Form\PersonFilter;
use Zend\Db\Sql\Sql;

class Person extends TableGateway {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginated = false){
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }  
    
    public function getOnePerson($id) {
        $id = (int) $id;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('person')
               ->join('baptisms', 'person.id = baptisms.idPerson', array('idBaptism' => 'id'), 'left')
               ->join('confirmations', 'person.id = confirmations.idPerson', array('idConfirmation' => 'id'), 'left')
               ->join('marriages', 'person.id = marriages.idPersonMale or person.id = marriages.idPersonFemale', array('idMarriage' => 'id'), 'left') 
               ->where(array('person.id' => $id));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            throw new \Exception("Could not find row $id");
        }
        return $results;        
    }
    
    public function getOnePersonById($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $resultSet = $rowset->current();
        if (!$resultSet) {
            throw new \Exception("Could not find row $id");
        }
        return $resultSet;       
    }

    public function addPersonBaptisms(BaptismsFilter $personFilter) {        
//        $bornInProvince = '';
//        if($personFilter->bornIn == 'Otros')
//            $bornInProvince = $personFilter->bornInOthers;
//        else
//            $bornInProvince = $personFilter->bornInProvince;
        $values = array(
            'ci' => $personFilter->ci,
            'firstName' => $personFilter->firstName,
            'firstSurname' => $personFilter->firstSurname,
            'secondSurname' => $personFilter->secondSurname,
            'birthDate' => $personFilter->birthDate,
            'maritalStatus' => 'Soltero',
            'bornIn' => $personFilter->bornIn,
            'bornInProvince' => $personFilter->bornInProvince,
            'bornInOthers' => $personFilter->bornInOthers,
            'fatherName' => $personFilter->fatherName,
            'fatherFirstSurname' => $personFilter->fatherFirstSurname,
            'fatherSecondSurname' => $personFilter->fatherSecondSurname,
            'matherName' => $personFilter->matherName,
            'matherFirstSurname' => $personFilter->matherFirstSurname,
            'matherSecondSurname' => $personFilter->matherSecondSurname,
        );
        $this->tableGateway->insert($values);
        $id = $this->tableGateway->getLastInsertValue();
        return $id;
    }
    
    public function addPersonConfirmations(ConfirmationsFilter $personFilter) {        
        $values = array(
            'ci' => $personFilter->ci,
            'firstName' => $personFilter->firstName,
            'firstSurname' => $personFilter->firstSurname,
            'secondSurname' => $personFilter->secondSurname,
            'birthDate' => $personFilter->birthDate,
            'maritalStatus' => 'Soltero',
            'fatherName' => $personFilter->fatherName,
            'fatherFirstSurname' => $personFilter->fatherFirstSurname,
            'fatherSecondSurname' => $personFilter->fatherSecondSurname,
            'matherName' => $personFilter->matherName,
            'matherFirstSurname' => $personFilter->matherFirstSurname,
            'matherSecondSurname' => $personFilter->matherSecondSurname,
        );
        $this->tableGateway->insert($values);
        $id = $this->tableGateway->getLastInsertValue();
        return $id;
    }
    
    public function addPersonMarriagesMale(MarriagesFilter $personFilter) {        
        $values = array(
            'ci' => $personFilter->ciMale,
            'firstName' => $personFilter->firstNameMale,
            'firstSurname' => $personFilter->firstSurnameMale,
            'secondSurname' => $personFilter->secondSurnameMale,
            'birthDate' => $personFilter->birthDateMale,
            'maritalStatus' => $personFilter->maritalStatusMale,
            'fatherName' => $personFilter->fatherNameMale,
            'fatherFirstSurname' => $personFilter->fatherFirstSurnameMale,
            'fatherSecondSurname' => $personFilter->fatherSecondSurnameMale,
            'matherName' => $personFilter->matherNameMale,
            'matherFirstSurname' => $personFilter->matherFirstSurnameMale,
            'matherSecondSurname' => $personFilter->matherSecondSurnameMale,
        );
        $this->tableGateway->insert($values);
        $id = $this->tableGateway->getLastInsertValue();
        return $id;
    }
    
    public function addPersonMarriagesFemale(MarriagesFilter $personFilter) {        
        $values = array(
            'ci' => $personFilter->ciFemale,
            'firstName' => $personFilter->firstNameFemale,
            'firstSurname' => $personFilter->firstSurnameFemale,
            'secondSurname' => $personFilter->secondSurnameFemale,
            'birthDate' => $personFilter->birthDateFemale,
            'maritalStatus' => $personFilter->maritalStatusFemale,
            'fatherName' => $personFilter->fatherNameFemale,
            'fatherFirstSurname' => $personFilter->fatherFirstSurnameFemale,
            'fatherSecondSurname' => $personFilter->fatherSecondSurnameFemale,
            'matherName' => $personFilter->matherNameFemale,
            'matherFirstSurname' => $personFilter->matherFirstSurnameFemale,
            'matherSecondSurname' => $personFilter->matherSecondSurnameFemale,
        );
        $this->tableGateway->insert($values);
        $id = $this->tableGateway->getLastInsertValue();
        return $id;
    }
    
    public function updatePersonBaptisms(BaptismsFilter $personFilter){
        $values = array(
            'ci' => $personFilter->ci,
            'firstName' => $personFilter->firstName,
            'firstSurname' => $personFilter->firstSurname,
            'secondSurname' => $personFilter->secondSurname,
            'birthDate' => $personFilter->birthDate,
            'bornIn' => $personFilter->bornIn,
            'bornInProvince' => $personFilter->bornInProvince,
            'bornInOthers' => $personFilter->bornInOthers,
            'fatherName' => $personFilter->fatherName,
            'fatherFirstSurname' => $personFilter->fatherFirstSurname,
            'fatherSecondSurname' => $personFilter->fatherSecondSurname,
            'matherName' => $personFilter->matherName,
            'matherFirstSurname' => $personFilter->matherFirstSurname,
            'matherSecondSurname' => $personFilter->matherSecondSurname,
        );
        $id = (int) $personFilter->idPerson;
        $this->tableGateway->update($values, array('id' => $id));
    }
    
    public function updatePersonConfirmations(ConfirmationsFilter $personFilter){
        $values = array(
            'ci' => $personFilter->ci,
            'firstName' => $personFilter->firstName,
            'firstSurname' => $personFilter->firstSurname,
            'secondSurname' => $personFilter->secondSurname,
            'birthDate' => $personFilter->birthDate,
            'fatherName' => $personFilter->fatherName,
            'fatherFirstSurname' => $personFilter->fatherFirstSurname,
            'fatherSecondSurname' => $personFilter->fatherSecondSurname,
            'matherName' => $personFilter->matherName,
            'matherFirstSurname' => $personFilter->matherFirstSurname,
            'matherSecondSurname' => $personFilter->matherSecondSurname,
        );
        $id = (int) $personFilter->idPerson;
        $this->tableGateway->update($values, array('id' => $id));
    }

}

