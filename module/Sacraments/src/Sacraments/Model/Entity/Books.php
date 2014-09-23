<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Sacraments\Form\BooksFilter;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class Books extends TableGateway {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginated = false){
        if($paginated){
             $select = new Select();
             $select->from('bookofsacraments')
                    ->join('parishes', 'parishes.id = bookofsacraments.idParishes', array('parishName'));
             $paginatorAdapter = new DbSelect(
                 $select,
                 $this->tableGateway->getAdapter()
             );
             $paginator = new Paginator($paginatorAdapter);
             return $paginator;
        }
        return $this->tableGateway->select();
    }
    
    public function fetchAllByIdParish($paginated = false, $idParish){
        error_log('logM. idParish ='.$idParish);
        if($paginated){
             $select = new Select();
             $select->from('bookofsacraments')
                    ->where(array('idParishes' => $idParish));
             $paginatorAdapter = new DbSelect(
                 $select,
                 $this->tableGateway->getAdapter()
             );
             $paginator = new Paginator($paginatorAdapter);
             return $paginator;
        }
        return $this->tableGateway->select();
    }
    
    public function fetchAllBaptismByBook($idBook){
        $idBook = (int) $idBook;
        error_log('logM. idBook ='.$idBook);
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('bookofsacraments')
               ->columns(array('sacramentName')) 
               ->join('baptisms', 'bookofsacraments.id = baptisms.idBookofsacraments', array('id','page', 'item'))
               ->join('person', 'person.id = baptisms.idPerson', array('firstName', 'firstSurname', 'secondSurname')) 
               ->where(array('baptisms.idBookofsacraments' => $idBook));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
//        $results = $resultSet->current();
        return $resultSet; 
    }

    public function getBookByIdParish($idParish, $sacrament) {
        $idParish = (int) $idParish;
        error_log('logM. idParish ='.$idParish);
        $rowset = $this->tableGateway->select(array('idParishes' => $idParish, 'sacramentName' => $sacrament));
        return $rowset;
    }
    
    public function getBookBySacrament($idParish, $sacrament) {
        $idParish = (int) $idParish;
        error_log('logM. idParish ='.$idParish);
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('bookofsacraments')
               ->columns(array('book' => new Expression('MAX(book)')))
               ->where(array('idParishes' => $idParish, 'sacramentName' => $sacrament));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
//        $results = $resultSet->current();
        return $resultSet; 
//        $idParish = (int) $idParish;
//        error_log($idParish);
//        $rowset = $this->tableGateway->select(array('idParishes' => $idParish, 'sacramentName' => $sacrament));
//        return $rowset;
    }

    public function fetchOneBook($id) {
        $id = (int) $id;
        error_log('logM. id ='.$id);
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No hay registros asociados al valor $id");
        }
        return $row;
    }
    
    public function getOneBook($id) {
        $id = (int) $id;
        error_log('logM. id ='.$id);
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('bookofsacraments')
               ->join('parishes', 'parishes.id = bookofsacraments.idParishes', array('parishName', 'idParish' => 'id'))
               ->where(array('bookofsacraments.id' => $id));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            throw new \Exception("Could not find row $id");
        }
        return $results;        
    }
    
    public function getOneBookByParish($id, $idParish) {
        $id = (int) $id;
        error_log('logM. id ='.$id);
        $idParish = (int) $idParish;
        error_log('logM. idParish ='.$idParish);
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('bookofsacraments')
               ->join('parishes', 'parishes.id = bookofsacraments.idParishes', array('parishName', 'idParish' => 'id'))
               ->where(array('bookofsacraments.id' => $id, 'bookofsacraments.idParishes' => $idParish));
        $selectString = $sql->getSqlStringForSqlObject($select); 
        $resultSet = $this->tableGateway->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $results = $resultSet->current();
        if (!$results) {
            throw new \Exception("Could not find row $id");
        }
        return $results;        
    }

    public function addBook(BooksFilter $booksFilter) {
        $code = $this->getCodeSacrament($booksFilter, $booksFilter->idParish);
        error_log('AddM code = '.$code);
        $values = array(
            'idParishes' => $booksFilter->idParish,
            'sacramentName' => $booksFilter->sacramentName,
            'book' => $booksFilter->book,
            'startItem' => $booksFilter->startItem,
            'registrationDate' => date("Y-m-d"),
            'statusBook' => $booksFilter->statusBook,
            'typeBook' => $booksFilter->typeBook,
            'code' => $code,
        );
        $this->tableGateway->insert($values);
    }
    
    public function addpBook(BooksFilter $booksFilter, $idParish) {
        $code = $this->getCodeSacrament($booksFilter, $idParish);
        error_log('addM code = '.$code);
        $values = array(
            'idParishes' => $idParish,
            'sacramentName' => $booksFilter->sacramentName,
            'book' => $booksFilter->book,
            'startItem' => $booksFilter->startItem,
            'registrationDate' => date("Y-m-d"),
            'statusBook' => 'Libro',
            'typeBook' => 'Original',
            'code' => $code,
        );
        $this->tableGateway->insert($values);
    }
    
    public function getCodeSacrament(BooksFilter $booksFilter, $idParish){
        $code="";
        if($booksFilter->sacramentName =="Bautismos")
            $code ="BA";
        if($booksFilter->sacramentName =="Matrimonios")
            $code ="MA";
        if($booksFilter->sacramentName =="Confirmaciones")
            $code ="CO";
        $code="P".$idParish."-".$code."-L".$booksFilter->book;
        return $code;
    }

    public function updateBook(BooksFilter $booksFilter) {
        $code = $this->getCodeSacrament($booksFilter, $booksFilter->idParishes);
        $values = array(
            'idParishes' => $booksFilter->idParishes,
            'sacramentName' => $booksFilter->sacramentName,
            'book' => $booksFilter->book,
            'statusBook' => $booksFilter->statusBook,
            'typeBook' => $booksFilter->typeBook,
            'code' => $code,
        );
        $id = (int) $booksFilter->id;
        $this->tableGateway->update($values, array('id' => $id));
    }
    
    public function updatepBook(BooksFilter $booksFilter, $idParish) {
        $code = $this->getCodeSacrament($booksFilter, $idParish);
        $values = array(
            'startItem' => $booksFilter->startItem,
        );
        $id = (int) $booksFilter->id;
        $this->tableGateway->update($values, array('id' => $id));
    }

    public function deleteParish($id) {
        $this->tableGateway->delete(array('id' => $id));
    }

}

