<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Form;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class BooksFilter implements InputFilterAwareInterface {

    public $id;
    public $code;
    public $sacramentName;
    public $book;
    public $startItem;
    public $registrationDate;
    public $typeBook;
    public $statusBook;
    public $idParish;
    
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->code = (isset($data['code'])) ? $data['code'] : null;
        $this->sacramentName = (isset($data['sacramentName'])) ? $data['sacramentName'] : null;
        $this->book = (isset($data['book'])) ? $data['book'] : null;
        $this->startItem = (isset($data['startItem'])) ? $data['startItem'] : null;
        $this->registrationDate = (isset($data['registrationDate'])) ? $data['registrationDate'] : null;
        $this->typeBook = (isset($data['typeBook'])) ? $data['typeBook'] : null;
        $this->statusBook = (isset($data['statusBook'])) ? $data['statusBook'] : null;
        $this->idParish = (isset($data['idParish'])) ? $data['idParish'] : null;
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
//                'name' => 'sacramentName',
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
