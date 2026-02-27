<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - School Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            display: flex;
            flex-direction: column;
            margin: 50px auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .nav {
            margin: 20px 0;
        }
        .nav a {
            display: inline-block;
            margin-right: 15px;
            padding: 10px 20px;
            background-color: #001489;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .nav a:hover {
            background-color: #000c5a;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/partials/header.php'; ?>
    <div class="container">
        <h1>Student Portal</h1>
        <p>Welcome to the School Management System - Student Section</p>
        
        <div class="nav">
            <a href="/">Return to Home</a>
            <a href="/assign-student">Assign Student to Course</a>
            <a href="/create-student">Create New Student</a>
        </div>
    <h2>Students List</h2>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #001489; color: white;">
                <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">ID</th>
                <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Name</th>
                <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Email</th>
                <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Courses</th>
                <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Enrollment Number</th>
                <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
            <tr style="border: 1px solid #ddd;">
                <td style="padding: 12px;"><?php echo htmlspecialchars($student->getId()); ?></td>
                <td style="padding: 12px;"><?php
                foreach ($users as $user) {
                    if ($user->getId() === $student->getUserId()) {
                        echo htmlspecialchars($user->getName());
                        break;
                    }
                }
                 ?></td>
                <td style="padding: 12px;"><?php 
                foreach ($users as $user) {
                    if ($user->getId() === $student->getUserId()) {
                        echo htmlspecialchars($user->getEmail());
                        break;
                        }
                        }
                        ?></td>
                <td style="padding: 12px;"><?php
                foreach ($courses as $course) {
                    if ($course->getId() === $student->getCourseId()) {
                        echo htmlspecialchars($course->getName());
                        break;
                    }
                }
                ?></td>
                <td style="padding: 12px;"><?php echo htmlspecialchars($student->getEnrollmentNumber()); ?></td>
                <td style="padding: 12px;"><a href="/delete-student?id=<?php echo urlencode($student->getId()); ?>" style="color: #f44336; text-decoration: none;" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    </body>
    <?php
    require_once __DIR__ . '/partials/footer.php';
    ?>
</html>
