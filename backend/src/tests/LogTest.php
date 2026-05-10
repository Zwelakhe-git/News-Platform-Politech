<?php
namespace Thunderpc\Vkurse\Tests;

require_once __DIR__ . '/../../vendor/autoload.php';
use Thunderpc\Vkurse\Utils\Log;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase{
    private string $msg;
    
    protected function setUp(): void{
        $this->msg = 'Log Test';
        Log::init();
    }

    public function testInfoLog(): void{
        Log::info($this->msg);
        $this->assertTrue(empty(error_get_last()));
    }
    public function testWarnLog(): void{
        Log::warn($this->msg);
        $this->assertTrue(empty(error_get_last()));
    }
    public function testErrorLog(): void{
        Log::error($this->msg);
        $this->assertTrue(empty(error_get_last()));
    }
}
?>