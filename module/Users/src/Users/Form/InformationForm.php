<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Users\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class InformationForm extends Form {

    protected $dbAdapter;

    public function __construct(AdapterInterface $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
        parent::__construct("users");

        /* button Modify*/
        $this->add(array(
            'name' => 'modify',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Guardar Cambios',
                'class' => 'btn btn-primary'
            ),
        ));
        /* input text id user */
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'inputId'
            ),
        ));
        /* input text first name */
        $this->add(array(
            'name' => 'firstName',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFirstName',
                'placeholder' => 'Nombres'
            ),
        ));
        /* input text last name */
        $this->add(array(
            'name' => 'lastName',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputLastName',
                'placeholder' => 'Apellidos'
            ),
        ));
        /* input text email */
//        $this->add(array(
//            'name' => 'email',
//            'attributes' => array(
//                'type' => 'email',
//                'class' => 'form-control',
//                'id' => 'inputEmail',
//                'placeholder' => 'Email'
//            ),
//        ));
        /* input text phone */
        $this->add(array(
            'name' => 'phone',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '15',
                'class' => 'form-control',
                'id' => 'inpuPhone',
                'placeholder' => 'Teléfono'
            ),
        ));
        /* input text cell_phone */
        $this->add(array(
            'name' => 'cellPhone',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '15',
                'class' => 'form-control',
                'id' => 'inputCellPhone',
                'placeholder' => 'Celular'
            ),
        ));
        /* input text address */
        $this->add(array(
            'name' => 'address',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '70',
                'class' => 'form-control',
                'id' => 'inputAddress',
                'placeholder' => 'Dirección'
            ),
        ));
        /* input select charge */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'charge',
            'attributes' => array(
                'id' => 'inputSelectProfession',
                'class' => 'form-control'
            ),
            'options' => array(
                //'empty_option' => 'Por favor seleccione un cargo',
                'value_options' => array(
                    'Mons.' => 'Monseñor',
                    'Pbro.' => 'Párroco',
                    'Vicar.' => 'Vicario',
                    'Diac.' => 'Diácono',
                    'Tr.' => 'Otros'
                ),
            )
        ));
    }

}
?>

