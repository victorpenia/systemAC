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
use Users\Form\CertificateForm;
use Users\Form\CertificateFilter;
use Zend\Authentication\AuthenticationService;
use Zend\Validator\Db\RecordExists;

class CertificateController extends AbstractActionController {

    protected $certificateTable;
    protected $usersTable;
    public $dbAdapter;
    
    public $authUser;
    public $parishName = "Arzobispado de Cochabamba";

    public function getCertificateUsersTable() {
        if (!$this->certificateTable) {
            $sm = $this->getServiceLocator();
            $this->certificateTable = $sm->get('Users\Model\Entity\Certificate');
        }
        return $this->certificateTable;
    }

    public function getUsersTable() {
        if (!$this->usersTable) {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('Users\Model\Entity\Users');
        }
        return $this->usersTable;
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
        $paginator = $this->getCertificateUsersTable()->fetchAll(true);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(500);
        $values = array(
            'title' => 'CERTIFICADOS DE USUARIOS',
            'data' => $paginator
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
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/users/certificate/index');
        }
        try {
            $certificate = $this->getCertificateUsersTable()->getOneCertificate($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/users/certificate/index');
        }
        $values = array(
            'title' => 'CERTIFICADOS DE USUARIOS',
            'data' => $certificate
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function loadAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = '.$id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/users/certificate/index');
        }
        try {
            $user = $this->getUsersTable()->getOneUser($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/users/certificate/index');
        }
        $messages = null;
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new CertificateForm('upload-file');
//        $prg = $this->fileprg($form);
        $request = $this->getRequest();        
        if ($request->isPost()) {
            $post = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
            );
            if($post['cert-file']['error'] == 0 && $post['key-file']['error'] == 0){
                if($post['cert-file']['type'] == 'application/x-x509-ca-cert' && $post['key-file']['type'] == 'application/octet-stream'){
                    if($this->exitsCertificateInDatabase($post['cert-file']['name'])){
                        $form->setData($post);   
                        if ($form->isValid()) {
                            $data = $form->getData();
                            error_log('certificate file = '.$data['cert-file']['name']);
                            error_log('certificate key = '.$data['key-file']['name']);
                            move_uploaded_file($data['cert-file']['tmp_name'], './public/certificates/cert/' . $data['cert-file']['name']);
                            $certificateFilter = new CertificateFilter();
                            $certificateData = $this->lodingCertificateData($data['cert-file']['name']);
                            $certificateFilter->exchangeArray($data['cert-file']['name'], $id, $certificateData, $data['key-file']['name']);
                            $this->getCertificateUsersTable()->addCertificate($certificateFilter);
                            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/users/user/index');
                        }
                    }else{
                        $messages .= "<br /><p style='color:#a94442' >Error los certificados ya estan en uso.</p>"; 
                    }
                }else{
                    $messages .= "<br /><p style='color:#a94442' >Error los certificados no son correctos.</p>"; 
                }                
            }else{
                $messages .= "<br /><p style='color:#a94442' >Error no cargo los certificados correctamente.</p>"; 
            }
        }
        $values = array(
            'title' => 'CERTIFICADOS DE USUARIOS',
            'data' => $user,
            'form' => $form,
            'messages' => $messages,
            'url' => $this->getRequest()->getBaseUrl()
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function lodingCertificateData($fileName) {
        $f = fopen('./public/certificates/cert/' . $fileName, "r");
        $cert = fread($f, 8192);
        fclose($f);
        /* la funcion openssl_x509_parse nos extrae los datos y los convierte en un array */
        $data = openssl_x509_parse($cert, 0);
        return $data;
    }
    
    public function exitsCertificateInDatabase($certificate) {
        $validator = new RecordExists(
                array(
            'table' => 'certificates',
            'field' => 'certificateName',
            'adapter' => $this->dbAdapter
                )
        );
        if ($validator->isValid($certificate)) {
            return false;
        } else {
            return true;
        }
    }
}
