<?php
/**
 * Description of Tableclass
 *
 * @author Universidad Colima
 */
class Table implements ArrayAccess{

    public $header;
    public $body;
    public $footer;

    public $htmlAttributes;

    protected $directionInterpret;

//    private $cellSelected;
//    private $selected = false;

    protected $columns = null;

    protected $parts = array();

    public function __construct($c = null){
        $this->header = new HeadTable();
        $this->body = new BodyTable();
        $this->footer = new FootTable();

        $this->htmlAttributes = new HtmlAttributes();

        $this->directionInterpret = new DirectionInterpret();

        //$this->cellSelected = array();

        if($c != null && $c > 0){
            $this->columns = $c;

            $this->header->setColumns($c);
            $this->body->setColumns($c);
            $this->footer->setColumns($c);
        }

        $this->parts = array($this->header, $this->body, $this->footer);
    }

    public function __call($method, $args){
        echo $method;
        utils::pre($args);
    }

    public function setColumns($columns){
        if($columns >= 0){
            $this->columns = $columns;

            $this->header->setColumns($columns);
            $this->body->setColumns($columns);
            $this->footer->setColumns($columns);
        }
    }

    public function save(){}


    public function getBodyArrayTable(){
            $table[] = array();
            foreach($this->body as $r => $row){
                $mergecol = 0;
                $mergerow = 0;
                $acumulateMergecol = 0;
                $x = 0;
                foreach($row as $ce => $cell){

                    while(isset($table[$r][$x]) && $table[$r][$x] == "MERGEROW"){
                        $x++;
                        $acumulateMergecol++;
                    }

                    $table[$r][$x] = $cell->getValue();

                    // Colspan -> MERGECOL
                    if( isset($cell->htmlAttributes['colspan']) ){
                        $mergecol = $cell->htmlAttributes['colspan'];
                        $acumulateMergecol += $mergecol ;
                    }else{
                        $acumulateMergecol++;
                    }

                    for($i = 1; $i <= ($mergecol - 1); $i++){
                        $table[$r][$x + $i] = "MERGECOL";
                    }
                    $x += $i - 1;

                    // Rowspan -> MERGEROW
                    if( isset($cell->htmlAttributes['rowspan']) ){
                        $mergerow = $cell->htmlAttributes['rowspan'];
                    }

                    for($i = 1; $i <= ($mergerow - 1); $i++ ){
                        $table[$r + $i][$acumulateMergecol - 1] = "MERGEROW";
                    }

                    $mergecol = 0;
                    $mergerow = 0;
                    $x++;
                }

            }
    }

    public function getArrayTable(){
        $table = array();
//        $mergerow = 0;
//        $mergecol = 0;
        foreach($this->parts as $p => $part){
            $table[] = array();
            foreach($part as $r => $row){

                $acumulateMergecol = 0;
                $x = 0;
                foreach($row as $ce => $cell){
                    if($cell instanceof Cell){
                        $mergecol = 1;
                        $mergerow = 1;

                        while(isset($table[$p][$r][$x]) && $table[$p][$r][$x] == "MERGEROW"){
                            $x++;
                            $acumulateMergecol++;
                        }

    //                    echo "$p - $r - $x ".$cell->getValue()."<br />";
                        $table[$p][$r][$x] = $cell->getValue();

                        // Colspan -> MERGECOL
                        if( isset($cell->htmlAttributes['colspan']) ){
                            $mergecol = $cell->htmlAttributes['colspan'];
                            $acumulateMergecol += $mergecol ;
                        }else{
                            $acumulateMergecol++;
                        }

                        for($i = 1; $i <= ($mergecol - 1); $i++){
                            $table[$p][$r][$x + $i] = "MERGECOL";
                        }
                        $x += $i - 1;

                        // Rowspan -> MERGEROW
                        if( isset($cell->htmlAttributes['rowspan']) ){
                            $mergerow = $cell->htmlAttributes['rowspan'];
                        }

                        for($i = 1; $i <= ($mergerow - 1); $i++ ){
                            $table[$p][$r + $i][$acumulateMergecol - 1] = "MERGEROW";
                        }


                        $x++;
                    }
                }
            }
        }

        return $table;
    }

    public function getHtmlTable(){
        $html = "<table";
        $html .= $this->htmlAttributes;
        $html .= ">";
        $html .= $this->header->getHtml();
        $html .= $this->footer->getHtml();
        $html .= $this->body->getHtml();
        $html .= "</table>";
        
        return $html;
    }



    /*
     * Todo: mejorar este metodo
     */
    public function setHtmlAttribute($attribute, $value, $selection = null){
        if($selection == null){

            $this->htmlAttributes[$attribute] = $value;
        }else{
            
            $cellSelected = $this->directionInterpret->interpret($selection);

            $parts = array($this->header, $this->body, $this->footer);            

            foreach($parts as $part){
                if($cellSelected['CI']['F'] >= 0){
                    if($part->countRows() > 0 && $part->countRows() >= ($cellSelected['CI']['F'] + 1)){
                        $vF = (($cellSelected['CF']['F'] + 1) > $part->countRows())?$part->countRows():($cellSelected['CF']['F'] + 1);
                        
                        for($i = $cellSelected['CI']['F']; $i < $vF; $i++){
                            for($j = $cellSelected['CI']['C']; $j <= $cellSelected['CF']['C']; $j++){
                                $part[$i][$j]->htmlAttributes[$attribute] = $value;
                            }
                        }
                    }

                    $cellSelected['CI']['F'] -= $part->countRows();
                    $cellSelected['CF']['F'] -= $part->countRows();
                }
            }
        }
    }

    /*
     * Todo: Mejorar este metodo
     */
    public function merge($selection){
        $cellSelected = $this->directionInterpret->interpret($selection);
        
        //utils::pre($cellSelected, false);

        /*
         * Mejorar las siguiente seccion
         */
        $parts = array($this->header, $this->body, $this->footer);

        $colspan = ($cellSelected['CF']['C'] - $cellSelected['CI']['C']) + 1;
        $rowspan = ($cellSelected['CF']['F'] - $cellSelected['CI']['F']) + 1;

        /*
         * Aplica el colspan y rowspan
         */
        $vF = $cellSelected['CI']['F'];

        if($colspan > 1 || $rowspan > 1){
            foreach($parts as $part){
                if($vF >= 0){
                    if(isset($part[$vF]) && isset($part[$vF][$cellSelected['CI']['C']])){
                        if($colspan > 1)
                            $part[$vF][$cellSelected['CI']['C']]->htmlAttributes->colspan = $colspan;
                        if($rowspan > 1)
                            $part[$vF][$cellSelected['CI']['C']]->htmlAttributes->rowspan = $rowspan;
                    }
                    $vF -= $part->countRows();
                }
            }
        }

        $vF = $cellSelected['CI']['F'];

        foreach($parts as $part){
            if($vF >= 0){
                if($part->countRows() > 0 && $part->countRows() >= ($vF + 1) && $part->countColumns() > 0 && $part->countColumns() >= ($cellSelected['CF']['C'] + 1)){
                    // Seleccionar el final de la parte si el final de seleccion es mayor
                    $cF = (($cellSelected['CF']['F'] + 1) > $part->countRows())?$part->countRows():($cellSelected['CF']['F'] + 1);
                    for($i = $vF; $i < $cF; $i++){
                        // Evitar eliminar solo la primera celda de la primera fila
                        $iC = ($i != $vF)?$cellSelected['CI']['C']:($cellSelected['CI']['C'] + 1);
                        for($j = $iC; $j <= $cellSelected['CF']['C']; $j++){
                            unset($part[$i][$j]);
                        }
                    }
                }

                $vF -= $part->countRows();
                $cellSelected['CF']['F'] -= $part->countRows();
            }
        }

        
    }


    /*
     * ArrayAccess
     */
    /*
     * Creo que este quedo bien.
     */
    public function offsetSet($offset, $value) {
        $selectionCell = $this->directionInterpret->interpret($offset);

        $vFi = $selectionCell['CI']['F'];
        $vFf = $selectionCell['CF']['F'];

        foreach($this->parts as $k => $part){
            $rows = $part->countRows();
            $columns = $part->countColumns();
            
            if($vFi >= 0 && $rows > 0 && $rows > $vFi && $columns > $selectionCell['CI']['C']){
                $fF = $vFf <= $rows?$vFf:$rows;
                for($f = $vFi; $f <= $fF; $f++){
                    for($c = $selectionCell['CI']['C']; $c <= $selectionCell['CF']['C']; $c++){
                        if(isset($part[$f][$c]) == true){
                            $part[$f][$c]->setValue($value);
                        }else{
                            $part[$f][$c] = new Cell($value);
                        }
                    }
                }
            }
            $vFi -= $rows;
            $vFf -= $rows;
        }
    }
    /*
     * Todo: Mejorar este medotodo. Puede que regrese una array de valores boleanos 
     */
    public function offsetExists($offset) {
        $selectionCell = $this->directionInterpret->interpret($selection);

        $isset= false;

        $vFi = $selectionCell['CI']['F'];
        $vFf = $selectionCell['CF']['F'];

        foreach($this->parts as $k => $part){
            $rows = $part->countRows();
            $columns = $part->countColumns();

            if($vFi >= 0 && $rows > 0 && $rows > $vFi && $columns > $selectionCell['CI']['C']){
                $fF = $vFf <= $rows?$vFf:$rows;
                for($f = $vFi; $f <= $fF; $f++){
                    for($c = $selectionCell['CI']['C']; $c <= $selectionCell['CF']['C']; $c++){
                        $isset = isset($part[$f][$c]);
                    }
                }
            }
            $vFi -= $rows;
            $vFf -= $rows;
        }

        return $isset;
        //return isset($this->cells[$offset]);
    }
    /*
     * 
     */
    public function offsetUnset($offset) {

        $selectionCell = $this->directionInterpret->interpret($selection);

        $vFi = $selectionCell['CI']['F'];
        $vFf = $selectionCell['CF']['F'];

        foreach($this->parts as $k => $part){
            $rows = $part->countRows();
            $columns = $part->countColumns();

            if($vFi >= 0 && $rows > 0 && $rows > $vFi && $columns > $selectionCell['CI']['C']){
                $fF = $vFf <= $rows?$vFf:$rows;
                for($f = $vFi; $f <= $fF; $f++){
                    for($c = $selectionCell['CI']['C']; $c <= $selectionCell['CF']['C']; $c++){
                        unset($part[$f][$c]);
                    }
                }
            }
            $vFi -= $rows;
            $vFf -= $rows;
        }
    }
    
    /*
     * Todo: Mejorar este metodo.
     */
    public function offsetGet($offset) {
        //return isset($this->cells[$offset]) ? $this->cells[$offset] : null;
        $rows = array();

        $cells = array();

        $selectionCell = $this->directionInterpret->interpret($selection);

        $vFi = $selectionCell['CI']['F'];
        $vFf = $selectionCell['CF']['F'];

        $i = 0;

        foreach($this->parts as $k => $part){
            $rows = $part->countRows();
            $columns = $part->countColumns();

            if($vFi >= 0 && $rows > 0 && $rows > $vFi && $columns > $selectionCell['CI']['C']){
                $fF = $vFf <= $rows?$vFf:$rows;
                for($f = $vFi; $f <= $fF; $f++){
                    for($c = $selectionCell['CI']['C']; $c <= $selectionCell['CF']['C']; $c++){
                        $cells[$i][] = $part[$f][$c];
                    }
                    $i++;
                }
            }
            $vFi -= $rows;
            $vFf -= $rows;
        }

        return $cells;
    }

}
?>