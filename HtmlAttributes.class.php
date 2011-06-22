<?php
/**
 * Description of HtmlAttributesclass
 *
 * @author Universidad Colima
 */
class HtmlAttributes implements ArrayAccess{

    private $htmlAttributes;

    public function __construct(array $htmlAttributes = array()){
        $this->htmlAttributes = $htmlAttributes;
    }

    public function __set($attribute, $value){
        $this->htmlAttributes[$attribute] = $value;
    }

    public function __get($attribute){
        return isset($this->htmlAttributes[$attribute]) ? $this->htmlAttributes[$attribute] : null;
    }

    public function __toString(){
        $str = " ";
        foreach($this->htmlAttributes as $attribute => $value){
            $str .= "$attribute=\"{$value}\" ";
        }
        return $str;
    }

    /*
     * ArrayAccess
     */
    public function offsetSet($offset, $value) {
        $this->htmlAttributes[$offset] = $value;
    }
    public function offsetExists($offset) {
        return isset($this->htmlAttributes[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->htmlAttributes[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->htmlAttributes[$offset]) ? $this->htmlAttributes[$offset] : null;
    }
}
?>
