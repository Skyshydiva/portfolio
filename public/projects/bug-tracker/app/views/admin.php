<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bug Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

        header {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 1.8em;
        }

        header nav a {
            text-decoration: none;
            padding: 10px 15px;
            background-color: #0056b3;
            border-radius: 4px;
            color: #fff;
            transition: background-color 0.3s;
        }

        header nav a:hover {
            background-color: #004080;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .btn-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 30px;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            text-align: center;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        a,
        button {
            text-decoration: none;
            padding: 8px 12px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        a:hover,
        button:hover {
            background-color: #0056b3;
        }

    </style>
</head>

<body>
    <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
        <p>You must be logged in to view this page. <a href="?login">Login here</a>.</p>
    <?php else: ?>
        <header>
            <button onclick="window.location.href='?home'">Back to Home</button>
            <h1>Admin Dashboard</h1>
            <button onclick="window.location.href='?logout'">Logout</button>
        </header>

        <div class="container">
            <div class="btn-container">

                <?php if ($_SESSION['user_role'] === 'Admin'): ?>
                    <!-- Navigate to add user page -->
                    <a href="?admin/add-user" class="btn">Add User</a>

                    <!-- Navigate to delete user page -->
                    <a href="?admin/delete-user" class="btn">Delete User</a>
                <?php endif ?>

                <!-- Navigate to create project page -->
                <a href="?admin/add-project" class="btn">Create Project</a>

                <!-- Navigate to delete project page -->
                <a href="?admin/delete-project" class="btn">Delete Project</a>

                <!-- Navigate to assign user to project page -->
                <a href="?admin/assign-user" class="btn">Assign User to Project</a>
            </div>
        </div>
    <?php endif; ?>
</body>

</html>
