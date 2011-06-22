<?php
/**
 * Description of BodyTableclass
 *
 * @author Universidad Colima
 */
class BodyTable extends TableContent {
    
    public function getHtml() {
        $html = "\t<tbody $this->htmlAttributes >\n";
        foreach($this->content as $row){
            $html .= $row->getHtml();
        }
        $html .= "\t</tbody>\n";

        return $html;
    }
}
?>