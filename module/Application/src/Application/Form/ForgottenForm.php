<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form;

use Zend\Form\Form;

class ForgottenForm extends Form {

    public function __construct($name = null) {

        parent::__construct($name);
        /* button send */
        $this->add(array(
            'name' => 'send',
            'attributes' => array(
                'type' => 'submit',
                'class' => 'btn btn-lg btn-primary btn-block',
                'value' => 'Enviar'
            ),
        ));
        /* input text last email */
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type' => 'email',
                'maxlength' => '50',
                'class' => 'form-control',
                'id' => 'inputEmail',
                'placeholder' => 'Email'
            ),
        ));        
    }
}
?>

