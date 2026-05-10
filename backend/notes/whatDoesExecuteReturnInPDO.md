Короткий ответ: **`execute()` возвращает `false` ТОЛЬКО при ошибке выполнения SQL-запроса**. Отсутствие результатов или неудачная вставка (например, нарушение ограничений) — это не ошибка выполнения, а логический результат.

# practical example
```php
public function getCategoryId($name){
    $stmt = $this->pdo->prepare('SELECT id FROM categories WHERE name = ?');
    $res = $stmt->execute([$name]);
    
    return $res ? $res->fetchColumn() : -1;
}
```
```shell
Error: Call to a member function fetchColumn() on true
```

## Для SELECT

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$result = $stmt->execute([999]); // всегда true при успешном выполнении

// Проверка наличия строк
$rows = $stmt->fetchAll(); // вернет [] (пустой массив), если строк нет
if (empty($rows)) {
    echo "Строки не найдены";
}
```

**Важно**: `execute()` вернет `false` только в случаях:
- Синтаксическая ошибка SQL
- Ошибка соединения
- Неверное количество параметров
- Другие фатальные ошибки БД

## Для INSERT

```php
$stmt = $pdo->prepare("INSERT INTO users (email) VALUES (?)");
$result = $stmt->execute(['duplicate@email.com']);

// execute() вернет true даже если:
// - вставка не произошла из-за дубликата (UNIQUE constraint)
// - нарушено NOT NULL ограничение

// Проверка количества затронутых строк
if ($stmt->rowCount() === 0) {
    echo "Вставка не выполнена (нарушены ограничения)";
}
```

## Правильный подход

```php
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    
    // Для SELECT
    $data = $stmt->fetchAll();
    if (count($data) === 0) {
        // Нет данных — это не ошибка execute()
    }
    
    // Для INSERT/UPDATE/DELETE
    $stmt = $pdo->prepare("INSERT INTO users (email) VALUES (?)");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() === 0) {
        // Ничего не вставлено (обычно из-за ограничений)
    }
    
} catch (PDOException $e) {
    // Реальная ошибка: синтаксис, соединение, и т.д.
    // execute() в этом случае не вызывается, исключение выбрасывается
    // (если PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
}
```

## Рекомендация

Всегда используйте `PDO::ERRMODE_EXCEPTION` в настройках:

```php
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

Тогда `execute()` никогда не вернет `false` — при ошибке будет выброшено исключение, что упрощает обработку.


Да, **для DELETE тоже нужно проверять `rowCount()`**, и это работает аналогично INSERT/UPDATE.

## DELETE с rowCount()

```php
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

$deletedRows = $stmt->rowCount();

if ($deletedRows === 0) {
    echo "Ни одна запись не удалена (запись с id=$id не найдена)";
} else {
    echo "Удалено записей: $deletedRows";
}
```

## Важные особенности

### 1. **DELETE без WHERE**
```php
$stmt = $pdo->prepare("DELETE FROM users"); // опасный запрос!
$stmt->execute();

echo $stmt->rowCount(); // вернет количество удаленных строк (0 или более)
```

### 2. **Внешние ключи (CASCADE)**
```php
// Если есть ON DELETE CASCADE, rowCount() покажет ТОЛЬКО
// количество удаленных строк в целевой таблице
$stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
$stmt->execute([1]);

echo $stmt->rowCount(); // 1 (даже если каскадно удалилось 100 товаров)
```

### 3. **Триггеры**
```php
// Триггеры НЕ влияют на rowCount()
// rowCount() возвращает только количество строк, 
// затронутых самим DELETE
```

## Полный пример с обработкой

```php
try {
    $pdo->beginTransaction();
    
    // DELETE
    $stmt = $pdo->prepare("DELETE FROM users WHERE status = ? AND created_at < ?");
    $stmt->execute(['inactive', '2025-01-01']);
    
    $deletedCount = $stmt->rowCount();
    
    if ($deletedCount === 0) {
        echo "Нет подходящих записей для удаления";
        $pdo->rollBack();
    } else {
        echo "Удалено записей: $deletedCount";
        $pdo->commit();
    }
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Ошибка: " . $e->getMessage();
}
```

## Итог по всем операциям

| Операция | execute() возвращает false? | Как проверить результат |
|----------|---------------------------|------------------------|
| **SELECT** | Только при ошибке SQL | `fetchAll()` — пустой массив = нет данных |
| **INSERT** | Только при ошибке SQL | `rowCount()` > 0 = вставлено |
| **UPDATE** | Только при ошибке SQL | `rowCount()` — сколько обновлено (может быть 0) |
| **DELETE** | Только при ошибке SQL | `rowCount()` — сколько удалено (может быть 0) |

**Ключевой момент**: `rowCount() === 0` для DELETE — это **нормальная ситуация**, а не ошибка. Это просто означает, что условие WHERE не нашло подходящих записей.


