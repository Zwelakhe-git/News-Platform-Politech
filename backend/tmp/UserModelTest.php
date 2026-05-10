<?php
namespace Thunderpc\Vkurse\Tests;
require_once __DIR__ . '/../../vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Models\UserModel;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase{
    private $mode;
    private $mock_data;
    public function setUp(){
        $this->model = new UserModel();
        $this->mock_data = [
            'email' => 'jonhdoe@gmail.com',
            'password_hash' => $password_hash,
            'name' => 'John Doe',
            'avatar_ulr' => null,
        ];
    }

    public function testAuthorCreate(): void{
        $this->mock_data['role'] = 'author';
        $res = $this->model->createUser($this->mock_data);
        assertTrue($res['success']);
    }

    public function testReaderCreate(): void{
        $this->mock_data['role'] = 'reader';
        $res = $this->model->createUser($this->mock_data);
        assertTrue($res['success']);
    }

    public function testUserDelete(): void{
        $user = $this->model->getUserDetails($this->mock_data['name'], $this->mock_data['email']);
        $success = $this->model->deleteUser($user['id']);
        assertTrue($success);
    }
}
?>