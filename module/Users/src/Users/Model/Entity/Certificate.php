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
use Users\Form\CertificateFilter;
use Zend\Db\Sql\Sql;

class Certificate {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginated = false){
        if($paginated){
             $select = new Select();
             $select->from('certificates')
                    ->join('users', 'users.id = certificates.idUsers', array('firstName', 'lastName', 'charge'));
             $paginatorAdapter = new DbSelect(
                 $select,
                 $this->tableGateway->getAdapter()
             );
             $paginator = new Paginator($paginatorAdapter);
             return $paginator;
        }
        return $this->tableGateway->select();
    }    
    
    public function getOneCertificate($id) {
        $id = (int) $id;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('certificates')
               ->join('users', 'users.id = certificates.idUsers', array('firstName', 'lastName', 'charge', 'idUser' => 'id'))
               ->where(array('certificates.id' => $id));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            throw new \Exception("Could not find row $id");
        }
        return $results;        
    }

    public function addCertificate(CertificateFilter $certificateFilter) {        
        $values = array(
            'certificateName' => $certificateFilter->certificateName,
            'privateKey' => $certificateFilter->privateKey,
            'createDate' => $certificateFilter->createDate,
            'expirationDate' => $certificateFilter->expirationDate,
            'version' => $certificateFilter->version,
            'serialNumber' => $certificateFilter->serialNumber,
            'ca' => $certificateFilter->ca,
            'idUsers' => $certificateFilter->idUser           
        );
        $this->tableGateway->insert($values);
    }
}

