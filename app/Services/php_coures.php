<?php
// العمليات الحسابية
$a = 10;
$b = 5;

echo "مجموع a و b: " . ($a + $b) . "\n"; // الجمع
echo "فرق a و b: " . ($a - $b) . "\n"; // الطرح
echo "حاصل ضرب a و b: " . ($a * $b) . "\n"; // الضرب
echo "القسمة a على b: " . ($a / $b) . "\n"; // القسمة
echo "باقي القسمة: " . ($a % $b) . "\n"; // باقي القسمة

// العمليات المنطقية
$x = true;
$y = false;

echo "x و y مع AND: " . ($x && $y ? "true" : "false") . "\n"; // و
echo "x أو y مع OR: " . ($x || $y ? "true" : "false") . "\n"; // أو
echo "عكس x مع NOT: " . (!$x ? "true" : "false") . "\n"; // نفي

// الشروط
$age = 20;
if ($age >= 18) {
    echo "أنت بالغ.\n";
} else {
    echo "أنت قاصر.\n";
}

// الحلقات
for ($i = 0; $i < 5; $i++) {
    echo "رقم الحلقة: $i\n"; // حلقة for
}

$j = 0;
while ($j < 5) {
    echo "رقم while: $j\n"; // حلقة while
    $j++;
}

$k = 0;
do {
    echo "رقم do while: $k\n"; // حلقة do-while
    $k++;
} while ($k < 5);

// المصفوفات
$fruits = ["x", "y", "z"];

foreach ($fruits as $fruit) {
    echo "الفاكهة: $fruit\n"; // التنقل عبر المصفوفة
}

// التوابع
// تابع لحساب مجموع رقمين
function sum($num1, $num2) {
    return $num1 + $num2;
}

echo "مجموع 4 و 6 هو: " . sum(4, 6) . "\n";

// تابع للتحقق مما إذا كان الرقم زوجيًا
function isEven($num) {
    return $num % 2 === 0 ? "زوجي" : "فردي";
}

echo "الرقم 7 هو: " . isEven(7) . "\n";

// تابع لطباعة رسالة ترحيب
function greet($name) {
    return "مرحبًا، " . $name . "!";
}

echo greet("alaa") . "\n";

// تابع لحساب عامل العدد (التكراري)
function factorial($n) {
    if ($n === 0) {
        return 1;
    }
    return $n * factorial($n - 1);
}

echo "عامل العدد 5 هو: " . factorial(5) . "\n";

?>
