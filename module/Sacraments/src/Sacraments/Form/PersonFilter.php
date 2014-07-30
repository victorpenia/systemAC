<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Form;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class PersonFilter implements InputFilterAwareInterface {

    public $id;
    public $ci;
    public $firstName;
    public $firstSurname;
    public $secondSurname;
    public $birthDate;
    public $bornIn;
    public $bornInProvince;
    public $maritalStatus;
    public $fatherName;
    public $fatherFirstSurname;
    public $fatherSecondSurname;
    public $matherName;
    public $matherFirstSurname;
    public $matherSecondSurname;
    
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->ci = (isset($data['ci'])) ? $data['ci'] : null;
        $this->firstName = (isset($data['firstName'])) ? $data['firstName'] : null;
        $this->firstSurname = (isset($data['firstSurname'])) ? $data['firstSurname'] : null;
        $this->secondSurname = (isset($data['secondSurname'])) ? $data['secondSurname'] : null;
        $this->birthDate = (isset($data['birthDate'])) ? $data['birthDate'] : null;
        $this->bornIn = (isset($data['bornIn'])) ? $data['bornIn'] : null;
        $this->bornInProvince = (isset($data['bornInProvince'])) ? $data['bornInProvince'] : null;
        $this->maritalStatus = (isset($data['maritalStatus'])) ? $data['maritalStatus'] : null;
        $this->fatherName = (isset($data['fatherName'])) ? $data['fatherName'] : null;
        $this->fatherFirstSurname = (isset($data['fatherFirstSurname'])) ? $data['fatherFirstSurname'] : null;
        $this->fatherSecondSurname = (isset($data['fatherSecondSurname'])) ? $data['fatherSecondSurname'] : null;
        $this->matherName = (isset($data['matherName'])) ? $data['matherName'] : null;
        $this->matherFirstSurname = (isset($data['matherFirstSurname'])) ? $data['matherFirstSurname'] : null;
        $this->matherSecondSurname = (isset($data['matherSecondSurname'])) ? $data['matherSecondSurname'] : null;
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
