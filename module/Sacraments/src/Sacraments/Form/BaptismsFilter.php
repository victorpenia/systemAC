<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Form;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class BaptismsFilter implements InputFilterAwareInterface {

    public $id;
    public $ci;
    public $page;
    public $item;
    public $baptismDate;
    public $baptismPriest;
    public $baptismPriestOthers;
    public $firstName;
    public $firstSurname;
    public $secondSurname;
    public $bornIn;
    public $bornInProvince;
    public $bornInOthers;
    public $birthDate;
    public $matherName;
    public $matherFirstSurname;
    public $matherSecondSurname;
    public $fatherName;
    public $fatherFirstSurname;
    public $fatherSecondSurname;
    public $congregation;
    public $godfatherNameOne;
    public $godfatherSurnameOne;
    public $godfatherNameTwo;
    public $godfatherSurnameTwo;
    public $oficialiaRC;
    public $bookLN;
    public $departure;
    public $folioFS;
//    public $year;
    public $attestPriest;
    public $attestPriestOthers;
    public $observation;
    public $idBookofsacraments;
    public $idPerson;
    public $idParishes;
    
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->ci = (isset($data['ci'])) ? $data['ci'] : null;
        $this->page = (isset($data['page'])) ? $data['page'] : null;
        $this->item = (isset($data['item'])) ? $data['item'] : null;
        $this->baptismDate = (isset($data['baptismDate'])) ? $data['baptismDate'] : null;
        $this->baptismPriest = (isset($data['baptismPriest'])) ? $data['baptismPriest'] : null;
        $this->baptismPriestOthers = (isset($data['baptismPriestOthers'])) ? $data['baptismPriestOthers'] : null;
        $this->firstName = (isset($data['firstName'])) ? $data['firstName'] : null;
        $this->firstSurname = (isset($data['firstSurname'])) ? $data['firstSurname'] : null;
        $this->secondSurname = (isset($data['secondSurname'])) ? $data['secondSurname'] : null;
        $this->bornIn = (isset($data['bornIn'])) ? $data['bornIn'] : null;
        $this->bornInProvince = (isset($data['bornInProvince'])) ? $data['bornInProvince'] : null;
        $this->bornInOthers = (isset($data['bornInOthers'])) ? $data['bornInOthers'] : null;
        $this->birthDate = (isset($data['birthDate'])) ? $data['birthDate'] : null;
        $this->matherName = (isset($data['matherName'])) ? $data['matherName'] : null;
        $this->matherFirstSurname = (isset($data['matherFirstSurname'])) ? $data['matherFirstSurname'] : null;
        $this->matherSecondSurname = (isset($data['matherSecondSurname'])) ? $data['matherSecondSurname'] : null;
        $this->fatherName = (isset($data['fatherName'])) ? $data['fatherName'] : null;
        $this->fatherFirstSurname = (isset($data['fatherFirstSurname'])) ? $data['fatherFirstSurname'] : null;
        $this->fatherSecondSurname = (isset($data['fatherSecondSurname'])) ? $data['fatherSecondSurname'] : null;
        $this->congregation = (isset($data['congregation'])) ? $data['congregation'] : null;
        $this->godfatherNameOne = (isset($data['godfatherNameOne'])) ? $data['godfatherNameOne'] : null;
        $this->godfatherSurnameOne = (isset($data['godfatherSurnameOne'])) ? $data['godfatherSurnameOne'] : null;
        $this->godfatherNameTwo = (isset($data['godfatherNameTwo'])) ? $data['godfatherNameTwo'] : null;
        $this->godfatherSurnameTwo = (isset($data['godfatherSurnameTwo'])) ? $data['godfatherSurnameTwo'] : null;
        $this->oficialiaRC = (isset($data['oficialiaRC'])) ? $data['oficialiaRC'] : null;
        $this->bookLN = (isset($data['bookLN'])) ? $data['bookLN'] : null;
        $this->departure = (isset($data['departure'])) ? $data['departure'] : null;
        $this->folioFS = (isset($data['folioFS'])) ? $data['folioFS'] : null;
//        $this->year = (isset($data['year'])) ? $data['year'] : null;
        $this->attestPriest = (isset($data['attestPriest'])) ? $data['attestPriest'] : null;
        $this->attestPriestOthers = (isset($data['attestPriestOthers'])) ? $data['attestPriestOthers'] : null;
        $this->observation = (isset($data['observation'])) ? $data['observation'] : null;
        $this->idBookofsacraments = (isset($data['idBookofsacraments'])) ? $data['idBookofsacraments'] : null;
        $this->idPerson = (isset($data['idPerson'])) ? $data['idPerson'] : null;
        $this->idParishes = (isset($data['idParishes'])) ? $data['idParishes'] : null;
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
//                            'max' => 100,
//                        ),
//                    ),
//                ),
//            ));
            
//            $inputFilter->add(array(
//                'name' => 'godfatherOne',
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
            
//            $inputFilter->add(array(
//                'name' => 'baptismPriest',
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
            
//            $inputFilter->add(array(
//                'name' => 'attestPriest',
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

//            $inputFilter->add(array(
//                'name' => 'page',
//                'required' => false,
//                'filters' => array(
//                    array('name' => 'StripTags'),
//                    array('name' => 'StringTrim'),
//                ),
//                'validators' => array(
//                    array(
//                        'name' => 'StringLength',
//                        'options' => array(
//                            'encoding' => 'UTF-8',
//                            'min' => 1,
//                            'max' => 10,
//                        ),
//                    ),
//                ),
//            ));
            
//            $inputFilter->add(array(
//                'name' => 'item',
//                'required' => false,
//                'filters' => array(
//                    array('name' => 'StripTags'),
//                    array('name' => 'StringTrim'),
//                ),
//                'validators' => array(
//                    array(
//                        'name' => 'StringLength',
//                        'options' => array(
//                            'encoding' => 'UTF-8',
//                            'min' => 1,
//                            'max' => 10,
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
