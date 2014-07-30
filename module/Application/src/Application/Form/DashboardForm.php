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
        
        /* input text Page */
//        $this->add(array(
//            'name' => 'page',
//            'attributes' => array(
//                'type' => 'text',
//                'maxlength' => '4',
//                'class' => 'form-control',
//                'id' => 'inputPage',
//                'placeholder' => 'Página'
//            ),
//        ));
        /* input text Item */
//        $this->add(array(
//            'name' => 'item',
//            'attributes' => array(
//                'type' => 'text',
//                'maxlength' => '5',
//                'class' => 'form-control',
//                'id' => 'inpuItem',
//                'placeholder' => 'Partida'
//            ),
//        ));
        /* input text Baptism Date */
//        $this->add(array(
//            'name' => 'baptismDate',
//            'attributes' => array(
//                'type' => 'text',
//                'class' => 'form-control',
//                'id' => 'inputBaptismDate',
//                'placeholder' => 'Fecha bautismo'
//            ),
//        ));
        
              
        /* input select born in */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'year',
            'attributes' => array(
                'id' => 'inputSelectBornIn',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => array(
                    '2010' => '2010',
                    '2011' => '2011',
                    '2012' => '2012',
                    '2013' => '2013',
                    '2014' => '2014',
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
//                'empty_option' => 'Seleccione un parroquia',
                'value_options' => $this->getOptionsForSelectParishes(),
            )
        ));        
    }

    
//    public function getOptionsForSelectBooks()
//    {
//        $dbAdapter = $this->dbadapter;
//        $sql = "SELECT id, code FROM bookofsacraments where sacramentName = 'Bautismo'";
//        $statement = $dbAdapter->query($sql);
//        $result = $statement->execute();
//        $selectData = array();
//        foreach ($result as $res) {
//            $selectData[$res['id']] = $res['code'];
//        }
//        return $selectData;
//    }
    public function getOptionsForSelectParishes()
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
}
?>

