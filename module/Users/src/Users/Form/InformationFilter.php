<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Users\Form;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class InformationFilter implements InputFilterAwareInterface {

    public $id;
    public $firstName;
    public $lastName;
    public $phone;
    public $cellPhone;
    public $address;
    public $email;
    public $charge;
    public $createdDate;
    public $modifiedDate;
    
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->firstName = (isset($data['firstName'])) ? $data['firstName'] : null;
        $this->lastName = (isset($data['lastName'])) ? $data['lastName'] : null;
        $this->phone = (isset($data['phone'])) ? $data['phone'] : null;
        $this->cellPhone = (isset($data['cellPhone'])) ? $data['cellPhone'] : null;
        $this->address = (isset($data['address'])) ? $data['address'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->charge = (isset($data['charge'])) ? $data['charge'] : null;
        $this->createdDate = (isset($data['createdDate'])) ? $data['createdDate'] : null;
        $this->modifiedDate = (isset($data['modifiedDate'])) ? $data['modifiedDate'] : null;
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

            $inputFilter->add(array(
                'name' => 'lastName',
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
//                'name' => 'address',
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
//                            'max' => 150,
//                        ),
//                    ),
//                ),
//            ));

//            $inputFilter->add(array(
//                'name' => 'email',
//                'required' => true,
//                'validators' => array(
//                    array(
//                        'name' => 'EmailAddress'
//                    ),
//                ),
//            ));
            
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

}

?>
