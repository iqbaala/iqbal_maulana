<?php

namespace WellBe\Controllers;

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

class ExerciseController
{
    private $collection;

    public function __construct()
    {
        $client = new Client('mongodb://localhost:27017');
        $this->collection = $client->wellbe->exercise;
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];

        // Ambil data exercise dari MongoDB
        $exerciseRecords = $this->getExerciseRecords($userId);
        
        // Ambil ringkasan mingguan
        $exerciseSummary = $this->getWeeklySummary($userId);

        // Ambil data tantangan aktif
        $activeChallenges = $this->getActiveChallenges($userId);

        // Load view dengan data
        require __DIR__ . '/../views/exercise/index.php';
    }

    private function getExerciseRecords($userId)
    {
        // Ambil catatan olahraga
        $cursor = $this->collection->find(['user_id' => $userId]);
        
        $exerciseRecords = [];
        foreach ($cursor as $record) {
            $exerciseRecords[] = [
                'id' => (string)$record->_id,
                'date' => $record->date->toDateTime()->format('Y-m-d'),
                'time' => $record->date->toDateTime()->format('H:i'),
                'exercise_name' => $record->exercise_name,
                'duration' => $record->duration,
                'intensity' => $record->intensity,
                'calories_burned' => $record->calories_burned,
                'notes' => $record->notes ?? ''
            ];
        }
        
        // Urutkan berdasarkan tanggal terbaru
        usort($exerciseRecords, function($a, $b) {
            return strtotime($b['date'] . ' ' . $b['time']) - strtotime($a['date'] . ' ' . $a['time']);
        });
        
        return $exerciseRecords;
    }

    private function getWeeklySummary($userId)
    {
        // Set target olahraga mingguan
        $targets = [
            'duration' => 150,      // 150 menit per minggu (rekomendasi WHO)
            'calories' => 2000,     // 2000 kalori per minggu
            'sessions' => 5         // 5 sesi per minggu
        ];

        // Ambil data 7 hari terakhir
        $startDate = new \MongoDB\BSON\UTCDateTime(strtotime('-7 days') * 1000);
        $cursor = $this->collection->find([
            'user_id' => $userId,
            'date' => ['$gte' => $startDate]
        ]);

        // Inisialisasi total
        $totals = [
            'duration' => 0,
            'calories_burned' => 0,
            'sessions' => 0
        ];

        // Hitung frekuensi setiap jenis aktivitas
        $activityFrequency = [];

        // Hitung total exercise dan frekuensi aktivitas
        foreach ($cursor as $doc) {
            $totals['duration'] += $doc->duration;
            $totals['calories_burned'] += $doc->calories_burned;
            $totals['sessions']++;

            // Hitung frekuensi aktivitas
            $activityName = $doc->exercise_name;
            if (!isset($activityFrequency[$activityName])) {
                $activityFrequency[$activityName] = 0;
            }
            $activityFrequency[$activityName]++;
        }

        // Urutkan aktivitas berdasarkan frekuensi tertinggi
        arsort($activityFrequency);

        // Ambil 3 aktivitas terbanyak
        $mostFrequentActivities = array_slice($activityFrequency, 0, 3, true);

        return [
            'totalDuration' => $totals['duration'],
            'totalCaloriesBurned' => $totals['calories_burned'],
            'totalSessions' => $totals['sessions'],
            'targetDuration' => $targets['duration'],
            'targetCalories' => $targets['calories'],
            'targetSessions' => $targets['sessions'],
            'mostFrequentActivities' => $mostFrequentActivities
        ];
    }

    private function getWeeklyData($userId) {
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
                    'totalDuration' => ['$sum' => '$duration'],
                    'totalCaloriesBurned' => ['$sum' => '$calories_burned'],
                    'totalSessions' => ['$sum' => 1]
                ]
            ],
            [
                '$sort' => ['_id.date' => 1]
            ]
        ];

        $cursor = $this->collection->aggregate($pipeline);
        
        // Inisialisasi array untuk setiap hari dalam seminggu
        $weeklyData = [
            'duration' => array_fill(0, 7, 0),
            'calories' => array_fill(0, 7, 0),
            'sessions' => array_fill(0, 7, 0)
        ];

        // Isi data
        foreach ($cursor as $doc) {
            $date = new \DateTime($doc->_id['date']);
            $dayIndex = (int)$date->format('N') - 1; // 1 (Senin) menjadi 0, 7 (Minggu) menjadi 6
            
            $weeklyData['duration'][$dayIndex] = $doc->totalDuration;
            $weeklyData['calories'][$dayIndex] = $doc->totalCaloriesBurned;
            $weeklyData['sessions'][$dayIndex] = $doc->totalSessions;
        }

        return $weeklyData;
    }

    public function store()
    {
        try {
            $userId = $_SESSION['user_id'];
            
            // Gabungkan tanggal dan waktu
            $dateTime = $_POST['date'] . ' ' . ($_POST['time'] ?? '00:00');
            
            $data = [
                'user_id' => $userId,
                'exercise_name' => $_POST['activity_name'] ?? $_POST['exercise_name'] ?? '',
                'duration' => (int)$_POST['duration'],
                'calories_burned' => (int)$_POST['calories_burned'],
                'intensity' => $_POST['intensity'],
                'notes' => $_POST['notes'] ?? '',
                'date' => new \MongoDB\BSON\UTCDateTime(strtotime($dateTime) * 1000)
            ];
            
            $result = $this->collection->insertOne($data);
            
            if ($result->getInsertedCount() > 0) {
                $_SESSION['success'] = 'Data olahraga berhasil ditambahkan';
            } else {
                $_SESSION['error'] = 'Gagal menambahkan data';
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal menambahkan data: ' . $e->getMessage();
        }
        
        header('Location: /exercise');
        exit;
    }

    public function update()
    {
        try {
            if (!isset($_POST['id'])) {
                throw new \Exception('ID tidak ditemukan');
            }

            $id = new ObjectId($_POST['id']);
            $userId = $_SESSION['user_id'];
            
            // Gabungkan tanggal dan waktu
            $dateTime = $_POST['date'] . ' ' . ($_POST['time'] ?? '00:00');
            
            $data = [
                '$set' => [
                    'exercise_name' => $_POST['exercise_name'],
                    'duration' => (int)$_POST['duration'],
                    'calories_burned' => (int)$_POST['calories_burned'],
                    'intensity' => $_POST['intensity'],
                    'notes' => $_POST['notes'] ?? '',
                    'date' => new \MongoDB\BSON\UTCDateTime(strtotime($dateTime) * 1000)
                ]
            ];
            
            $result = $this->collection->updateOne(
                ['_id' => $id, 'user_id' => $userId],
                $data
            );
            
            if ($result->getModifiedCount() > 0) {
                $_SESSION['success'] = 'Data olahraga berhasil diperbarui';
            } else {
                $_SESSION['error'] = 'Data tidak ditemukan atau Anda tidak memiliki akses';
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal memperbarui data: ' . $e->getMessage();
        }
        
        header('Location: /exercise');
        exit;
    }

    public function delete()
    {
        try {
            if (!isset($_POST['id'])) {
                throw new \Exception('ID tidak ditemukan');
            }

            $id = new ObjectId($_POST['id']);
            $userId = $_SESSION['user_id'];

            $result = $this->collection->deleteOne([
                '_id' => $id,
                'user_id' => $userId
            ]);

            if ($result->getDeletedCount() > 0) {
                $_SESSION['success'] = 'Data olahraga berhasil dihapus';
            } else {
                $_SESSION['error'] = 'Data tidak ditemukan atau Anda tidak memiliki akses';
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal menghapus data: ' . $e->getMessage();
        }

        header('Location: /exercise');
        exit;
    }

    private function getActiveChallenges($userId) {
        // Ambil data minggu ini (Senin-Minggu)
        $startDate = new \MongoDB\BSON\UTCDateTime(strtotime('monday this week') * 1000);
        $endDate = new \MongoDB\BSON\UTCDateTime(strtotime('sunday this week') * 1000);

        // Ambil data exercise untuk minggu ini
        $cursor = $this->collection->find([
            'user_id' => $userId,
            'date' => [
                '$gte' => $startDate,
                '$lte' => $endDate
            ]
        ]);

        // Inisialisasi challenges
        $challenges = [
            'daily_exercise' => [
                'name' => '30 Menit Olahraga Setiap Hari',
                'target' => 30, // 30 menit per hari
                'achieved_days' => 0,
                'total_days' => 7,
                'percentage' => 0
            ],
            'high_intensity' => [
                'name' => 'Latihan Intensitas Tinggi',
                'target' => ['Tinggi'], // Target intensitas tinggi
                'achieved_days' => 0,
                'total_days' => 7,
                'percentage' => 0
            ],
            'calorie_burn' => [
                'name' => 'Target Kalori Terbakar',
                'target' => 300, // 300 kalori per hari
                'achieved_days' => 0,
                'total_days' => 7,
                'percentage' => 0
            ]
        ];

        // Kelompokkan data berdasarkan tanggal
        $dailyRecords = [];
        foreach ($cursor as $record) {
            $date = $record->date->toDateTime()->format('Y-m-d');
            if (!isset($dailyRecords[$date])) {
                $dailyRecords[$date] = [
                    'duration' => 0,
                    'intensity' => [],
                    'calories_burned' => 0
                ];
            }
            $dailyRecords[$date]['duration'] += $record->duration;
            $dailyRecords[$date]['intensity'][] = $record->intensity;
            $dailyRecords[$date]['calories_burned'] += $record->calories_burned;
        }

        // Evaluasi pencapaian untuk setiap hari
        foreach ($dailyRecords as $date => $record) {
            // Challenge 30 Menit Olahraga Setiap Hari
            if ($record['duration'] >= $challenges['daily_exercise']['target']) {
                $challenges['daily_exercise']['achieved_days']++;
            }

            // Challenge Latihan Intensitas Tinggi
            if (in_array('Tinggi', $record['intensity'])) {
                $challenges['high_intensity']['achieved_days']++;
            }

            // Challenge Target Kalori Terbakar
            if ($record['calories_burned'] >= $challenges['calorie_burn']['target']) {
                $challenges['calorie_burn']['achieved_days']++;
            }
        }

        // Hitung persentase untuk setiap challenge
        foreach ($challenges as &$challenge) {
            $challenge['percentage'] = round(($challenge['achieved_days'] / $challenge['total_days']) * 100);
        }

        return $challenges;
    }

    public function getData() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new \Exception('Unauthorized');
            }

            $userId = $_SESSION['user_id'];
            
            // Ambil ringkasan exercise
            $exerciseSummary = $this->getWeeklySummary($userId);
            
            // Ambil data mingguan untuk grafik
            $weeklyData = $this->getWeeklyData($userId);
            
            // Gabungkan data
            $exerciseSummary['weeklyData'] = $weeklyData;
            
            // Ambil tantangan aktif secara real-time
            $activeChallenges = $this->getActiveChallenges($userId);

            echo json_encode([
                'exerciseSummary' => $exerciseSummary,
                'activeChallenges' => $activeChallenges,
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
}