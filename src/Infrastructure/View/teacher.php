<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Portal - School Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px;
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
        <h1>Teacher Portal</h1>
        <p>Welcome to the School Management System - Teacher Section</p>
        
        <div class="nav">
            <a href="/">Return to Home</a>
            <a href="/assign-teacher">Assign Teacher to Department</a>
            <a href="/create-teacher">Create New Teacher</a>
        </div>
    <h2>Teachers List</h2>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #001489; color: white;">
                <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Name</th>
                <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Email</th>
                <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Department</th>
                <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Specialization</th>
                <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td style="padding: 12px; text-align: left; border: 1px solid #ddd;"><?php
                        foreach ($users as $user) {
                            if ($user->getId() === $teacher->getUserId()) {
                                echo htmlspecialchars($user->getName());
                                break;
                            }
                        }
                    ?></td>
                    <td style="padding: 12px; text-align: left; border: 1px solid #ddd;"><?php foreach ($users as $user) {
                            if ($user->getId() === $teacher->getUserId()) {
                                echo htmlspecialchars($user->getEmail());
                                break;
                            }
                        }
                    ?></td>
                    <td style="padding: 12px; text-align: left; border: 1px solid #ddd;"><?php
                        foreach ($departments as $department) {
                            if ( $teacher->getDepartmentId() !== null && $department->getId() === $teacher->getDepartmentId()) {
                                echo htmlspecialchars($department->getName());
                                break;
                            }
                        }
                    ?></td>
                    <td style="padding: 12px; text-align: left; border: 1px solid #ddd;"><?= htmlspecialchars($teacher->getSpecialty()) ?></td>
                    <td style="padding: 12px; text-align: left; border: 1px solid #ddd;"><a href="/delete-teacher?id=<?= htmlspecialchars($teacher->getId()) ?>" style="color: #f44336; text-decoration: none;" onclick="return confirm('Are you sure you want to delete this teacher?');">Delete</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php
    require_once __DIR__ . '/partials/footer.php';
    ?>
    
</body>
</html>

