<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Form;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ConfirmationsFilter implements InputFilterAwareInterface {

    public $id;
    public $ci;
    public $page;
    public $item;
    public $confirmationDate;
    public $firstName;
    public $firstSurname;
    public $secondSurname;
    public $birthDate;
    public $baptismParish;
    public $baptismParishOthers;
    public $matherName;
    public $matherFirstSurname;
    public $matherSecondSurname;
    public $fatherName;
    public $fatherFirstSurname;
    public $fatherSecondSurname;
    public $godfatherNameOne;
    public $godfatherSurnameOne;
    public $godfatherNameTwo;
    public $godfatherSurnameTwo;
    public $attestPriest;
    public $attestPriestOthers;
    public $observation;
    public $idBookofsacraments;
    public $idPerson;
    public $idParish;
    public $book;
    public $parishName;
    
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->ci = (isset($data['ci'])) ? $data['ci'] : null;
        $this->page = (isset($data['page'])) ? $data['page'] : null;
        $this->item = (isset($data['item'])) ? $data['item'] : null;
        $this->confirmationDate = (isset($data['confirmationDate'])) ? $data['confirmationDate'] : null;
        $this->firstName = (isset($data['firstName'])) ? $data['firstName'] : null;
        $this->firstSurname = (isset($data['firstSurname'])) ? $data['firstSurname'] : null;
        $this->secondSurname = (isset($data['secondSurname'])) ? $data['secondSurname'] : null;
        $this->birthDate = (isset($data['birthDate'])) ? $data['birthDate'] : null;
        $this->baptismParish = (isset($data['baptismParish'])) ? $data['baptismParish'] : null;
        $this->baptismParishOthers = (isset($data['baptismParishOthers'])) ? $data['baptismParishOthers'] : null;
        $this->matherName = (isset($data['matherName'])) ? $data['matherName'] : null;
        $this->matherFirstSurname = (isset($data['matherFirstSurname'])) ? $data['matherFirstSurname'] : null;
        $this->matherSecondSurname = (isset($data['matherSecondSurname'])) ? $data['matherSecondSurname'] : null;
        $this->fatherName = (isset($data['fatherName'])) ? $data['fatherName'] : null;
        $this->fatherFirstSurname = (isset($data['fatherFirstSurname'])) ? $data['fatherFirstSurname'] : null;
        $this->fatherSecondSurname = (isset($data['fatherSecondSurname'])) ? $data['fatherSecondSurname'] : null;
        $this->godfatherNameOne = (isset($data['godfatherNameOne'])) ? $data['godfatherNameOne'] : null;
        $this->godfatherSurnameOne = (isset($data['godfatherSurnameOne'])) ? $data['godfatherSurnameOne'] : null;
        $this->godfatherNameTwo = (isset($data['godfatherNameTwo'])) ? $data['godfatherNameTwo'] : null;
        $this->godfatherSurnameTwo = (isset($data['godfatherSurnameTwo'])) ? $data['godfatherSurnameTwo'] : null;
        $this->attestPriest = (isset($data['attestPriest'])) ? $data['attestPriest'] : null;
        $this->attestPriestOthers = (isset($data['attestPriestOthers'])) ? $data['attestPriestOthers'] : null;
        $this->observation = (isset($data['observation'])) ? $data['observation'] : null;
        $this->idParish = (isset($data['idParish'])) ? $data['idParish'] : null;
        $this->idBookofsacraments = (isset($data['idBookofsacraments'])) ? $data['idBookofsacraments'] : null;
        $this->idPerson = (isset($data['idPerson'])) ? $data['idPerson'] : null;        
        $this->book = (isset($data['book'])) ? $data['book'] : null;
        $this->parishName = (isset($data['parishName'])) ? $data['parishName'] : null;
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
            $inputFilter->add(array(
                'name' => 'firstName',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 30,
                        ),
                    ),
                ),
            ));
            
//            $inputFilter->add(array(
//                'name' => 'lastName',
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
//                            'max' => 30,
//                        ),
//                    ),
//                ),
//            ));
            
//            $inputFilter->add(array(
//                'name' => 'confirmationDate',
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
//                            'max' => 30,
//                        ),
//                    ),
//                ),
//            ));
            
            $inputFilter->add(array(
                'name' => 'attestPriest',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 75,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'page',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 10,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'item',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 10,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
?>
