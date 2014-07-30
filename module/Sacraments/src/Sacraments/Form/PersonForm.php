<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class PersonForm extends Form {

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
        /* input text first Book */
        $this->add(array(
            'name' => 'book',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '15',
                'class' => 'form-control',
                'id' => 'inputBook',
                'placeholder' => 'Libro'
            ),
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
                'value_options' => array(
                    'Bautismos' => 'Bautismo',
                    'Matrimonios' => 'Matrimonio',
                    'Confirmaciones' => 'Confirmación',
                ),
            )
        ));
        /* input select type book */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'typeBook',
            'attributes' => array(
                'id' => 'inputSelectTypeBook',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => array(
                    'Original' => 'Original',
                    'Copia' => 'Copia'                    
                ),
            )
        ));
        /* input select type book */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'statusBook',
            'attributes' => array(
                'id' => 'inputSelectStatusBook',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => array(
                    'Libro' => 'Libro',
                    'Cuaderno' => 'Cuaderno',
                    'Cuadernillo' => 'Cuadernillo'
                ),
            )
        ));
        /* input select idParish */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'idParishes',
            'attributes' => array(
                'id' => 'inputSelectIdvicarious',
                'class' => 'form-control'
            ),
            'options' => array(
                'empty_option' => 'Seleccione un Parroquia',
                'value_options' => $this->getOptionsForSelectParishes(),
            )
        ));
    }
    /* query database parishes */
    public function getOptionsForSelectParishes()
    {
        $dbAdapter = $this->dbadapter;
        $sql = 'SELECT id, parishName FROM parishes order by parishName';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = array();
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['parishName'];
        }
        return $selectData;
    }
}
?>

