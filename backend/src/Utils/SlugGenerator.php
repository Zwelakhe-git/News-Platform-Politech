<?php
namespace Thunderpc\Vkurse\Utils;

class SlugGenerator{
    /**
     * Генерирует slug из заголовка
     * Пример: "Привет мир!" -> "privet-mir"
     */
    public static function generate(string $title): string
    {
        // Транслитерация (кириллица -> латиница)
        $slug = self::transliterate($title);
        
        // Приводим к нижнему регистру
        $slug = mb_strtolower($slug);
        
        // Заменяем пробелы и спецсимволы на дефисы
        $slug = preg_replace('/[^\p{L}\p{N}]+/u', '-', $slug);
        
        // Удаляем дефисы в начале и конце
        $slug = trim($slug, '-');
        
        // Ограничиваем длину (обычно 100-200 символов)
        $slug = mb_substr($slug, 0, 100);
        
        return $slug;
    }
    
    /**
     * Простая транслитерация
     */
    private static function transliterate(string $string): string
    {
        $rules = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        ];
        
        return strtr(mb_strtolower($string), $rules);
    }
    
    /**
     * Проверяет уникальность slug и генерирует с суффиксом при необходимости
     */
    public static function makeUnique(string $title, callable $existsCheck): string
    {
        $baseSlug = self::generate($title);
        $slug = $baseSlug;
        $counter = 1;
        
        while ($existsCheck($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}
/*
// Использование
$title = "Как создать API ключи в PHP";
$slug = SlugGenerator::generate($title);
// Результат: "kak-sozdat-api-klyuchi-v-php"

// С проверкой уникальности
$slug = SlugGenerator::makeUnique($title, function($slug) use ($db) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM articles WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetchColumn() > 0;
});*/
?>