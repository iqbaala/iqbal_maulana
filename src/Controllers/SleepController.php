<?php

namespace WellBe\Controllers;

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

class SleepController
{
    private $collection;

    public function __construct()
    {
        $client = new Client('mongodb://localhost:27017');
        $this->collection = $client->wellbe->sleep;
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];

        // Ambil data tidur dari MongoDB
        $sleepRecords = $this->getSleepRecords($userId);
        
        // Ambil ringkasan mingguan
        $sleepSummary = $this->getWeeklySummary($userId);

        // Ambil data tantangan aktif
        $activeChallenges = $this->getActiveChallenges($userId);

        // Load view dengan data
        require __DIR__ . '/../views/sleep/index.php';
    }

    private function getSleepRecords($userId)
    {
        // Ambil data tidur dari MongoDB
        $cursor = $this->collection->find(['user_id' => $userId]);
        $sleepRecords = [];
        
        foreach ($cursor as $doc) {
            $sleepRecords[] = [
                'id' => (string)$doc->_id,
                'date' => date('Y-m-d', $doc->sleep_start->toDateTime()->getTimestamp()),
                'sleep_start' => date('H:i', $doc->sleep_start->toDateTime()->getTimestamp()),
                'sleep_end' => date('H:i', $doc->sleep_end->toDateTime()->getTimestamp()),
                'duration' => $doc->duration,
                'quality' => $doc->quality,
                'notes' => $doc->notes ?? ''
            ];
        }
        
        return $sleepRecords;
    }

    private function getWeeklySummary($userId)
    {
        // Set target tidur
        $targets = [
            'duration' => 8,        // 8 jam per hari
            'sleep_score' => 85,    // Target skor kualitas tidur
            'bedtime' => '22:00',   // Target waktu tidur
            'wake_time' => '06:00'  // Target waktu bangun
        ];

        // Ambil data minggu ini (Senin-Minggu)
        $startDate = new \MongoDB\BSON\UTCDateTime(strtotime('monday this week') * 1000);
        $endDate = new \MongoDB\BSON\UTCDateTime(strtotime('sunday this week') * 1000);

        $cursor = $this->collection->find([
            'user_id' => $userId,
            'sleep_start' => [
                '$gte' => $startDate,
                '$lte' => $endDate
            ]
        ]);

        // Inisialisasi total
        $totals = [
            'duration' => 0,
            'quality_sum' => 0,
            'days_tracked' => 0,
            'on_time_sleep' => 0,
            'on_time_wake' => 0
        ];

        // Hitung total tidur
        foreach ($cursor as $doc) {
            $totals['duration'] += $doc->duration;
            $totals['quality_sum'] += $doc->quality;
            $totals['days_tracked']++;

            // Hitung ketepatan waktu tidur
            $sleepTime = date('H:i', $doc->sleep_start->toDateTime()->getTimestamp());
            if (strtotime($sleepTime) <= strtotime($targets['bedtime'])) {
                $totals['on_time_sleep']++;
            }

            // Hitung ketepatan waktu bangun
            $wakeTime = date('H:i', $doc->sleep_end->toDateTime()->getTimestamp());
            if (strtotime($wakeTime) <= strtotime($targets['wake_time'])) {
                $totals['on_time_wake']++;
            }
        }

        // Hitung rata-rata
        $averageDuration = $totals['days_tracked'] > 0 ? $totals['duration'] / $totals['days_tracked'] : 0;
        $averageQuality = $totals['days_tracked'] > 0 ? $totals['quality_sum'] / $totals['days_tracked'] : 0;

        return [
            'averageDuration' => round($averageDuration, 1),
            'averageQuality' => round($averageQuality, 1),
            'daysTracked' => $totals['days_tracked'],
            'onTimeSleep' => $totals['on_time_sleep'],
            'onTimeWake' => $totals['on_time_wake'],
            'targetDuration' => $targets['duration'],
            'targetSleepScore' => $targets['sleep_score'],
            'targetBedtime' => $targets['bedtime'],
            'targetWakeTime' => $targets['wake_time']
        ];
    }

    private function getActiveChallenges($userId) {
        // Ambil data minggu ini (Senin-Minggu)
        $startDate = new \MongoDB\BSON\UTCDateTime(strtotime('monday this week') * 1000);
        $endDate = new \MongoDB\BSON\UTCDateTime(strtotime('sunday this week') * 1000);

        // Ambil data tidur untuk minggu ini
        $cursor = $this->collection->find([
            'user_id' => $userId,
            'sleep_start' => [
                '$gte' => $startDate,
                '$lte' => $endDate
            ]
        ]);

        // Inisialisasi challenges
        $challenges = [
            'sleep_duration' => [
                'name' => '7-9 Jam Tidur per Hari',
                'target' => [7, 9], // 7-9 jam per hari
                'achieved_days' => 0,
                'total_days' => 7,
                'percentage' => 0
            ],
            'sleep_quality' => [
                'name' => 'Kualitas Tidur Baik',
                'target' => ['Baik', 'Sangat Baik'],
                'achieved_days' => 0,
                'total_days' => 7,
                'percentage' => 0
            ],
            'consistent_schedule' => [
                'name' => 'Jadwal Tidur Konsisten',
                'target' => 1, // maksimal 1 jam perbedaan
                'achieved_days' => 0,
                'total_days' => 7,
                'percentage' => 0
            ]
        ];

        // Kelompokkan data berdasarkan tanggal
        $dailyRecords = [];
        foreach ($cursor as $record) {
            $date = $record->sleep_start->toDateTime()->format('Y-m-d');
            if (!isset($dailyRecords[$date])) {
                $dailyRecords[$date] = [
                    'duration' => $record->duration,
                    'quality' => $this->getQualityText($record->quality),
                    'sleep_time' => $record->sleep_start->toDateTime()->format('H:i')
                ];
            }
        }

        // Evaluasi pencapaian untuk setiap hari
        foreach ($dailyRecords as $date => $record) {
            // Challenge Durasi Tidur
            if ($record['duration'] >= $challenges['sleep_duration']['target'][0] && 
                $record['duration'] <= $challenges['sleep_duration']['target'][1]) {
                $challenges['sleep_duration']['achieved_days']++;
            }

            // Challenge Kualitas Tidur
            if (in_array($record['quality'], $challenges['sleep_quality']['target'])) {
                $challenges['sleep_quality']['achieved_days']++;
            }

            // Challenge Jadwal Konsisten
            $prevDate = date('Y-m-d', strtotime($date . ' -1 day'));
            if (isset($dailyRecords[$prevDate])) {
                $prevTime = strtotime($prevDate . ' ' . $dailyRecords[$prevDate]['sleep_time']);
                $currentTime = strtotime($date . ' ' . $record['sleep_time']);
                $timeDiff = abs($currentTime - $prevTime) / 3600; // dalam jam

                if ($timeDiff <= $challenges['consistent_schedule']['target']) {
                    $challenges['consistent_schedule']['achieved_days']++;
                }
            }
        }

        // Hitung persentase untuk setiap challenge
        foreach ($challenges as &$challenge) {
            $challenge['percentage'] = round(($challenge['achieved_days'] / $challenge['total_days']) * 100);
        }

        return $challenges;
    }

    private function getQualityText($quality) {
        return match($quality) {
            100 => 'Sangat Baik',
            80 => 'Baik',
            60 => 'Cukup',
            40 => 'Kurang',
            20 => 'Sangat Kurang',
            default => 'Tidak Diketahui'
        };
    }

    public function store()
    {
        try {
            $userId = $_SESSION['user_id'];
            
            // Gabungkan tanggal dengan waktu tidur dan bangun
            $sleepStart = $_POST['date'] . ' ' . $_POST['sleep_start'];
            $sleepEnd = $_POST['date'] . ' ' . $_POST['sleep_end'];
            
            // Jika waktu bangun lebih awal dari waktu tidur, tambahkan 1 hari
            if (strtotime($sleepEnd) < strtotime($sleepStart)) {
                $sleepEnd = date('Y-m-d H:i', strtotime($sleepEnd . ' +1 day'));
            }
            
            // Hitung durasi tidur dalam jam
            $duration = (strtotime($sleepEnd) - strtotime($sleepStart)) / 3600;
            
            $data = [
                'user_id' => $userId,
                'sleep_start' => new \MongoDB\BSON\UTCDateTime(strtotime($sleepStart) * 1000),
                'sleep_end' => new \MongoDB\BSON\UTCDateTime(strtotime($sleepEnd) * 1000),
                'duration' => $duration,
                'quality' => (int)$_POST['quality'],
                'notes' => $_POST['notes'] ?? '',
                'created_at' => new \MongoDB\BSON\UTCDateTime()
            ];

            $this->collection->insertOne($data);
            $_SESSION['success'] = 'Data tidur berhasil ditambahkan';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal menyimpan data: ' . $e->getMessage();
        }
        
        header('Location: /sleep');
        exit;
    }

    public function update()
    {
        try {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new \Exception('Invalid CSRF token');
            }

            $id = new ObjectId($_POST['id']);
            $userId = $_SESSION['user_id'];
            
            // Gabungkan tanggal dengan waktu tidur dan bangun
            $sleepStart = $_POST['date'] . ' ' . $_POST['sleep_start'];
            $sleepEnd = $_POST['date'] . ' ' . $_POST['sleep_end'];
            
            // Jika waktu bangun lebih awal dari waktu tidur, tambahkan 1 hari
            if (strtotime($sleepEnd) < strtotime($sleepStart)) {
                $sleepEnd = date('Y-m-d H:i', strtotime($sleepEnd . ' +1 day'));
            }
            
            // Hitung durasi tidur dalam jam
            $duration = (strtotime($sleepEnd) - strtotime($sleepStart)) / 3600;

            $data = [
                'sleep_start' => new \MongoDB\BSON\UTCDateTime(strtotime($sleepStart) * 1000),
                'sleep_end' => new \MongoDB\BSON\UTCDateTime(strtotime($sleepEnd) * 1000),
                'duration' => $duration,
                'quality' => (int)$_POST['quality'],
                'notes' => $_POST['notes'] ?? '',
                'updated_at' => new \MongoDB\BSON\UTCDateTime()
            ];

            $result = $this->collection->updateOne(
                ['_id' => $id, 'user_id' => $userId],
                ['$set' => $data]
            );

            if ($result->getModifiedCount() > 0) {
                $_SESSION['success'] = 'Data tidur berhasil diperbarui';
            } else {
                $_SESSION['error'] = 'Tidak ada perubahan data atau data tidak ditemukan';
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal memperbarui data: ' . $e->getMessage();
        }

        header('Location: /sleep');
        exit;
    }

    public function delete()
    {
        try {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new \Exception('Invalid CSRF token');
            }

            $id = new ObjectId($_POST['id']);
            $userId = $_SESSION['user_id'];

            $result = $this->collection->deleteOne([
                '_id' => $id,
                'user_id' => $userId
            ]);

            if ($result->getDeletedCount() > 0) {
                $_SESSION['success'] = 'Data tidur berhasil dihapus';
            } else {
                $_SESSION['error'] = 'Data tidak ditemukan atau Anda tidak memiliki akses';
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal menghapus data: ' . $e->getMessage();
        }

        header('Location: /sleep');
        exit;
    }

    private function getWeeklyData($userId) {
        // Ambil data minggu ini (Senin-Minggu)
        $startDate = new \MongoDB\BSON\UTCDateTime(strtotime('monday this week') * 1000);
        $endDate = new \MongoDB\BSON\UTCDateTime(strtotime('sunday this week') * 1000);

        $pipeline = [
            [
                '$match' => [
                    'user_id' => $userId,
                    'sleep_start' => [
                        '$gte' => $startDate,
                        '$lte' => $endDate
                    ]
                ]
            ],
            [
                '$group' => [
                    '_id' => [
                        'date' => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$sleep_start']]
                    ],
                    'totalDuration' => ['$sum' => '$duration'],
                    'avgQuality' => ['$avg' => '$quality']
                ]
            ],
            [
                '$sort' => ['_id.date' => 1]
            ]
        ];

        $cursor = $this->collection->aggregate($pipeline);
        
        // Inisialisasi array untuk setiap hari dalam seminggu
        $weeklyData = array_fill(0, 7, 0); // Array untuk durasi tidur

        // Isi data
        foreach ($cursor as $doc) {
            $date = new \DateTime($doc->_id['date']);
            $dayIndex = (int)$date->format('N') - 1; // 1 (Senin) menjadi 0, 7 (Minggu) menjadi 6
            
            $weeklyData[$dayIndex] = $doc->totalDuration;
        }

        return $weeklyData;
    }

    public function getData() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new \Exception('Unauthorized');
            }

            $userId = $_SESSION['user_id'];
            
            // Ambil ringkasan tidur
            $sleepSummary = $this->getWeeklySummary($userId);
            
            // Ambil data mingguan untuk grafik
            $weeklyData = $this->getWeeklyData($userId);
            
            // Gabungkan data
            $sleepSummary['weeklyData'] = $weeklyData;
            
            // Ambil tantangan aktif secara real-time
            $activeChallenges = $this->getActiveChallenges($userId);

            echo json_encode([
                'sleepSummary' => $sleepSummary,
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