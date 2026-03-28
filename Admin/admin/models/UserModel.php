<?php 
namespace Thunderpc\Vkurse\Admin\Models;
require_once __DIR__ . '/../../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Models\Database;

class UserModel extends Database{
    public function __construct(){
        parent::__construct();
    }
    
    public function createUser($data){
        try{
            $sql = 'INSERT INTO users (email, password_hash, full_name, role) VALUES (?,?,?,?)';
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);

            if(isset($data['avatar_url'])){
                $id = $this->pdo->lastInsertId();
                $this->updateUserAvatar(['avatar_url' => $data['avatar_url'], 'user_id' => $id]);
            }
            return [
                'success' => $res ? true : false,
                'user_id' => $res ? $this->pod->lastInsertId() : -1
            ];
        } catch(PDOException $e){
            error_log($e->getMessage());
            return ['success' => false];
        }
    }

    public function deleteUser($id){
        try{
            $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ? LIMIT 1');
            return $stmt->execute($id) ? true : false;
        } catch(PDOException $e){
            error_log($e->getMessage());
            return false;
        }
    }

    public function updateUserProfile($username, $currentPassword, $newName, $newEmail, $newAvatar, $newPassword) {
        try {
            $this->pdo->beginTransaction();

            // First, verify current password
            $stmt = $this->pdo->prepare("SELECT id, password_hash, email FROM users WHERE full_name = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['response' => 'fail', 'message' => 'User not found'];
            }

            if (!password_verify($currentPassword, $user['password_hash'])) {
                return ['response' => 'fail', 'message' => 'Current password is incorrect'];
            }

            // Check if new username or email already exists (if changed)
            if ($newName !== $username) {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE full_name = ? AND id != ?");
                $stmt->execute([$newName, $user['id']]);
                if ($stmt->fetchColumn() > 0) {
                    return ['response' => 'fail', 'message' => 'Username already taken'];
                }
            }

            if ($newEmail && $newEmail !== $user['email']) {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$newEmail, $user['id']]);
                if ($stmt->fetchColumn() > 0) {
                    return ['response' => 'fail', 'message' => 'Email already in use'];
                }
            }

            // Prepare update data
            $updateFields = ['name = ?', 'email = ?'];
            $updateParams = [$newName, $newEmail];

            // Add avatar if provided
            if ($newAvatar !== '') {
                $updateFields[] = 'avatar_url = ?';
                $updateParams[] = $newAvatar;
            }

            // Update password if provided
            if ($newPassword) {
                $updateFields[] = 'password_hash = ?';
                $updateParams[] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            // Add ID parameter
            $updateParams[] = $user['id'];

            // Execute update
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($updateParams);

            

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Profile updated successfully',
                'name' => $newName,
                'email' => $newEmail,
                'avatar_url' => $newAvatar
            ];

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Server Error'];
        }
    }
    
    public function updateUserAvatar($data){
        try{
            $sql = "UPDATE users SET avatar_url = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);

            if($stmt->execute([$data['avatar_url'], $data['user_id']])){
                return ["success" => true, "message" => "profile updated successfully"];
            }
            return ["success" => false, "message" => "failed to update profile"];
        } catch (Exception $e){
            error_log($e->getMessage());
            return ["success" => false, "message" => "server error"];
        }
    }
    public function updateLoginTime($name, $email){
        try{
            $sql = 'UPDATE users SET last_login = CURRENT_TIMESTAMP() WHERE full_name = ? AND email = ?';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $email]);
        } catch(Throwable $e){
            error_log($e->getMessage());
        }
    }
    

    /**
     * gives the id of a user, registered as an author.
     * @param name - username
     * @param email - user's email
     * @return (id, full_name, email, avatar_url, role)
     */
    public function getUserDetails($name, $email) {
         
        try {
            $stmt = $this->pdo->prepare("SELECT id, full_name, email, avatar_url, role, is_blocked FROM users WHERE full_name = ? AND email = ?");
            $stmt->execute([$name, $email]);
            $user = $stmt->fetch();

            return $user ? $user : null;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function getAuthorId($name, $email){
        /**
         * gives the id of a user, registered as an author.
         * @param name - username
         * @param email - user's email
         * @return -1 if the user doesnt have the author role, or if the user doesnt exist, else the user id
         */
        try{
            $sql = 'SELECT id FROM users WHERE full_name = ? AND email = ? AND role = "author"';
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute([$name, $email]);
            if(!$res){
                return -1;
            }
            return $res->fetchColumn();
        } catch(PDOException $e){
            error_log($e->getMessage());
            return -1;
        }
    }
} 
?>