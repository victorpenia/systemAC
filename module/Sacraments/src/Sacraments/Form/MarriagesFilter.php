<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Form;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class MarriagesFilter implements InputFilterAwareInterface {

    public $id;
    public $ciMale;
    public $ciFemale;
    public $page;
    public $item;
    public $marriageDate;
    public $marriagePriest;
    public $marriagePriestOthers;
    public $firstNameMale;
    public $firstSurnameMale;
    public $secondSurnameMale;
    public $birthDateMale;
    public $maritalStatusMale;
    public $baptismParishMale;
    public $baptismParishMaleOthers;
    public $matherNameMale;    
    public $matherFirstSurnameMale;
    public $matherSecondSurnameMale;    
    public $fatherNameMale;
    public $fatherFirstSurnameMale;
    public $fatherSecondSurnameMale;
    public $firstNameFemale;
    public $firstSurnameFemale;
    public $secondSurnameFemale;
    public $birthDateFemale;
    public $maritalStatusFemale;
    public $baptismParishFemale;
    public $baptismParishFemaleOthers;
    public $matherNameFemale;
    public $matherFirstSurnameFemale;
    public $matherSecondSurnameFemale;
    public $fatherNameFemale;
    public $fatherFirstSurnameFemale;
    public $fatherSecondSurnameFemale;
    public $godfatherNameOneInformation;
    public $godfatherSurnameOneInformation;
    public $godfatherNameTwoInformation;
    public $godfatherSurnameTwoInformation;
    public $godfatherNameOnePresence;
    public $godfatherSurnameOnePresence;
    public $godfatherNameTwoPresence;
    public $godfatherSurnameTwoPresence;
    public $attestPriest;
    public $attestPriestOthers;
    public $observation;
    public $idBookofsacraments;
    public $idPersonMale;
    public $idPersonFemale;
    public $idParish;
    
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->ciMale = (isset($data['ciMale'])) ? $data['ciMale'] : null;
        $this->ciFemale = (isset($data['ciFemale'])) ? $data['ciFemale'] : null;
        $this->page = (isset($data['page'])) ? $data['page'] : null;
        $this->item = (isset($data['item'])) ? $data['item'] : null;
        $this->marriagePriest = (isset($data['marriagePriest'])) ? $data['marriagePriest'] : null;
        $this->marriagePriestOthers = (isset($data['marriagePriestOthers'])) ? $data['marriagePriestOthers'] : null;
        $this->marriageDate = (isset($data['marriageDate'])) ? $data['marriageDate'] : null;
        $this->firstNameMale = (isset($data['firstNameMale'])) ? $data['firstNameMale'] : null;
        $this->firstNameFemale = (isset($data['firstNameFemale'])) ? $data['firstNameFemale'] : null;
        $this->firstSurnameMale = (isset($data['firstSurnameMale'])) ? $data['firstSurnameMale'] : null;
        $this->secondSurnameMale = (isset($data['secondSurnameMale'])) ? $data['secondSurnameMale'] : null;
        $this->firstSurnameFemale = (isset($data['firstSurnameFemale'])) ? $data['firstSurnameFemale'] : null; 
        $this->secondSurnameFemale = (isset($data['secondSurnameFemale'])) ? $data['secondSurnameFemale'] : null; 
        $this->birthDateMale = (isset($data['birthDateMale'])) ? $data['birthDateMale'] : null;
        $this->birthDateFemale = (isset($data['birthDateFemale'])) ? $data['birthDateFemale'] : null;
        $this->maritalStatusMale = (isset($data['maritalStatusMale'])) ? $data['maritalStatusMale'] : null;
        $this->maritalStatusFemale = (isset($data['maritalStatusFemale'])) ? $data['maritalStatusFemale'] : null;
        $this->fatherNameMale = (isset($data['fatherNameMale'])) ? $data['fatherNameMale'] : null;
        $this->fatherFirstSurnameMale = (isset($data['fatherFirstSurnameMale'])) ? $data['fatherFirstSurnameMale'] : null;
        $this->fatherSecondSurnameMale = (isset($data['fatherSecondSurnameMale'])) ? $data['fatherSecondSurnameMale'] : null;
        $this->fatherNameFemale = (isset($data['fatherNameFemale'])) ? $data['fatherNameFemale'] : null;
        $this->fatherFirstSurnameFemale = (isset($data['fatherFirstSurnameFemale'])) ? $data['fatherFirstSurnameFemale'] : null;
        $this->fatherSecondSurnameFemale = (isset($data['fatherSecondSurnameFemale'])) ? $data['fatherSecondSurnameFemale'] : null;
        $this->matherNameMale = (isset($data['matherNameMale'])) ? $data['matherNameMale'] : null;
        $this->matherFirstSurnameMale = (isset($data['matherFirstSurnameMale'])) ? $data['matherFirstSurnameMale'] : null;
        $this->matherSecondSurnameMale = (isset($data['matherSecondSurnameMale'])) ? $data['matherSecondSurnameMale'] : null;
        $this->matherNameFemale = (isset($data['matherNameFemale'])) ? $data['matherNameFemale'] : null;
        $this->matherFirstSurnameFemale = (isset($data['matherFirstSurnameFemale'])) ? $data['matherFirstSurnameFemale'] : null;
        $this->matherSecondSurnameFemale = (isset($data['matherSecondSurnameFemale'])) ? $data['matherSecondSurnameFemale'] : null;
        $this->baptismParishMale = (isset($data['baptismParishMale'])) ? $data['baptismParishMale'] : null;
        $this->baptismParishMaleOthers = (isset($data['baptismParishMaleOthers'])) ? $data['baptismParishMaleOthers'] : null;
        $this->baptismParishFemale = (isset($data['baptismParishFemale'])) ? $data['baptismParishFemale'] : null;
        $this->baptismParishFemaleOthers = (isset($data['baptismParishFemaleOthers'])) ? $data['baptismParishFemaleOthers'] : null;
        $this->godfatherNameOneInformation = (isset($data['godfatherNameOneInformation'])) ? $data['godfatherNameOneInformation'] : null;
        $this->godfatherSurnameOneInformation = (isset($data['godfatherSurnameOneInformation'])) ? $data['godfatherSurnameOneInformation'] : null;
        $this->godfatherNameTwoInformation = (isset($data['godfatherNameTwoInformation'])) ? $data['godfatherNameTwoInformation'] : null;
        $this->godfatherSurnameTwoInformation = (isset($data['godfatherSurnameTwoInformation'])) ? $data['godfatherSurnameTwoInformation'] : null;
        $this->godfatherNameOnePresence = (isset($data['godfatherNameOnePresence'])) ? $data['godfatherNameOnePresence'] : null;        
        $this->godfatherSurnameOnePresence = (isset($data['godfatherSurnameOnePresence'])) ? $data['godfatherSurnameOnePresence'] : null;        
        $this->godfatherNameTwoPresence = (isset($data['godfatherNameTwoPresence'])) ? $data['godfatherNameTwoPresence'] : null;
        $this->godfatherSurnameTwoPresence = (isset($data['godfatherSurnameTwoPresence'])) ? $data['godfatherSurnameTwoPresence'] : null;
        $this->attestPriest = (isset($data['attestPriest'])) ? $data['attestPriest'] : null;
        $this->attestPriestOthers = (isset($data['attestPriestOthers'])) ? $data['attestPriestOthers'] : null;
        $this->observation = (isset($data['observation'])) ? $data['observation'] : null;
        $this->idBookofsacraments = (isset($data['idBookofsacraments'])) ? $data['idBookofsacraments'] : null;
        $this->idPersonMale = (isset($data['idPersonMale'])) ? $data['idPersonMale'] : null;
        $this->idPersonFemale = (isset($data['idPersonFemale'])) ? $data['idPersonFemale'] : null;
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
            
            $inputFilter->add(array(
                'name' => 'page',
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
                            'min' => 1,
                            'max' => 5,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'item',
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
                            'min' => 1,
                            'max' => 6,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'marriagePriest',
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
                            'max' => 60,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'firstNameMale',
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
            
            $inputFilter->add(array(
                'name' => 'firstNameFemale',
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
//                'name' => 'lastNameMale',
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
//                'name' => 'lastNameFemale',
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
                'name' => 'godfatherNameOneInformation',
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
                            'max' => 60,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'godfatherSurnameOneInformation',
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
                            'max' => 60,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'godfatherNameTwoInformation',
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
                            'max' => 60,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'godfatherSurnameTwoInformation',
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
                            'max' => 60,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'godfatherNameOnePresence',
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
                            'max' => 60,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'godfatherSurnameOnePresence',
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
                            'max' => 60,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'godfatherNameTwoPresence',
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
                            'max' => 60,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'godfatherSurnameTwoPresence',
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
                            'max' => 60,
                        ),
                    ),
                ),
            ));
            
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
                            'max' => 60,
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
