<?php
/**
 * Description of Rowclass
 *
 * @author Universidad Colima
 */
class Row implements ArrayAccess, Iterator{

    private $_parent;

    private $model = null;
    private $modelName = null;
    
    public $relationPropertyModelCell;

    private $key;
    public $cells = array();

    public $htmlAttributes;

    public function __construct(/*$modelName = null,*/ $cells = array()){
        $this->cells = $cells;

        /*if($modelName != null){
            $this->model = new $modelName();
        }*/

        $this->htmlAttributes = new HtmlAttributes();
    }

    public function addCell(Cell $cell = null){
        if($cell == null){
            $cell = new Cell();
        }
//        if($value != null){
//            $cell->setValue($value);
//        }
        $this->cells[] = $cell;
    }

    public function addCells(array $cells){
        $this->cells = array_merge($this->cells, $cells);
    }

    public function insertCell($numCell, $cell, $first = false){

    }

    public function repleaceCell($numCell, $cell){
        $this->cells[$numCell] = $cell;
    }

    public function removeCell($numCell){
        unset($this->cells[$numCell]);
    }

    public function getHtml(){
        $html = "<tr";
        $html .= $this->htmlAttributes;
        $html .= ">";
        foreach($this->cells as $cell){
            $html .= $cell->getHtml();
        }
        $html .= "</tr>";
        return $html;
    }

    /*
     * Return values of cells in array
     */

    public function getArray(){
        $row = array();
        foreach($this->cells as $cell){
            $row[] = $cell->getValue();
        }
        return $row;
    }

    public function save(){
        if($this->model != null){
            $this->model->save();
        }
    }

    public function firstCell(){
        return $this->cells[0];
    }

    public function lastCell(){
        return $this->cells[count($this->cells) - 1];
    }

    /*
     * ArrayAccess
     */
    public function offsetSet($offset, $value) {
        $this->cells[$offset] = $value;
    }
    public function offsetExists($offset) {
        return isset($this->cells[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->cells[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->cells[$offset]) ? $this->cells[$offset] : null;
    }

    /*
     * Iterador
     */
    public function rewind() {
        $this->key = 0;
    }

    public function current() {
        return $this->cells[$this->key];
    }

    public function key() {
        return $this->key;
    }

    public function next() {
        ++$this->key;
    }

    public function valid() {
        return isset($this->cells[$this->key]);
    }
}
?>
