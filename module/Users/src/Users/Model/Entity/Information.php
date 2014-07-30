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
use Users\Form\InformationFilter;
use Zend\Db\Sql\Sql;

class Information {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
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

    public function updateUser(InformationFilter $informationFilter) {
        $values = array(
                    'firstName' => $informationFilter->firstName,
                    'lastName' => $informationFilter->lastName,
                    'phone' => $informationFilter->phone,
                    'cellPhone' => $informationFilter->cellPhone,
                    'address' => $informationFilter->address,
                    'charge' => $informationFilter->charge,
//                    'email' => $userFilter->email,
                    'modifiedDate' => date("Y-m-d")
                );
        $id = (int) $informationFilter->id;
        $this->tableGateway->update($values, array('id' => $id));
    }
}

