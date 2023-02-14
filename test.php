<?php
// Функция с двумя параметрами возвращает строку
function someFunction(bool $one, int $two = 123, ): string
{
    return $one . $two;
}
// Создаём объект рефлексии
// Передаём ему имя интересующей нас функции
$reflection = new ReflectionFunction('someFunction');
// Получаем тип возвращаемого функцией значения

echo '<pre>';
var_dump($reflection->getParameters());
echo '</pre>';