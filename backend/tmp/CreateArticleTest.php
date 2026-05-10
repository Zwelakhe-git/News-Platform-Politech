<?php
namespace Thunderpc\Vkurse\Tests;
require_once __DIR__ . '/../../vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Controllers\ArticleController;
use Thunderpc\Vkurse\Admin\Models\UserModel;
use PHPUnit\Framework\TestCase;

class CreateArticleTest extends TestCase{
    private $articleController;
    private $userModel;
    public function setUp(): void{
        $this->articleController = new ArticleController();
        $this->userModel = new UserModel();
    }

    public function testCreate(): void{
        $password_hash = password_hash('johndoe', PASSWORD_DEFAULT);
        $mock_data = ['user' => [
            'email' => 'jonhdoe@gmail.com',
            'password_hash' => $password_hash,
            'name' => 'John Doe',
            'avatar_ulr' => null,
            'role' => 'author'
        ]];
        session_start();
        $_SESSION['user'] = $mock_data['user'];
        if($this->userModel->createUser($mock_data['user'])){
            assertFalse(true, 'Failed to create user with author role');
        }
        $_POST[''];
        $result = $this->articleController->create();
        session_destroy();

        $this->articleController->delete($result['id']);
        $this->userModel->deleteUser();
        assertTrue($result['success'] === true);
    }
}
?>