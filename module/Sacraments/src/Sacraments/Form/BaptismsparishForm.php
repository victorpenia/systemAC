<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class BaptismsparishForm extends Form {

    protected $dbadapter;
    protected $idParish;

    public function __construct(AdapterInterface $dbAdapter, $idParish) {
        $this->dbadapter = $dbAdapter;
        $this->idParish = $idParish;
        parent::__construct("baptism");        
        
        
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
        /* input text id Person */
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
//        $this->add(array(
//            'type' => 'Zend\Form\Element\Select',
//            'name' => 'page',
//            'attributes' => array(
//                'id' => 'inputPage',
//                'class' => 'form-control'
//            ),
//            'options' => array(
//                'value_options' => array(),
//                'disable_inarray_validator' => true,
//            )
//        ));
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
//        $this->add(array(
//            'type' => 'Zend\Form\Element\Select',
//            'name' => 'item',
//            'attributes' => array(
//                'id' => 'inputItem',
//                'class' => 'form-control'
//            ),
//            'options' => array(
//                'value_options' => array(),
//                'disable_inarray_validator' => true,
//            )
//        ));
        /* input text Baptism Date */
        $this->add(array(
            'name' => 'baptismDate',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '0',
//                'readonly' => 'readonly',
                'data-date-format' => "yyyy-mm-dd",
                'value' => date("Y-m-d"),
                'class' => 'form-control',
                'id' => 'inputBaptismDate',
            ),
        ));
        /* input text Baptism Priest */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'baptismPriest',
            'attributes' => array(
                'id' => 'inputSelectBaptismPriest',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => $this->getOptionsForSelectBaptismPriest(),
            )
        ));
        /* input text baptism priest others */
        $this->add(array(
            'name' => 'baptismPriestOthers',
            'attributes' => array(
                'type' => 'text',
                'style'=> 'display:none',
                'maxlength' => '60',
                'class' => 'form-control',
                'id' => 'inputBaptismPriestOthers',
                'placeholder' => 'Párroco que bautizo'
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
        /* input select born in */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'bornIn',
            'attributes' => array(
                'id' => 'inputSelectBornIn',
                'class' => 'form-control'
            ),
            'options' => array(
//                'empty_option' => 'Seleccione un ciudad',
                'value_options' => array(
                    'Cochabamba' => 'Cochabamba',
                    'Beni' => 'Beni',                    
                    'Chuquisaca' => 'Chuquisaca',                    
                    'La Paz' => 'La Paz',
                    'Oruro' => 'Oruro',
                    'Pando' => 'Pando',
                    'Potosi' => 'Potosi',
                    'Santa Cruz' => 'Santa Cruz',
                    'Tarija' => 'Tarija',
                    'Otros' => 'Otros',
                ),
            )
        ));
        /* input text born in Province */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'bornInProvince',
            'attributes' => array(
                'id' => 'inputBornInProvince',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => array(
                    'Arani' => 'Arani',
                    'Arque' => 'Arque',                    
                    'Ayopaya' => 'Ayopaya',                    
                    'Bolívar' => 'Bolívar',
                    'Campero' => 'Campero',
                    'Capinota' => 'Capinota',
                    'Carrasco' => 'Carrasco',
                    'Cercado' => 'Cercado',
                    'Chapare' => 'Chapare',
                    'Esteban Arce' => 'Esteban Arce',
                    'Germán Jordán' => 'Germán Jordán',
                    'Mizque' => 'Mizque',
                    'Punata' => 'Punata',
                    'Quillacollo' => 'Quillacollo',
                    'Tapacarí' => 'Tapacarí',
                    'Tiraque' => 'Tiraque',
                ),
                'disable_inarray_validator' => true,
            )
        ));
        /* input text born in others */
        $this->add(array(
            'name' => 'bornInOthers',
            'attributes' => array(
                'type' => 'text',
                'style'=> 'display:none',
                'maxlength' => '50',
                'class' => 'form-control',
                'id' => 'inputBornInOthers',
                'placeholder' => 'Pais-Ciudad'
            ),
        ));        
        /* input text Birth date */
        $this->add(array(
            'name' => 'birthDate',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '0',
//                'readonly' => 'readonly',
                'data-date-format' => "yyyy-mm-dd",
                'value' => date("Y-m-d"),
                'class' => 'form-control',
                'id' => 'inputBirthDate',
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
        /* input text mather firstSurname */
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
        /* input text mather secondSurname */
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
        /* input text father firstSurname */
        $this->add(array(
            'name' => 'fatherFirstSurname',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
//                'value' => '',
                'class' => 'form-control',
                'id' => 'inputFatherFirstSurname',
                'placeholder' => 'Apellido paterno del padre'
            ),
        ));
        /* input text father name */
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
        /* input select congregation*/
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'congregation',
            'attributes' => array(
                'id' => 'inputSelectCongregation',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => array(
                    'Esta Parroquia' => 'De esta Parroquia',
                    'Otra Parroquia' => 'De otra Parroquia',
                ),
            )
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
        /* input text godfather name two */
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
        /* input text godfather Surname two */
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
        /* input text oficialia R.C. */
        $this->add(array(
            'name' => 'oficialiaRC',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '15',
                'class' => 'form-control',
                'id' => 'inputOficialiaRC',
                'placeholder' => 'Oficialia R.C.'
            ),
        ));
        /* input text book L.N. */
        $this->add(array(
            'name' => 'bookLN',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '10',
                'class' => 'form-control',
                'id' => 'inputBookLN',
                'placeholder' => 'Libro Nº'
            ),
        ));
        /* input text departure */
        $this->add(array(
            'name' => 'departure',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '10',
                'class' => 'form-control',
                'id' => 'inputDeparture',
                'placeholder' => 'Partida Nº'
            ),
        ));
        /* input text Folio F.S.*/
        $this->add(array(
            'name' => 'folioFS',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '10',
                'class' => 'form-control',
                'id' => 'inputFolioFS',
                'placeholder' => 'Folio Nº'
            ),
        ));
        /* input text priest attest */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'attestPriest',
            'attributes' => array(
                'id' => 'inputSelectAttestPriest',
                'class' => 'form-control'
            ),
            'options' => array(
//                'empty_option' => 'Seleccione un Parroco',
                'value_options' => $this->getOptionsForSelectBaptismPriest(),
//                'disable_inarray_validator' => true,
            )
        ));
        /* input text attest priest others */
        $this->add(array(
            'name' => 'attestPriestOthers',
            'attributes' => array(
                'type' => 'text',
                'style'=> 'display:none',
                'maxlength' => '60',
                'class' => 'form-control',
                'id' => 'inputAttestPriestOthers',
                'placeholder' => 'Doy fe párroco'
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
            'type' => 'Zend\Form\Element\Select',
            'name' => 'idBookofsacraments',
            'attributes' => array(
                'id' => 'inputSelectIdBook',
                'class' => 'form-control'
            ),
            'options' => array(
                'empty_option' => 'Seleccione un libro',
                'value_options' => $this->getOptionsForSelectBookofsacraments(),
//                'disable_inarray_validator' => true,
            )
        ));
        
    }
    
    public function getOptionsForSelectBookofsacraments()
    {
        $dbAdapter = $this->dbadapter;
        $sql = "SELECT id, sacramentName, code, book FROM bookofsacraments where  sacramentName = 'Bautismos' and idParishes =".$this->idParish;
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = array();
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['code']." (".$res['sacramentName']." libro ".$res['book']." )";
        }
        return $selectData;
    }
    
    public function getOptionsForSelectBaptismPriest()
    {
        $dbAdapter = $this->dbadapter;
        $sql = "SELECT id, firstName, lastName, charge FROM users where  idRoles = '3' and idParishes =".$this->idParish;
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
        $sql = "SELECT id, firstName, lastName FROM users where  idRoles = '3' and idParishes =".$this->idParish;
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
//        $selectData = array();
        foreach ($result as $res) {
            $selectData = $res['firstName']." ".$res['lastName'];
        }
        return $selectData;
    }
}
?>

