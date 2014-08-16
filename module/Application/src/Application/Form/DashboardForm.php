<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class DashboardForm extends Form {

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
        
        /* input select year */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'year',
            'attributes' => array(
                'id' => 'inputSelectBornIn',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => array(
                    '2011' => '2011',
                    '2012' => '2012',
                    '2013' => '2013',
                    '2014' => '2014',
                ),
            )
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
                    'Bautismos & Matrimonios' => 'Bautismos & Matrimonios',
                    'Bautismos' => 'Bautismos',
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
        $selectData['-1'] = 'Todos';
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
        $selectData['-1'] = 'Todos';
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['vicariousName'];
        }
        return $selectData;
    }
}
?>

