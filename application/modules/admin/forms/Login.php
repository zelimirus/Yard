<?php

/**
 * Login Form class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 *
 * @version  may 2013
 */
class Admin_Form_Login extends My_Form_Base {

    public function init() {
        //Setting up form's element
        $email = new Zend_Form_Element_Text('email');
        $email->setAttrib('placeholder', 'E-mail')
                ->setRequired(true)
                ->addValidator('NotEmpty', true)
                ->setAttrib('class', '');

        $password = new Zend_Form_Element_Password('password');
        $password->setAttrib('placeholder', 'Password')
                ->setRequired(true)
                ->addValidator('NotEmpty', true)
                ->setAttrib('class', 'b');

        $submit = new Zend_Form_Element_Submit('login');
        $submit->setAttrib('class', 'btn btn-primary');
        $submit->setLabel("Log In");

        //Add element to form and configur the form
        $this->setAction('')
                ->setMethod('post')
                ->addElement($email)
                ->addElement($password)
                ->addElement($submit);
    }

}