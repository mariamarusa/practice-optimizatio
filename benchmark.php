<?php
/**
 * benchmark.php
 * Скрипт для замера производительности функций из index.php
 */

// 1. Подключаем логику
require_once 'index.php';

// Настройки теста
$PRODUCT_COUNT = 50000;   // Размер базы данных
$SEARCH_ID = 49999;       // Какой ID искать (в конце списка для усложнения)
$ITERATIONS = 100;        // Сколько раз повторить тест для точности

echo "Генерация базы данных товаров...\n";
$products = generateProducts($PRODUCT_COUNT);
echo "Сгенерировано товаров: {$PRODUCT_COUNT}\n";
echo "Ищем товар с ID: {$SEARCH_ID}\n\n";

// СОЗДАЕМ ИНДЕКС ОДИН РАЗ ДЛЯ БЫСТРОГО ПОИСКА
echo "Создание индекса для быстрого поиска...\n";
$productsIndex = [];
foreach ($products as $product) {
    $productsIndex[$product['id']] = $product;
}
echo "Индекс создан\n\n";

// ---------------------------------------------------------
// ЗАДАНИЕ: Реализуйте функцию runBenchmark
// ---------------------------------------------------------

/**
 * Измеряет время выполнения функции
 *
 * @param string $funcName Имя функции для вызова
 * @param array $args Аргументы функции
 * @param int $iterations Количество повторов
 * @return array Результаты замеров
 */
function runBenchmark(string $funcName, array $args, int $iterations): array {
    // 1. Запомните время старта (microtime(true))
    $startTime = microtime(true);
    
    // 2. Цикл от 0 до $iterations:
    //    call_user_func($funcName, ...$args);
    for ($i = 0; $i < $iterations; $i++) {
        call_user_func($funcName, ...$args);
    }
    
    // 3. Запомните время конца
    $endTime = microtime(true);
    
    // 4. Посчитайте общее время и среднее на один вызов (в мс)
    $totalTimeSec = $endTime - $startTime;
    $avgTimeMs = ($totalTimeSec / $iterations) * 1000;

    return [
        'total_sec' => $totalTimeSec,
        'avg_ms'    => $avgTimeMs,
        'iterations' => $iterations
    ];
}

// ---------------------------------------------------------
// ЗАПУСК ТЕСТОВ
// ---------------------------------------------------------

echo "=== ЗАПУСК БЕНЧМАРКА ===\n";

// Тест 1: Медленная функция
echo "Тестируем searchProductSlow...\n";
$resultSlow = runBenchmark('searchProductSlow', [$products, $SEARCH_ID], $ITERATIONS);

// Тест 2: Быстрая функция (с использованием индекса)
echo "Тестируем searchProductFast...\n";
$resultFast = runBenchmark('searchProductFast', [$products, $SEARCH_ID, $productsIndex], $ITERATIONS);

// ---------------------------------------------------------
// ВЫВОД РЕЗУЛЬТАТОВ
// ---------------------------------------------------------

echo "\n=== ОТЧЕТ ===\n";
printf("%-25s | %-15s | %-15s\n", "Функция", "Всего (сек)", "Среднее (мс)");
echo str_repeat("-", 60) . "\n";

printf("%-25s | %-15.4f | %-15.4f\n", "searchProductSlow", $resultSlow['total_sec'], $resultSlow['avg_ms']);
printf("%-25s | %-15.4f | %-15.4f\n", "searchProductFast", $resultFast['total_sec'], $resultFast['avg_ms']);

// Расчет ускорения
if ($resultFast['avg_ms'] > 0) {
    $ratio = $resultSlow['avg_ms'] / $resultFast['avg_ms'];
    echo "\n🚀 Ускорение: в " . number_format($ratio, 2) . " раз(а)\n";
} else {
    echo "\n⚡ Быстрый вариант выполнился мгновенно (< 0.0001 мс)\n";
}