<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Archdiocese\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Archdiocese\Form\ParishesForm;
use Archdiocese\Form\ParishesFilter;
use Zend\Authentication\AuthenticationService;

class ParishesController extends AbstractActionController {

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
        $paginator = $this->getParishesTable()->fetchAll(true);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(200);
        $values = array(
            'title' => 'PARROQUIAS ARZOBISPADO',
            'data' => $paginator,
            'idRole' => $this->authUser->getIdentity()->idRoles 
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function viewAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = '.$id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/parishes/index');
        }
        try {
            $parish = $this->getParishesTable()->getOneParish($id);
            $users = $this->getParishesTable()->fetchAllUsers($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/parishes/index');
        }
        $values = array(
            'title' => 'PARROQUIAS ARZOBISPADO',
            'data' => $parish,
            'idRole' => $this->authUser->getIdentity()->idRoles ,
            'users' => $users
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function addAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new ParishesForm($this->dbAdapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $parishesFilter = new ParishesFilter();
            $form->setInputFilter($parishesFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $parishesFilter->exchangeArray($form->getData());
                $this->getParishesTable()->addParish($parishesFilter);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/parishes/index');
            }
        }
        $values = array(
            'title' => 'PARROQUIAS ARZOBISPADO',
            'form' => $form,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function editAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = '.$id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/parishes/index');
        }
        try {
            $parish = $this->getParishesTable()->fetchOneParish($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/parishes/index');
        }
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new ParishesForm($this->dbAdapter);
        $form->bind($parish);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($parish->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getParishesTable()->updateParish($parish);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/parishes/index');
            }
        }
        $values = array(
            'title' => 'PARROQUIAS ARZOBISPADO',
            'form' => $form,
            'id' => $id,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/parishes/index');
        }
        $this->getParishesTable()->deleteParish($id);
        return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/parishes/index');
    }
}
