<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TableDinamicclass
 *
 * @author Universidad Colima
 */
class TableDinamic extends Table{


    public function getHtmlTable($name){
        $this->body->htmlAttributes['id'] = $name;
        $this->body->lastRow()->htmlAttributes['id'] = "{$name}_plantilla";
//        $this->body->lastRow()->firstColumn()->setValue(" ".$thi->$this->body->lastRow()->firstColumn()->getValue());
        $html = parent::getHtmlTable();
        $html .= "<button dojotype=\"dijit.form.Button\" class=\"agregar\" onClick=\"return cloneRow('$name')\">Agregar registro</button>
";
        return $html;
    }


//    public function getArrayTable($deleteFirstColumn = true){
//        $table = array();
////        $table = parent::getArrayTable();
//
//        foreach($table as $p => $part){
//            foreach($part as $r => $row){
//                if($deleteFirstColumn == true){
//                    unset($table[$p][$r][0]);
//                }else{
//                    unset($table[$p][$r][count($row) - 1]);
//                }
//            }
//        }
//
//        return $table;
//    }
}
?>