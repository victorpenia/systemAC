<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Archdiocese\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class VicariousForm extends Form {

    protected $dbadapter;

    public function __construct(AdapterInterface $dbAdapter) {
        $this->dbadapter = $dbAdapter;
        parent::__construct("vicarious");

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
        /* input text id vicarious */
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'inputId'
            ),
        ));
        /* input text vicarious name */
        $this->add(array(
            'name' => 'vicariousName',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '50',
                'class' => 'form-control',
                'id' => 'inputVicariousName',
                'placeholder' => 'Vicaría'
            ),
        ));
        /* input text vicarious location */
        $this->add(array(
            'name' => 'location',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '50',
                'class' => 'form-control',
                'id' => 'inputLocation',
                'placeholder' => 'Ubicación'
            ),
        ));       
    }
}
?>

