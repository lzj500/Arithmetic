<?php
/**
 * Created by PhpStorm.
 * User: htj
 * Date: 2018/9/27
 * Time: 11:06
 */
namespace Arithmetic\Count;
/**
*  处理计算公式
*/
class Count
{
    //运算符栈,在栈里插入最小优先级的运算符，避免进入不到去运算符比较的循环里
    protected $opArr = ['#'];
    //运算数栈;
    protected $oprandArr = [];
    //运算符优先级
    protected $opLevelArr = [
            ')' => 2,
            '(' => 3,
            '+' => 4,
            '-' => 4,
            '*' => 5,
            '/' => 5,
            '#' => 1
    ];

    protected $bolanExprArr = [];
    protected $exprLen=0;
    protected $inop = false;
    protected $opNums = "";
    protected $expr="";
    /**
     * Notes: 百分号转数字 查找替换
     * author 何腾骥
     * Date: 2018/9/27
     * Time: 11:51
     * @param $expr
     * @return mixed
     */
    protected function doReplace($expr)
    {
        preg_match_all("/\d+(\.\d+)?%/",$expr,$arr);
        if(isset($arr[0]) && !empty($arr[0])){
            foreach ($arr[0] as $val){
                $expr = str_replace($val,(float) $val /100 ,$expr);
            }
            return $expr;
        }
        return $expr;

    }

    /**
     * Notes: 处理 计算
     * author 何腾骥
     * Date: 2018/9/27
     * Time: 11:53
     */
    public  function makeCount($expr)
    {
        error_reporting(0);
        // 处理百分号
        $this->expr = $this->doReplace($expr);
        // 计算长度
        $this->exprLen = strlen($expr);
        //解析表达式
        for($i = 0;$i <= $this->exprLen;$i++){
            $char = $this->expr[$i];
            //获取当前字符的优先级
            $level = intval($this->opLevelArr[$char]);
            //如果大于0，表示是运算符，否则是运算数，直接输出
            if($level > 0){
                $this->inop = true;
                //如果碰到左大括号，直接入栈
                if($level == 3){
                    array_push($this->opArr,$char);continue;
                }
                //与栈顶运算符比较，如果当前运算符优先级小于栈顶运算符，则栈顶运算符弹出，一直到当前运算符优先级不小于栈顶
                while($op = array_pop($this->opArr)){
                    if($op){
                        $currentLevel = intval($this->opLevelArr[$op]);
                        if($currentLevel == 3 && $level == 2) {
                            break;
                        }elseif($currentLevel >= $level && $currentLevel != 3){
                            array_push($this->bolanExprArr,$op);
                        }else{
                            array_push($this->opArr,$op);
                            array_push($this->opArr,$char);
                            break;
                        }
                    }
                }
            }else{
                //多位数拼接成一位数
                $this->opNums .= $char;
                if($this->opLevelArr[$this->expr[$i+1]] > 0){
                    array_push($this->bolanExprArr, $this->opNums);
                    $this->opNums = "";
                }
            }
        }
        array_push($this->bolanExprArr, $this->opNums);

        //输出剩余运算符
        while($leftOp = array_pop($this->opArr)){
            if($leftOp != '#'){
                array_push($this->bolanExprArr,$leftOp);
            }
        }

        //计算逆波兰表达式。
        foreach($this->bolanExprArr as $v){
            if(!isset($this->opLevelArr[$v])){
                array_push($this->oprandArr,$v);
            }else{
                $op1 = array_pop($this->oprandArr);
                $op2 = array_pop($this->oprandArr);

                eval("\$result = $op2 $v $op1;");

                array_push($this->oprandArr,$result);
            }
        }
        return $result;
    }

}

;




