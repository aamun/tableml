<?php
/**
 * Description of TableContentclass
 *
 * @author Universidad Colima
 */
abstract class TableContent implements ArrayAccess, Iterator{

    protected $key;
    protected $content;
    public $htmlAttributes;

    protected $modelName;

    /*
     * Numero de filas.
     */
    private $numRows;
    /*
     * Numero de columnas .
     */
    private $numColumns;
    
    private $directionInterpret;

    public function __construct(){
        $this->content = array();
        $this->key = 0;

        $this->htmlAttributes = new HtmlAttributes();

        $this->numColumns = 0;
        $this->numRows = 0;
        
        $this->directionInterpret = new DirectionInterpret();
    }

    abstract public function getHtml();

    
    public function setRows($rows){
        //$this->numRows = $rows;
        
        $newRows = array();

        for($f = 1;$f <= $rows; $f++){
            $cells = array();

            for($c = 1; $c <= $this->numColumns; $c++){
                $cells[] = new Cell();
            }
            $this->addRow(new Row($cells));
        }
    }

    public function setColumns($columns){
        $this->numColumns = $columns;
        // Agregar Columnas
    }

    public function countRows(){
        return $this->numRows;
    }

    public function countColumns(){
        return $this->numColumns;
    }


    public function addRow($row = null, $first = false){
        // Fixme: Agregarle las columnas que le hagan falta
        // Fixme: Agregar el modelo si esta asignado y no se envia un objeto fila.
        if($row == null){
            //$row = new Row();
            $cells = array();
            for($c = 1; $c <= $this->numColumns; $c++){
                $cells[] = new Cell();
            }
            $this->addRow(new Row($cells));
        }else{
            $this->numRows++;
            $this->content[] = $row;
        }
    }

    public function addRows(array $rows){
        $this->numRows += count($rows);
        // Fixme: Agregarle las columnas que le hagan falta
        $this->content = array_merge($this->content, $rows);
    }

    public function getArrayContent(){
        return $this->content;
    }

    public function setArrayContent($content){
        $this->content = $content;
    }

    /*
     * Return
     */

    public function getArray(){
        $content = array();
        foreach($this->content as $row){
            $content[] = $row->getArray();
        }

        return $content;
    }

    public function firstRow(){
        return $this->content[0];
    }

    public function lastRow(){
        return $this->content[count($this->content) - 1];
    }

    public function setModelName($modelName){
        $this->modelName = $modelName;
    }

    public function setModel(array $result, array $asignations, $numRow = null){
        if($numRow !== null){
            if(isset($this->content[$numRow])){
                $f = $numRow;
            }elseif($numRow  == $this->numRows){
                $this->addRow();
                $f = count($this->content) - 1;
            }
        }else{
            $this->addRow();
            $f = count($this->content) - 1;
        }
        foreach($asignations as $column => $propiety){
            $c = $this->directionInterpret->letterToNum($column);
            $this->content[$f][$c]->setValue($result[$propiety]);            
        }
    }

    public function setModels(array $results, array $asignations, $init = null){
        if($init === null && is_numeric($init) == false){
            foreach($results as $result){
                $this->setModel($result, $asignations);
            }
        }else{
            foreach($results as $result){
                $this->setModel($result, $asignations, $init++);
            }
        }
    }

    public function setModelsHorizontal(array $results, array $asignations, $step = null){
        $this->addRow();
        $step = ($step == null)?count($asignations):$step;
        foreach($asignations as $column => $propiety){
            $c = $this->directionInterpret->letterToNum($column);
            foreach($results as $k => $result){
                $this->content[count($this->content) - 1][$c]->setValue($result[$propiety]);
                $c += $step;
            }
        }
     }

    public function mergeCellsByRowColumn($r1, $c1, $r2, $c2){
        if($r2 > $r1){
            $this->content[$r1][$c1]->htmlAttributes->rowspan = ($r2 - $r1) + 1;
        }

        if($c2 > $c1){
            $this->content[$r1][$c1]->htmlAttributes->colspan = ($c2 - $c1) + 1;
        }

        for($i = $r1; $i <= $r2; $i++){
            for($j = $c1; $j <= $c2; $j++){
                // Yeah clases de logica xD
                if(!($i == $r1 && $j == $c1))
                unset($this->content[$i][$j]);
            }
        }
    }

    /*
     * Implements ArrayAccess
     */
    public function offsetSet($offset, $value) {
        $this->content[$offset] = $value;
    }
    public function offsetExists($offset) {
        return isset($this->content[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->content[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->content[$offset]) ? $this->content[$offset] : null;
    }

    /*
     * Implements Iterator
     */
    public function rewind() {
        $this->key = 0;
    }

    public function current() {
        return $this->content[$this->key];
    }

    public function key() {
        return $this->key;
    }

    public function next() {
        ++$this->key;
    }

    public function valid() {
        return isset($this->content[$this->key]);
    }
}
?>
