<?php
namespace WellBe\Models;

use MongoDB\BSON\ObjectId;
use WellBe\Config\Database;

class User {
    private $collection;

    public function __construct() {
        $this->collection = Database::getInstance()->getCollection('users');
    }

    private function isValidObjectId($id) {
        if (!is_string($id)) return false;
        return preg_match('/^[a-f\d]{24}$/i', $id);
    }

    public function create($userData) {
        try {
            // Validasi data
            if (empty($userData['email']) || empty($userData['password'])) {
                throw new \Exception('Email dan password harus diisi');
            }

            // Cek email unik
            if ($this->findByEmail($userData['email'])) {
                throw new \Exception('Email sudah terdaftar');
            }

            // Hash password
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            // Tambah timestamp
            $userData['created_at'] = new \MongoDB\BSON\UTCDateTime();
            $userData['updated_at'] = new \MongoDB\BSON\UTCDateTime();

            // Insert ke database
            $result = $this->collection->insertOne($userData);
            
            if ($result->getInsertedCount() > 0) {
                return $result->getInsertedId();
            }
            
            throw new \Exception('Gagal menyimpan data user');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function update($id, $data) {
        try {
            // Debug log
            error_log("Updating user with ID: " . $id);
            error_log("Update data: " . print_r($data, true));

            if (!$this->isValidObjectId($id)) {
                throw new \Exception('ID user tidak valid');
            }

            $objectId = new ObjectId($id);

            // Update timestamp
            $data['updated_at'] = new \MongoDB\BSON\UTCDateTime();

            // Debug log before update
            error_log("MongoDB query - Filter: " . json_encode(['_id' => $objectId]));
            error_log("MongoDB query - Update: " . json_encode(['$set' => $data]));

            $result = $this->collection->updateOne(
                ['_id' => $objectId],
                ['$set' => $data]
            );

            // Debug log after update
            error_log("MongoDB result - Modified count: " . $result->getModifiedCount());
            error_log("MongoDB result - Matched count: " . $result->getMatchedCount());

            if ($result->getModifiedCount() > 0 || $result->getMatchedCount() > 0) {
                return true;
            }

            // If no document was modified or matched, try to find if the document exists
            $document = $this->collection->findOne(['_id' => $objectId]);
            if (!$document) {
                throw new \Exception('User tidak ditemukan');
            }

            return false;
        } catch (\Exception $e) {
            error_log("Error in User::update: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function findById($id) {
        try {
            if (!$this->isValidObjectId($id)) {
                return null;
            }
            $objectId = new ObjectId($id);
            return $this->collection->findOne(['_id' => $objectId]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function findByEmail($email) {
        try {
            return $this->collection->findOne(['email' => $email]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function authenticate($email, $password) {
        try {
            $user = $this->findByEmail($email);
            
            if (!$user) {
                return false;
            }

            if (password_verify($password, $user['password'])) {
                // Update last login
                $this->collection->updateOne(
                    ['_id' => $user['_id']],
                    ['$set' => ['last_login' => new \MongoDB\BSON\UTCDateTime()]]
                );
                return $user;
            }

            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function delete($id) {
        try {
            if (!$this->isValidObjectId($id)) {
                throw new \Exception('ID user tidak valid');
            }

            $objectId = new ObjectId($id);
            $result = $this->collection->deleteOne(['_id' => $objectId]);
            return $result->getDeletedCount() > 0;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateAvatar($userId, $avatarUrl) {
        try {
            if (!$this->isValidObjectId($userId)) {
                throw new \Exception('ID user tidak valid');
            }

            $objectId = new ObjectId($userId);
            $result = $this->collection->updateOne(
                ['_id' => $objectId],
                ['$set' => ['avatar' => $avatarUrl]]
            );

            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error in updateAvatar: " . $e->getMessage());
            throw $e;
        }
    }

    public function getUserData($id) {
        try {
            if (!$this->isValidObjectId($id)) {
                throw new \Exception('ID user tidak valid');
            }

            $objectId = new ObjectId($id);
            $userData = $this->collection->findOne(['_id' => $objectId]);

            if (!$userData) {
                throw new \Exception('User tidak ditemukan');
            }

            // Convert MongoDB UTCDateTime to string for dates
            if (isset($userData['birth_date']) && $userData['birth_date'] instanceof \MongoDB\BSON\UTCDateTime) {
                $userData['birth_date'] = $userData['birth_date']->toDateTime()->format('Y-m-d');
            }
            if (isset($userData['created_at']) && $userData['created_at'] instanceof \MongoDB\BSON\UTCDateTime) {
                $userData['created_at'] = $userData['created_at']->toDateTime()->format('Y-m-d H:i:s');
            }
            if (isset($userData['updated_at']) && $userData['updated_at'] instanceof \MongoDB\BSON\UTCDateTime) {
                $userData['updated_at'] = $userData['updated_at']->toDateTime()->format('Y-m-d H:i:s');
            }

            return $userData;
        } catch (\Exception $e) {
            error_log("Error in getUserData: " . $e->getMessage());
            throw $e;
        }
    }
}
