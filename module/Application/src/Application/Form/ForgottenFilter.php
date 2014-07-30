<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ForgottenFilter implements InputFilterAwareInterface {

    public $email;
    public $password;
    public $passwordSalt;
    public $state;
    
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
        $this->passwordSalt = (isset($data['passwordSalt'])) ? $data['passwordSalt'] : null;
        $this->state = (isset($data['state'])) ? $data['state'] : null;
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
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                        'name' => 'email',
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
                                    'max' => 75,
                                ),
                            ),
                        ),
            )));
//            $inputFilter->add($factory->createInput(array(
//                        'name' => 'password',
//                        'required' => true,
//                        'filters' => array(
//                            array('name' => 'StripTags'),
//                            array('name' => 'StringTrim'),
//                        ),
//                        'validators' => array(
//                            array(
//                                'name' => 'StringLength',
//                                'options' => array(
//                                    'encoding' => 'UTF-8',
//                                    'min' => 1,
//                                    'max' => 75,
//                                ),
//                            ),
//                        ),
//            )));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

}

?>
