<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Users\Form;

use Zend\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class CertificateForm extends Form {

//    protected $dbAdapter;

    public function __construct($name = null, $options = array()) {
//        $this->dbAdapter = $dbAdapter;
        parent::__construct($name, $options);

        /* button register */
        $this->add(array(
            'name' => 'load',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Cargar',
                'class' => 'btn btn-primary'
            ),
        ));
        /* button Modify*/
        $this->add(array(
            'name' => 'modify',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Guardar Cambios',
                'class' => 'btn btn-primary'
            ),
        ));
        
//        $this->add(array(
//            'name' => 'cert-file',
//            'attributes' => array(
//                'type'  => 'file',
//            ),
//            'options' => array(
//                'label' => 'File Upload',
//            ),
//        )); 
        
        $this->addElements();
        $this->addElementsKey();
        $this->addInputFilter();
        $this->addInputFilterKey();
    }
    
    public function addElements() {
        $file = new Element\File('cert-file');
        $file->setLabel('Avatar Image Upload')
                ->setAttribute('id', 'cert-file');
        $this->add($file);
    }
    
    public function addElementsKey() {
        $fileKey = new Element\File('key-file');
        $fileKey->setLabel('Avatar Image Upload')
                ->setAttribute('id', 'key-file');
        $this->add($fileKey);
    }
    
    public function addInputFilter() {
        $inputFilter = new InputFilter\InputFilter();
        $fileInput = new InputFilter\FileInput('cert-file');
        $fileInput->setRequired(true);
        $fileInput->getValidatorChain()
            ->attachByName('filesize',      array('max' => 10000));
//            ->attachByName('filemimetype',  array('mimeType' => 'image/png,image/x-png'));
        $fileInput->getFilterChain()->attachByName(
            'filerenameupload', array(
            'target' => './public/certificates/cert/',
            'randomize' => false,
            "UseUploadname" => true,
                )
        );
        $inputFilter->add($fileInput);
        $this->setInputFilter($inputFilter);
    }
    
    public function addInputFilterKey() {
        $inputFilter = new InputFilter\InputFilter();
        $fileInput = new InputFilter\FileInput('key-file');
        $fileInput->setRequired(true);
        $fileInput->getValidatorChain()
            ->attachByName('filesize',      array('max' => 10000));
        $fileInput->getFilterChain()->attachByName(
                'filerenameupload', array(
            'target' => './public/certificates/key/',
            'randomize' => false,
            "UseUploadname" => true,
                )
        );
        $inputFilter->add($fileInput);
        $this->setInputFilter($inputFilter);
    }
}
?>

