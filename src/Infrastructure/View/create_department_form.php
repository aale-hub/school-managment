<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Student to Department</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            width: 100%;
            max-width: 500px;
        }
        h1 {
            color: #000000;
            border-bottom: 3px solid #1e90ff;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #000000;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #1e90ff;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            background-color: #1e90ff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #1873cc;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .nav {
            margin: 20px 0;
        }
        .nav a {
            color: #1e90ff;
            text-decoration: none;
        }
        .nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Department</h1>

        <?php if (isset($message)): ?>
            <div class="message <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/create-department">
            <!-- user  part-->
            <div class="form-group">
                <label>Insert Name:</label>
                <input type="text" name="name" required>
                <label>Insert Code:</label>
                <input type="text" name="code" required>
            </div>

            <button type="submit">Create Department</button>
        </form>

        <div class="nav">
            <a href="/department">← Back to Department Portal</a>
        </div>
    </div>
</body>
</html>
