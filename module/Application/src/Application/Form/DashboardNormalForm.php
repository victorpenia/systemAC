<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class DashboardNormalForm extends Form {

    protected $dbadapter;

    public function __construct(AdapterInterface $dbAdapter) {
        $this->dbadapter = $dbAdapter;
        parent::__construct("dashboard");        
        
        
        /* button register */
        $this->add(array(
            'name' => 'send',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Ver',
                'class' => 'btn btn-primary'
            ),
        )); 
        
        /* input select type */
//        $this->add(array(
//            'type' => 'Zend\Form\Element\Select',
//            'name' => 'type',
//            'attributes' => array(
//                'id' => 'inputSelectType',
//                'class' => 'form-control'
//            ),
//            'options' => array(
//                'value_options' => array(
//                    'Reportes por linea' => 'Reportes por linea',
//                    'Reportes por torta' => 'Reportes por torta',
//                ),
//            )
//        ));
        
        /* input select year */
//        $this->add(array(
//            'type' => 'Zend\Form\Element\Select',
//            'name' => 'year',
//            'attributes' => array(
//                'id' => 'inputSelectBornIn',
//                'class' => 'form-control'
//            ),
//            'options' => array(
//                'value_options' => array(
//                    '2011' => '2011',
//                    '2012' => '2012',
//                    '2013' => '2013',
//                    '2014' => '2014',
//                ),
//            )
//        ));
        /* input text start Date */
        $this->add(array(
            'name' => 'startDate',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '0',
                'data-date-format' => "yyyy-mm-dd",
                'value' => '2014-01-01',
                'class' => 'form-control',
                'id' => 'inputStartDate',
            ),
        ));
        /* input text end Date */
        $this->add(array(
            'name' => 'endDate',
            'attributes' => array(
                'type' => 'text',
                'autocomplete' => 'off',
                'maxlength' => '0',
                'data-date-format' => "yyyy-mm-dd",
                'value' => date("Y-m-d"),
                'class' => 'form-control',
                'id' => 'inputEndDate',
            ),
        ));
        
        /* input select sacrament */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'sacrament',
            'attributes' => array(
                'id' => 'inputSelectSacrament',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => array(
                    'Bautismos' => 'Bautismos',
                    'Confirmaciones' => 'Confirmaciones',
                    'Matrimonios' => 'Matrimonios'
                ),
            )
        )); 
       
        /* input comboBox idParishes */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'idParishes',
            'attributes' => array(
                'id' => 'inputSelectIdParish',
                'class' => 'form-control'
            ),
            'options' => array(
//                'value_options' => array(
//                    '-1' => 'TODOS',
//                ),
//                'disable_inarray_validator' => true,
                'value_options' => $this->getOptionsForSelectParishes(),
            )
        ));
        
        /* input comboBox idvicarious */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'idVicarious',
            'attributes' => array(
                'id' => 'inputSelectIdVicarious',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => $this->getOptionsForSelectVicarious(),
            )
        ));
    }

            
    public function getOptionsForSelectParishes()
    {
        $dbAdapter = $this->dbadapter;
        $sql = 'SELECT id, parishName FROM parishes order by parishName';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = array();
        $selectData['-1'] = 'TODOS';
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['parishName'];
        }
        return $selectData;
    }
    
    public function getOptionsForSelectVicarious()
    {
        $dbAdapter = $this->dbadapter;
        $sql = 'SELECT id, vicariousName FROM vicarious order by vicariousName';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = array();
        $selectData['-1'] = 'TODOS';
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['vicariousName'];
        }
        return $selectData;
    }
}
?>

