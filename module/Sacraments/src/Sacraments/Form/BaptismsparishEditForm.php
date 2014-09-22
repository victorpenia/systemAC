<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sacraments\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class BaptismsparishEditForm extends Form {

    protected $dbadapter;
    protected $idParish;
    protected $bornIn;
    protected $baptismPriest;
    protected $attestPriest;

    public function __construct(AdapterInterface $dbAdapter, $idParish, $bornIn, $baptismPriest, $attestPriest) {
        $this->dbadapter = $dbAdapter;
        $this->idParish = $idParish;
        $this->bornIn = $bornIn;
        $this->baptismPriest = $baptismPriest;
        $this->attestPriest = $attestPriest;
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
            'name' => 'baptismDate',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '0',
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
                'style'=> ($this->baptismPriest == 'Otros') ? '' : 'display:none',
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
                'style'=> ($this->bornIn == 'Otros') ? 'display:none' : '',
                'class' => 'form-control'
            ),            
            'options' => array( 
                'value_options' => $this->getProvinceByCity($this->bornIn),
                'disable_inarray_validator' => true,
            )
        ));
        /* input text born in others */
        $this->add(array(
            'name' => 'bornInOthers',
            'attributes' => array(
                'type' => 'text',
                'style'=> ($this->bornIn == 'Otros') ? '' : 'display:none',
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
                'style'=> ($this->baptismPriest == 'Otros') ? '' : 'display:none',
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
    
    public function getProvinceByCity($bornIn){
        $selectData = array();
        if($bornIn == 'Beni'){
            $selectData["Antonio Vaca Díez"] = "Antonio Vaca Díez";
            $selectData["Cercado"] = "Cercado";
            $selectData["General José Ballivián"] = "General José Ballivián";
            $selectData["Iténez"] = "Iténez";
            $selectData["Mamoré"] = "Mamoré";
            $selectData["Marbán"] = "Marbán";
            $selectData["Moxos"] = "Moxos";
            $selectData["Yacuma"] = "Yacuma";
        }
        if($bornIn == 'Chuquisaca'){
            $selectData["Belisario Boeto"] = "Belisario Boeto";
            $selectData["Hernando Siles"] = "Hernando Siles";
            $selectData["Jaime Zudáñez"] = "Jaime Zudáñez";
            $selectData["Juana Azurduy"] = "Juana Azurduy";
            $selectData["Luis Calvo"] = "Luis Calvo";
            $selectData["Nor Cinti"] = "Nor Cinti";
            $selectData["Oropeza"] = "Oropeza";
            $selectData["Sud Cinti"] = "Sud Cinti";
            $selectData["Tomina"] = "Tomina";
            $selectData["Yamparáez"] = "Yamparáez";
        }
        if($bornIn == 'Cochabamba'){
            $selectData["Arani"] = "Arani";
            $selectData["Arque"] = "Arque";
            $selectData["Ayopaya"] = "Ayopaya";
            $selectData["Bolívar"] = "Bolívar";
            $selectData["Campero"] = "Campero";
            $selectData["Capinota"] = "Capinota";
            $selectData["Carrasco"] = "Carrasco";
            $selectData["Cercado"] = "Cercado";
            $selectData["Chapare"] = "Chapare";
            $selectData["Esteban Arce"] = "Esteban Arce";
            $selectData["Germán Jordán"] = "Germán Jordán";
            $selectData["Mizque"] = "Mizque";
            $selectData["Punata"] = "Punata";
            $selectData["Quillacollo"] = "Quillacollo";
            $selectData["Tapacarí"] = "Tapacarí";
            $selectData["Tiraque"] = "Tiraque";
        }
        if($bornIn == 'La Paz'){
            $selectData["Abel Iturralde"] = "Abel Iturralde";
            $selectData["Aroma"] = "Aroma";
            $selectData["Bautista Saavedra"] = "Bautista Saavedra";
            $selectData["Caranavi"] = "Caranavi";
            $selectData["Eliodoro Camacho"] = "Eliodoro Camacho";
            $selectData["Franz Tamayo"] = "Franz Tamayo";
            $selectData["General José Manuel Pando"] = "General José Manuel Pando";
            $selectData["Gualberto Villaroel"] = "Gualberto Villaroel";
            $selectData["Ingavi"] = "Ingavi";
            $selectData["Inquisivi"] = "Inquisivi";
            $selectData["José Ramón Loayza"] = "José Ramón Loayza";
            $selectData["Larecaja"] = "Larecaja";
            $selectData["Los Andes"] = "Los Andes";
            $selectData["Manco Kapac"] = "Manco Kapac";
            $selectData["Muñecas"] = "Muñecas";
            $selectData["Nor Yungas"] = "Nor Yungas";
            $selectData["Omasuyos"] = "Omasuyos";
            $selectData["Pacajes"] = "Pacajes";
            $selectData["Pedro Domingo Murillo"] = "Pedro Domingo Murillo";
            $selectData["Sud Yungas"] = "Sud Yungas";         
        }
        if($bornIn == 'Oruro'){
            $selectData["Carangas"] = "Carangas";
            $selectData["Cercado"] = "Cercado";
            $selectData["Eduardo Avaroa"] = "Eduardo Avaroa";
            $selectData["Ladislao Cabrera"] = "Ladislao Cabrera";
            $selectData["Litoral"] = "Litoral";
            $selectData["Mejillones"] = "Mejillones";
            $selectData["Nor Carangas"] = "Nor Carangas";
            $selectData["Pantaleón Dalence"] = "Pantaleón Dalence";
            $selectData["Poopó"] = "Poopó";
            $selectData["Sabaya"] = "Sabaya";
            $selectData["Sajama"] = "Sajama";
            $selectData["San Pedro de Totora"] = "San Pedro de Totora";
            $selectData["Saucarí"] = "Saucarí";
            $selectData["Sebastian Pagador"] = "Sebastian Pagador";
            $selectData["Sud Carangas"] = "Sud Carangas";
            $selectData["Tomas Barrón"] = "Tomas Barrón";            
        }
        if($bornIn == 'Pando'){
            $selectData["Abuná"] = "Abuná";
            $selectData["Federico Román"] = "Federico Román";
            $selectData["Madre de Dios"] = "Madre de Dios";
            $selectData["Manuripi"] = "Manuripi";
            $selectData["Nicolás Suárez"] = "Nicolás Suárez";
        }
        if($bornIn == 'Potosi'){
            $selectData["Alonso de Ibáñez"] = "Alonso de Ibáñez";
            $selectData["Antonio Quijarro"] = "Antonio Quijarro";
            $selectData["Bernardino Bilbao"] = "Bernardino Bilbao";
            $selectData["Charcas"] = "Charcas";
            $selectData["Chayanta"] = "Chayanta";
            $selectData["Cornelio Saavedra"] = "Cornelio Saavedra";
            $selectData["Daniel Campos"] = "Daniel Campos";
            $selectData["Enrique Baldivieso"] = "Enrique Baldivieso";
            $selectData["José María Linares"] = "José María Linares";
            $selectData["Modesto Omiste"] = "Modesto Omiste";
            $selectData["Nor Chichas"] = "Nor Chichas";
            $selectData["Nor Lípez"] = "Nor Lípez";
            $selectData["Rafael Bustillo"] = "Rafael Bustillo";
            $selectData["Sud Chichas"] = "Sud Chichas";
            $selectData["Sud Lípez"] = "Sud Lípez";
            $selectData["Tomás Frías"] = "Tomás Frías";
        }
        if($bornIn == 'Santa Cruz'){
            $selectData["Andrés Ibáñez"] = "Andrés Ibáñez";
            $selectData["Angel sandoval"] = "Angel sandoval";
            $selectData["Chiquitos"] = "Chiquitos";
            $selectData["Cordillera"] = "Cordillera";
            $selectData["Florida"] = "Florida";
            $selectData["German Bush"] = "German Bush";
            $selectData["Guarayos"] = "Guarayos";
            $selectData["Ichilo"] = "Ichilo";
            $selectData["Manuel Maria Caballero"] = "Manuel Maria Caballero";
            $selectData["Ñuflo de Chávez"] = "Ñuflo de Chávez";
            $selectData["Obispo Santisteban"] = "Obispo Santisteban";
            $selectData["Sara"] = "Sara";
            $selectData["Velasco"] = "Velasco";
            $selectData["Vallegrande"] = "Vallegrande";
            $selectData["Warnes"] = "Warnes";
        }
        if($bornIn == 'Tarija'){
            $selectData["Aniceto Arce"] = "Aniceto Arce";
            $selectData["Burdet O'Connor"] = "Burdet O'Connor";
            $selectData["Cercado"] = "Cercado";
            $selectData["Eustaquio Méndez"] = "Eustaquio Méndez";
            $selectData["Gran Chaco"] = "Gran Chaco";
            $selectData["José María Avilés"] = "José María Avilés";
        }
        if($bornIn == 'Otros'){
            $selectData["Otros"] = "Otros";
        }
        return $selectData;
    }
}
?>

