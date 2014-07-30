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
use Users\Form\UserForm;
use Users\Form\UserFilter;
use Zend\Authentication\AuthenticationService;
use Zend\Validator\Db\RecordExists;

class UserController extends AbstractActionController {

    protected $usersTable;
    public $dbAdapter;
    
    public $authUser;
    public $parishName = "Arzobispado de Cochabamba";

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
        $paginator = $this->getUsersTable()->fetchAll(true);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(500);
        $values = array(
            'title' => 'USUARIOS ARZOBISPADO',
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
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/users/user/index');
        }
        try {
            $user = $this->getUsersTable()->getOneUser($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/users/user/index');
        }
        $values = array(
            'title' => 'USUARIOS ARZOBISPADO',
            'idRoles' => $this->authUser->getIdentity()->idRoles,
            'data' => $user
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function addAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $messages = null;
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new UserForm($this->dbAdapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $userFilter = new UserFilter();
            $form->setInputFilter($userFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $password = $this->generatePassword();
                $userFilter->exchangeArray($this->prepareData($data, $password));
                if ($this->exitsEmailAddressInDatabase($userFilter->email)) {
                    $this->getUsersTable()->addUser($userFilter);
//                    $this->sendPasswordByEmail($usr_email, $password);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/users/user/index');
                } else {
                    $messages .= "<p style='color:#a94442' >Error el email ya existe en la base de datos</p>";                    
                }
            }
        }
        $values = array(
            'title' => 'USUARIOS ARZOBISPADO',
            'form' => $form,
            'messages' => $messages,
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
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/users/user/index');
        }
        try {
            $user = $this->getUsersTable()->fetchOneUser($id);
            $parish = $user->idParishes;
            $messages = null;
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/users/user/index');
        }

        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new UserForm($this->dbAdapter);
        $form->bind($user);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                if ($this->exitsEmailAddressInDatabaseEdit($user->email, $id)) {
                    $this->getUsersTable()->updateUser($user);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/users/user/index');
                } else {
                    $messages .= "<p style='color:#a94442' >El email ya existe en la base de datos</p>";
                }
            }
        }

        $values = array(
            'title' => 'USUARIOS ARZOBISPADO',
            'form' => $form,
            'id' => $id,
            'messages' => $messages,
            'parish' => $parish,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function exitsEmailAddressInDatabase($email) {
        $validator = new RecordExists(
                array(
            'table' => 'users',
            'field' => 'email',
            'adapter' => $this->dbAdapter
                )
        );
        if ($validator->isValid($email)) {
            return false;
        } else {
            return true;
        }
    }

    public function exitsEmailAddressInDatabaseEdit($email, $idUser) {
        $validator = new RecordExists(
                array(
            'table' => 'users',
            'field' => 'email',
            'adapter' => $this->dbAdapter,
            'exclude' => array(
                'field' => 'id',
                'value' => $idUser
            )
                )
        );
        if ($validator->isValid($email)) {
            error_log('exit');
            return false;
        } else {
            error_log('no exit');
            return true;
        }
    }

    public function prepareData($data, $password) {
        $data['passwordSalt'] = $this->generateDynamicSalt();
        $data['staticSalt'] = $this->getStaticSalt();
        $data['password'] = $this->encriptPassword($data['staticSalt'], $password, $data['passwordSalt']);
        return $data;
    }

    public function generateDynamicSalt() {
        $dynamicSalt = '';
        for ($i = 0; $i < 50; $i++) {
            $dynamicSalt .= chr(rand(33, 126));
        }
        return $dynamicSalt;
    }

    public function getStaticSalt() {
        $config = $this->getServiceLocator()->get('Config');
        $staticSalt = $config['staticSalt'];
        return $staticSalt;
    }

    public function encriptPassword($staticSalt, $password, $dynamicSalt) {
        return $password = md5($staticSalt . $password . $dynamicSalt);
    }

    public function generatePassword($l = 8, $c = 0, $n = 0, $s = 0) {
        // get count of all required minimum special chars
        $count = $c + $n + $s;
        $out = '';
        // sanitize inputs; should be self-explanatory
        if (!is_int($l) || !is_int($c) || !is_int($n) || !is_int($s)) {
            trigger_error('Argument(s) not an integer', E_USER_WARNING);
            return false;
        } elseif ($l < 0 || $l > 20 || $c < 0 || $n < 0 || $s < 0) {
            trigger_error('Argument(s) out of range', E_USER_WARNING);
            return false;
        } elseif ($c > $l) {
            trigger_error('Number of password capitals required exceeds password length', E_USER_WARNING);
            return false;
        } elseif ($n > $l) {
            trigger_error('Number of password numerals exceeds password length', E_USER_WARNING);
            return false;
        } elseif ($s > $l) {
            trigger_error('Number of password capitals exceeds password length', E_USER_WARNING);
            return false;
        } elseif ($count > $l) {
            trigger_error('Number of password special characters exceeds specified password length', E_USER_WARNING);
            return false;
        }
        // all inputs clean, proceed to build password
        // change these strings if you want to include or exclude possible password characters
        $chars = "abcdefghijklmnopqrstuvwxyz";
        $caps = strtoupper($chars);
        $nums = "0123456789";
        $syms = "!@#$%^&*()-+?";

        // build the base password of all lower-case letters
        for ($i = 0; $i < $l; $i++) {
            $out .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        // create arrays if special character(s) required
        if ($count) {
            // split base password to array; create special chars array
            $tmp1 = str_split($out);
            $tmp2 = array();

            // add required special character(s) to second array
            for ($i = 0; $i < $c; $i++) {
                array_push($tmp2, substr($caps, mt_rand(0, strlen($caps) - 1), 1));
            }
            for ($i = 0; $i < $n; $i++) {
                array_push($tmp2, substr($nums, mt_rand(0, strlen($nums) - 1), 1));
            }
            for ($i = 0; $i < $s; $i++) {
                array_push($tmp2, substr($syms, mt_rand(0, strlen($syms) - 1), 1));
            }
            // hack off a chunk of the base password array that's as big as the special chars array
            $tmp1 = array_slice($tmp1, 0, $l - $count);
            // merge special character(s) array with base password array
            $tmp1 = array_merge($tmp1, $tmp2);
            // mix the characters up
            shuffle($tmp1);
            // convert to string for output
            $out = implode('', $tmp1);
        }
        error_log('password:');
        error_log($out);
        return $out;
    }

//    public function sendPasswordByEmail($usr_email, $password) {
//        $transport = $this->getServiceLocator()->get('mail.transport');
//        $message = new Message();
//        $this->getRequest()->getServer();  //Server vars
//        $message->addTo($usr_email)
//                ->addFrom('praktiki@coolcsn.com')
//                ->setSubject('Your password has been changed!')
//                ->setBody("Your password at  " .
//                        $this->getRequest()->getServer('HTTP_ORIGIN') .
//                        ' has been changed. Your new password is: ' .
//                        $password
//        );
//        $transport->send($message);
//    }
}
