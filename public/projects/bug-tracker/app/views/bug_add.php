<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Bug - Bug Tracker</title>
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

        header button {
            text-decoration: none;
            padding: 8px 12px;
            color: #fff;
            background-color: #0056b3;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        header button:hover {
            background-color: #004080;
        }

        .container {
            width: 60%;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 95%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button[type="submit"] {
            display: block;
            width: 100%;
            padding: 12px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
            font-weight: bold;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        .back-link a:hover {
            color: #0056b3;
        }

    </style>
</head>
<body>
<body>
    <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
        <p>You must be logged in to view this page. <a href="?login">Login here</a>.</p>
    <?php else: ?>
        <header>
            <!-- Back to Home Button -->
            <button onclick="window.location.href='?home'">Back to Home</button>

            <h1>Add a New Bug</h1>

            <!-- Logout Button -->
            <button onclick="window.location.href='?logout'">Logout</button>
        </header>

        <div class="container">
            <?php if (isset($error)): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form id="addBugForm" action="?bug/add" method="post" onsubmit="return validateForm()">
                <label for="projectId">Project:</label>
                <?php if ($_SESSION['user_role'] === 'Admin' || $_SESSION['user_role'] === 'Manager'): ?>
                    <select name="projectId" id="projectId" required>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= htmlspecialchars($project->getId()) ?>">
                                <?= htmlspecialchars($project->getName()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input type="hidden" name="projectId" value="<?= htmlspecialchars($userProject['Id']) ?>">
                    <p><?= htmlspecialchars($userProject['Project']) ?></p>
                <?php endif; ?>

                <label for="summary">Summary:</label>
                <input type="text" name="summary" id="summary" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description" required></textarea>

                <?php if ($_SESSION['user_role'] === 'Admin' || $_SESSION['user_role'] === 'Manager'): ?>
                    <label for="assignedToId">Assigned To (User ID):</label>
                    <select name="assignedToId" id="assignedToId">
                        <option value="">Unassigned</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['Id']) ?>" data-project-id="<?= htmlspecialchars($user['ProjectId']) ?>">
                                <?= htmlspecialchars($user['Name']) ?> (ID: <?= htmlspecialchars($user['Id']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

                <label for="priorityId">Priority:</label>
                <select name="priorityId" id="priorityId">
                    <option value="1">Low</option>
                    <option value="2">Medium</option>
                    <option value="3">High</option>
                    <option value="4">Urgent</option>
                </select>

                <label for="targetDate">Target Resolution Date:</label>
                <input type="date" name="targetDate" id="targetDate">

                <button type="submit">Add Bug</button>
            </form>

            <div class="back-link">
                <p><a href="?bug">Back to Bug Management</a></p>
            </div>
        </div>

        <script>
            function validateForm() {
                const projectId = document.getElementById('projectId').value;
                const assignedToSelect = document.getElementById('assignedToId');
                const assignedToId = assignedToSelect ? assignedToSelect.value : null;

                if (assignedToId) {
                    const selectedUserOption = assignedToSelect.querySelector(`option[value="${assignedToId}"]`);
                    const userProjectId = selectedUserOption.getAttribute('data-project-id');

                    if (userProjectId && userProjectId !== projectId && userProjectId !== 'null') {
                        alert('The selected user is assigned to a different project. Please choose another user or unassign them from their current project.');
                        return false; // Prevent form submission
                    }
                }
                return true;
            }
        </script>

    <?php endif; ?>
</body>

</html>
