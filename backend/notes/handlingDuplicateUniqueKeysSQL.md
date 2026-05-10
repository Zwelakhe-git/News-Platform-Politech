В MySQL нет стандартного `ON CONFLICT` (это синтаксис PostgreSQL). В MySQL используются три разных подхода:

## 1. **ON DUPLICATE KEY UPDATE** (самый распространенный)

Обновляет существующую запись при конфликте с PRIMARY KEY или UNIQUE ключом:

```sql
-- Вставка или обновление
INSERT INTO users (id, email, username, last_login) 
VALUES (1, 'user@example.com', 'john', NOW())
ON DUPLICATE KEY UPDATE 
    email = VALUES(email),
    username = VALUES(username),
    last_login = VALUES(last_login),
    updated_at = NOW();
```

### **Сложный пример с расчетами**

```sql
INSERT INTO stats (user_id, views, last_view) 
VALUES (123, 1, NOW())
ON DUPLICATE KEY UPDATE 
    views = views + VALUES(views),  -- увеличиваем существующее значение
    last_view = VALUES(last_view);   -- обновляем дату
```

### **Использование с составными ключами**

```sql
CREATE TABLE user_roles (
    user_id INT,
    role_id INT,
    assigned_at DATETIME,
    PRIMARY KEY (user_id, role_id)  -- составной PRIMARY KEY
);

INSERT INTO user_roles (user_id, role_id, assigned_at) 
VALUES (1, 5, NOW())
ON DUPLICATE KEY UPDATE 
    assigned_at = NOW();  -- обновляем дату если связь уже существует
```

## 2. **REPLACE** (удаляет и вставляет)

Полностью заменяет запись (DELETE + INSERT):

```sql
-- Удалит существующую запись и вставит новую
REPLACE INTO users (id, email, username) 
VALUES (1, 'newemail@example.com', 'john_updated');

-- Внимание: все поля, не указанные в запросе, будут установлены в DEFAULT!
-- Это может привести к потере данных
```

### **Когда использовать REPLACE**

```sql
-- Хорошо для простых кэшей или временных данных
REPLACE INTO cache (key, value, expires_at) 
VALUES ('user_123_profile', '{"name":"John"}', DATE_ADD(NOW(), INTERVAL 1 HOUR));
```

## 3. **INSERT IGNORE** (игнорирует конфликт)

Пропускает вставку при конфликте, не вызывая ошибку:

```sql
-- Просто игнорирует, если запись существует
INSERT IGNORE INTO users (id, email, username) 
VALUES (1, 'user@example.com', 'john');

-- Возвращает 0 affected rows, если запись существует
```

### **Пример с массовой вставкой**

```sql
-- Добавляем только новые записи, существующие пропускаем
INSERT IGNORE INTO tags (name, created_at) 
VALUES 
    ('php', NOW()),
    ('mysql', NOW()),
    ('javascript', NOW());
```

## 4. **Сравнение подходов**

| Способ | Поведение | Сохраняет данные | Возвращает ID | Использование |
|--------|-----------|-----------------|---------------|---------------|
| `ON DUPLICATE KEY UPDATE` | UPDATE существующей | ✅ Да | `LAST_INSERT_ID()` обновляется | Предпочтительный |
| `REPLACE` | DELETE + INSERT | ❌ Нет (кроме указанных) | Новый ID (если AUTO_INCREMENT) | Только для замены целиком |
| `INSERT IGNORE` | Пропуск вставки | N/A | Не вставляет | Для игнорирования дубликатов |

## 5. **Практические примеры**

### **API ключи с ограничением количества**

```sql
-- Максимум 5 активных ключей на пользователя
INSERT INTO api_keys (user_id, key_hash, created_at) 
VALUES (1, 'hashed_key_123', NOW())
ON DUPLICATE KEY UPDATE 
    -- Если ключ уже существует, обновляем время
    created_at = VALUES(created_at);

-- А проверку лимитов делаем отдельно
DELETE FROM api_keys 
WHERE user_id = 1 
AND id NOT IN (
    SELECT id FROM (
        SELECT id FROM api_keys 
        WHERE user_id = 1 
        ORDER BY created_at DESC 
        LIMIT 5
    ) tmp
);
```

### **Счетчики и агрегаты**

```sql
-- Обновление статистики
INSERT INTO daily_stats (date, user_id, page_views, unique_visitors) 
VALUES (CURDATE(), 123, 1, 1)
ON DUPLICATE KEY UPDATE 
    page_views = page_views + 1,
    unique_visitors = unique_visitors + VALUES(unique_visitors);
```

### **Получение ID после upsert**

```php
// Вставка или обновление с получением ID
$stmt = $pdo->prepare("
    INSERT INTO users (email, username) 
    VALUES (?, ?)
    ON DUPLICATE KEY UPDATE 
        username = VALUES(username),
        updated_at = NOW()
");

$stmt->execute(['user@example.com', 'john']);

// Получаем ID (работает и для INSERT, и для UPDATE)
$userId = $pdo->lastInsertId();

if ($userId == 0) {
    // Если была UPDATE, lastInsertId() может вернуть 0
    // Нужно получить ID по email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['user@example.com']);
    $userId = $stmt->fetchColumn();
}
```

## 6. **Альтернативы для разных СУБД**

```sql
-- PostgreSQL
INSERT INTO users (id, email) 
VALUES (1, 'user@example.com')
ON CONFLICT (id) DO UPDATE 
SET email = EXCLUDED.email;

-- MySQL
INSERT INTO users (id, email) 
VALUES (1, 'user@example.com')
ON DUPLICATE KEY UPDATE 
email = VALUES(email);

-- SQLite
INSERT INTO users (id, email) 
VALUES (1, 'user@example.com')
ON CONFLICT(id) DO UPDATE SET email = excluded.email;

-- Microsoft SQL Server
MERGE INTO users AS target
USING (SELECT 1 AS id, 'user@example.com' AS email) AS source
ON target.id = source.id
WHEN MATCHED THEN UPDATE SET email = source.email
WHEN NOT MATCHED THEN INSERT (id, email) VALUES (source.id, source.email);
```

## 7. **Важные нюансы**

### **AUTO_INCREMENT при ON DUPLICATE KEY UPDATE**

```sql
-- При UPDATE счетчик AUTO_INCREMENT может увеличиваться
INSERT INTO users (email) VALUES ('test@example.com')
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- Проблема: каждый конфликт увеличивает AUTO_INCREMENT
-- Решение: использовать отдельную таблицу для счетчиков или IGNORE
```

### **Множественные уникальные ключи**

```sql
CREATE TABLE users (
    id INT PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    username VARCHAR(255) UNIQUE
);

-- При конфликте с ЛЮБЫМ уникальным ключом сработает UPDATE
INSERT INTO users (id, email, username) 
VALUES (1, 'user@example.com', 'john')
ON DUPLICATE KEY UPDATE 
    email = VALUES(email),
    username = VALUES(username);
```

### **Условное обновление**

```sql
-- Обновляем только если дата изменилась
INSERT INTO articles (slug, title, content, updated_at) 
VALUES ('my-article', 'Title', 'Content', NOW())
ON DUPLICATE KEY UPDATE 
    title = VALUES(title),
    content = VALUES(content),
    updated_at = IF(
        title != VALUES(title) OR content != VALUES(content),
        VALUES(updated_at),
        updated_at
    );
```

## 8. **Рекомендации**

1. **Используйте `ON DUPLICATE KEY UPDATE`** для большинства случаев upsert
2. **Избегайте `REPLACE`** если не хотите потерять данные
3. **`INSERT IGNORE`** используйте только когда точно нужно игнорировать дубликаты
4. **Всегда проверяйте `affected_rows`** чтобы понять, была вставка или обновление
5. **Для сложной логики** используйте транзакции с SELECT перед INSERT

```php
// Пример с транзакцией для сложной логики
$pdo->beginTransaction();

try {
    // Проверяем существование
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? FOR UPDATE");
    $stmt->execute(['user@example.com']);
    $user = $stmt->fetch();
    
    if ($user) {
        // Обновляем
        $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->execute(['john_updated', $user['id']]);
        $userId = $user['id'];
    } else {
        // Вставляем
        $stmt = $pdo->prepare("INSERT INTO users (email, username) VALUES (?, ?)");
        $stmt->execute(['user@example.com', 'john_updated']);
        $userId = $pdo->lastInsertId();
    }
    
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    throw $e;
}
```