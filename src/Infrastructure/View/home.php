<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - School Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        
        .container {
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        
        h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 2em;
        }
        
        p {
            color: #666;
            font-size: 1.1em;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .nav {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .nav a {
            display: inline-block;
            padding: 15px 25px;
            background-color: #001489;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-weight: bold;
        }
        
        .nav a:hover {
            background-color: #000c5a;
            transform: translateY(-2px);
        }
        
        
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/partials/header.php'; ?>
    <main>
    <div class="container">
            <h2>Home</h2>
        <p>Welcome to the School Management System - Home Section</p>
        
        <div class="nav">
            <a href="/student">Student Portal</a>
            <a href="/teacher">Teacher Portal</a>
            <a href="/department">Department Portal</a>
            <a href="/course">Course Portal</a>
        </div>
    </div>
    </main>
    <?php
    require_once __DIR__ . '/partials/footer.php';
    ?>
</body>
</html>
