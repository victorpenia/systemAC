<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Users\Form;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class UserFilter implements InputFilterAwareInterface {

    public $id;
//    public $ci;
    public $firstName;
    public $lastName;
    public $phone;
    public $cellPhone;
    public $address;
    public $email;
    public $idRoles;
    public $idParishes;
    public $status;
    public $charge;
    public $createdDate;
    public $modifiedDate;
//    public $nameRole;
    public $password;    
//    public $confirmPassword;
    public $passwordSalt;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
//        $this->ci = (isset($data['ci'])) ? $data['ci'] : null;
        $this->firstName = (isset($data['firstName'])) ? $data['firstName'] : null;
        $this->lastName = (isset($data['lastName'])) ? $data['lastName'] : null;
        $this->phone = (isset($data['phone'])) ? $data['phone'] : null;
        $this->cellPhone = (isset($data['cellPhone'])) ? $data['cellPhone'] : null;
        $this->address = (isset($data['address'])) ? $data['address'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->idRoles = (isset($data['idRoles'])) ? $data['idRoles'] : null;
        $this->idParishes = (isset($data['idParishes'])) ? $data['idParishes'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->charge = (isset($data['charge'])) ? $data['charge'] : null;
        $this->createdDate = (isset($data['createdDate'])) ? $data['createdDate'] : null;
        $this->modifiedDate = (isset($data['modifiedDate'])) ? $data['modifiedDate'] : null;
//        $this->nameRole = (isset($data['nameRole'])) ? $data['nameRole'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
//        $this->confirmPassword = (isset($data['confirmPassword'])) ? $data['confirmPassword'] : null;
        $this->passwordSalt = (isset($data['passwordSalt'])) ? $data['passwordSalt'] : null;
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
//                'name' => 'idParishes',
//                'required' => false                
//            ));
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

            $inputFilter->add(array(
                'name' => 'email',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'EmailAddress'
                    ),
                ),
            ));
//            $inputFilter->add(array(
//                'name' => 'password',
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
//                            'min' => 5,
//                            'max' => 100,
//                        ),
//                    ),
//                ),
//            ));
//            $inputFilter->add(array(
//                'name' => 'confirmPassword',
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
//                            'min' => 5,
//                            'max' => 100,
//                        ),
//                    ),
//                    array(
//                        'name' => 'Identical',
//                        'options' => array(
//                            'token' => 'password',
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
