<?php
/**
 * УЧЕБНЫЙ ПРИМЕР: Оптимизация поиска в массиве
 * 
 * ЗАДАЧА:
 * У нас есть список из 50 000 "товаров" (чисел).
 * Нам нужно найти конкретный товар и проверить, есть ли он в списке.
 * 
 * ВНИМАНИЕ: Код ниже написан специально МЕДЛЕННО.
 * Ваша задача - ускорить его.
 */

// 1. ГЕНЕРАЦИЯ ДАННЫХ (Эту часть менять не нужно)

function generateProducts(int $count): array {
    $products = [];
    for ($i = 0; $i < $count; $i++) {
        $products[] = [
            'id' => $i,
            'name' => 'Product_' . $i,
            'price' => rand(100, 10000)
        ];
    }
    return $products;
}

// ---------------------------------------------------------
// ВАРИАНТ 1: МЕДЛЕННЫЙ КОД
// ---------------------------------------------------------

function searchProductSlow($products, $searchId) {
    // ПРОБЛЕМА: Мы перебираем ВЕСЬ массив в цикле, даже если нашли товар в начале.
    // Это линейный поиск O(N).
    foreach ($products as $product) {
        if ($product['id'] == $searchId) {
            return $product;
        }
    }
    return null;
}

// ---------------------------------------------------------
// ВАРИАНТ 2: БЫСТРЫЙ КОД
// ---------------------------------------------------------

function searchProductFast($products, $searchId, $productsIndex) {
    // ЗАДАНИЕ:
    // Перепишите эту функцию так, чтобы она работала быстрее.
    // Подсказка 1: Используйте array_column() и array_search()
    // Подсказка 2: Или преобразуйте массив в ассоциативный, где ключ = ID
    // Подсказка 3: Используйте isset() для проверки существования
    
    // --- НАЧНИТЕ ИЗМЕНЕНИЯ ЗДЕСЬ ---
    
    // Преобразуется массив в ассоциативный, где ключ = ID (Подсказка 2)
    // Используется isset() для проверки существования (Подсказка 3)
    if (isset($productsIndex[$searchId])) {
        return $productsIndex[$searchId];
    }
    
    // --- КОНЕЦ ИЗМЕНЕНИЙ ---
    
    return null;
}

// ---------------------------------------------------------
// ТЕСТИРОВАНИЕ И ЗАМЕРЫ ВРЕМЕНИ
// ---------------------------------------------------------

// Генерируем данные
echo "Генерация базы данных товаров...\n";
$products = generateProducts(50000);
echo "Товаров создано: " . count($products) . "\n\n";

// СОЗДАЕМ ИНДЕКС ОДИН РАЗ (вне функции)
echo "Создание индекса для быстрого поиска...\n";
$productsIndex = [];
foreach ($products as $product) {
    $productsIndex[$product['id']] = $product;
}
echo "Индекс создан\n\n";

// Искомый ID (где-то в конце списка, чтобы замедлить плохой алгоритм)
$searchId = 49999; 

echo "--- ТЕСТ 1: Медленный поиск ---\n";
$start1 = microtime(true);

$result1 = searchProductSlow($products, $searchId);

$end1 = microtime(true);
$time1 = $end1 - $start1;

if ($result1) {
    echo "Найден товар: " . $result1['name'] . "\n";
} else {
    echo "Товар не найден\n";
}
echo "Время выполнения: " . number_format($time1 * 1000, 4) . " мс\n\n";


echo "--- ТЕСТ 2: Быстрый поиск (индекс + isset) ---\n";
$start2 = microtime(true);

$result2 = searchProductFast($products, $searchId, $productsIndex);

$end2 = microtime(true);
$time2 = $end2 - $start2;

if ($result2) {
    echo "Найден товар: " . $result2['name'] . "\n";
} else {
    echo "Товар не найден\n";
}
echo "Время выполнения: " . number_format($time2 * 1000, 4) . " мс\n\n";


// ---------------------------------------------------------
// ИТОГ
// ---------------------------------------------------------
echo "=== РЕЗУЛЬТАТЫ ===\n";
echo "Медленный: " . number_format($time1 * 1000, 4) . " мс\n";
echo "Быстрый:   " . number_format($time2 * 1000, 4) . " мс\n";

if ($time1 > 0) {
    $speedup = $time1 / $time2;
    echo "Ускорение в: " . number_format($speedup, 2) . " раз(а)\n";
}