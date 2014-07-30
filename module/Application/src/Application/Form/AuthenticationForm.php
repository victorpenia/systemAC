<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form;

use Zend\Form\Form;

class AuthenticationForm extends Form {

    public function __construct($name = null) {

        parent::__construct($name);
        /* button send */
        $this->add(array(
            'name' => 'login',
            'attributes' => array(
                'type' => 'submit',
                'class' => 'btn btn-lg btn-primary btn-block',
                'value' => 'Entrar'
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
        /* input text Password */
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'maxlength' => '50',
                'class' => 'form-control',
                'id' => 'inputPassword',
                'placeholder' => 'Contraseña'
            ),
        ));
        /* checkbox remenber me*/
        $this->add(array(
            'name' => 'rememberme',
            'attributes' => array(
                'type' => 'checkbox',
                'value' => '1',
            ),
        ));
    }
}
?>

