<?php

namespace WellBe\Config;

use MongoDB\Client;

class Database {
    private static $instance = null;
    private $client;
    private $database;

    private function __construct() {
        // Gunakan environment variables jika ada, jika tidak gunakan default
        $uri = getenv('MONGODB_URI') ?: 'mongodb+srv://Iqbaal_la:iqbalcuy@cluster0.uimck.mongodb.net';
        $dbName = getenv('MONGODB_DB') ?: 'nama_database';
        
        try {
            $this->client = new Client($uri);
            $this->database = $this->client->selectDatabase($dbName);
        } catch (\Exception $e) {
            throw new \Exception("Koneksi database gagal: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getCollection($collectionName) {
        return $this->database->selectCollection($collectionName);
    }
} 