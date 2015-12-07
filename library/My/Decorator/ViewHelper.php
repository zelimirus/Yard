<?php
require_once 'Zend/Form/Decorator/Abstract.php';

/**
 * Class for overriding standard Zend ViewHelper
 *
 * @author tasmaniski <tasmaniski@gmail.com>
 * @version  December 2012
 */
class Zend_Form_Decorator_ViewHelper extends Zend_Form_Decorator_Abstract{
	/**
	 * Element types that represent buttons
	 * @var array
	 */
	protected $_buttonTypes = array(
        'Zend_Form_Element_Button',
        'Zend_Form_Element_Reset',
        'Zend_Form_Element_Submit',
	);

	/**
	 * View helper to use when rendering
	 * @var string
	 */
	protected $_helper;

	/**
	 * Set view helper to use when rendering
	 *
	 * @param  string $helper
	 * @return Zend_Form_Decorator_Element_ViewHelper
	 */
	public function setHelper($helper){
		$this->_helper = (string)$helper;
		return $this;
	}

	/**
	 * Retrieve view helper for rendering element
	 *
	 * @return string
	 */
	public function getHelper(){
		if(null === $this->_helper){
			$options = $this->getOptions();
			if(isset($options['helper'])){
				$this->setHelper($options['helper']);
				$this->removeOption('helper');
			}
			else{
				$element = $this->getElement();
				if(null !== $element){
					if(null !== ($helper = $element->getAttrib('helper'))){
						$this->setHelper($helper);
					}
					else{
						$type = $element->getType();
						if($pos = strrpos($type, '_')){
							$type = substr($type, $pos + 1);
						}
						$this->setHelper('form'.ucfirst($type));
					}
				}
			}
		}

		return $this->_helper;
	}

	/**
	 * Get name
	 *
	 * If element is a Zend_Form_Element, will attempt to namespace it if the
	 * element belongs to an array.
	 *
	 * @return string
	 */
	public function getName(){
		if(null === ($element = $this->getElement())){
			return '';
		}

		$name = $element->getName();

		if(!$element instanceof Zend_Form_Element){
			return $name;
		}

		if(null !== ($belongsTo = $element->getBelongsTo())){
			$name = $belongsTo.'['
			.$name
			.']';
		}

		if($element->isArray()){
			$name .= '[]';
		}

		return $name;
	}

	/**
	 * Retrieve element attributes
	 *
	 * Set id to element name and/or array item.
	 *
	 * @return array
	 */
	public function getElementAttribs(){
		if(null === ($element = $this->getElement())){
			return null;
		}

		$attribs = $element->getAttribs();
		if(isset($attribs['helper'])){
			unset($attribs['helper']);
		}

		if(method_exists($element, 'getSeparator')){
			if(null !== ($listsep = $element->getSeparator())){
				$attribs['listsep'] = $listsep;
			}
		}

		if(isset($attribs['id'])){
			return $attribs;
		}

		$id = $element->getName();

		if($element instanceof Zend_Form_Element){
			if(null !== ($belongsTo = $element->getBelongsTo())){
				$belongsTo = preg_replace('/\[([^\]]+)\]/', '-$1', $belongsTo);
				$id = $belongsTo.'-'.$id;
			}
		}

		$element->setAttrib('id', $id);
		$attribs['id'] = $id;

		return $attribs;
	}

	/**
	 * Get value
	 *
	 * If element type is one of the button types, returns the label.
	 *
	 * @param  Zend_Form_Element $element
	 * @return string|null
	 */
	public function getValue($element){
		if(!$element instanceof Zend_Form_Element){
			return null;
		}

		foreach($this->_buttonTypes as $type){
			if($element instanceof $type){
				if(stristr($type, 'button')){
					$element->content = $element->getLabel();
					return null;
				}
				return $element->getLabel();
			}
		}

		return $element->getValue();
	}

	/**
	 * Render an element using a view helper
	 *
	 * Determine view helper from 'viewHelper' option, or, if none set, from
	 * the element type. Then call as
	 * helper($element->getName(), $element->getValue(), $element->getAttribs())
	 *
	 * @param  string $content
	 * @return string
	 * @throws Zend_Form_Decorator_Exception if element or view are not registered
	 */
	public function render($content){
		$element = $this->getElement();

		$view = $element->getView();
		if(null === $view){
			require_once 'Zend/Form/Decorator/Exception.php';
			throw new Zend_Form_Decorator_Exception('ViewHelper decorator cannot render without a registered view object');
		}

		if(method_exists($element, 'getMultiOptions')){
			$element->getMultiOptions();
		}

		$helper = $this->getHelper();
		$separator = $this->getSeparator();
		$value = $this->getValue($element);
		$attribs = $this->getElementAttribs();
		$name = $element->getFullyQualifiedName();
		$id = $element->getId();
		$attribs['id'] = $id;

		$helperObject = $view->getHelper($helper);
		if(method_exists($helperObject, 'setTranslator')){
			$helperObject->setTranslator($element->getTranslator());
		}

		// Custom code
		if($element instanceof Zend_Form_Element_Text){
			$attribs['class'] = 'form-control';
			$attribs['placeholder'] = ucfirst(str_replace('_', ' ', $name));
		}
		elseif($element instanceof Zend_Form_Element_Button){
			$attribs['class'] = 'btn';
		}
		// End custom code

		$elementContent = $view->$helper($name, $value, $attribs, $element->options);

		// Custom code
		if($element instanceof Zend_Form_Element_Button){
			if($element->getLabel() == 'Reset'){
				$link = $attribs['href'];
				$elementContent = '<a href="'.$link.'" class="btn btn-default"><span class="glyphicon glyphicon-refresh"></span></a>';
			}
			else{
				$elementContent = str_replace('>Search', '><i class="glyphicon glyphicon-search"></i>', $elementContent);    
			}
		}
		// End custom code

		switch($this->getPlacement()){
			case self::APPEND:
				return $content.$separator.$elementContent;
			case self::PREPEND:
				return $elementContent.$separator.$content;
			default:
				return $elementContent;
		}
	}
}