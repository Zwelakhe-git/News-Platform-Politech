<?php
namespace Thunderpc\Vkurse\Tests;
ini_set('error_log', __DIR__ . '/../log/log.log');
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Thunderpc\Vkurse\Utils\Utils;

class StringTranslateTest extends TestCase{
    private $data_src;
    private Utils $utils;
    public function setUp(): void{
        $this->data_src = "abcde";
        $this->utils = new Utils();
    }

    public function testFullTranslation(): void{
        $repl_str = "efg";
        $result = $this->utils->translate($this->data_src, "abc", $repl_str);
        $expected = "efgde";
        $this->assertTrue($result === $expected);
    }
}
?>