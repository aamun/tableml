<?php
/*
 * 
 */
class Calculation extends singleton{

        //	Operator Precedence
        //	This list includes all valid operators, whether binary (including boolean) or unary (such as %)
        //	Array key is the operator, the value is its precedence
        private $operatorPrecedence	= array('_' => 6,																//	Negation
                                        '%' => 5,																//	Percentage
                                        '^' => 4,																//	Exponentiation
                                        '*' => 3, '/' => 3, 													//	Multiplication and Division
                                        '+' => 2, '-' => 2,														//	Addition and Subtraction
                                        '&' => 1,																//	Concatenation
                                        '>' => 0, '<' => 0, '=' => 0, '>=' => 0, '<=' => 0, '<>' => 0			//	Comparison
        );

    protected function __construct(){}

    public static function getInstance() {
        return parent::getInstance(get_class());
    }

    public function calculate($formula, $parent){
//        $formula = $cell->getValue();

        // Quitar espacios
        $formula = trim($formula);
        if($this->isFormula($formula)){
           $value = $this->_calculate($formula, $parent);
        }else{
            $value = $formula;
        }

        return $value;
    }

    private function _calculate($formula, $parent){
        $formula = $this->formulaToPosfijo($formula);
        return $this->evalPosfijo($formula, $parent);
    }

    /*
     * Si comienza con '=' es una formula que hay que calculadar
     */
    public function isFormula($formula){
        if(strlen($formula) >= 1){
            return ($formula[0] != '=')?false:true;
        }else{
            return false;
        }
    }

    /*
     * Transforma la formula de la forma infijo a posfijo
     */
    public function formulaToPosfijo($formula){
        $formulaLenght = strlen(substr($formula, 1));
        if($formulaLenght > 1){
            
            $parse = new FormulaParser($formula);
            $pila = array(); // Variable auxiliar para transformar a posfijo
            $pFormula = array(); // resultado, la formula en posfijo.
            
            for ($i = 0; $i < $parse->getTokenCount(); $i++) {
                
                $token = $parse->getToken($i);

                switch($token->getTokenType()){
                    case FormulaToken::TOKEN_TYPE_OPERAND: // Si es un operando: numero o rango.
                        array_push($pFormula, $token->getValue());
                    break;
                    case FormulaToken::TOKEN_TYPE_SUBEXPRESSION: // Si es un parentesis
                        if($token->getTokenSubType() == FormulaToken::TOKEN_SUBTYPE_START){ // Si es parentesis izquierdo
                            array_push($pila, $token->getValue());
                        }else{
                            do{
                                $signo = array_pop($pila);
                                array_puch($pFormula, $signo);
                            }while(count($pila) >= 1 && $signo != "(" );
                        }
                    break;
                    case FormulaToken::TOKEN_TYPE_OPERATORINFIX:
                        // Mientras la pila no este vacía y su cima sea un operador de precedencia mayor o igual que la del token
                        while(count($pila) >= 1 && in_array($pila[0], $this->operatorPrecedence) == true && $this->operatorPrecedence[$pila[0]] >= $this->operatorPrecedence[$token->getValue()]){
                            //$signo = array_pop($pila);
                            array_push($pFormula, array_pop($pila));
                        }
                        array_push($pila, $token->getValue());
                    break;
                }
            }
            foreach($pila as $signo){
                array_push($pFormula, array_pop($pila));
            }

            $formula = implode($pFormula);
        }

        utils::pre($formula);

        return $formula;
    }

    /*
     * Ejecuta la formual
     */
    public function evalPosfijo($formula, $parent){
        
    }
    




}


//    /**	Constants				*/
//    /**	Regular Expressions		*/
//    //	Numeric operand
//    const CALCULATION_REGEXP_NUMBER		= '[-+]?\d*\.?\d+(e[-+]?\d+)?';
//    //	String operand
//    const CALCULATION_REGEXP_STRING		= '"(?:[^"]|"")*"';
//    //	Opening bracket
//    const CALCULATION_REGEXP_OPENBRACE	= '\(';
//    //	Function
//    const CALCULATION_REGEXP_FUNCTION	= '([A-Z][A-Z0-9\.]*)[\s]*\(';
//    //	Cell reference (cell or range of cells, with or without a sheet reference)
//    const CALCULATION_REGEXP_CELLREF	= '(((\w*)|(\'.*\')|(\".*\"))!)?\$?([a-z]+)\$?(\d+)(:\$?([a-z]+)\$?(\d+))?';
//    //	Named Range of cells
//    const CALCULATION_REGEXP_NAMEDRANGE	= '(((\w*)|(\'.*\')|(\".*\"))!)?([_A-Z][_A-Z0-9]*)';
//    //	Error
//    const CALCULATION_REGEXP_ERROR		= '\#[^!]+!';
//
//
//    public function __construct(){}
//
//
//    public function calculate(Cell $cell = null){
//        $this->calculateCellValue($cell);
//    }
//
//    public function calculateCellValue(Cell $cell = null){
//
//
//        if (is_null($cell)) {
//            return null;
//        }
//
//        $formula = $cell->getValue();
//        // Obtener el identificador de la celda ejemplo: CellID = A1
//        // $cellID = $cell->getCoordinate();
//
//        // return self::_unwrapResult($this->_calculateFormulaValue($formula, $cellID, $cell));
//        echo $this->_calculateFormulaValue($formula, null, $cell);
//        die();
//    }
//
//    public function _calculateFormulaValue($formula, $cellID = null, Cell $cell = null){
//
//        $cellValue = '';
//
//        $formula = trim($formula);
//        if($formula[0] != '=') return $formula; // _wrapResult($formula);
//        $formula = trim(substr($formula,1));
//        $formulaLength = strlen($formula);
//        if($formulaLength < 1) return $formula; // _wrapResult($formula);
//
//        // buscar en Cache ... pero en este caso aun no tendra cache
//
//        //$cellValue = $this->
//    }
//
//
//    // Convert infix to postfix notation
//    public function _parseFormula($formula){
//        // TODO: Convertir indicadores de matrices de excel {} en funcion MKMatrix
//
//        //	Binary Operators
//        //	These operators always work on two values
//        //	Array key is the operator, the value indicates whether this is a left or right associative operator
//        $operatorAssociativity	= array('^' => 0,								//	Exponentiation
//                                        '*' => 0, '/' => 0, 							//	Multiplication and Division
//                                        '+' => 0, '-' => 0,							//	Addition and Subtraction
//                                        '&' => 1,								//	Concatenation
//                                        '>' => 0, '<' => 0, '=' => 0, '>=' => 0, '<=' => 0, '<>' => 0		//	Comparison
//        );
//
//        //	Comparison (Boolean) Operators
//        //	These operators work on two values, but always return a boolean result
//        $comparisonOperators	= array('>', '<', '=', '>=', '<=', '<>');
//
//        //	Operator Precedence
//        //	This list includes all valid operators, whether binary (including boolean) or unary (such as %)
//        //	Array key is the operator, the value is its precedence
//        $operatorPrecedence	= array('_' => 6,																//	Negation
//                                        '%' => 5,																//	Percentage
//                                        '^' => 4,																//	Exponentiation
//                                        '*' => 3, '/' => 3, 													//	Multiplication and Division
//                                        '+' => 2, '-' => 2,														//	Addition and Subtraction
//                                        '&' => 1,																//	Concatenation
//                                        '>' => 0, '<' => 0, '=' => 0, '>=' => 0, '<=' => 0, '<>' => 0			//	Comparison
//        );
//
//
//        $stack = new Token_Stack(); // Clase interna que se comporta como una pila.
//
//        $output = array();
//
//
//
//    }
//}
//
//// for internal use
//class Token_Stack {
//
//	private $_stack = array();
//	private $_count = 0;
//
//	public function count() {
//		return $this->_count;
//	}
//
//	public function push($value) {
//		$this->_stack[$this->_count++] = $value;
//	}
//
//	public function pop() {
//		if ($this->_count > 0) {
//			return $this->_stack[--$this->_count];
//		}
//		return null;
//	}
//
//	public function last($n=1) {
//		if ($this->_count-$n < 0) {
//			return null;
//		}
//		return $this->_stack[$this->_count-$n];
//	}
//
//	function __construct() {
//	}
//
//}	//	class Token_Stack
?>