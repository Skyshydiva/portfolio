<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Bug Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
        }

        header {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        header h1 {
            margin: 0;
            font-size: 1.8em;
            text-align: center;
        }

        header nav {
            position: absolute;
            right: 20px;
        }

        header nav a {
            color: #fff;
            text-decoration: none;
            padding: 8px 15px;
            background-color: #0056b3;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        header nav a:hover {
            background-color: #004080;
        }

        .content {
            padding: 40px;
            text-align: center;
        }

        nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 0;
        }

        nav ul li {
            display: inline;
        }

        nav ul a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        nav ul a:hover {
            background-color: #d1e7ff;
        }
    </style>
</head>

<body>
    <header>
        <h1>Welcome to the Bug Tracker Application</h1>
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
            <nav>
                <a href="?logout">Logout</a>
            </nav>
        <?php endif; ?>
    </header>

    <div class="content">
        <nav>
            <ul>
                <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
                    <li><a href="?login">Login</a></li>
                <?php else: ?>
                    <?php if ($_SESSION['user_role'] === 'Admin' || $_SESSION['user_role'] === 'Manager'): ?>
                        <li><a href="?admin">Admin Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="?bug">Bug Management</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</body>

</html>
