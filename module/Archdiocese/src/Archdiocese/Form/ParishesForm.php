<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Archdiocese\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class ParishesForm extends Form {

    protected $dbadapter;

    public function __construct(AdapterInterface $dbAdapter) {
        $this->dbadapter = $dbAdapter;
        parent::__construct("parishes");

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
        /* input text first parish Name */
        $this->add(array(
            'name' => 'parishName',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '50',
                'class' => 'form-control',
                'id' => 'inputName',
                'placeholder' => 'Parroquia'
            ),
        ));
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
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'idVicarious',
            'attributes' => array(
                'id' => 'inputSelectIdvicarious',
                'class' => 'form-control'
            ),
            'options' => array(
                'empty_option' => 'Seleccione una vicaría',
                'value_options' => $this->getOptionsForSelectVicarious(),
            )
        ));
    }    
    public function getOptionsForSelectVicarious()
    {
        $dbAdapter = $this->dbadapter;
        $sql = 'SELECT id, vicariousName FROM vicarious order by vicariousName';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = array();
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['vicariousName'];
        }
        return $selectData;
    }
}
?>

