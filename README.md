支持字符串运算符 + - * /  数字支持百分数 小数点

$model = new ArithmeticCount();

$expr = '1+1+1+1*0.50%+50%';

$expr = '2+45+6/2-45+(23-4)*5';

echo $model->makeCount($expr)