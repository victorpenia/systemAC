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
use Application\Form\DashboardNormalForm;
use Application\Form\DashboardoneForm;
use Application\Form\DashboardFilter;

class DashboardController extends AbstractActionController {
    
//    protected $baptismsTable;
    protected $parishesTable;
    public $dbAdapter;
    
    public $authUser;
    public $parishName = "Arzobispado de Cochabamba";
    
    public function getParishesTable() {
        if (!$this->parishesTable) {
            $sm = $this->getServiceLocator();
            $this->parishesTable = $sm->get('Archdiocese\Model\Entity\Parishes');
        }
        return $this->parishesTable;
    }
    
    
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
    
    public function getParishesByVicariousAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $response->setStatusCode(200);
            $idVicarious = $request->getPost('idVicarious');
            $data = $this->getParishesTable()->fetchAllParishesByIdVicarious($idVicarious);
            $buffer = "<option value='-1'>TODOS</option>";
            foreach ($data as $item_data) {
                $buffer.='<option value=' . $item_data->id . '>' . $item_data->parishName .'</option>';
            }
            $response->setContent($buffer);
            $headers = $response->getHeaders();
        }
        return $response;
    }
    
    public function chartsoneAction() {
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
                error_log('year: '.$dashboardFilter->year);
                error_log('sacrament: '.$dashboardFilter->sacrament);
                error_log('vicariuos: '.$dashboardFilter->idVicarious);
                error_log('parish: '.$dashboardFilter->idParishes);
                $year = $dashboardFilter->year;
                $sacrament = $dashboardFilter->sacrament;
                if($dashboardFilter->idVicarious == -1){
                    if($dashboardFilter->idParishes == -1){
                        $data = $this->getBaptismByMonthVicariousAll($dashboardFilter->year);
                        $data1 = $this->getMarriageByMonthVicariousAll($dashboardFilter->year);
                    }else{
                        $data = $this->getBaptismByMonth($dashboardFilter->idParishes, $dashboardFilter->year);
                        $data1 = $this->getMarriageByMonth($dashboardFilter->idParishes, $dashboardFilter->year);
                    } 
                }else{
                    if($dashboardFilter->idParishes == -1){
                        $data = $this->getBaptismByMonthVicarious($dashboardFilter->idVicarious, $dashboardFilter->year);
                        $data1 = $this->getMarriageByMonthVicarious($dashboardFilter->idVicarious, $dashboardFilter->year);
                    }else{
                        $data = $this->getBaptismByMonth($dashboardFilter->idParishes, $dashboardFilter->year);
                        $data1 = $this->getMarriageByMonth($dashboardFilter->idParishes, $dashboardFilter->year);
                    }                    
                }
            }
        }else{
            $year = 2011;
            $sacrament = 'Bautismos & Matrimonios';
            $data = $this->getBaptismByMonthVicariousAll($year);
            $data1 = $this->getMarriageByMonthVicariousAll($year);
        }
        $values = array(
            'form' => $form,
            'year' => $year,
            'sacrament' => $sacrament,
            'data' => $data,
            'data1' => $data1,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        $view = new ViewModel($values);
        $this->layout('layout/layoutDashboard');
        return $view;
    }
    
    public function normalAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $messages = null;
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new DashboardNormalForm($this->dbAdapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $dashboardFilter = new DashboardFilter();
            $form->setInputFilter($dashboardFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $dashboardFilter->exchangeArray($form->getData());
                error_log('sacrament: '.$dashboardFilter->sacrament);
                $startDate = $dashboardFilter->startDate;
                $sacrament = $dashboardFilter->sacrament;
                $endDate = $dashboardFilter->endDate;
                if($dashboardFilter->sacrament == 'Bautismos'){
                    if($dashboardFilter->idVicarious == -1){
                        $vicarious = 'TODOS';
                        $data = $this->getBaptismByDates($startDate, $endDate);
                    }else{
                        $vicarious = 'UNO';
                        if($dashboardFilter->idParishes == -1){                            
                            $data = $this->getBaptismByDatesByVicarious($dashboardFilter->idVicarious, $startDate, $endDate);
                        }else{
                            $data = $this->getBaptismByDatesByParishes($dashboardFilter->idParishes, $startDate, $endDate);
                        }
                    }
                }
                if($dashboardFilter->sacrament == 'Confirmaciones'){
                    if($dashboardFilter->idVicarious == -1){
                        $vicarious = 'TODOS';
                        $data = $this->getConfirmationByDates($startDate, $endDate);
                    }else{
                        $vicarious = 'UNO';
                        if($dashboardFilter->idParishes == -1){                            
                            $data = $this->getConfirmationByDatesByVicarious($dashboardFilter->idVicarious, $startDate, $endDate);
                        }else{
                            $data = $this->getConfirmationByDatesByParishes($dashboardFilter->idParishes, $startDate, $endDate);
                        }
                    }
                }
                if($dashboardFilter->sacrament == 'Matrimonios'){
                    if($dashboardFilter->idVicarious == -1){
                        $vicarious = 'TODOS';
                        $data = $this->getMarriageByDates($startDate, $endDate);
                    }else{
                        $vicarious = 'UNO';
                        if($dashboardFilter->idParishes == -1){                            
                            $data = $this->getMarriageByDatesByVicarious($dashboardFilter->idVicarious, $startDate, $endDate);
                        }else{
                            $data = $this->getMarriageByDatesByParishes($dashboardFilter->idParishes, $startDate, $endDate);
                        }
                    }
                }
                
            }
        }else{
            $startDate = '2014-01-01';
            $endDate = '2014-12-20';
            $vicarious = 'TODOS';
            $sacrament = 'Bautismos';
            $data = $this->getBaptismByDates($startDate, $endDate);
        }
        $values = array(
            'form' => $form,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'sacrament' => $sacrament,
            'vicarious' => $vicarious,
            'data' => $data,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }
    
    public function chartsthreeAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $messages = null;
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new DashboardoneForm($this->dbAdapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $dashboardFilter = new DashboardFilter();
            $form->setInputFilter($dashboardFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $dashboardFilter->exchangeArray($form->getData());
                error_log($dashboardFilter->year);
                error_log($dashboardFilter->sacrament);
                error_log($dashboardFilter->idVicarious);
                $year = $dashboardFilter->year;
                $sacrament = $dashboardFilter->sacrament;
                if($dashboardFilter->idVicarious == -1){
                    if($dashboardFilter->sacrament == 'Bautismos'){
                        $data = $this->getBaptismByYearVicariousAll($dashboardFilter->year);
                    } if($dashboardFilter->sacrament == 'Confirmaciones'){
                        $data = $this->getConfirmactionByYearVicariousAll($dashboardFilter->year);
                    } if($dashboardFilter->sacrament == 'Matrimonios'){                        
                        $data = $this->getMarriageByYearVicariousAll($dashboardFilter->year);
                    }
                }else{
                    if($dashboardFilter->sacrament == 'Bautismos'){
                        $data = $this->getBaptismByYearVicarious($dashboardFilter->idVicarious, $dashboardFilter->year);
                    } if($dashboardFilter->sacrament == 'Confirmaciones'){
                        $data = $this->getConfirmactionByYearVicarious($dashboardFilter->idVicarious, $dashboardFilter->year);
                    } if($dashboardFilter->sacrament == 'Matrimonios'){                        
                        $data = $this->getMarriageByYearVicarious($dashboardFilter->idVicarious, $dashboardFilter->year);
                    }
                }
            }
        }else{
            $year = 2011;
            $sacrament = 'Bautismos';
            $data = $this->getBaptismByYearVicariousAll($year);
        }
        $values = array(
            'form' => $form,
            'year' => $year,
            'sacrament' => $sacrament,
            'data' => $data,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        $view = new ViewModel($values);
        $this->layout('layout/layoutDashboardFlot');
        return $view;
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
    
    public function indexAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $values = array(
            'title' => 'ARZOBISPADO DE COCHABAMBA',
            'idRol' => $this->authUser->getIdentity()->idRoles
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        $view = new ViewModel($values);
        $this->layout('layout/layoutDashboard');
        return $view;
        
    } 
    
    private function getBaptismByMonth($idParish, $year) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT MONTH(baptismDate) as month,count(id) as bap FROM baptisms WHERE YEAR(baptismDate) = '$year' and idParish = '$idParish' GROUP BY MONTH(baptismDate)";
//        $sql = "SELECT count(id) as bap FROM baptisms, bookofsacraments WHERE baptisms.idBookofsacraments = bookofsacraments.id";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getMarriageByMonth($idParish, $year) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT MONTH(marriageDate) as monthM,count(id) as marri FROM marriages WHERE YEAR(marriageDate) = '$year' and idParish = '$idParish' GROUP BY MONTH(marriageDate)";
//        $sql = "SELECT count(id) as bap FROM baptisms, bookofsacraments WHERE baptisms.idBookofsacraments = bookofsacraments.id";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getBaptismByMonthVicarious($idVicarious, $year) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
//        $sql = "SELECT MONTH(baptismDate) as month,count(id) as bap FROM baptisms WHERE YEAR(baptismDate) = '$year' and idParish = '$idParish' GROUP BY MONTH(baptismDate)";
        $sql = "SELECT MONTH(baptisms.baptismDate) as month,count(baptisms.id) as bap FROM baptisms, parishes WHERE YEAR(baptisms.baptismDate) = '$year' and parishes.idVicarious = '$idVicarious' and baptisms.idParish = parishes.id GROUP BY MONTH(baptismDate)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getBaptismByMonthVicariousAll($year) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
//        $sql = "SELECT MONTH(baptismDate) as month,count(id) as bap FROM baptisms WHERE YEAR(baptismDate) = '$year' and idParish = '$idParish' GROUP BY MONTH(baptismDate)";
        $sql = "SELECT MONTH(baptisms.baptismDate) as month,count(baptisms.id) as bap FROM baptisms, parishes WHERE YEAR(baptisms.baptismDate) = '$year' and baptisms.idParish = parishes.id GROUP BY MONTH(baptismDate)";
//        $sql = "SELECT MONTH(baptisms.baptismDate) as month,count(baptisms.id) as bap FROM baptisms, parishes WHERE baptisms.baptismDate BETWEEN '$startDate' AND '$endDate' and baptisms.idParish = parishes.id GROUP BY MONTH(baptismDate)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getBaptismByDates($startDate, $endDate) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT vicarious.vicariousName, count(baptisms.id) as bap FROM baptisms, parishes, vicarious WHERE baptisms.baptismDate BETWEEN '$startDate' AND '$endDate' and baptisms.idParish = parishes.id and vicarious.id = parishes.idVicarious GROUP BY (vicarious.vicariousName)";
//        $sql = "SELECT parishes.parishName, count(baptisms.id) as bap FROM parishes LEFT JOIN baptisms ON (baptisms.idParish = parishes.id) WHERE baptisms.baptismDate BETWEEN '$startDate' AND '$endDate' GROUP BY (parishes.parishName)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getBaptismByDatesByVicarious($idVicarious ,$startDate, $endDate) {
        error_log($idVicarious);
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT parishes.parishName, count(baptisms.id) as bap FROM baptisms, parishes WHERE baptisms.baptismDate BETWEEN '$startDate' AND '$endDate' and baptisms.idParish = parishes.id and parishes.idVicarious = '$idVicarious' GROUP BY (parishes.parishName)";
//        $sql = "SELECT parishes.parishName, count(baptisms.id) as bap FROM parishes LEFT JOIN baptisms ON (baptisms.idParish = parishes.id) WHERE baptisms.baptismDate BETWEEN '$startDate' AND '$endDate' GROUP BY (parishes.parishName)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getBaptismByDatesByParishes($idParish ,$startDate, $endDate) {
        error_log($idParish);
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT parishes.parishName, count(baptisms.id) as bap FROM baptisms, parishes WHERE baptisms.baptismDate BETWEEN '$startDate' AND '$endDate' and baptisms.idParish = parishes.id and baptisms.idParish = '$idParish'";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getConfirmationByDates($startDate, $endDate) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT vicarious.vicariousName, count(confirmations.id) as bap FROM confirmations, parishes, vicarious WHERE confirmations.confirmationDate BETWEEN '$startDate' AND '$endDate' and confirmations.idParish = parishes.id and vicarious.id = parishes.idVicarious GROUP BY (vicarious.vicariousName)";
//        $sql = "SELECT parishes.parishName, count(baptisms.id) as bap FROM parishes LEFT JOIN baptisms ON (baptisms.idParish = parishes.id) WHERE baptisms.baptismDate BETWEEN '$startDate' AND '$endDate' GROUP BY (parishes.parishName)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getConfirmationByDatesByVicarious($idVicarious ,$startDate, $endDate) {
        error_log($idVicarious);
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT parishes.parishName, count(confirmations.id) as bap FROM confirmations, parishes WHERE confirmations.confirmationDate BETWEEN '$startDate' AND '$endDate' and confirmations.idParish = parishes.id and parishes.idVicarious = '$idVicarious' GROUP BY (parishes.parishName)";
//        $sql = "SELECT parishes.parishName, count(baptisms.id) as bap FROM parishes LEFT JOIN baptisms ON (baptisms.idParish = parishes.id) WHERE baptisms.baptismDate BETWEEN '$startDate' AND '$endDate' GROUP BY (parishes.parishName)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getConfirmationByDatesByParishes($idParish ,$startDate, $endDate) {
        error_log($idParish);
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT parishes.parishName, count(confirmations.id) as bap FROM confirmations, parishes WHERE confirmations.confirmationDate BETWEEN '$startDate' AND '$endDate' and confirmations.idParish = parishes.id and confirmations.idParish = '$idParish'";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getMarriageByDates($startDate, $endDate) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT vicarious.vicariousName, count(marriages.id) as bap FROM marriages, parishes, vicarious WHERE marriages.marriageDate BETWEEN '$startDate' AND '$endDate' and marriages.idParish = parishes.id and vicarious.id = parishes.idVicarious GROUP BY (vicarious.vicariousName)";
//        $sql = "SELECT parishes.parishName, count(baptisms.id) as bap FROM parishes LEFT JOIN baptisms ON (baptisms.idParish = parishes.id) WHERE baptisms.baptismDate BETWEEN '$startDate' AND '$endDate' GROUP BY (parishes.parishName)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getMarriageByDatesByVicarious($idVicarious ,$startDate, $endDate) {
        error_log($idVicarious);
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT parishes.parishName, count(marriages.id) as bap FROM marriages, parishes WHERE marriages.marriageDate BETWEEN '$startDate' AND '$endDate' and marriages.idParish = parishes.id and parishes.idVicarious = '$idVicarious' GROUP BY (parishes.parishName)";
//        $sql = "SELECT parishes.parishName, count(baptisms.id) as bap FROM parishes LEFT JOIN baptisms ON (baptisms.idParish = parishes.id) WHERE baptisms.baptismDate BETWEEN '$startDate' AND '$endDate' GROUP BY (parishes.parishName)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getMarriageByDatesByParishes($idParish ,$startDate, $endDate) {
        error_log($idParish);
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT parishes.parishName, count(marriages.id) as bap FROM marriages, parishes WHERE marriages.marriageDate BETWEEN '$startDate' AND '$endDate' and marriages.idParish = parishes.id and marriages.idParish = '$idParish'";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getMarriageByMonthVicarious($idVicarious, $year) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
//        $sql = "SELECT MONTH(marriageDate) as monthM,count(id) as marri FROM marriages WHERE YEAR(marriageDate) = '$year' and idParish = '$idParish' GROUP BY MONTH(marriageDate)";
        $sql = "SELECT MONTH(marriages.marriageDate) as monthM,count(marriages.id) as marri FROM marriages, parishes WHERE YEAR(marriages.marriageDate) = '$year' and parishes.idVicarious = '$idVicarious' and marriages.idParish = parishes.id GROUP BY MONTH(marriageDate)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getMarriageByMonthVicariousAll($year) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
//        $sql = "SELECT MONTH(marriageDate) as monthM,count(id) as marri FROM marriages WHERE YEAR(marriageDate) = '$year' and idParish = '$idParish' GROUP BY MONTH(marriageDate)";
        $sql = "SELECT MONTH(marriages.marriageDate) as monthM,count(marriages.id) as marri FROM marriages, parishes WHERE YEAR(marriages.marriageDate) = '$year' and marriages.idParish = parishes.id GROUP BY MONTH(marriageDate)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getBaptismByYearVicarious($idVicarious, $year) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT parishes.parishName, count(baptisms.id) as sacrament FROM baptisms, parishes WHERE YEAR(baptisms.baptismDate) = '$year' and parishes.idVicarious = '$idVicarious' and baptisms.idParish = parishes.id GROUP BY(parishes.parishName)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getBaptismByYearVicariousAll( $year) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT parishes.parishName, count(baptisms.id) as sacrament FROM baptisms, parishes WHERE YEAR(baptisms.baptismDate) = '$year' and baptisms.idParish = parishes.id GROUP BY(parishes.parishName)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getMarriageByYearVicarious($idVicarious, $year) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT parishes.parishName, count(marriages.id) as sacrament FROM marriages, parishes WHERE YEAR(marriages.marriageDate) = '$year' and parishes.idVicarious = '$idVicarious' and marriages.idParish = parishes.id GROUP BY(parishes.parishName)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getMarriageByYearVicariousAll($year) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT parishes.parishName, count(marriages.id) as sacrament FROM marriages, parishes WHERE YEAR(marriages.marriageDate) = '$year' and marriages.idParish = parishes.id GROUP BY(parishes.parishName)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getConfirmactionByYearVicarious($idVicarious, $year) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT parishes.parishName, count(confirmations.id) as sacrament FROM confirmations, parishes WHERE YEAR(confirmations.confirmationDate) = '$year' and parishes.idVicarious = '$idVicarious' and confirmations.idParish = parishes.id GROUP BY(parishes.parishName)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    private function getConfirmactionByYearVicariousAll($year) {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT parishes.parishName, count(confirmations.id) as sacrament FROM confirmations, parishes WHERE YEAR(confirmations.confirmationDate) = '$year' and confirmations.idParish = parishes.id GROUP BY(parishes.parishName)";
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
