<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class DashboardFilter implements InputFilterAwareInterface {

    public $idParishes;
    public $idVicarious;
    public $year;
    public $sacrament;
//    public $page;
//    public $item;
//    public $baptismDate;
    
    
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->idParishes = (isset($data['idParishes'])) ? $data['idParishes'] : null;
        $this->idVicarious = (isset($data['idVicarious'])) ? $data['idVicarious'] : null;
        $this->year = (isset($data['year'])) ? $data['year'] : null;
        $this->sacrament = (isset($data['sacrament'])) ? $data['sacrament'] : null;
//        $this->page = (isset($data['page'])) ? $data['page'] : null;
//        $this->item = (isset($data['item'])) ? $data['item'] : null;        
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
//            $inputFilter->add(array(
//                'name' => 'firstName',
//                'required' => true,
//                'filters' => array(
//                    array('name' => 'StripTags'),
//                    array('name' => 'StringTrim'),
//                ),
//                'validators' => array(
//                    array(
//                        'name' => 'StringLength',
//                        'options' => array(
//                            'encoding' => 'UTF-8',
//                            'min' => 3,
//                            'max' => 100,
//                        ),
//                    ),
//                ),
//            ));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
?>
