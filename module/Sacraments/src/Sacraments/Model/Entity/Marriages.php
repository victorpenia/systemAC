<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Sacraments\Form\MarriagesFilter;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class Marriages extends TableGateway {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginated = false){
        if($paginated){
             $select = new Select();
             $select->from('marriages')
                    ->join(array('personMale' => 'person'), 'marriages.idPersonMale= personMale.id ', array( 'firstNameMale' => 'firstName', 'firstSurnameMale' => 'firstSurname', 'secondSurnameMale' => 'secondSurname', 'ciMale' => 'ci'))
                    ->join(array('personFemale' => 'person'), 'marriages.idPersonFemale= personFemale.id', array('firstNameFemale' => 'firstName', 'firstSurnameFemale' => 'firstSurname', 'secondSurnameFemale' => 'secondSurname', 'ciFemale' => 'ci'))
                    ->join('bookofsacraments', 'bookofsacraments.id = marriages.idBookofsacraments', array('code', 'book'))
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
             $select->from('marriages')
                    ->join(array('personMale' => 'person'), 'marriages.idPersonMale= personMale.id ', array( 'firstNameMale' => 'firstName', 'firstSurnameMale' => 'firstSurname', 'secondSurnameMale' => 'secondSurname', 'ciMale' => 'ci'))
                    ->join(array('personFemale' => 'person'), 'marriages.idPersonFemale= personFemale.id', array('firstNameFemale' => 'firstName', 'firstSurnameFemale' => 'firstSurname', 'secondSurnameFemale' => 'secondSurname', 'ciFemale' => 'ci'))
                    ->join('bookofsacraments', 'bookofsacraments.id = marriages.idBookofsacraments', array('code', 'book'))
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
    
    public function getOneMariage($id) {
        $id = (int) $id;
        $select = new Select();
        $select->from('marriages')
               ->join(array('personMale' => 'person'), 'marriages.idPersonMale = personMale.id', array('idPersonMale' => 'id', 'ciMale' => 'ci', 'firstNameMale' =>'firstName', 'firstSurnameMale' =>'firstSurname', 'secondSurnameMale' =>'secondSurname', 
                      'birthDateMale' => 'birthDate', 'maritalStatusMale' => 'maritalStatus', 'fatherNameMale' => 'fatherName', 'fatherFirstSurnameMale' => 'fatherFirstSurname', 'fatherSecondSurnameMale' => 'fatherSecondSurname',
                      'matherNameMale' => 'matherName', 'matherFirstSurnameMale' => 'matherFirstSurname', 'matherSecondSurnameMale' => 'matherSecondSurname'))
               ->join(array('personFemale' => 'person'), 'marriages.idPersonFemale = personFemale.id', array('idPersonFemale' => 'id', 'ciFemale' => 'ci', 'firstNameFemale' =>'firstName', 'firstSurnameFemale' =>'firstSurname', 'secondSurnameFemale' =>'secondSurname', 
                      'birthDateFemale' => 'birthDate', 'maritalStatusFemale' => 'maritalStatus', 'fatherNameFemale' => 'fatherName', 'fatherFirstSurnameFemale' => 'fatherFirstSurname', 'fatherSecondSurnameFemale' => 'fatherSecondSurname',
                      'matherNameFemale' => 'matherName', 'matherFirstSurnameFemale' => 'matherFirstSurname', 'matherSecondSurnameFemale' => 'matherSecondSurname')) 
               ->where(array('marriages.id' => $id));
        $rowset = $this->tableGateway->selectWith($select);
        $resultSet = $rowset->current();
        if (!$resultSet) {
            throw new \Exception("Could not find row $id");
        }
        return $resultSet;
    }
    
    public function getOneMariageAndParish($id) {
        $id = (int) $id;
        $select = new Select();
        $select->from('marriages')
               ->join(array('personMale' => 'person'), 'marriages.idPersonMale = personMale.id', array('idPersonMale' => 'id', 'ciMale' => 'ci', 'firstNameMale' =>'firstName', 'firstSurnameMale' =>'firstSurname', 'secondSurnameMale' =>'secondSurname', 
                      'birthDateMale' => 'birthDate', 'maritalStatusMale' => 'maritalStatus', 'fatherNameMale' => 'fatherName', 'fatherFirstSurnameMale' => 'fatherFirstSurname', 'fatherSecondSurnameMale' => 'fatherSecondSurname',
                      'matherNameMale' => 'matherName', 'matherFirstSurnameMale' => 'matherFirstSurname', 'matherSecondSurnameMale' => 'matherSecondSurname'))
               ->join(array('personFemale' => 'person'), 'marriages.idPersonFemale = personFemale.id', array('idPersonFemale' => 'id', 'ciFemale' => 'ci', 'firstNameFemale' =>'firstName', 'firstSurnameFemale' =>'firstSurname', 'secondSurnameFemale' =>'secondSurname', 
                      'birthDateFemale' => 'birthDate', 'maritalStatusFemale' => 'maritalStatus', 'fatherNameFemale' => 'fatherName', 'fatherFirstSurnameFemale' => 'fatherFirstSurname', 'fatherSecondSurnameFemale' => 'fatherSecondSurname',
                      'matherNameFemale' => 'matherName', 'matherFirstSurnameFemale' => 'matherFirstSurname', 'matherSecondSurnameFemale' => 'matherSecondSurname')) 
               ->join('parishes', 'marriages.idParish = parishes.id', array('parishName'))
               ->where(array('marriages.id' => $id));
        $rowset = $this->tableGateway->selectWith($select);
        $resultSet = $rowset->current();
        if (!$resultSet) {
            throw new \Exception("Could not find row $id");
        }
        return $resultSet;
    }
    
    public function getOneMarriageById($id) {
        $id = (int) $id;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('marriages')
               ->join(array('personMale' => 'person'), 'marriages.idPersonMale = personMale.id', array('ciMale' => 'ci', 'firstNameMale' =>'firstName', 'firstSurnameMale' =>'firstSurname', 'secondSurnameMale' =>'secondSurname', 
                      'birthDateMale' => 'birthDate', 'maritalStatusMale' => 'maritalStatus', 'fatherNameMale' => 'fatherName', 'fatherFirstSurnameMale' => 'fatherFirstSurname', 'fatherSecondSurnameMale' => 'fatherSecondSurname',
                      'matherNameMale' => 'matherName', 'matherFirstSurnameMale' => 'matherFirstSurname', 'matherSecondSurnameMale' => 'matherSecondSurname'))
               ->join(array('personFemale' => 'person'), 'marriages.idPersonFemale = personFemale.id', array('ciFemale' => 'ci', 'firstNameFemale' =>'firstName', 'firstSurnameFemale' =>'firstSurname', 'secondSurnameFemale' =>'secondSurname', 
                      'birthDateFemale' => 'birthDate', 'maritalStatusFemale' => 'maritalStatus', 'fatherNameFemale' => 'fatherName', 'fatherFirstSurnameFemale' => 'fatherFirstSurname', 'fatherSecondSurnameFemale' => 'fatherSecondSurname',
                      'matherNameFemale' => 'matherName', 'matherFirstSurnameFemale' => 'matherFirstSurname', 'matherSecondSurnameFemale' => 'matherSecondSurname')) 
               ->join('bookofsacraments', 'bookofsacraments.id = marriages.idBookofsacraments', array('code', 'book', 'idParishes'))
               ->join('parishes', 'bookofsacraments.idParishes = parishes.id', array('parishName'))
               ->join('Users', 'marriages.idUserCertificate = users.id', array('idRoles'), 'left')
               ->join('Certificates', 'Certificates.idUsers = users.id', array('certificateName', 'privateKey'), 'left') 
               ->where(array('marriages.id' => $id));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            throw new \Exception("Could not find row $id");
        }
        return $results;        
    }
    
    public function getOneMarriageByParish($id, $idParish) {
        $id = (int) $id;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('marriages')
               ->join(array('personMale' => 'person'), 'marriages.idPersonMale = personMale.id', array('ciMale' => 'ci', 'firstNameMale' =>'firstName', 'firstSurnameMale' =>'firstSurname', 'secondSurnameMale' =>'secondSurname', 
                      'birthDateMale' => 'birthDate', 'maritalStatusMale' => 'maritalStatus', 'fatherNameMale' => 'fatherName', 'fatherFirstSurnameMale' => 'fatherFirstSurname', 'fatherSecondSurnameMale' => 'fatherSecondSurname',
                      'matherNameMale' => 'matherName', 'matherFirstSurnameMale' => 'matherFirstSurname', 'matherSecondSurnameMale' => 'matherSecondSurname'))
               ->join(array('personFemale' => 'person'), 'marriages.idPersonFemale = personFemale.id', array('ciFemale' => 'ci', 'firstNameFemale' =>'firstName', 'firstSurnameFemale' =>'firstSurname', 'secondSurnameFemale' =>'secondSurname', 
                      'birthDateFemale' => 'birthDate', 'maritalStatusFemale' => 'maritalStatus', 'fatherNameFemale' => 'fatherName', 'fatherFirstSurnameFemale' => 'fatherFirstSurname', 'fatherSecondSurnameFemale' => 'fatherSecondSurname',
                      'matherNameFemale' => 'matherName', 'matherFirstSurnameFemale' => 'matherFirstSurname', 'matherSecondSurnameFemale' => 'matherSecondSurname')) 
               ->join('bookofsacraments', 'bookofsacraments.id = marriages.idBookofsacraments', array('code', 'book', 'idParishes'))
               ->join('parishes', 'bookofsacraments.idParishes = parishes.id', array('parishName'))
               ->join('Users', 'marriages.idUserCertificate = users.id', array('idRoles'), 'left')
               ->join('Certificates', 'Certificates.idUsers = users.id', array('certificateName', 'privateKey'), 'left') 
               ->where(array('marriages.id' => $id, 'bookofsacraments.idParishes' => $idParish));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            throw new \Exception("Could not find row $id");
        }
        return $results;        
    }
    
    public function fetchOneMarriages($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No hay registros asociados al valor $id");
        }
        return $row;
    }   
    
    public function getIdBookofSacrament($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('idBookofsacraments' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return true;
    } 

    public function addMarriage(MarriagesFilter $marriagesFilter, $idPersonMale, $idPersonFemale, $idUser, $idParish) {
        if(empty($marriagesFilter->observation)){
            $marriagesFilter->observation ='Ninguna';
        }
        if($marriagesFilter->marriagePriest != 'Otros')
            $marriagesFilter->marriagePriestOthers = '';
        if($marriagesFilter->attestPriest != 'Otros')
            $marriagesFilter->attestPriestOthers = '';
        if($marriagesFilter->baptismParishMale != 'Otros')
            $marriagesFilter->baptismParishMaleOthers = '';
        if($marriagesFilter->baptismParishFemale != 'Otros')
            $marriagesFilter->baptismParishFemaleOthers = '';
        $values = array(
            'idBookofsacraments' => $marriagesFilter->idBookofsacraments,
            'page' => $marriagesFilter->page,
            'item' => $marriagesFilter->item,
            'marriagePriest' => $marriagesFilter->marriagePriest,
            'marriagePriestOthers' => $marriagesFilter->marriagePriestOthers,
            'marriageDate' => $marriagesFilter->marriageDate,
            'baptismParishMale' => $marriagesFilter->baptismParishMale,
            'baptismParishMaleOthers' => $marriagesFilter->baptismParishMaleOthers,
            'baptismParishFemale' => $marriagesFilter->baptismParishFemale,
            'baptismParishFemaleOthers' => $marriagesFilter->baptismParishFemaleOthers,
            'godfatherNameOneInformation' => $marriagesFilter->godfatherNameOneInformation,
            'godfatherSurnameOneInformation' => $marriagesFilter->godfatherSurnameOneInformation,
            'godfatherNameTwoInformation' => $marriagesFilter->godfatherNameTwoInformation,
            'godfatherSurnameTwoInformation' => $marriagesFilter->godfatherSurnameTwoInformation,
            'godfatherNameOnePresence' => $marriagesFilter->godfatherNameOnePresence,
            'godfatherSurnameOnePresence' => $marriagesFilter->godfatherSurnameOnePresence,
            'godfatherNameTwoPresence' => $marriagesFilter->godfatherNameTwoPresence,
            'godfatherSurnameTwoPresence' => $marriagesFilter->godfatherSurnameTwoPresence,
            'attestPriest' => $marriagesFilter->attestPriest,
            'attestPriestOthers' => $marriagesFilter->attestPriestOthers,
            'observation' => $marriagesFilter->observation,
            'idPersonMale' => $idPersonMale,
            'idPersonFemale' => $idPersonFemale,
            'idUserMarriage' => $idUser,
            'idParish' => $idParish,
        );
        $this->tableGateway->insert($values);
    }
    
    public function getOneMarriageByPersonCIMale($CI){
        error_log('logM. CiMale = '.$CI);
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('marriages')
               ->join('person', 'marriages.idPersonMale = person.id', array('ci')) 
               ->where(array('person.ci' => $CI));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            return true;
        }
        return false;        
    }
    
    public function getOneMarriageByPersonNameMale($firstName, $firstSurname, $secondSurname){
        error_log('logM. name personMale = '.$firstName);
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('marriages')
               ->join('person', 'marriages.idPersonMale = person.id', array('ci')) 
               ->where(array('person.firstName' => $firstName, 'person.firstSurname' => $firstSurname, 'person.secondSurname' => $secondSurname));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            return true;
        }
        return false;        
    }
    
    public function getOneMarriageByPersonCIFemale($CI){
        error_log('logM. CiFemale = '.$CI);
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('marriages')
               ->join('person', 'marriages.idPersonFemale = person.id', array('ci')) 
               ->where(array('person.ci' => $CI));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            return true;
        }
        return false;        
    }
    
    public function getOneMarriageByPersonNameFemale($firstName, $firstSurname, $secondSurname){
        error_log('logM. name personFemale = '.$firstName);
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('marriages')
               ->join('person', 'marriages.idPersonFemale = person.id', array('ci')) 
               ->where(array('person.firstName' => $firstName, 'person.firstSurname' => $firstSurname, 'person.secondSurname' => $secondSurname));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            return true;
        }
        return false;        
    }

    public function updateMarriages(MarriagesFilter $marriagesFilter) {
        if($marriagesFilter->marriagePriest != 'Otros')
            $marriagesFilter->marriagePriestOthers = '';
        if($marriagesFilter->attestPriest != 'Otros')
            $marriagesFilter->attestPriestOthers = '';
        if($marriagesFilter->baptismParishMale != 'Otros')
            $marriagesFilter->baptismParishMaleOthers = '';
        if($marriagesFilter->baptismParishFemale != 'Otros')
            $marriagesFilter->baptismParishFemaleOthers = '';
        $values = array(
            'marriagePriest' => $marriagesFilter->marriagePriest,
            'marriagePriestOthers' => $marriagesFilter->marriagePriestOthers,
            'marriageDate' => $marriagesFilter->marriageDate,
            'baptismParishMale' => $marriagesFilter->baptismParishMale,
            'baptismParishMaleOthers' => $marriagesFilter->baptismParishMaleOthers,
            'baptismParishFemale' => $marriagesFilter->baptismParishFemale,
            'baptismParishFemaleOthers' => $marriagesFilter->baptismParishFemaleOthers,
            'godfatherNameOneInformation' => $marriagesFilter->godfatherNameOneInformation,
            'godfatherSurnameOneInformation' => $marriagesFilter->godfatherSurnameOneInformation,
            'godfatherNameTwoInformation' => $marriagesFilter->godfatherNameTwoInformation,
            'godfatherSurnameTwoInformation' => $marriagesFilter->godfatherSurnameTwoInformation,
            'godfatherNameOnePresence' => $marriagesFilter->godfatherNameOnePresence,
            'godfatherSurnameOnePresence' => $marriagesFilter->godfatherSurnameOnePresence,
            'godfatherNameTwoPresence' => $marriagesFilter->godfatherNameTwoPresence,
            'godfatherSurnameTwoPresence' => $marriagesFilter->godfatherSurnameTwoPresence,
            'attestPriest' => $marriagesFilter->attestPriest,
            'attestPriestOthers' => $marriagesFilter->attestPriestOthers,
            'observation' => $marriagesFilter->observation,
        );
        $id = (int) $marriagesFilter->id;
        $this->tableGateway->update($values, array('id' => $id));
    }
    
    public function signMarriage($idMarriage, $idUser) {
        $values = array(
            'idUserCertificate' => $idUser,
        );
        $idMarriage = (int) $idMarriage;
        $this->tableGateway->update($values, array('id' => $idMarriage));
    }

    public function deleteMarriages($id) {
        $this->tableGateway->delete(array('id' => $id));
    }

}

