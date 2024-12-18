<?php
namespace WellBe\Api;

class CalorieCalculator {
    // Konstanta untuk perhitungan BMR
    private const BMR_MALE_CONSTANT = 88.362;
    private const BMR_FEMALE_CONSTANT = 447.593;
    private const BMR_WEIGHT_MULTIPLIER_MALE = 13.397;
    private const BMR_WEIGHT_MULTIPLIER_FEMALE = 9.247;
    private const BMR_HEIGHT_MULTIPLIER_MALE = 4.799;
    private const BMR_HEIGHT_MULTIPLIER_FEMALE = 3.098;
    private const BMR_AGE_MULTIPLIER_MALE = 5.677;
    private const BMR_AGE_MULTIPLIER_FEMALE = 4.330;

    // Faktor aktivitas
    private const ACTIVITY_FACTORS = [
        'sedentary' => 1.2,      // Jarang bergerak
        'light' => 1.375,        // Aktivitas ringan
        'moderate' => 1.55,      // Aktivitas sedang
        'active' => 1.725,       // Sangat aktif
        'very_active' => 1.9     // Ekstra aktif
    ];

    /**
     * Menghitung BMR (Basal Metabolic Rate)
     */
    public function calculateBMR($weight, $height, $age, $gender) {
        if ($gender === 'L') {
            return self::BMR_MALE_CONSTANT +
                   (self::BMR_WEIGHT_MULTIPLIER_MALE * $weight) +
                   (self::BMR_HEIGHT_MULTIPLIER_MALE * $height) -
                   (self::BMR_AGE_MULTIPLIER_MALE * $age);
        } else {
            return self::BMR_FEMALE_CONSTANT +
                   (self::BMR_WEIGHT_MULTIPLIER_FEMALE * $weight) +
                   (self::BMR_HEIGHT_MULTIPLIER_FEMALE * $height) -
                   (self::BMR_AGE_MULTIPLIER_FEMALE * $age);
        }
    }

    /**
     * Menghitung kebutuhan kalori harian
     */
    public function calculateDailyCalories($weight, $height, $age, $gender, $activityLevel) {
        $bmr = $this->calculateBMR($weight, $height, $age, $gender);
        $activityFactor = self::ACTIVITY_FACTORS[$activityLevel] ?? self::ACTIVITY_FACTORS['moderate'];
        return round($bmr * $activityFactor);
    }

    /**
     * Menghitung kalori yang terbakar saat olahraga
     */
    public function calculateExerciseCalories($weight, $duration, $exerciseType) {
        $metValues = [
            'walking' => 3.5,
            'jogging' => 7.0,
            'running' => 10.0,
            'cycling' => 6.0,
            'swimming' => 6.0,
            'yoga' => 3.0,
            'strength_training' => 4.0,
            'hiit' => 8.0
        ];

        $met = $metValues[$exerciseType] ?? 4.0; // Default MET jika tipe olahraga tidak ditemukan
        return round(($met * 3.5 * $weight * $duration) / 200);
    }

    /**
     * Menghitung distribusi makronutrien yang direkomendasikan
     */
    public function calculateMacronutrients($dailyCalories) {
        // Distribusi makronutrien standar: 50% karbo, 30% protein, 20% lemak
        $carbs = round(($dailyCalories * 0.5) / 4); // 4 kalori per gram karbohidrat
        $protein = round(($dailyCalories * 0.3) / 4); // 4 kalori per gram protein
        $fat = round(($dailyCalories * 0.2) / 9); // 9 kalori per gram lemak

        return [
            'carbs' => $carbs,
            'protein' => $protein,
            'fat' => $fat
        ];
    }

    /**
     * Menghitung IMT (Indeks Massa Tubuh)
     */
    public function calculateBMI($weight, $height) {
        $heightInMeters = $height / 100;
        $bmi = $weight / ($heightInMeters * $heightInMeters);
        
        $category = '';
        if ($bmi < 18.5) {
            $category = 'Berat Badan Kurang';
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            $category = 'Berat Badan Normal';
        } elseif ($bmi >= 25 && $bmi < 30) {
            $category = 'Berat Badan Berlebih';
        } else {
            $category = 'Obesitas';
        }

        return [
            'bmi' => round($bmi, 1),
            'category' => $category
        ];
    }

    /**
     * Menghitung rekomendasi kalori untuk target berat badan
     */
    public function calculateTargetCalories($dailyCalories, $weightGoal) {
        switch ($weightGoal) {
            case 'lose':
                return round($dailyCalories - 500); // Defisit 500 kalori untuk menurunkan berat
            case 'gain':
                return round($dailyCalories + 500); // Surplus 500 kalori untuk menaikkan berat
            default:
                return $dailyCalories; // Mempertahankan berat badan
        }
    }
} 