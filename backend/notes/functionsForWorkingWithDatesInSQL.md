В SQL есть специальные функции для работы с датами и интервалами. Складывать `timestamp` с `int` напрямую нельзя (в отличие от Unix timestamp), нужно использовать правильные конструкции.

## 1. **MySQL функции для работы с датами**

### **DATE_ADD / DATE_SUB** (добавление/вычитание интервалов)

```sql
-- Ваш случай: fetched_at + update_interval < current_timestamp
SELECT * FROM your_table 
WHERE DATE_ADD(fetched_at, INTERVAL update_interval MINUTE) < NOW();

-- Или с разными единицами
WHERE DATE_ADD(fetched_at, INTERVAL update_interval SECOND) < NOW();
WHERE DATE_ADD(fetched_at, INTERVAL update_interval HOUR) < NOW();
WHERE DATE_ADD(fetched_at, INTERVAL update_interval DAY) < NOW();
```

### **TIMESTAMPADD** (альтернативный синтаксис)

```sql
-- Более гибкая функция
SELECT * FROM your_table 
WHERE TIMESTAMPADD(MINUTE, update_interval, fetched_at) < NOW();

-- Единицы: MICROSECOND, SECOND, MINUTE, HOUR, DAY, WEEK, MONTH, QUARTER, YEAR
```

### **INTERVAL в арифметике**

```sql
-- Более короткий синтаксис (только с константами, не с полями!)
WHERE fetched_at + INTERVAL 30 MINUTE < NOW();

-- С полем нужно использовать DATE_ADD или TIMESTAMPADD
WHERE fetched_at + INTERVAL update_interval MINUTE; -- ОШИБКА! update_interval не поддерживается
```

## 2. **PostgreSQL функции**

```sql
-- PostgreSQL (более элегантный синтаксис)
SELECT * FROM your_table 
WHERE fetched_at + (update_interval * INTERVAL '1 minute') < CURRENT_TIMESTAMP;

-- Или с использованием make_interval
WHERE fetched_at + make_interval(mins => update_interval) < CURRENT_TIMESTAMP;

-- Или через NOW() + interval
WHERE fetched_at + (update_interval || ' minutes')::INTERVAL < NOW();
```

## 3. **Практические примеры**

### **Ваш конкретный случай**

```sql
-- MySQL (если update_interval в минутах)
SELECT * FROM api_data 
WHERE DATE_ADD(fetched_at, INTERVAL update_interval MINUTE) < NOW();

-- Если update_interval в секундах
SELECT * FROM api_data 
WHERE DATE_ADD(fetched_at, INTERVAL update_interval SECOND) < NOW();

-- Если нужно учитывать NULL значения
SELECT * FROM api_data 
WHERE fetched_at IS NULL 
   OR DATE_ADD(fetched_at, INTERVAL COALESCE(update_interval, 3600) SECOND) < NOW();
```

### **Сравнение с Unix timestamp**

```sql
-- Если вы храните fetched_at как INT (Unix timestamp)
SELECT * FROM your_table 
WHERE fetched_at + update_interval < UNIX_TIMESTAMP();

-- Преобразование timestamp в Unix
SELECT * FROM your_table 
WHERE UNIX_TIMESTAMP(fetched_at) + update_interval < UNIX_TIMESTAMP();
```

## 4. **Другие полезные функции MySQL**

### **Текущее время**
```sql
NOW()           -- 2026-03-28 15:30:45
CURDATE()       -- 2026-03-28
CURTIME()       -- 15:30:45
UTC_TIMESTAMP() -- 2026-03-28 12:30:45 UTC
```

### **Извлечение частей даты**
```sql
YEAR(date)      -- год
MONTH(date)     -- месяц
DAY(date)       -- день
HOUR(time)      -- час
MINUTE(time)    -- минуты
DATE_FORMAT(date, '%Y-%m-%d') -- форматирование
```

### **Разница между датами**
```sql
DATEDIFF(date1, date2)     -- разница в днях
TIMEDIFF(time1, time2)     -- разница во времени
TIMESTAMPDIFF(MINUTE, date1, date2) -- разница в указанной единице
```

## 5. **Полный пример с проверкой устаревания данных**

```sql
-- Таблица
CREATE TABLE api_endpoints (
    id INT PRIMARY KEY,
    name VARCHAR(255),
    fetched_at DATETIME,
    update_interval INT DEFAULT 3600, -- в секундах
    is_active BOOLEAN DEFAULT TRUE
);

-- Выборка данных, которые нужно обновить
SELECT 
    id,
    name,
    fetched_at,
    update_interval,
    DATE_ADD(fetched_at, INTERVAL update_interval SECOND) AS next_update_at,
    TIMESTAMPDIFF(SECOND, fetched_at, NOW()) AS seconds_since_fetch,
    TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(fetched_at, INTERVAL update_interval SECOND)) AS seconds_until_update
FROM api_endpoints
WHERE is_active = TRUE
    AND (
        fetched_at IS NULL 
        OR DATE_ADD(fetched_at, INTERVAL update_interval SECOND) < NOW()
    );
```

## 6. **Индексация для производительности**

```sql
-- Создание индекса для ускорения таких запросов
CREATE INDEX idx_fetch_interval ON api_endpoints(fetched_at, update_interval);

-- Или виртуальная колонка (MySQL 5.7+)
ALTER TABLE api_endpoints 
ADD COLUMN next_update_at DATETIME 
GENERATED ALWAYS AS (DATE_ADD(fetched_at, INTERVAL update_interval SECOND)) STORED;

CREATE INDEX idx_next_update ON api_endpoints(next_update_at);

-- Тогда запрос будет быстрее
SELECT * FROM api_endpoints 
WHERE next_update_at < NOW();
```

## 7. **Варианты с использованием PHP**

Если логика простая, можно делать в PHP:

```php
// Получаем данные
$stmt = $pdo->query("SELECT id, fetched_at, update_interval FROM api_endpoints");
$rows = $stmt->fetchAll();

$toUpdate = array_filter($rows, function($row) {
    $nextUpdate = strtotime($row['fetched_at']) + $row['update_interval'];
    return $nextUpdate < time();
});

// Обновляем найденные
foreach ($toUpdate as $row) {
    // update logic
}
```

Но лучше делать в SQL для производительности:

```php
$stmt = $pdo->prepare("
    UPDATE api_endpoints 
    SET fetched_at = NOW(), data = ? 
    WHERE id = ? 
    AND (fetched_at IS NULL OR DATE_ADD(fetched_at, INTERVAL update_interval SECOND) < NOW())
");

foreach ($updates as $update) {
    $stmt->execute([$update['data'], $update['id']]);
    
    if ($stmt->rowCount() > 0) {
        echo "Обновлено: " . $update['id'] . "\n";
    }
}
```

## 8. **Особенности разных БД**

| Операция | MySQL | PostgreSQL | SQLite |
|----------|-------|------------|---------|
| Добавить интервал | `DATE_ADD(date, INTERVAL val MINUTE)` | `date + (val * INTERVAL '1 minute')` | `datetime(date, '+' \|\| val \|\| ' minutes')` |
| Текущее время | `NOW()` | `CURRENT_TIMESTAMP` | `CURRENT_TIMESTAMP` |
| Разница | `TIMESTAMPDIFF(MINUTE, d1, d2)` | `EXTRACT(EPOCH FROM (d2 - d1))/60` | `(strftime('%s', d2) - strftime('%s', d1))/60` |

**Рекомендация**: Используйте `DATE_ADD(fetched_at, INTERVAL update_interval MINUTE) < NOW()` для MySQL, это наиболее читаемый и производительный вариант.