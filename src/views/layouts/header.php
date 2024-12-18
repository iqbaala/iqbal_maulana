<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WellBe - Aplikasi Gaya Hidup Sehat</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/public/assets/images/favicon.png">
    
    <!-- Custom Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Back to Home Button for Auth Pages -->
    <?php 
    $current_page = $_SERVER['REQUEST_URI'];
    if (strpos($current_page, '/login') !== false || strpos($current_page, '/register') !== false): 
    ?>
    <div class="fixed top-4 left-4 z-50">
        <a href="/" class="flex items-center justify-center w-12 h-12 bg-white/80 backdrop-blur-sm rounded-full shadow-lg hover:bg-white transition-all duration-300 text-gray-600 hover:text-gray-800 hover:scale-110">
            <i class="fas fa-home text-xl"></i>
        </a>
    </div>
    <?php endif; ?>
</body>
</html> 