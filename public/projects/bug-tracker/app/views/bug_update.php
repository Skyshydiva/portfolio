<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Bug - Bug Tracker</title>
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
            transition: background-color 0.3s;
        }

        header button:hover {
            background-color: #004080;
        }

        .container {
            max-width: 60%;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="date"],
        select,
        textarea {
            width: 96%;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button {
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-link {
            margin-top: 20px;
            text-align: center;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        .back-link a:hover {
            color: #004080;
        }

        .error-message {
            color: red;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
        <p>You must be logged in to view this page. <a href="?login">Login here</a>.</p>
    <?php else: ?>
        <header>
            <button onclick="window.location.href='?home'">Back to Home</button>
            <h1>Update Bug</h1>
            <button onclick="window.location.href='?logout'">Logout</button>
        </header>

        <div class="container">
            <?php if (isset($error)): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form id="updateBugForm" action="?bug/update/<?= htmlspecialchars($bug->getId()) ?>" method="post" onsubmit="return validateForm()">
                <label for="projectId">Project:</label>
                <select name="projectId" id="projectId" required>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?= htmlspecialchars($project->getId()) ?>" <?= $project->getId() == $bug->getProjectId() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($project->getName()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="summary">Summary:</label>
                <input type="text" name="summary" id="summary" value="<?= htmlspecialchars($bug->getSummary()) ?>" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description" required><?= htmlspecialchars($bug->getDescription()) ?></textarea>

                <?php if ($_SESSION['user_role'] === 'Admin' || $_SESSION['user_role'] === 'Manager'): ?>
                    <label for="assignedToId">Assigned To (User ID):</label>
                    <select name="assignedToId" id="assignedToId">
                        <option value="">Unassigned</option> <!-- Default to Unassigned -->
                        <?php foreach ($users as $user): ?>
                            <!-- Ensure that the user is not Admin or Manager -->
                            <?php if ($user['RoleID'] !== '1' && $user['RoleID'] !== '2'): ?>
                                <option value="<?= htmlspecialchars($user['Id']) ?>" data-project-id="<?= htmlspecialchars($user['ProjectId']) ?>" <?= $user['Id'] == $bug->getAssignedToId() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['Name']) ?> (ID: <?= htmlspecialchars($user['Id']) ?>)
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select><br>
                <?php endif; ?>

                <label for="statusId">Status:</label>
                <select name="statusId" id="statusId" required>
                    <option value="2" <?= $bug->getStatusId() == 2 ? 'selected' : '' ?>>Assigned</option>
                    <option value="3" <?= $bug->getStatusId() == 3 ? 'selected' : '' ?>>Closed</option>
                </select>
                <div id="statusError" class="error-message" style="display: none;">Regular users cannot set status to "Unassigned".</div>

                <label for="priorityId">Priority:</label>
                <select name="priorityId" id="priorityId" required>
                    <option value="1" <?= $bug->getPriorityId() == 1 ? 'selected' : '' ?>>Low</option>
                    <option value="2" <?= $bug->getPriorityId() == 2 ? 'selected' : '' ?>>Medium</option>
                    <option value="3" <?= $bug->getPriorityId() == 3 ? 'selected' : '' ?>>High</option>
                    <option value="4" <?= $bug->getPriorityId() == 4 ? 'selected' : '' ?>>Urgent</option>
                </select>

                <?php
                    // Format the targetDate to YYYY-MM-DD for the input field
                    $targetDate = $bug->getTargetDate();
                    $formattedTargetDate = '';

                    if ($targetDate) {
                        $dateObject = new DateTime($targetDate);
                        $formattedTargetDate = $dateObject->format('Y-m-d');
                    }
                    ?>

                <!-- Target Resolution Date input -->
                <label for="targetDate">Target Resolution Date:</label>
                <input type="date" name="targetDate" id="targetDate" value="<?= htmlspecialchars($formattedTargetDate) ?>"><br>

                <label for="dateClosed">Actual Resolution Date:</label>
                <input type="date" name="dateClosed" id="dateClosed" value="<?= htmlspecialchars($bug->getDateClosed()) ?>">

                <label for="fixDescription">Fix Description:</label>
                <textarea name="fixDescription" id="fixDescription"><?= htmlspecialchars($bug->getFixDescription()) ?></textarea>

                <button type="submit">Update Bug</button>
            </form>

            <div class="back-link">
                <p><a href="?bug/view/<?= htmlspecialchars($bug->getId()) ?>">Back to Bug Details</a></p>
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
