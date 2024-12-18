<?php

namespace WellBe\Controllers;

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

class NutritionController
{
    private $collection;

    public function __construct()
    {
        // Koneksi MongoDB sederhana tanpa autentikasi
        $client = new Client('mongodb://localhost:27017');
        $this->collection = $client->wellbe->nutrition;
    }

    public function index()
    {
        try {
            $userId = $_SESSION['user_id'];
            
            // Ambil ringkasan nutrisi mingguan
            $nutritionSummary = $this->getWeeklySummary($userId);
            
            // Ambil catatan nutrisi
            $cursor = $this->collection->find(['user_id' => $userId]);
            $nutritionRecords = [];
            
            foreach ($cursor as $doc) {
                $nutritionRecords[] = [
                    'id' => (string)$doc->_id,
                    'date' => date('Y-m-d', $doc->date->toDateTime()->getTimestamp()),
                    'time' => date('H:i', $doc->date->toDateTime()->getTimestamp()),
                    'food_name' => $doc->food_name,
                    'calories' => $doc->calories,
                    'protein' => $doc->protein,
                    'carbs' => $doc->carbs,
                    'fat' => $doc->fat
                ];
            }

            // Hitung pencapaian pola makan seimbang
            $balancedDietAchievement = $this->calculateBalancedDietAchievement($userId);
            
            // Ambil tantangan aktif
            $activeChallenges = $this->getActiveChallenges($userId);
            
            require __DIR__ . '/../views/nutrition/index.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            require __DIR__ . '/../views/nutrition/index.php';
        }
    }

    private function getWeeklySummary($userId)
    {
        // Set target nutrisi harian
        $targets = [
            'calories' => 2000,    // 2000 kkal per hari
            'protein' => 60,       // 60 gram protein per hari
            'carbs' => 250,        // 250 gram karbohidrat per hari
            'fat' => 65            // 65 gram lemak per hari
        ];

        // Ambil data 7 hari terakhir
        $startDate = new \MongoDB\BSON\UTCDateTime(strtotime('-7 days') * 1000);
        $cursor = $this->collection->find([
            'user_id' => $userId,
            'date' => ['$gte' => $startDate]
        ]);

        // Inisialisasi total
        $totals = [
            'calories' => 0,
            'protein' => 0,
            'carbs' => 0,
            'fat' => 0
        ];

        // Hitung total nutrisi
        foreach ($cursor as $doc) {
            $totals['calories'] += $doc->calories;
            $totals['protein'] += $doc->protein;
            $totals['carbs'] += $doc->carbs;
            $totals['fat'] += $doc->fat;
        }

        // Siapkan data ringkasan
        return [
            'totalCalories' => $totals['calories'],
            'totalProtein' => $totals['protein'],
            'totalCarbs' => $totals['carbs'],
            'totalFat' => $totals['fat'],
            'targetCalories' => $targets['calories'] * 7, // Target mingguan
            'targetProtein' => $targets['protein'] * 7,
            'targetCarbs' => $targets['carbs'] * 7,
            'targetFat' => $targets['fat'] * 7
        ];
    }

    private function calculateBalancedDietAchievement($userId)
    {
        // Set target nutrisi harian
        $targets = [
            'calories' => [1800, 2200],  // Range kalori yang sehat (min, max)
            'protein' => [45, 65],       // Range protein dalam gram (min, max)
            'carbs' => [225, 325],       // Range karbohidrat dalam gram (min, max)
            'fat' => [44, 78]            // Range lemak dalam gram (min, max)
        ];

        // Ambil data minggu ini (Senin-Minggu)
        $startDate = new \MongoDB\BSON\UTCDateTime(strtotime('monday this week') * 1000);
        $endDate = new \MongoDB\BSON\UTCDateTime(strtotime('sunday this week') * 1000);

        $pipeline = [
            [
                '$match' => [
                    'user_id' => $userId,
                    'date' => [
                        '$gte' => $startDate,
                        '$lte' => $endDate
                    ]
                ]
            ],
            [
                '$group' => [
                    '_id' => [
                        'date' => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$date']]
                    ],
                    'totalCalories' => ['$sum' => '$calories'],
                    'totalProtein' => ['$sum' => '$protein'],
                    'totalCarbs' => ['$sum' => '$carbs'],
                    'totalFat' => ['$sum' => '$fat']
                ]
            ],
            [
                '$sort' => ['_id.date' => 1]
            ]
        ];

        $dailyTotals = $this->collection->aggregate($pipeline);
        
        $balancedDays = 0;
        $totalDays = 0;
        $dailyStats = [];

        foreach ($dailyTotals as $day) {
            $totalDays++;
            $isBalanced = true;
            $date = $day['_id']['date'];

            // Cek apakah nutrisi dalam range yang sehat
            if ($day['totalCalories'] < $targets['calories'][0] || $day['totalCalories'] > $targets['calories'][1]) {
                $isBalanced = false;
            }
            if ($day['totalProtein'] < $targets['protein'][0] || $day['totalProtein'] > $targets['protein'][1]) {
                $isBalanced = false;
            }
            if ($day['totalCarbs'] < $targets['carbs'][0] || $day['totalCarbs'] > $targets['carbs'][1]) {
                $isBalanced = false;
            }
            if ($day['totalFat'] < $targets['fat'][0] || $day['totalFat'] > $targets['fat'][1]) {
                $isBalanced = false;
            }

            if ($isBalanced) {
                $balancedDays++;
            }

            $dailyStats[$date] = [
                'isBalanced' => $isBalanced,
                'calories' => $day['totalCalories'],
                'protein' => $day['totalProtein'],
                'carbs' => $day['totalCarbs'],
                'fat' => $day['totalFat']
            ];
        }

        // Hitung persentase pencapaian
        $percentage = round(($balancedDays / 7) * 100); // Selalu gunakan 7 sebagai pembagi (satu minggu)

        return [
            'balancedDays' => $balancedDays,
            'totalDays' => 7, // Selalu 7 hari (Senin-Minggu)
            'percentage' => $percentage,
            'dailyStats' => $dailyStats,
            'startDate' => date('Y-m-d', strtotime('monday this week')),
            'endDate' => date('Y-m-d', strtotime('sunday this week'))
        ];
    }

    private function getActiveChallenges($userId) {
        // Ambil data minggu ini (Senin-Minggu)
        $startDate = new \MongoDB\BSON\UTCDateTime(strtotime('monday this week') * 1000);
        $endDate = new \MongoDB\BSON\UTCDateTime(strtotime('sunday this week') * 1000);

        // Ambil data nutrisi untuk minggu ini
        $cursor = $this->collection->find([
            'user_id' => $userId,
            'date' => [
                '$gte' => $startDate,
                '$lte' => $endDate
            ]
        ]);

        // Inisialisasi challenges
        $challenges = [
            'daily_protein' => [
                'name' => 'Protein Harian',
                'target' => 60, // 60g protein per hari
                'achieved_days' => 0,
                'total_days' => 7,
                'percentage' => 0
            ],
            'balanced_meals' => [
                'name' => 'Makan Seimbang',
                'target' => [
                    'calories' => [1800, 2200],
                    'protein' => [45, 65],
                    'carbs' => [225, 325],
                    'fat' => [44, 78]
                ],
                'achieved_days' => 0,
                'total_days' => 7,
                'percentage' => 0
            ],
            'calorie_control' => [
                'name' => 'Kontrol Kalori',
                'target' => 2000, // 2000 kkal per hari
                'achieved_days' => 0,
                'total_days' => 7,
                'percentage' => 0
            ]
        ];

        // Kelompokkan data berdasarkan tanggal
        $dailyTotals = [];
        foreach ($cursor as $record) {
            $date = $record->date->toDateTime()->format('Y-m-d');
            if (!isset($dailyTotals[$date])) {
                $dailyTotals[$date] = [
                    'calories' => 0,
                    'protein' => 0,
                    'carbs' => 0,
                    'fat' => 0
                ];
            }
            $dailyTotals[$date]['calories'] += $record->calories;
            $dailyTotals[$date]['protein'] += $record->protein;
            $dailyTotals[$date]['carbs'] += $record->carbs;
            $dailyTotals[$date]['fat'] += $record->fat;
        }

        // Evaluasi pencapaian untuk setiap hari
        foreach ($dailyTotals as $date => $totals) {
            // Challenge Protein Harian
            if ($totals['protein'] >= $challenges['daily_protein']['target']) {
                $challenges['daily_protein']['achieved_days']++;
            }

            // Challenge Makan Seimbang
            $isBalanced = true;
            $target = $challenges['balanced_meals']['target'];
            
            if ($totals['calories'] < $target['calories'][0] || $totals['calories'] > $target['calories'][1]) {
                $isBalanced = false;
            }
            if ($totals['protein'] < $target['protein'][0] || $totals['protein'] > $target['protein'][1]) {
                $isBalanced = false;
            }
            if ($totals['carbs'] < $target['carbs'][0] || $totals['carbs'] > $target['carbs'][1]) {
                $isBalanced = false;
            }
            if ($totals['fat'] < $target['fat'][0] || $totals['fat'] > $target['fat'][1]) {
                $isBalanced = false;
            }

            if ($isBalanced) {
                $challenges['balanced_meals']['achieved_days']++;
            }

            // Challenge Kontrol Kalori
            if ($totals['calories'] <= $challenges['calorie_control']['target']) {
                $challenges['calorie_control']['achieved_days']++;
            }
        }

        // Hitung persentase untuk setiap challenge
        foreach ($challenges as &$challenge) {
            $challenge['percentage'] = round(($challenge['achieved_days'] / $challenge['total_days']) * 100);
        }

        return $challenges;
    }

    public function create()
    {
        require __DIR__ . '/../views/nutrition/create.php';
    }

    public function store($request)
    {
        try {
            $userId = $_SESSION['user_id'];
            
            // Gabungkan tanggal dan waktu
            $dateTime = $_POST['date'] . ' ' . ($_POST['time'] ?? '00:00');
            
            $data = [
                'user_id' => $userId,
                'food_name' => $_POST['food_name'],
                'calories' => (int)$_POST['calories'],
                'protein' => (float)$_POST['protein'],
                'carbs' => (float)$_POST['carbs'],
                'fat' => (float)$_POST['fat'],
                'date' => new \MongoDB\BSON\UTCDateTime(strtotime($dateTime) * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime()
            ];

            $this->collection->insertOne($data);
            $_SESSION['success'] = 'Data nutrisi berhasil ditambahkan';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal menyimpan data: ' . $e->getMessage();
        }
        
        header('Location: /nutrition');
        exit;
    }

    public function update()
    {
        try {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new \Exception('Invalid CSRF token');
            }

            if (!isset($_POST['id'])) {
                throw new \Exception('ID data tidak ditemukan');
            }

            $id = new ObjectId($_POST['id']);
            $userId = $_SESSION['user_id'];
            
            // Gabungkan tanggal dan waktu
            $dateTime = $_POST['date'] . ' ' . ($_POST['time'] ?? '00:00');

            $data = [
                'food_name' => $_POST['food_name'],
                'calories' => (int)$_POST['calories'],
                'protein' => (float)$_POST['protein'],
                'carbs' => (float)$_POST['carbs'],
                'fat' => (float)$_POST['fat'],
                'date' => new \MongoDB\BSON\UTCDateTime(strtotime($dateTime) * 1000),
                'updated_at' => new \MongoDB\BSON\UTCDateTime()
            ];

            $result = $this->collection->updateOne(
                ['_id' => $id, 'user_id' => $userId],
                ['$set' => $data]
            );

            if ($result->getModifiedCount() > 0) {
                $_SESSION['success'] = 'Data nutrisi berhasil diperbarui';
            } else {
                $_SESSION['error'] = 'Tidak ada perubahan data atau data tidak ditemukan';
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal memperbarui data: ' . $e->getMessage();
        }

        header('Location: /nutrition');
        exit;
    }

    public function delete()
    {
        try {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new \Exception('Invalid CSRF token');
            }

            if (!isset($_POST['id'])) {
                throw new \Exception('ID data tidak ditemukan');
            }

            $id = new ObjectId($_POST['id']);
            $userId = $_SESSION['user_id'];

            $result = $this->collection->deleteOne([
                '_id' => $id,
                'user_id' => $userId
            ]);

            if ($result->getDeletedCount() > 0) {
                $_SESSION['success'] = 'Data nutrisi berhasil dihapus';
            } else {
                $_SESSION['error'] = 'Data tidak ditemukan atau Anda tidak memiliki akses';
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal menghapus data: ' . $e->getMessage();
        }

        header('Location: /nutrition');
        exit;
    }

    public function getData() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new \Exception('Unauthorized');
            }

            $userId = $_SESSION['user_id'];
            
            // Ambil data ringkasan nutrisi
            $nutritionSummary = $this->getWeeklySummary($userId);
            
            // Ambil data harian untuk grafik
            $weeklyData = $this->getWeeklyData($userId);
            
            // Gabungkan data
            $nutritionSummary['weeklyData'] = $weeklyData;
            
            echo json_encode([
                'nutritionSummary' => $nutritionSummary,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function getWeeklyData($userId) {
        // Ambil data 7 hari terakhir
        $startDate = new \MongoDB\BSON\UTCDateTime(strtotime('monday this week') * 1000);
        $endDate = new \MongoDB\BSON\UTCDateTime(strtotime('sunday this week') * 1000);

        $pipeline = [
            [
                '$match' => [
                    'user_id' => $userId,
                    'date' => [
                        '$gte' => $startDate,
                        '$lte' => $endDate
                    ]
                ]
            ],
            [
                '$group' => [
                    '_id' => [
                        'date' => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$date']]
                    ],
                    'totalCalories' => ['$sum' => '$calories'],
                    'totalProtein' => ['$sum' => '$protein'],
                    'totalCarbs' => ['$sum' => '$carbs'],
                    'totalFat' => ['$sum' => '$fat']
                ]
            ],
            [
                '$sort' => ['_id.date' => 1]
            ]
        ];

        $cursor = $this->collection->aggregate($pipeline);
        
        // Inisialisasi array untuk setiap hari dalam seminggu
        $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $weeklyData = [
            'calories' => array_fill(0, 7, 0),
            'protein' => array_fill(0, 7, 0),
            'carbs' => array_fill(0, 7, 0),
            'fat' => array_fill(0, 7, 0)
        ];

        // Isi data
        foreach ($cursor as $doc) {
            $date = new \DateTime($doc->_id['date']);
            $dayIndex = (int)$date->format('N') - 1; // 1 (Senin) menjadi 0, 7 (Minggu) menjadi 6
            
            $weeklyData['calories'][$dayIndex] = $doc->totalCalories;
            $weeklyData['protein'][$dayIndex] = $doc->totalProtein;
            $weeklyData['carbs'][$dayIndex] = $doc->totalCarbs;
            $weeklyData['fat'][$dayIndex] = $doc->totalFat;
        }

        return $weeklyData;
    }
} 