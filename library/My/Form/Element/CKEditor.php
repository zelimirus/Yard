<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CKEditor
 *
 * @author nikola
 */
class My_Form_Element_CKEditor extends Zend_Form_Element_Textarea {

    public function __construct($spec, $options = null) {
        parent::__construct($spec, $options);
        //grab a reference to the view rendering the form element
        $view = $this->getView();
        //include scripts and initialize the ckeditor
        $view->headScript()->appendFile('/golive_theme/neon_theme/js/ckeditor/ckeditor.js', 'text/javascript');
        $_SESSION['KCFINDER']['uploadURL'] = $view->baseUrl("/uploads/");
        //give the textarea a class name that ckeditor recognises
        $this->setAttrib('class', 'ckeditor');
    }

}