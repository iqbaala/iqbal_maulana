<?php

namespace WellBe\Controllers;

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

class DashboardController
{
    private $nutritionCollection;
    private $exerciseCollection;
    private $sleepCollection;

    public function __construct()
    {
        $client = new Client('mongodb://localhost:27017');
        $this->nutritionCollection = $client->wellbe->nutrition;
        $this->exerciseCollection = $client->wellbe->exercise;
        $this->sleepCollection = $client->wellbe->sleep;
    }

    public function index()
    {
        try {
            $userId = $_SESSION['user_id'];
            $dashboardData = [
                'nutrition' => $this->getNutritionSummary($userId),
                'exercise' => $this->getExerciseSummary($userId),
                'sleep' => $this->getSleepSummary($userId),
                'recentActivities' => $this->getRecentActivities($userId),
                'weeklyProgress' => $this->calculateWeeklyProgress($userId)
            ];
            
            require __DIR__ . '/../views/dashboard/index.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal mengambil data: ' . $e->getMessage();
            require __DIR__ . '/../views/dashboard/index.php';
        }
    }

    private function calculateWeeklyProgress($userId)
    {
        $startDate = new \MongoDB\BSON\UTCDateTime(strtotime('-7 days') * 1000);
        
        // Hitung progress nutrisi
        $nutritionProgress = $this->calculateNutritionProgress($userId, $startDate);
        
        // Hitung progress exercise
        $exerciseProgress = $this->calculateExerciseProgress($userId, $startDate);
        
        // Hitung progress tidur
        $sleepProgress = $this->calculateSleepProgress($userId, $startDate);

        return [
            'nutrition' => ['percentage' => $nutritionProgress],
            'exercise' => ['percentage' => $exerciseProgress],
            'sleep' => ['percentage' => $sleepProgress]
        ];
    }

    private function calculateNutritionProgress($userId, $startDate)
    {
        $cursor = $this->nutritionCollection->find([
            'user_id' => $userId,
            'date' => ['$gte' => $startDate]
        ]);

        $totalDays = 0;
        $targetDays = 7;
        $targetCalories = 2000;
        $daysOnTarget = 0;

        foreach ($cursor as $doc) {
            $totalDays++;
            if ($doc->calories >= ($targetCalories * 0.8) && $doc->calories <= ($targetCalories * 1.2)) {
                $daysOnTarget++;
            }
        }

        return $totalDays > 0 ? round(($daysOnTarget / $targetDays) * 100) : 0;
    }

    private function calculateExerciseProgress($userId, $startDate)
    {
        $cursor = $this->exerciseCollection->find([
            'user_id' => $userId,
            'date' => ['$gte' => $startDate]
        ]);

        $totalMinutes = 0;
        $targetMinutes = 210; // 30 menit per hari

        foreach ($cursor as $doc) {
            $totalMinutes += $doc->duration;
        }

        return min(round(($totalMinutes / $targetMinutes) * 100), 100);
    }

    private function calculateSleepProgress($userId, $startDate)
    {
        $cursor = $this->sleepCollection->find([
            'user_id' => $userId,
            'sleep_start' => ['$gte' => $startDate]
        ]);

        $totalDays = 0;
        $targetDays = 7;
        $targetHours = 8;
        $daysOnTarget = 0;

        foreach ($cursor as $doc) {
            $totalDays++;
            if ($doc->duration >= ($targetHours * 0.8) && $doc->duration <= ($targetHours * 1.2)) {
                $daysOnTarget++;
            }
        }

        return $totalDays > 0 ? round(($daysOnTarget / $targetDays) * 100) : 0;
    }

    private function getNutritionSummary($userId)
    {
        // Ambil data nutrisi 7 hari terakhir
        $startDate = new \MongoDB\BSON\UTCDateTime(strtotime('-7 days') * 1000);
        $cursor = $this->nutritionCollection->find([
            'user_id' => $userId,
            'date' => ['$gte' => $startDate]
        ]);

        $totalCalories = 0;
        $totalProtein = 0;
        $totalCarbs = 0;
        $totalFat = 0;
        $count = 0;

        foreach ($cursor as $doc) {
            $totalCalories += $doc->calories;
            $totalProtein += $doc->protein;
            $totalCarbs += $doc->carbs;
            $totalFat += $doc->fat;
            $count++;
        }

        return [
            'avgCalories' => $count > 0 ? round($totalCalories / $count) : 0,
            'avgProtein' => $count > 0 ? round($totalProtein / $count, 1) : 0,
            'avgCarbs' => $count > 0 ? round($totalCarbs / $count, 1) : 0,
            'avgFat' => $count > 0 ? round($totalFat / $count, 1) : 0,
            'daysTracked' => $count
        ];
    }

    private function getExerciseSummary($userId)
    {
        // Ambil data exercise 7 hari terakhir
        $startDate = new \MongoDB\BSON\UTCDateTime(strtotime('-7 days') * 1000);
        $cursor = $this->exerciseCollection->find([
            'user_id' => $userId,
            'date' => ['$gte' => $startDate]
        ]);

        $totalDuration = 0;
        $totalCaloriesBurned = 0;
        $activities = [];
        $count = 0;

        foreach ($cursor as $doc) {
            $totalDuration += $doc->duration;
            $totalCaloriesBurned += $doc->calories_burned;
            $activities[] = $doc->exercise_name;
            $count++;
        }

        return [
            'totalDuration' => $totalDuration,
            'avgDuration' => $count > 0 ? round($totalDuration / $count) : 0,
            'totalCaloriesBurned' => $totalCaloriesBurned,
            'uniqueActivities' => count(array_unique($activities)),
            'daysTracked' => $count
        ];
    }

    private function getSleepSummary($userId)
    {
        // Ambil data tidur 7 hari terakhir
        $startDate = new \MongoDB\BSON\UTCDateTime(strtotime('-7 days') * 1000);
        $cursor = $this->sleepCollection->find([
            'user_id' => $userId,
            'sleep_start' => ['$gte' => $startDate]
        ]);

        $totalDuration = 0;
        $totalQuality = 0;
        $count = 0;

        foreach ($cursor as $doc) {
            $totalDuration += $doc->duration;
            $totalQuality += $doc->quality;
            $count++;
        }

        return [
            'avgDuration' => $count > 0 ? round($totalDuration / $count, 1) : 0,
            'avgQuality' => $count > 0 ? round($totalQuality / $count) : 0,
            'daysTracked' => $count
        ];
    }

    private function getRecentActivities($userId)
    {
        $activities = [];

        // Ambil 5 data nutrisi terbaru
        $nutritionCursor = $this->nutritionCollection->find(
            ['user_id' => $userId],
            ['sort' => ['date' => -1], 'limit' => 5]
        );

        foreach ($nutritionCursor as $doc) {
            $activities[] = [
                'type' => 'nutrition',
                'date' => $doc->date->toDateTime(),
                'description' => "Mencatat makanan: {$doc->food_name} ({$doc->calories} kkal)"
            ];
        }

        // Ambil 5 data exercise terbaru
        $exerciseCursor = $this->exerciseCollection->find(
            ['user_id' => $userId],
            ['sort' => ['date' => -1], 'limit' => 5]
        );

        foreach ($exerciseCursor as $doc) {
            $activities[] = [
                'type' => 'exercise',
                'date' => $doc->date->toDateTime(),
                'description' => "Melakukan aktivitas: {$doc->exercise_name} ({$doc->duration} menit)"
            ];
        }

        // Ambil 5 data sleep terbaru
        $sleepCursor = $this->sleepCollection->find(
            ['user_id' => $userId],
            ['sort' => ['sleep_start' => -1], 'limit' => 5]
        );

        foreach ($sleepCursor as $doc) {
            $activities[] = [
                'type' => 'sleep',
                'date' => $doc->sleep_start->toDateTime(),
                'description' => "Tidur selama {$doc->duration} jam dengan kualitas {$doc->quality}%"
            ];
        }

        // Urutkan semua aktivitas berdasarkan tanggal
        usort($activities, function($a, $b) {
            return $b['date']->getTimestamp() - $a['date']->getTimestamp();
        });

        // Ambil 10 aktivitas terakhir
        return array_slice($activities, 0, 10);
    }
} 