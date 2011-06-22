<?php
/**
 * Description of Cellclass
 *
 * @author Universidad Colima
 */
class Cell {

    protected $formControl = null;
    //protected $isChangeable;
    
    protected $_parent;

    protected $_value;

    public $htmlAttributes;

    
    protected $formula;
    // FIXME: talvez no sea necesario
    
    protected $_isCalculate;
    //
    protected $calculator;

    /*
     * Helper HTML
     */
    protected $html = null;
    ///

    /*
     * FIXME: Mejorar esto con patrones de diseño
     */
    const INPUT_NONE = "none";
    const INPUT_TEXTBOX = "textbox";
    const INPUT_TEXTAREA = "textarea";
    const INPUT_SELECT = "select";
    const INPUT_HIDDEN = "hidden";
    const INPUT_DATE = "calendar";

    public function __construct($value = null, $parent = null){
        $this->_value = $value;
        //$this->interprete = new Interprete();

        $this->html = html::getInstance();
        $this->htmlAttributes = new HtmlAttributes();

        $this->_parent = $parent;
        $this->calculator = Calculation::getInstance();

//        $this->_isCalculate = $calculate;
    }

    public function setInputControl($formControl, $name,HtmlAttributes $htmlAttributesForControl = null){
        //$value = $this->getCalculateValue($this->_value);
        switch($formControl){
            case self::INPUT_TEXTBOX:
                $this->formControl = $this->html->textField($name,"value=\"{$this->getValue()}\" $htmlAttributesForControl");
            break;
            case self::INPUT_TEXTAREA:
                $this->formControl = $this->html->textArea($name,$this->getValue(), $htmlAttributesForControl);
            break;
            case self::INPUT_NONE:
                $this->formControl = $this->getValue();
            break;
            case self::INPUT_HIDDEN:
                $this->formControl = $this->html->hiddenField($name,$this->getValue(), $htmlAttributesForControl).$this->getValue();
            break;
            case self::INPUT_DATE:
                $this->formControl = $this->html->textField($name,"value=\"{$this->getValue()}\" $htmlAttributesForControl dojoType=\"dijit.form.DateTextBox\" required=\"true\" ");
            break;
        }
    }

    /*
     * FIXME: Cambiar la forma de agregar controles de formularios a las celdas.
     */
    public function setSelectControl($name,array $values, $numericKey = false){

        $this->formControl = $this->html->select($name, $values, $this->getValue(), $numericKey);
    }

    //public function save(){}
    
    public function getHtml(){
        $html = "<td";
        $html .= $this->htmlAttributes;
        $html .= ">";
        // set Form control in html
        $html .= ($this->formControl != null)?$this->formControl:$this->getValue();

        $html .= "</td>\n";
        return $html;
    }

    /*
     * Set & Get Value
     */
    public function getValue(){
        if($this->calculator->isFormula($this->_value) == true){
            $value = $this->getCalculateValue();
        }else{
            $value = $this->_value;
        }
        return $value;
    }

    public function getCalculateValue(){
        return $this->calculator->calculate($this->_value, $this->_parent);
    }

    public function setValue($value){
//        $this->_isCalculate = $calculate;
        $this->_value = $value;
    }
}
?>
