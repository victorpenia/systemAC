<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class ConfirmationsparishEditForm extends Form {

    protected $dbadapter;
    protected $idParish;
    protected $attestPriest;
    protected $baptismParish;

    public function __construct(AdapterInterface $dbAdapter, $idParish, $attestPriest, $baptismParish) {
        $this->dbadapter = $dbAdapter;
        $this->idParish = $idParish;
        $this->attestPriest = $attestPriest;
        $this->baptismParish = $baptismParish;
        
        parent::__construct("confirmations");        
        
        
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
        /* input text id Baptism */
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'inputId'
            ),
        ));
        /* input text id person */
        $this->add(array(
            'name' => 'idPerson',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'inputIdPerson'
            ),
        ));
        /* input text Page */
        $this->add(array(
            'name' => 'page',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '0',
                'autocomplete' => 'off',
                'class' => 'form-control',
                'id' => 'inputPage',
                'placeholder' => 'Página'
            ),
        ));
        /* input text Item */
        $this->add(array(
            'name' => 'item',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '0',
                'class' => 'form-control',
                'id' => 'inputItem',
                'placeholder' => 'Partida'
            ),
        ));
        /* input text Confirmation Date */
        $this->add(array(
            'name' => 'confirmationDate',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '0',
                'data-date-format' => "yyyy-mm-dd",
                'value' => date("Y-m-d"),
                'class' => 'form-control',
                'id' => 'inputConfirmationDate',
//                'placeholder' => 'Fecha confirmación'
            ),
        )); 
        /* input text ci Person */
        $this->add(array(
            'name' => 'ci',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '15',
                'class' => 'form-control',
                'id' => 'inputCI',
                'placeholder' => 'CI/Rua'
            ),
        ));
        /* input text first name Person */
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
        /* input text first surname person */
        $this->add(array(
            'name' => 'firstSurname',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFirstSurname',
                'placeholder' => 'Apellido paterno'
            ),
        ));
        /* input text second surname person */
        $this->add(array(
            'name' => 'secondSurname',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputSecondSurname',
                'placeholder' => 'Apellido materno'
            ),
        ));  
        /* input text Birth date */
        $this->add(array(
            'name' => 'birthDate',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '0',
                'data-date-format' => "yyyy-mm-dd",
                'value' => date("Y-m-d"),
                'class' => 'form-control',
                'id' => 'inputBirthDate',
            ),
        ));
        /* input text baptism parish*/        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'baptismParish',
            'attributes' => array(
                'id' => 'inputBaptismParish',
                'class' => 'form-control'
            ),
            'options' => array(
//                'empty_option' => 'Seleccione un parroquia',
                'value_options' => $this->getOptionsForSelectParishes(),
            )
        ));
        /* input text baptism parish  others */
        $this->add(array(
            'name' => 'baptismParishOthers',
            'attributes' => array(
                'type' => 'text',
                'style'=> ($this->baptismParish == 'Otros') ? '' : 'display:none',
                'maxlength' => '60',
                'class' => 'form-control',
                'id' => 'inputBaptismParishOthers',
                'placeholder' => 'Parroquia'
            ),
        ));
        /* input text mather name */
        $this->add(array(
            'name' => 'matherName',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputMatherName',
                'placeholder' => 'Nombres de la madre'
            ),
        ));
        /* input text mather first surname */
        $this->add(array(
            'name' => 'matherFirstSurname',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputMatherFirstSurname',
                'placeholder' => 'Apellido paterno de la madre'
            ),
        ));
        /* input text mather second surname */
        $this->add(array(
            'name' => 'matherSecondSurname',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputMatherSecondSurname',
                'placeholder' => 'Apellido materno de la madre'
            ),
        ));
        /* input text father name */
        $this->add(array(
            'name' => 'fatherName',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFatherName',
                'placeholder' => 'Nombres del padre'
            ),
        ));
        /* input text father first surname */
        $this->add(array(
            'name' => 'fatherFirstSurname',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFatherFirstSurname',
                'placeholder' => 'Apellido paterno del padre'
            ),
        ));
        /* input text father second surname */
        $this->add(array(
            'name' => 'fatherSecondSurname',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFatherSecondSurname',
                'placeholder' => 'Apellido materno del padre'
            ),
        ));
        /* input text godfather name one */
        $this->add(array(
            'name' => 'godfatherNameOne',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputGodfatherNameOne',
                'placeholder' => 'Nombres del padrino(a)'
            ),
        ));
        /* input text godfather surname one */
        $this->add(array(
            'name' => 'godfatherSurnameOne',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputGodfatherSurnameOne',
                'placeholder' => 'Apellidos del padrino(a)'
            ),
        ));
        /* input text godfather Name two */
        $this->add(array(
            'name' => 'godfatherNameTwo',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputGodfatherNameTwo',
                'placeholder' => 'Nombres del padrino(a)'
            ),
        ));
        /* input text godfather surname two */
        $this->add(array(
            'name' => 'godfatherSurnameTwo',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputGodfatherSurnameTwo',
                'placeholder' => 'Apellidos del padrino(a)'
            ),
        ));
        /* input text priest  attest */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'attestPriest',
            'attributes' => array(
                'id' => 'inputSelectAttestPriest',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => $this->getOptionsForSelectConfirmationPriest(),
            )
        ));
        /* input text attest priest others */
        $this->add(array(
            'name' => 'attestPriestOthers',
            'attributes' => array(
                'type' => 'text',
                'style'=> ($this->attestPriest == 'Otros') ? '' : 'display:none',
                'maxlength' => '60',
                'class' => 'form-control',
                'id' => 'inputAttestPriestOthers',
                'placeholder' => 'Obispo o confirmante'
            ),
        )); 
        /* input text observation */
        $this->add(array(
            'name' => 'observation',
            'attributes' => array(
                'type' => 'Zend\Form\Element\Textarea',
                'maxlength' => '250',
                'class' => 'form-control',
                'id' => 'inputObservation'
            ),
        ));
        /* input combobox idBook */
        $this->add(array(
            'name' => 'idBookofsacraments',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '0',
                'autocomplete' => 'off',
                'class' => 'form-control',
                'id' => 'inputIdBookofsacraments',
            ),
        ));
//        $this->add(array(
//            'type' => 'Zend\Form\Element\Select',
//            'name' => 'idBookofsacraments',
//            'attributes' => array(
//                'id' => 'inputSelectIdBook',
//                'class' => 'form-control'
//            ),
//            'options' => array(
//                'empty_option' => 'Seleccione un libro',
//                'value_options' => $this->getOptionsForSelectBookofsacraments(),
////                'disable_inarray_validator' => true,
//            )
//        ));
        
    }
    
    public function getOptionsForSelectBookofsacraments()
    {
        $dbAdapter = $this->dbadapter;
        $sql = "SELECT id, sacramentName, code, book FROM bookofsacraments where  sacramentName = 'Confirmaciones' and idParishes =".$this->idParish;
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = array();
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['code']." (".$res['sacramentName']." libro ".$res['book']." )";
        }
        return $selectData;
    }
    
    public function getOptionsForSelectConfirmationPriest()
    {
        $dbAdapter = $this->dbadapter;
        $sql = "SELECT id, firstName, lastName, charge FROM users where charge = 'Mons.'";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = array();
        foreach ($result as $res) {
            $selectData[$res['charge']." ".$res['firstName']." ".$res['lastName']] = $res['charge']." ".$res['firstName']." ".$res['lastName'];
        }
        $selectData["Otros"] = "Otros";
        return $selectData;
    }
    
    public function getOptionsForSelectParishes()
    {
        $dbAdapter = $this->dbadapter;
        $sql = 'SELECT id, parishName FROM parishes order by parishName';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = array();
        foreach ($result as $res) {
            $selectData[$res['parishName']] = $res['parishName'];
        }
        $selectData["Otros"] = "Otros";
        return $selectData;
    }
}
?>
