<?php
/**
 * Description of HeaderTableclass
 *
 * @author Universidad Colima
 */
class HeadTable extends TableContent {
    
    public function getHtml() {
        $html = "\t<thead>\n";
        foreach($this->content as $row){
            $html.= $row->getHtml();
        }
        $html .= "\t</thead>\n";

        return $html;
    }
}
?>