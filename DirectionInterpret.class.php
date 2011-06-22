<?php
class DirectionInterpret /*extends Singleton */{


    private $letters = array(
        "A" => 1,
        "B" => 2,
        "C" => 3,
        "D" => 4,
        "E" => 5,
        "F" => 6,
        "G" => 7,
        "H" => 8,
        "I" => 9,
        "J" => 10,
        "K" => 11,
        "L" => 12,
        "M" => 13,
        "N" => 14,
        "O" => 15,
        "P" => 16,
        "Q" => 17,
        "R" => 18,
        "S" => 19,
        "T" => 20,
        "U" => 21,
        "V" => 22,
        "W" => 23,
        "X" => 24,
        "Y" => 25,
        "Z" => 26
    );

    const Base = 26;


    public function interpret($direction){

        if(strpos($direction,":") === false){
            $cellInicial = $this->getRowColumn($direction);
            $cellFinal = $cellInicial;
        }else{
            list($inicio, $fin) = explode(":", $direction);
            $cellInicial = $this->getRowColumn($inicio);
            $cellFinal = $this->getRowColumn($fin);
        }
        $D = array("CI" => $cellInicial, "CF" => $cellFinal);
        
        return $D;
    }

    public function getRowColumn($cellDirection){
        // Separar letras de numeros
        preg_match("/([a-zA-Z]+)([0-9]+)/",$cellDirection,$parts);
        // Convertir letras en numero de columna
        $c = $this->letterToNum($parts[1]);
        // Crear arreglo con la fila y la columna de la celda
        $RowColumn = array("F" => ($parts[2] - 1), "C" => $c);
        return $RowColumn;
    }

    public function letterToNum($letters){
        $posiciones = strlen($letters);
        
        $c = 0;
        for($i = 0; $i < $posiciones; $i++){
            $c += pow(DirectionInterpret::Base, $i) * $this->letters[strtoupper($letters[$i])];
        }
        
        return ($c-1);
    }
}
?>