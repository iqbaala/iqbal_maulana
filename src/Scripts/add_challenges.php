<?php
require_once __DIR__ . '/../../vendor/autoload.php';

// Koneksi ke MongoDB
$client = new MongoDB\Client;
$db = $client->wellbe;
$challengesCollection = $db->challenges;

// Hapus tantangan yang ada (opsional)
$challengesCollection->deleteMany([]);

// Data tantangan nutrisi
$nutritionChallenges = [
    [
        'name' => 'Protein Harian',
        'description' => 'Konsumsi protein minimal 60 gram setiap hari',
        'category' => 'nutrition',
        'type' => 'protein_target',
        'target' => 60, // dalam gram
        'icon' => 'egg',
        'status' => 'active'
    ],
    [
        'name' => 'Makan Seimbang',
        'description' => 'Jaga asupan kalori antara 1800-2200 kkal setiap hari',
        'category' => 'nutrition',
        'type' => 'calorie_target',
        'target' => [
            'min' => 1800,
            'max' => 2200
        ],
        'icon' => 'utensils',
        'status' => 'active'
    ],
    [
        'name' => 'Kontrol Kalori',
        'description' => 'Konsumsi 2000 kalori setiap hari',
        'category' => 'nutrition',
        'type' => 'calorie_target',
        'target' => [
            'min' => 1900, // memberikan sedikit toleransi
            'max' => 2100  // memberikan sedikit toleransi
        ],
        'icon' => 'chart-line',
        'status' => 'active'
    ]
];

// Data tantangan olahraga
$exerciseChallenges = [
    [
        'name' => '30 Menit Olahraga Setiap Hari',
        'description' => 'Lakukan olahraga minimal 30 menit setiap hari',
        'category' => 'exercise',
        'type' => 'duration',
        'target' => 30, // dalam menit
        'icon' => 'activity',
        'status' => 'active'
    ],
    [
        'name' => 'Latihan Intensitas Tinggi',
        'description' => 'Lakukan latihan dengan intensitas tinggi',
        'category' => 'exercise',
        'type' => 'intensity',
        'target' => 'high', // high intensity
        'icon' => 'zap',
        'status' => 'active'
    ],
    [
        'name' => 'Target Kalori Terbakar',
        'description' => 'Bakar minimal 300 kalori setiap hari',
        'category' => 'exercise',
        'type' => 'calories_burned',
        'target' => 300, // dalam kalori
        'icon' => 'flame',
        'status' => 'active'
    ]
];

// Data tantangan tidur
$sleepChallenges = [
    [
        'name' => '7-9 Jam Tidur per Hari',
        'description' => 'Tidur 7-9 jam setiap malam selama seminggu',
        'category' => 'sleep',
        'type' => 'duration',
        'target' => [
            'min' => 7,
            'max' => 9
        ],
        'icon' => 'moon',
        'status' => 'active'
    ],
    [
        'name' => 'Kualitas Tidur Baik',
        'description' => 'Capai kualitas tidur baik atau sangat baik',
        'category' => 'sleep',
        'type' => 'quality',
        'target' => ['Baik', 'Sangat Baik'], // array nilai yang diterima
        'icon' => 'star',
        'status' => 'active'
    ],
    [
        'name' => 'Jadwal Tidur Konsisten',
        'description' => 'Jaga perbedaan waktu tidur maksimal 1 jam',
        'category' => 'sleep',
        'type' => 'consistency',
        'target' => 1, // maksimal perbedaan dalam jam
        'icon' => 'clock',
        'status' => 'active'
    ]
];

// Masukkan semua tantangan ke database
try {
    $challengesCollection->insertMany($nutritionChallenges);
    $challengesCollection->insertMany($exerciseChallenges);
    $challengesCollection->insertMany($sleepChallenges);
    echo "Berhasil menambahkan tantangan ke database!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 