<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bug Details - Bug Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
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

        header nav {
            text-align: right;
        }

        header nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 15px;
            padding: 8px 15px;
            background-color: #0056b3;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        header nav a:hover {
            background-color: #004080;
        }

        .content {
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
        }

        p {
            font-size: 1.1em;
            line-height: 1.6;
        }

        p strong {
            color: #555;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        a:hover {
            color: #004080;
        }

        .button-container {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1em;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #004080;
        }

        .back-button {
            margin-top: 30px;
            display: block;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
        <p style="text-align: center; margin-top: 50px;">You must be logged in to view this page. <a href="?login">Login here</a>.</p>
    <?php else: ?>
        <header>
            <h1>Bug Tracker - Bug Details</h1>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <nav>
                    <a href="?logout">Logout</a>
                </nav>
            <?php endif; ?>
        </header>

        <div class="content">
            <h1>Bug Details</h1>
            <p><strong>Summary:</strong> <?= htmlspecialchars($bug->getSummary()) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($bug->getDescription()) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($statusName) ?></p>
            <p><strong>Assigned To:</strong> <?= htmlspecialchars($assignedToName) ?></p>
            <p><strong>Date Raised:</strong> <?= htmlspecialchars($bug->getDateRaised()) ?></p>
            <p><strong>Priority:</strong> <?= htmlspecialchars($priorityName) ?></p>
            <p><strong>Target Resolution Date:</strong> <?= htmlspecialchars($bug->getTargetDate()) ?></p>

            <div class="back-button">
                <a href="?bug" class="button">Back to Bug Management</a>
            </div>

            <?php if ($_SESSION['user_role'] === 'Admin' || $_SESSION['user_role'] === 'Manager' || $_SESSION['user_id'] == $bug->getAssignedToId()): ?>
                <div class="button-container">
                    <a href="?bug/update/<?= htmlspecialchars($bug->getId()) ?>" class="button">Update Bug</a>
                    <a href="?bug/delete/<?= htmlspecialchars($bug->getId()) ?>" class="button">Delete Bug</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>

</html>
