<?php
/**
 * Description of CellThclass
 *
 * @author Universidad Colima
 */
class CellTh extends Cell {

    public function getHtml(){
        $html = "<th";
        $html .= $this->htmlAttributes;
        $html .= ">";
        // set Form control in html
        $html .= ($this->formControl != null)?$this->formControl:$this->value;

        $html .= "</th>\n";
        return $html;
    }
}
?>
