<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class DashboardoneForm extends Form {

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

