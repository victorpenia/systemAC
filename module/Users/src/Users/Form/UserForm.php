<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Users\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class UserForm extends Form {

    protected $dbAdapter;

    public function __construct(AdapterInterface $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
        parent::__construct("users");

        /* button register */
        $this->add(array(
            'name' => 'register',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Registrar',
                'class' => 'btn btn-primary'
            ),
        ));
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
        /* input text CI */
//        $this->add(array(
//            'name' => 'ci',
//            'attributes' => array(
//                'type' => 'text',
//                'class' => 'form-control',
//                'id' => 'inputCi',
//                'placeholder' => 'CI/Rua'
//            ),
//        ));
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
        /* input text phone */
        $this->add(array(
            'name' => 'phone',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '10',
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
                'maxlength' => '10',
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
        /* input text Password */
//        $this->add(array(
//            'name' => 'password',
//            'attributes' => array(
//                'type' => 'password',
//                'class' => 'form-control',
//                'id' => 'inputPassword',
//                'placeholder' => 'Contraseña'
//            ),
//        ));
//        /* input text confirm password */
//        $this->add(array(
//            'name' => 'confirmPassword',
//            'attributes' => array(
//                'type' => 'password',
//                'class' => 'form-control',
//                'id' => 'inputConfirmPassword',
//                'placeholder' => 'Confirmar contraseña'
//            ),
//        ));
        /* input select role */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'idRoles',
            'attributes' => array(
                'id' => 'inputSelectRole',
                'class' => 'form-control'                
            ),
            'options' => array(
//                'empty_option' => 'Por favor seleccione un rol',
                'value_options' => $this->getOptionsForSelectRole(),
            )
        ));
        /* input select state */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'status',
            'attributes' => array(
                'id' => 'inputSelectState',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => array(
                    'Activo' => 'Activo',
                    'Bloqueado' => 'Bloqueado',
                    'Inactivo' => 'Inactivo'
                ),
            )
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
                    'Sem. Teol.' => 'Sem. Teólogo',
                    'Sem. Diac.' => 'Sem. Diácono',
                    'Tr.' => 'Otros'
                ),
            )
        ));
        /* input select parish */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'idParishes',
            'attributes' => array(
                'id' => 'inputSelectParish',
                'class' => 'form-control',
//                'disabled' => 'disabled'
            ),
            'options' => array(
//                'empty_option' => 'Por favor seleccione un cargo',
                'value_options' => $this->getOptionsForSelectParishes()
            )
        ));
    }

    public function getOptionsForSelectRole() {
        $dbAdapter = $this->dbAdapter;
        $sql = 'SELECT id, nameRole FROM roles';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = array();
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['nameRole'];
        }
        return $selectData;
    }
    
    public function getOptionsForSelectParishes() {
        $dbAdapter = $this->dbAdapter;
        $sql = 'SELECT id, parishName FROM parishes order by parishName';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = array();
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['parishName'];
        }
        return $selectData;
    }
    
//    public function getOptionsForSelectVicarious()
//    {
//        $dbAdapter = $this->dbadapter;
//        $sql = 'SELECT id, vicariousName FROM vicarious';
//        $statement = $dbAdapter->query($sql);
//        $result = $statement->execute();
//        $selectData = array();
//        foreach ($result as $res) {
//            $selectData[$res['id']] = $res['vicariousName'];
//        }
//        return $selectData;
//    }

}
?>

