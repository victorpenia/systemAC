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

class Users {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginated = false){
        if($paginated){
             $select = new Select();
             $select->from('users');
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
    
    public function getAllUsers() {
        $dbAdapter = $this->adapter;
        $sql = 'SELECT * FROM users, roles where users.idRole = roles.id';
        $statement = $dbAdapter->query($sql);
        return $result = $statement->execute();
    }

    public function getOneUser($id) {
        $id = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No hay registros asociados al valor $id");
        }
        return $row;
    }

    public function addUser(UserFilter $userFilter) {
        $values = array(
                    'ci' => $userFilter->ci,
                    'firstName' => $userFilter->firstName,
                    'lastName' => $userFilter->lastName,
                    'phone' => $userFilter->phone,
                    'cellPhone' => $userFilter->cellPhone,
                    'address' => $userFilter->address,
                    'email' => $userFilter->email,
                    'idRole' => $userFilter->role,
                    'status' => $userFilter->state,
                    'createdDate' => date("Y-m-d"),
                    'modifiedDate' => date("Y-m-d"),
                    'charge' => $userFilter->charge,
                    'password' => $userFilter->password,
                    'passwordSalt' => $userFilter->passwordSalt
                );
        $this->tableGateway->insert($values);
    }

    public function updateUser(UserFilter $userFilter) {
        $values = array(
                    'ci' => $userFilter->ci,
                    'firstName' => $userFilter->firstName,
                    'lastName' => $userFilter->lastName,
                    'phone' => $userFilter->phone,
                    'cellPhone' => $userFilter->cellPhone,
                    'address' => $userFilter->address,
                    'email' => $userFilter->email,
                    'idRole' => $userFilter->role,
                    'status' => $userFilter->state,
                    'modifiedDate' => date("Y-m-d"),
                    'charge' => $userFilter->charge
                );
        $id = (int) $userFilter->id;
        $this->update($values, array('id' => $id));
    }

    public function deleteVicariou($id) {
        $this->delete(array('id' => $id));
    }

}

