<?php
/**
 * Description of TableRecordSetclass
 *
 * @author Universidad Colima
 */
class TableRecordSet implements ArrayAccess, Iterator{

    private $tables = array();

    const TYPE_PHP = "php";
    
    // Talvez cambie a una extencion propia
    const TYPE_XML = "xml";
    
    private $path;
    /*
     * Iterator variables
     */

    private $key;

    public function __construct(){

        // Directorio en la aplicacion donde se deberian encontrar los templates tableml
        $this->path = Absolute_Path.APPDIR.DIRSEP."tableml".DIRSEP;

    }

    public function __set($name, Table $table){
        $this->tables[$name] = $table;
    }

    public function __get($name){
        return isset($this->tables[$name]) ? $this->tables[$name] : null;
    }

    public function fetch($name, $type = null, array $values = array()){

        switch($type){
            case self::TYPE_PHP:
                $path = $this->path.$name.".".self::TYPE_PHP;
            break;
            case self::TYPE_XML:
            default:
                $path = $this->path.$name.".".self::TYPE_PHP;
            break;
        }
        
        if (file_exists($path) == false) {
            throw new Exception("FlavorPHP error: The <strong>Table</strong> '<em>".$name."</em>' does not exist.");
            return false;
        }

        foreach ($values as $key => $value) {
            $$key = $value;
        }

        ob_start();
        include ($path);
        $contents = ob_get_contents();
        if($contents != ""){
            echo $contents;
            die;
        }
        ob_end_clean();
        
        // Cache
        $this->tables[$name] = $$name;

        return $$name;
    }

    public function add($name, Table $table){
        $this->tables[$name] = $table;
    }

    public function replace($name, Table $table){
        $this->tables[$name] = $table;
    }

    public function drop($name){
        unset($this->tables[$name]);
    }

    public function tablesToHtml(){
        $html = "";
        foreach($this->tables as $table){
            $html .= $table->getHtmlTable();
        }
        return $html;
    }

    public function getTables(){
        return $this->tables;
    }

    /*
     * Implements ArrayAccess
     */
    public function offsetSet($offset, $value) {
        $this->tables[$offset] = $value;
    }
    public function offsetExists($offset) {
        return isset($this->tables[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->tables[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->tables[$offset]) ? $this->tables[$offset] : null;
    }

    /*
     * Implements Iterator
     */

    public function rewind() {
        $this->key = 0;
    }

    public function current() {
        return $this->tables[$this->key];
    }

    public function key() {
        return $this->key;
    }

    public function next() {
        ++$this->key;
    }

    public function valid() {
        return isset($this->tables[$this->key]);
    }
}
?>
