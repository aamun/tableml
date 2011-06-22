<?php
/**
 * Description of FootTableclass
 *
 * @author Universidad Colima
 */
class FootTable extends TableContent {
    
    public function getHtml() {
        $html = "\t<tfoot>\n";
        foreach($this->content as $row){
            $html .= $row->getHtml();
        }
        $html .= "\t</tfoot>\n";

        return $html;
    }
}
?>