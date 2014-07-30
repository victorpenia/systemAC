<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Users\Form\InformationForm;
use Users\Form\InformationFilter;
use Zend\Authentication\AuthenticationService;

class InformationController extends AbstractActionController {

    protected $InformationTable;
    public $dbAdapter;
    
    public $authUser;
    public $parishName = "Arzobispado de Cochabamba";

    public function getInformationTable() {
        if (!$this->InformationTable) {
            $sm = $this->getServiceLocator();
            $this->InformationTable = $sm->get('Users\Model\Entity\Information');
        }
        return $this->InformationTable;
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

    public function viewAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = '.$id);
        if (!$id || $id != $this->authUser->getIdentity()->id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/application/dashboard/index');
        }
        try {
            $user = $this->getInformationTable()->fetchOneUser($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/application/dashboard/index');
        }
        $values = array(
            'title' => 'INFORMACI&Oacute;N PERSONAL',
            'data' => $user
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
        if (!$id || $id != $this->authUser->getIdentity()->id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/users/information/view');
        }
        try {
            $user = $this->getInformationTable()->fetchOneUser($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/users/information/view');
        }
        
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new InformationForm($this->dbAdapter);
        $form->bind($user);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getInformationTable()->updateUser($user);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/users/information/view/'.$user->id);
           }
        }

        $values = array
        (
            'title' => 'INFORMACI&Oacute;N PERSONAL',
            'form' => $form,
            'id' => $id,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }
}
