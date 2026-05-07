<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Student to Course</title>
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
            border-bottom: 3px solid #001489;
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
            border: 1px solid #001489;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            background-color: #001489;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #000c5a;
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
            color: #001489;
            text-decoration: none;
        }
        .nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Student</h1>

        <?php if (isset($message)): ?>
            <div class="message <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/create-student">
            <!-- user  part-->
            <div class="form-group">
                <label>Insert Name:</label>
                <input type="text" name="name" required>
                <label>Insert Email:</label>
                <input type="email" name="email" required>
                <label>Insert Enrollment Number:</label>
                <input type="text" name="enrollment_number" required>
            </div>
            <label for="course_id">Select Course:</label>
                <select name="course_id" id="course_id">
                    <option value="">-- Choose a course --</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course->getId() ?>">
                            <?= htmlspecialchars($course->getName()) ?> (<?= htmlspecialchars($course->getCode()) ?>) - <?= $course->getCredits() ?> credits
                        </option>
                    <?php endforeach; ?>
                </select>

            <button type="submit">Create Student</button>
        </form>

        <div class="nav">
            <a href="/student">← Back to Student Portal</a>
        </div>
    </div>
</body>
</html>
