<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Application\Form\DashboardForm;
use Application\Form\DashboardFilter;

class DashboardController extends AbstractActionController {
    
//    protected $baptismsTable;
    
    public $dbAdapter;
    
    public $authUser;
    public $parishName = "Arzobispado de Cochabamba";
    
//    public function getBaptismsTable() {
//        if (!$this->baptismsTable) {
//            $sm = $this->getServiceLocator();
//            $this->baptismsTable = $sm->get('Sacraments\Model\Entity\Baptisms');
//        }
//        return $this->baptismsTable;
//    }
    
    private function authenticationService() {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return false;
        }
        $this->authUser = $auth;
        if($auth->getIdentity()->idRoles == '3' || $auth->getIdentity()->idRoles == '4' ){
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
            $sql = 'SELECT id, parishName FROM parishes where id = '.$auth->getIdentity()->idParishes;
            $statement = $dbAdapter->query($sql);
            $result = $statement->execute();
            foreach ($result as $res) {
                $this->parishName = "Parroquia \"".$res['parishName']."\"";
            }           
        }
        return true;
    }

    public function indexAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $messages = null;
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new DashboardForm($this->dbAdapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $dashboardFilter = new DashboardFilter();
            $form->setInputFilter($dashboardFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $dashboardFilter->exchangeArray($form->getData());
                error_log($dashboardFilter->year);
                $year = $dashboardFilter->year;
                $data = $this->getBaptismByMonth($dashboardFilter->idParishes, $dashboardFilter->year);
                $data1 = $this->getMarriageByMonth($dashboardFilter->idParishes, $dashboardFilter->year);
//                $idPerson = $this->getPersonTable()->addPerson($baptismsFilter);
//                $this->getBaptismsTable()->addBaptism($baptismsFilter, $idPerson, $this->authUser->getIdentity()->id);
//                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/index');
//                
            }
        }else{
            $year = 2010;
            $data = $this->getBaptismByMonth(35, $year);
            $data1 = $this->getMarriageByMonth(35, $year);
        }
        $values = array(
            'form' => $form,
            'year' => $year,
            'data' => $data,
            'data1' => $data1,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        $view = new ViewModel($values);
        $this->layout('layout/layoutDashboard');
        return $view;
        
        
//        $data = $this->getBaptismByMonth();
//        $values = array(
//            'data' => $data,
//            'title' => 'Arzobispado de Cochabamba'
//        );
//        $this->layout()->setVariable('authUser', $this->authUser);
//        $this->layout()->setVariable('parishName', $this->parishName);
//        $view = new ViewModel($values);
//        $this->layout('layout/layoutDashboard');
//        return $view;
        
    } 
    public function leadAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $values = array(
            'title' => 'Guía de usuario Arzobispado de Cochabamba',
            'idRol' => $this->authUser->getIdentity()->idRoles
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        $view = new ViewModel($values);
        $this->layout('layout/layoutDashboard');
        return $view;
        
    } 
    
    private function getBaptismByMonth($idParish, $year) {
//        $year = '2011';
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT MONTH(baptismDate) as month,count(id) as bap FROM baptisms WHERE YEAR(baptismDate) = '$year' and idParish = '$idParish' GROUP BY MONTH(baptismDate)";
//        $sql = "SELECT count(id) as bap FROM baptisms, bookofsacraments WHERE baptisms.idBookofsacraments = bookofsacraments.id";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getMarriageByMonth($idParish, $year) {
//        $year = '2011';
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT MONTH(marriageDate) as monthM,count(id) as marri FROM marriages WHERE YEAR(marriageDate) = '$year' and idParish = '$idParish' GROUP BY MONTH(marriageDate)";
//        $sql = "SELECT count(id) as bap FROM baptisms, bookofsacraments WHERE baptisms.idBookofsacraments = bookofsacraments.id";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
