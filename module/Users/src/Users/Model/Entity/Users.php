<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Users\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Select;
use Users\Form\UserFilter;
use Application\Form\ForgottenFilter;
use Zend\Db\Sql\Sql;

class Users {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginated = false){
        if($paginated){
             $select = new Select();
             $select->from('users')
                    ->join('roles', 'users.idRoles = roles.id', array('nameRole'))
                    ->join('certificates', 'certificates.idUsers = users.id', array('serialNumber'), 'left')
                    ->join('parishes', 'parishes.id = users.idParishes', array('parishName'), 'left');
             $paginatorAdapter = new DbSelect(
                 $select,
                 $this->tableGateway->getAdapter()
             );
             $paginator = new Paginator($paginatorAdapter);
             return $paginator;
        }
        return $this->tableGateway->select();
    }
    
    public function fetchOneUser($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getUserByEmail($email) {
        $rowset = $this->tableGateway->select(array('email' => $email, 'status' => 'Activo'));
        $row = $rowset->current();
//        if (!$row) {
//            throw new \Exception("Could not find row $email");
//        }
        return $row;
    }

    public function getOneUser($id) {
        $id = (int) $id;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('users')
               ->join('roles', 'users.idRoles = roles.id', array('nameRole'))
               ->join('parishes', 'parishes.id = users.idParishes', array('parishName'), 'left')
               ->join('certificates', 'certificates.idUsers = users.id', array('certificateName', 'idCertificate' => 'id'), 'left')
               ->where(array('users.id' => $id));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            throw new \Exception("Could not find row $id");
        }
        return $results;        
    }
    
    public function verifiedPasswordUser($password,$id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('password' => $password, 'id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return true;       
    }
    
    public function getOnePriest($idParish) {
        $idParish = (int) $idParish;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('users')
               ->where(array('users.idParishes' => $idParish , 'users.idRoles' => '3'));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            throw new \Exception("Could not find row $id");
        }
        return $results;        
    }

    public function addUser(UserFilter $userFilter) {
        if($userFilter->idRoles == 1 || $userFilter->idRoles ==2)
            $idParish = 0;
        else
            $idParish = $userFilter->idParishes;
        $values = array(
//            'ci' => $userFilter->ci,
            'firstName' => $userFilter->firstName,
            'lastName' => $userFilter->lastName,
            'phone' => $userFilter->phone,
            'cellPhone' => $userFilter->cellPhone,
            'address' => $userFilter->address,
            'email' => $userFilter->email,
            'idRoles' => $userFilter->idRoles,
            'idParishes' => $idParish,
            'status' => $userFilter->status,
            'createdDate' => date("Y-m-d"),
            'modifiedDate' => date("Y-m-d"),
            'charge' => $userFilter->charge,
            'password' => $userFilter->password,
            'passwordSalt' => $userFilter->passwordSalt
        );
        $this->tableGateway->insert($values);
    }

    public function updateUser(UserFilter $userFilter) {
        if($userFilter->idRoles == 1 || $userFilter->idRoles ==2)
            $idParish = 0;
        else
            $idParish = $userFilter->idParishes;
        $values = array(
//                    'ci' => $userFilter->ci,
                    'firstName' => $userFilter->firstName,
                    'lastName' => $userFilter->lastName,
                    'phone' => $userFilter->phone,
                    'cellPhone' => $userFilter->cellPhone,
                    'address' => $userFilter->address,
                    'email' => $userFilter->email,
                    'idRoles' => $userFilter->idRoles,
                    'idParishes' => $idParish,
                    'status' => $userFilter->status,
                    'modifiedDate' => date("Y-m-d"),
                    'charge' => $userFilter->charge
                );
        $id = (int) $userFilter->id;
        $this->tableGateway->update($values, array('id' => $id));
    }
    
    public function ChangePasswordUser(ForgottenFilter $forgottenFilter) {
        $values = array(
            'password' => $forgottenFilter->password,
            'passwordSalt' => $forgottenFilter->passwordSalt
        );
        $this->tableGateway->update($values, array('email' => $forgottenFilter->email));
    }

    public function deleteVicariou($id) {
        $this->delete(array('id' => $id));
    }

}

