function classifyAge(int  $age): string {
	if ($age > 17){
		return "Взрослый";
	}
	elseif ($age > 11){
                return "Подросток";
        }
	else {
		return "Ребенок";
	}
}

echo classifyAge(8);
echo classifyAge(15);
echo classifyAge(25);

$cities = ["Москва", "Санкт-Петербург", "Казань", "Екатеринбург", "Чита"]
foreach ($city in $cities){
	echo $city . "\n";
}

for ($i = 0; $i < 100, $i++){
	if($i % 3 == 0 && $i % 5 ==0){
		echo "FizzBuzz";
	}
	elseif ($i % 3 == 0){
		echo "Fizz";
	}
	elseif ($i % 5 == 0){
		echo "Buzz";
	}
}

function convertCelciusToFarenheit(float $celsius): float{
	return (($celsius * 9 / 5) + 32);
}

echo convertCelsiusToFarenheit(0);
echo convertCelsiusToFarenheit(25);
echo convertCelsiusToFarenheit(-10);
echo convertCelsiusToFarenheit(100);
