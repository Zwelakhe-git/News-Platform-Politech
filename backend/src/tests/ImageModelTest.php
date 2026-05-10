<?php
namespace Thunderpc\Vkurse\Tests;

require_once __DIR__ . '/../../vendor/autoload.php';

use Thunderpc\Vkurse\Models\ImageModel;
use PHPUnit\Framework\TestCase;

class ImageModelTest extends TestCase{
    private $model;
    
    protected function setUp(): void{
        $this->model = new ImageModel();
    }

    public function testModelCreate(): void{
        $this->assertNotNull($this->model, "Failed to create image model");
    }
}
?>