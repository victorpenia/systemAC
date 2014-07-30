<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class BooksparishForm extends Form {

    protected $dbadapter;

    public function __construct(AdapterInterface $dbAdapter) {
        $this->dbadapter = $dbAdapter;
        parent::__construct("books");

        /* button register */
        $this->add(array(
            'name' => 'register',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Registrar',
                'class' => 'btn btn-primary'
            ),
        ));
        /* button Modify */
        $this->add(array(
            'name' => 'modify',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Guardar Cambios',
                'class' => 'btn btn-primary'
            ),
        ));
        /* input text id parish */
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'inputId'
            ),
        ));
        /* input text startItem Book */
        $this->add(array(
            'name' => 'startItem',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '6',
                'class' => 'form-control',
                'id' => 'inputStartItem',
                'placeholder' => 'Partida inicial'
            ),
        ));
        /* input text registration Date */
//        $this->add(array(
//            'name' => 'registrationDate',
//            'attributes' => array(
//                'type' => 'text',
//                'maxlength' => '10',
//                'value' => date("Y-m-d"),
//                'class' => 'form-control',
//                'id' => 'inputRegistrationDate',
//                'placeholder' => 'Fecha registro'
//            ),
//        ));
        /* input text book */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'book',
            'attributes' => array(
                'id' => 'inputBook',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => array(),
                'disable_inarray_validator' => true,
            )
        ));
        /* input select Sacrament Name */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'sacramentName',
            'attributes' => array(
                'id' => 'inputSelectSacrament',
                'class' => 'form-control'
            ),
            'options' => array(
                'empty_option' => 'Seleccione un libro',
                'value_options' => array(
                    'Bautismos' => 'Bautismo',
                    'Matrimonios' => 'Matrimonio',
                    'Confirmaciones' => 'Confirmación',
                ),
            )
        ));
    }
}
?>

