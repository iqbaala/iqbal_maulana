<?php

class ChallengeController {
    private $db;
    private $nutritionCollection;
    private $exerciseCollection;
    private $sleepCollection;
    private $challengesCollection;

    public function __construct() {
        // Koneksi ke MongoDB
        $this->db = (new MongoDB\Client)->wellbe;
        $this->nutritionCollection = $this->db->nutrition;
        $this->exerciseCollection = $this->db->exercise;
        $this->sleepCollection = $this->db->sleep;
        $this->challengesCollection = $this->db->challenges;
    }

    public function getActiveChallenges($userId) {
        $challenges = [];

        // Ambil tantangan dari database
        $nutritionChallenges = $this->challengesCollection->find([
            'category' => 'nutrition',
            'status' => 'active'
        ])->toArray();

        $exerciseChallenges = $this->challengesCollection->find([
            'category' => 'exercise',
            'status' => 'active'
        ])->toArray();

        $sleepChallenges = $this->challengesCollection->find([
            'category' => 'sleep',
            'status' => 'active'
        ])->toArray();

        // Ambil progress untuk setiap tantangan
        foreach ($nutritionChallenges as $challenge) {
            $progress = $this->getNutritionProgress($userId, $challenge);
            $challenges[] = [
                'name' => $challenge['name'],
                'description' => $challenge['description'],
                'type' => 'success',
                'icon' => $challenge['icon'] ?? 'egg-fried',
                'category' => 'nutrition',
                'achieved_days' => $progress['achieved_days'],
                'total_days' => 7,
                'percentage' => $progress['percentage'],
                'target' => $challenge['target'],
                'status' => 'Aktif'
            ];
        }

        foreach ($exerciseChallenges as $challenge) {
            $progress = $this->getExerciseProgress($userId, $challenge);
            $challenges[] = [
                'name' => $challenge['name'],
                'description' => $challenge['description'],
                'type' => 'info',
                'icon' => $challenge['icon'] ?? 'activity',
                'category' => 'exercise',
                'achieved_days' => $progress['achieved_days'],
                'total_days' => 7,
                'percentage' => $progress['percentage'],
                'target' => $challenge['target'],
                'status' => 'Aktif'
            ];
        }

        foreach ($sleepChallenges as $challenge) {
            $progress = $this->getSleepProgress($userId, $challenge);
            $challenges[] = [
                'name' => $challenge['name'],
                'description' => $challenge['description'],
                'type' => 'warning',
                'icon' => $challenge['icon'] ?? 'moon',
                'category' => 'sleep',
                'achieved_days' => $progress['achieved_days'],
                'total_days' => 7,
                'percentage' => $progress['percentage'],
                'target' => $challenge['target'],
                'status' => 'Aktif'
            ];
        }

        return $challenges;
    }

    private function getNutritionProgress($userId, $challenge) {
        // Ambil data dari Senin sampai Minggu minggu ini
        $startDate = new MongoDB\BSON\UTCDateTime(strtotime('monday this week') * 1000);
        $endDate = new MongoDB\BSON\UTCDateTime(strtotime('sunday this week') * 1000);

        // Ambil semua record nutrisi untuk minggu ini
        $records = $this->nutritionCollection->find([
            'user_id' => new MongoDB\BSON\ObjectId($userId),
            'date' => [
                '$gte' => $startDate,
                '$lte' => $endDate
            ]
        ])->toArray();

        $achievedDays = 0;
        
        // Hitung hari yang berhasil mencapai target
        foreach ($records as $record) {
            switch ($challenge['type']) {
                case 'protein_target':
                    // Cek apakah protein mencapai target (60 gram)
                    if (isset($record['protein']) && $record['protein'] >= $challenge['target']) {
                        $achievedDays++;
                    }
                    break;

                case 'calorie_target':
                    // Cek apakah kalori dalam rentang target
                    if (isset($record['calories'])) {
                        $calories = $record['calories'];
                        if ($calories >= $challenge['target']['min'] && 
                            $calories <= $challenge['target']['max']) {
                            $achievedDays++;
                        }
                    }
                    break;
            }
        }

        // Hitung persentase keberhasilan
        $percentage = count($records) > 0 ? round(($achievedDays / 7) * 100) : 0;

        return [
            'achieved_days' => $achievedDays,
            'percentage' => $percentage
        ];
    }

    private function getExerciseProgress($userId, $challenge) {
        $startDate = new MongoDB\BSON\UTCDateTime(strtotime('monday this week') * 1000);
        $endDate = new MongoDB\BSON\UTCDateTime(strtotime('sunday this week') * 1000);

        $records = $this->exerciseCollection->find([
            'user_id' => new MongoDB\BSON\ObjectId($userId),
            'date' => [
                '$gte' => $startDate,
                '$lte' => $endDate
            ]
        ])->toArray();

        $achievedDays = 0;
        foreach ($records as $record) {
            switch ($challenge['type']) {
                case 'duration':
                    if (isset($record['duration']) && $record['duration'] >= $challenge['target']) {
                        $achievedDays++;
                    }
                    break;
                case 'intensity':
                    if (isset($record['intensity']) && $record['intensity'] === $challenge['target']) {
                        $achievedDays++;
                    }
                    break;
                case 'calories_burned':
                    if (isset($record['calories_burned']) && $record['calories_burned'] >= $challenge['target']) {
                        $achievedDays++;
                    }
                    break;
            }
        }

        return [
            'achieved_days' => $achievedDays,
            'percentage' => round(($achievedDays / 7) * 100)
        ];
    }

    private function getSleepProgress($userId, $challenge) {
        $startDate = new MongoDB\BSON\UTCDateTime(strtotime('monday this week') * 1000);
        $endDate = new MongoDB\BSON\UTCDateTime(strtotime('sunday this week') * 1000);

        $records = $this->sleepCollection->find([
            'user_id' => new MongoDB\BSON\ObjectId($userId),
            'date' => [
                '$gte' => $startDate,
                '$lte' => $endDate
            ]
        ])->toArray();

        $achievedDays = 0;
        foreach ($records as $record) {
            switch ($challenge['type']) {
                case 'duration':
                    if (isset($record['duration']) && 
                        $record['duration'] >= $challenge['target']['min'] && 
                        $record['duration'] <= $challenge['target']['max']) {
                        $achievedDays++;
                    }
                    break;
                case 'quality':
                    if (isset($record['quality']) && in_array($record['quality'], $challenge['target'])) {
                        $achievedDays++;
                    }
                    break;
                case 'consistency':
                    if (isset($record['sleep_time'])) {
                        // Cek konsistensi dengan catatan tidur sebelumnya
                        $prevDay = date('Y-m-d', strtotime(date('Y-m-d', $record['date']->toDateTime()->getTimestamp()) . ' -1 day'));
                        $prevRecord = $this->sleepCollection->findOne([
                            'user_id' => new MongoDB\BSON\ObjectId($userId),
                            'date' => new MongoDB\BSON\UTCDateTime(strtotime($prevDay) * 1000)
                        ]);

                        if ($prevRecord && isset($prevRecord['sleep_time'])) {
                            $currentSleepTime = new DateTime($record['sleep_time']);
                            $prevSleepTime = new DateTime($prevRecord['sleep_time']);
                            $timeDiff = abs($currentSleepTime->getTimestamp() - $prevSleepTime->getTimestamp()) / 3600;

                            if ($timeDiff <= $challenge['target']) {
                                $achievedDays++;
                            }
                        }
                    }
                    break;
            }
        }

        return [
            'achieved_days' => $achievedDays,
            'percentage' => round(($achievedDays / 7) * 100)
        ];
    }
} 