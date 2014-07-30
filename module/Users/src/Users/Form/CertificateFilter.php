<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Users\Form;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class CertificateFilter implements InputFilterAwareInterface {

    public $id;
    public $certificateName;
    public $privateKey;
    public $createDate;
    public $expirationDate;            
    public $version;
    public $serialNumber;
    public $ca;
    public $idUser;
    
    protected $inputFilter;

    public function exchangeArray($data, $idUser, $certificate, $privateKey ) {
        $createDate =date("Y-m-d",$certificate['validFrom_time_t']);
        $expirationDate = date("Y-m-d",$certificate['validTo_time_t']);
        
        $this->id = (isset($data)) ? $data : null;
        $this->certificateName = (isset($data)) ? $data : null;
        $this->privateKey = (isset($privateKey)) ? $privateKey : null;
        $this->createDate = (isset($createDate)) ? $createDate : null;
        $this->expirationDate = (isset($expirationDate)) ? $expirationDate : null;
        $this->version = (isset($certificate['version'])) ? $certificate['version'] : null;
        $this->serialNumber = (isset($certificate['serialNumber'])) ? $certificate['serialNumber'] : null;
        $this->ca = (isset($certificate['issuer']['commonName'])) ? $certificate['issuer']['commonName'] : null;
        $this->idUser = (isset($idUser)) ? $idUser : null;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            
//            $inputFilter->add(array(
//                'name' => 'cert-file',
//                'required' => true,
//                'validators' => array(
//                    new \Zend\Validator\File\UploadFile()
//                 )
//            ));
            
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

}

?>
