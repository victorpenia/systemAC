<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class MarriagesEditForm extends Form {

    protected $dbadapter;
    protected $marriagePriest;
    protected $attestPriets;
    protected $baptismParishMale;
    protected $baptismParishFemale;

    public function __construct(AdapterInterface $dbAdapter, $marriagePriest, $attestPriest, $baptismParishMale, $baptismParishFemale) {
        $this->dbadapter = $dbAdapter;
        $this->marriagePriest = $marriagePriest;
        $this->attestPriets = $attestPriest;
        $this->baptismParishMale = $baptismParishMale;
        $this->baptismParishFemale = $baptismParishFemale;
        parent::__construct("marriages");        
        
        
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
            'name' => 'idPersonMale',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'inputIdPersonMale'
            ),
        ));
        /* input text id Person */
        $this->add(array(
            'name' => 'idPersonFemale',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'inputIdPersonFemale'
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
        /* input text Baptism Date */
        $this->add(array(
            'name' => 'marriageDate',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '0',
                'autocomplete' => 'off',
                'data-date-format' => "yyyy-mm-dd",
                'value' => date("Y-m-d"),
                'class' => 'form-control',
                'id' => 'inputMarriageDate',
//                'placeholder' => 'Fecha matrimonio'
            ),
        ));
        /* input text marriage Priest */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'marriagePriest',
            'attributes' => array(
                'id' => 'inputSelectMarriagePriest',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => $this->getOptionsForSelectMarriagePriest(),
            )
        ));
        /* input text marriage priest others */
        $this->add(array(
            'name' => 'marriagePriestOthers',
            'attributes' => array(
                'type' => 'text',
                'style'=> ($this->marriagePriest == 'Otros') ? '' : 'display:none',
                'maxlength' => '60',
                'class' => 'form-control',
                'id' => 'inputMarriagePriestOthers',
                'placeholder' => 'Párroco que bendijo el matrimonio'
            ),
        )); 
        /* input text ci Person Male*/
        $this->add(array(
            'name' => 'ciMale',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '15',
                'class' => 'form-control',
                'id' => 'inputCIMale',
                'placeholder' => 'CI/Rua'
            ),
        ));
        /* input text ci Person Female*/
        $this->add(array(
            'name' => 'ciFemale',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '15',
                'class' => 'form-control',
                'id' => 'inputCIFemale',
                'placeholder' => 'CI/Rua'
            ),
        ));
        /* input text first name Person Male*/
        $this->add(array(
            'name' => 'firstNameMale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFirstNameMale',
                'placeholder' => 'Nombres'
            ),
        ));
        /* input text first name Person Female*/
        $this->add(array(
            'name' => 'firstNameFemale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFirstNameFemale',
                'placeholder' => 'Nombres'
            ),
        ));
        /* input text last name person Male*/
        $this->add(array(
            'name' => 'firstSurnameMale',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFirstSurnameMale',
                'placeholder' => 'Apellido paterno'
            ),
        ));
        /* input text last name person Male*/
        $this->add(array(
            'name' => 'secondSurnameMale',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputSecondSurnameMale',
                'placeholder' => 'Apellido materno'
            ),
        ));
        /* input text last name person Female*/
        $this->add(array(
            'name' => 'firstSurnameFemale',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFirstSurnameFemale',
                'placeholder' => 'Apellido paterno'
            ),
        ));
        /* input text last name person Female*/
        $this->add(array(
            'name' => 'secondSurnameFemale',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputSecondSurnameFemale',
                'placeholder' => 'Apellido materno'
            ),
        ));        
        
        /* input text Birth date Male */
        $this->add(array(
            'name' => 'birthDateMale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '0',
                'autocomplete' => 'off',
                'data-date-format' => "yyyy-mm-dd",
                'value' => date("Y-m-d"),
                'class' => 'form-control',
                'id' => 'inputBirthDateMale',
            ),
        ));
        /* input text Birth date Female*/
        $this->add(array(
            'name' => 'birthDateFemale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '0',
                'autocomplete' => 'off',
                'data-date-format' => "yyyy-mm-dd",
                'value' => date("Y-m-d"),
                'class' => 'form-control',
                'id' => 'inputBirthDateFemale',
            ),
        ));
        /* input text marital status male*/
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'maritalStatusMale',
            'attributes' => array(
                'id' => 'inputSelectMaritalStatusMale',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => array(
                    'Soltero' => 'Soltero',
                    'Viudo' => 'Viudo',
                ),
            )
        ));
        /* input text marital status Female*/
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'maritalStatusFemale',
            'attributes' => array(
                'id' => 'inputSelectMaritalStatusFemale',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => array(
                    'Soltera' => 'Soltera',
                    'Viuda' => 'Viuda',
                ),
            )
        ));
        /* input text baptism parish Male*/
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'baptismParishMale',
            'attributes' => array(
                'id' => 'inputBaptismParishMale',
                'class' => 'form-control'
            ),
            'options' => array(
//                'empty_option' => 'Seleccione un parroquia',
                'value_options' => $this->getOptionsForSelectParishes(),
            )
        ));   
        /* input text baptism parish Male others */
        $this->add(array(
            'name' => 'baptismParishMaleOthers',
            'attributes' => array(
                'type' => 'text',
                'style'=> ($this->baptismParishMale == 'Otros') ? '' : 'display:none',
                'maxlength' => '60',
                'class' => 'form-control',
                'id' => 'inputBaptismParishMaleOthers',
                'placeholder' => 'Parroquia'
            ),
        )); 
        /* input text baptism parish Female*/
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'baptismParishFemale',
            'attributes' => array(
                'id' => 'inputBaptismParishFemale',
                'class' => 'form-control'
            ),
            'options' => array(
//                'empty_option' => 'Seleccione un parroquia',
                'value_options' => $this->getOptionsForSelectParishes(),
            )
        ));
        /* input text baptism parish Female others */
        $this->add(array(
            'name' => 'baptismParishFemaleOthers',
            'attributes' => array(
                'type' => 'text',
                'style'=> ($this->baptismParishFemale == 'Otros') ? '' : 'display:none',
                'maxlength' => '60',
                'class' => 'form-control',
                'id' => 'inputBaptismParishFemaleOthers',
                'placeholder' => 'Parroquia'
            ),
        ));
        /* input text mather name Male*/
        $this->add(array(
            'name' => 'matherNameMale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputMatherNameMale',
                'placeholder' => 'Nombre de la madre'
            ),
        ));
        /* input text mather first surname Male*/
        $this->add(array(
            'name' => 'matherFirstSurnameMale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputMatherFirstSurnameMale',
                'placeholder' => 'Apellido paterno de la madre'
            ),
        ));
        /* input text mather second surname Male*/
        $this->add(array(
            'name' => 'matherSecondSurnameMale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputMatherSecondSurnameMale',
                'placeholder' => 'Apellido materno de la madre'
            ),
        ));
        /* input text mather name Female*/
        $this->add(array(
            'name' => 'matherNameFemale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputMatherNameFemale',
                'placeholder' => 'Nombre de la madre'
            ),
        ));
        /* input text mather first surname Female*/
        $this->add(array(
            'name' => 'matherFirstSurnameFemale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputMatherFirstSurnameFemale',
                'placeholder' => 'Apellido paterno de la madre'
            ),
        ));
        /* input text mather second surname Female*/
        $this->add(array(
            'name' => 'matherSecondSurnameFemale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputMatherSecondSurnameFemale',
                'placeholder' => 'Apellido materno de la madre'
            ),
        ));
        /* input text father name Male */
        $this->add(array(
            'name' => 'fatherNameMale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFatherNameMale',
                'placeholder' => 'Nombre del padre'
            ),
        ));
        /* input text father first surname Male */
        $this->add(array(
            'name' => 'fatherFirstSurnameMale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFatherFirstSurnameMale',
                'placeholder' => 'Apellido paterno del padre'
            ),
        ));
        /* input text father second surname Male */
        $this->add(array(
            'name' => 'fatherSecondSurnameMale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFatherSecondSurnameMale',
                'placeholder' => 'Apellido materno del padre'
            ),
        ));
        /* input text father name Female*/
        $this->add(array(
            'name' => 'fatherNameFemale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFatherNameFemale',
                'placeholder' => 'Nombre del padre'
            ),
        )); 
        /* input text father first surname Female*/
        $this->add(array(
            'name' => 'fatherFirstSurnameFemale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFatherFirstSurnameFemale',
                'placeholder' => 'Apellido paterno del padre'
            ),
        )); 
        /* input text father second surname Female*/
        $this->add(array(
            'name' => 'fatherSecondSurnameFemale',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputFatherSecondSurnameFemale',
                'placeholder' => 'Apellido materno del padre'
            ),
        )); 
        /* input text godfather name one information*/
        $this->add(array(
            'name' => 'godfatherNameOneInformation',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputGodfatherNameOneInformation',
                'placeholder' => 'Testigos de información'
            ),
        ));
        /* input text godfather surname one information*/
        $this->add(array(
            'name' => 'godfatherSurnameOneInformation',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputGodfatherSurnameOneInformation',
                'placeholder' => 'Testigos de información'
            ),
        ));
        /* input text godfather name two information*/
        $this->add(array(
            'name' => 'godfatherNameTwoInformation',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputGodfatherNameTwoInformation',
                'placeholder' => 'Testigos de información'
            ),
        ));
        /* input text godfather surname two information*/
        $this->add(array(
            'name' => 'godfatherSurnameTwoInformation',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputGodfatherSurnameTwoInformation',
                'placeholder' => 'Testigos de información'
            ),
        ));
        /* input text godfather name one precence*/
        $this->add(array(
            'name' => 'godfatherNameOnePresence',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputGodfatherNameOnePresence',
                'placeholder' => 'Testigos presenciales'
            ),
        ));
        /* input text godfather surname one precence*/
        $this->add(array(
            'name' => 'godfatherSurnameOnePresence',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputGodfatherSurnameOnePresence',
                'placeholder' => 'Testigos presenciales'
            ),
        ));
        /* input text godfather name two presence*/
        $this->add(array(
            'name' => 'godfatherNameTwoPresence',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputGodfatherNameTwoPresence',
                'placeholder' => 'Testigos presenciales'
            ),
        ));
        /* input text godfather surname two presence*/
        $this->add(array(
            'name' => 'godfatherSurnameTwoPresence',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '30',
                'class' => 'form-control',
                'id' => 'inputGodfatherSurnameTwoPresence',
                'placeholder' => 'Testigos presenciales'
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
                'value_options' => $this->getOptionsForSelectMarriagePriest(),
            )
        ));
        /* input text attest priest others */
        $this->add(array(
            'name' => 'attestPriestOthers',
            'attributes' => array(
                'type' => 'text',
                'style'=> ($this->attestPriets == 'Otros') ? '' : 'display:none',
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
            'name' => 'idBookofsacraments',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '0',
                'autocomplete' => 'off',
                'class' => 'form-control',
                'id' => 'inputIdBookofsacraments',
                'placeholder' => 'Página'
            ),
        ));
        $this->add(array(
            'name' => 'parishName',
            'attributes' => array(
                'type' => 'text',
                'maxlength' => '0',
                'autocomplete' => 'off',
                'class' => 'form-control',
                'id' => 'inputParishName',
            ),
        ));
        /* input comboBox idParishes */
        $this->add(array(
            'name' => 'idParish',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'inputIdParish'
            ),
        ));       
    }    
    
    public function getOptionsForSelectParishesMarriage()
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
    
    public function getOptionsForSelectBookofsacraments()
    {
        $dbAdapter = $this->dbadapter;
        $sql = "SELECT id, sacramentName, code, book FROM bookofsacraments where  sacramentName = 'Matrimonios'";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = array();
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['code']." (".$res['sacramentName']." libro ".$res['book']." )";
        }
        return $selectData;
    }
    
    public function getOptionsForSelectMarriagePriest()
    {
        $dbAdapter = $this->dbadapter;
        $sql = "SELECT id, firstName, lastName, charge FROM users where  idRoles = '3'";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = array();
        foreach ($result as $res) {
            $selectData[$res['charge']." ".$res['firstName']." ".$res['lastName']] = $res['charge']." ".$res['firstName']." ".$res['lastName'];
        }
        $selectData["Otros"] = "Otros";
        return $selectData;
    }
}
?>

