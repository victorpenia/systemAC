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
use Application\Form\ForgottenForm;
use Application\Form\ForgottenFilter;

class ForgottenController extends AbstractActionController {
    
    protected $userTable;
    public $dbAdapter;
    

    public function getUserTable() {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Users\Model\Entity\Users');
        }
        return $this->userTable;
    }
    
    public function sendAction(){
        $values = array
        (
            'title' => 'Arzobispado de Cochabamba',
        );
        $view = new ViewModel($values);
        $this->layout('layout/layoutMain');
        return $view;
    }

    public function indexAction() {
        $messages = null;
        $form = new ForgottenForm("form");
        $request = $this->getRequest();
        if ($request->isPost()) {
            $forgottenFilter = new ForgottenFilter();
            $form->setInputFilter($forgottenFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $userEmail = $data['email'];
                error_log($userEmail);
                $user = $this->getUserTable()->getUserByEmail($userEmail);
                if(!empty($user)){
                    $password =  $this->generatePassword();
                    $forgottenFilter->exchangeArray($this->prepareData($data, $password));
                    $this->getUserTable()->ChangePasswordUser($forgottenFilter);
//                    $this->sendPasswordByEmail($usr_email, $password);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/application/forgotten/send');
                }else{
                    $messages = "<p style='color:#ff0000'>El email no está en uso.</p>";
                }
            }
        }
        $values = array
        (
            'title' => 'Arzobispado de Cochabamba',
            'form' => $form,
            'messages' => $messages,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $view = new ViewModel($values);
        $this->layout('layout/layoutMain');
        return $view;
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
