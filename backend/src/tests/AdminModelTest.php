<?php
namespace Thunderpc\Vkurse\Tests;

require_once __DIR__ . '/../../vendor/autoload.php';

use Thunderpc\Vkurse\Models\AdminModel;
use PHPUnit\Framework\TestCase;

class AdminModelTest extends TestCase{
    private $model;

    protected function setUp(): void{
        $this->model = new AdminModel();
    }

    public function testCreateExternalSource(){
        $data = [
            'name' => 'TheNews',
            'base_url' => 'https://newsapi.org/v2/top-headlines',
            'api_key' => 'cc98e14ae02b481da90d93fbebd2ca23'
        ];
        $result = $this->model->addExternalNewsSource($data);
        $this->assertTrue($result['success'], 'Failed to add external source');
    }

    public function testFindEnvVariable(){
        $varName = "TheNews_api_key";
        $result = $this->model->envVariableExists($varName);
        $this->assertTrue($result, "variable is not set");
    }
}
?>