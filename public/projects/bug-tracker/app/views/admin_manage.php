<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Admin Management</title>

    <!-- Modal Styles -->
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
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #333;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            text-align: left;
        }

        th,
        td {
            padding: 12px;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        .btn {
            padding: 10px 20px;
            margin: 10px 0;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
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

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        select {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
        <p>You must be logged in to view this page. <a href="?login">Login here</a>.</p>
    <?php else: ?>
        <header>
            <button onclick="window.location.href='?home'">Back to Home</button>
            <h1><?= htmlspecialchars($title) ?></h1>
            <button onclick="window.location.href='?logout'">Logout</button>
        </header>

        <div class="container">
            <?php if ($action === 'add-user' && $_SESSION['user_role'] === 'Admin'): ?>
                <h2>Add User</h2>
                <form method="POST" action="?admin/add-user">
                    <label for="name">Name:</label>
                    <input type="text" name="name" required>

                    <label for="username">Username:</label>
                    <input type="text" name="username" required>

                    <label for="password">Password:</label>
                    <input type="password" name="password" required>

                    <label for="roleId">Role:</label>
                    <select name="roleId" id="roleId" required>
                        <option value="">Select a Role</option>
                        <option value="1">Admin</option>
                        <option value="2">Manager</option>
                        <option value="3">Regular User</option>
                    </select>

                    <label for="projectId">Assign to Project (Optional):</label>
                    <select name="projectId">
                        <option value=''>None</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= htmlspecialchars($project->getId()) ?>"><?= htmlspecialchars($project->getName()) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn">Add User</button>
                </form>

                <?php if (isset($success) && $success === true && isset($user)): ?>
                    <div id="successModal" class="modal">
                        <div class="modal-content">
                            <span class="close" onclick="document.getElementById('successModal').style.display='none'">&times;</span>
                            <h2>User Created Successfully!</h2>
                            <p><strong>ID:</strong> <?= htmlspecialchars($user['id']) ?></p>
                            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
                            <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                            <p><strong>Password:</strong> <?= htmlspecialchars($user['password']) ?></p>
                            <p><strong>Role ID:</strong> <?= htmlspecialchars($user['roleId']) ?></p>
                            <p><strong>Project ID:</strong> <?= htmlspecialchars($user['projectId']) ?: 'Not Assigned' ?></p>
                            <button onclick="document.getElementById('successModal').style.display='none'" class="btn">OK</button>
                        </div>
                    </div>
                    <script>
                        document.getElementById('successModal').style.display = 'block';
                    </script>
                <?php endif; ?>
            <?php elseif ($action === 'delete-user' && $_SESSION['user_role'] === 'Admin'): ?>
                <h2>Delete a User</h2>
                <?php if (isset($users) && count($users) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Project ID</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['Id']) ?></td>
                                    <td><?= htmlspecialchars($user['Name']) ?></td>
                                    <td><?= htmlspecialchars($user['ProjectId'] ?? 'None') ?></td>
                                    <td>
                                        <button class="btn" onclick="confirmDeleteUser(<?= htmlspecialchars($user['Id']) ?>, '<?= htmlspecialchars($user['Name']) ?>', '<?= htmlspecialchars($user['ProjectId'] ?? '') ?>')">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No users available for deletion.</p>
                <?php endif; ?>

                <!-- Delete Confirmation Modal -->
                <div id="deleteUserModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('deleteUserModal')">&times;</span>
                        <h2>Delete User Confirmation</h2>
                        <p>Are you sure you want to delete user: <span id="deleteUserName"></span>?</p>
                        <form action="?admin/delete-user" method="POST">
                            <input type="hidden" name="userId" id="deleteUserId">
                            <button type="submit" class="btn">Yes, Delete</button>
                            <button type="button" onclick="closeModal('deleteUserModal')" class="btn">No, Cancel</button>
                        </form>
                    </div>
                </div>

                <!-- Project Confirmation Modal -->
                <div id="deleteUserProjectModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('deleteUserProjectModal')">&times;</span>
                        <h2>Warning</h2>
                        <p>By deleting this user, you are also removing them from project: <span id="deleteUserProjectName"></span>. Do you wish to continue?</p>
                        <form action="?admin/delete-user" method="POST">
                            <input type="hidden" name="userId" id="deleteUserProjectId">
                            <button type="submit" class="btn">Yes, Delete</button>
                            <button type="button" onclick="closeModal('deleteUserProjectModal')" class="btn">No, Cancel</button>
                        </form>
                    </div>
                </div>
            <?php elseif ($action === 'add-project' && $_SESSION['user_role'] === 'Admin'): ?>
                <h2>Add Project</h2>
                <?php if (isset($error)): ?>
                    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <form action="?admin/add-project" method="POST">
                    <label for="projectName">Project Name:</label>
                    <input type="text" name="projectName" id="projectName" required>
                    <button type="submit" class="btn">Add Project</button>
                </form>

                <!-- Project Creation Success Modal -->
                <?php if (isset($success) && $success === true && isset($createdProject)): ?>
                    <div id="projectSuccessModal" class="modal" style="display: block;">
                        <div class="modal-content">
                            <span class="close" onclick="closeModal('projectSuccessModal')">&times;</span>
                            <h2>Project Added Successfully</h2>
                            <p>The following project has been added:</p>
                            <ul>
                                <li><strong>ID:</strong> <?= htmlspecialchars($createdProject['id']) ?></li>
                                <li><strong>Name:</strong> <?= htmlspecialchars($createdProject['name']) ?></li>
                            </ul>
                            <button onclick="closeModal('projectSuccessModal')" class="btn">OK</button>
                        </div>
                    </div>
                <?php endif; ?>
            <?php elseif ($action === 'delete-project' && $_SESSION['user_role'] === 'Admin'): ?>
                <h2>Delete Project</h2>
                <?php if (isset($projects) && count($projects) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Project Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td><?= htmlspecialchars($project->getId()) ?></td>
                                    <td><?= htmlspecialchars($project->getName()) ?></td>
                                    <td>
                                        <button class="btn" onclick="confirmDeleteProject(<?= htmlspecialchars($project->getId()) ?>, '<?= htmlspecialchars($project->getName()) ?>')">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No projects available for deletion.</p>
                <?php endif; ?>

                <!-- Delete Project Confirmation Modal -->
                <div id="deleteProjectModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="document.getElementById('deleteProjectModal').style.display='none'">&times;</span>
                        <h2>Delete Project Confirmation</h2>
                        <p>Are you sure you want to delete the project: <span id="deleteProjectName"></span>?</p>
                        <form action="?admin/delete-project" method="POST">
                            <input type="hidden" name="projectId" id="deleteProjectId">
                            <button type="submit" class="btn">Yes, Delete</button>
                            <button type="button" onclick="closeModal('deleteProjectModal')" class="btn">No, Cancel</button>
                        </form>
                    </div>
                </div>
            <?php elseif ($action === 'assign-user' && $_SESSION['user_role'] === 'Admin'): ?>
                <h2>Assign User to Project</h2>
                <form action="?admin/assign-user" method="POST">
                    <label for="userId">Select User:</label>
                    <select name="userId" id="userId" required>
                        <option value="">Select User</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['Id']) ?>"><?= htmlspecialchars($user['Name']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="projectId">Select Project:</label>
                    <select name="projectId" id="projectId" required>
                        <option value="">Select Project</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= htmlspecialchars($project->getId()) ?>"><?= htmlspecialchars($project->getName()) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn">Assign User to Project</button>
                </form>

                <!-- Success Modal -->
                <?php if (isset($success) && $success): ?>
                    <div id="successModal" class="modal" style="display: block;">
                        <div class="modal-content">
                            <span class="close" onclick="closeModal('successModal')">&times;</span>
                            <h2>Success</h2>
                            <p><?= htmlspecialchars($message) ?></p>
                            <button onclick="window.location.href='?admin'" class="btn">OK</button>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <p><a href="?admin">Back to Admin Dashboard</a></p>
        </div>

        <!-- JavaScript to Open and Close Modals -->
        <script>
            function confirmDeleteUser(userId, userName, userProjectId) {
                if (userProjectId && userProjectId !== 'None') {
                    document.getElementById('deleteUserProjectId').value = userId;
                    document.getElementById('deleteUserProjectName').innerText = userProjectId;
                    document.getElementById('deleteUserProjectModal').style.display = 'block';
                } else {
                    document.getElementById('deleteUserId').value = userId;
                    document.getElementById('deleteUserName').innerText = userName;
                    document.getElementById('deleteUserModal').style.display = 'block';
                }
            }

            function confirmDeleteProject(projectId, projectName) {
                document.getElementById('deleteProjectId').value = projectId;
                document.getElementById('deleteProjectName').textContent = projectName;
                document.getElementById('deleteProjectModal').style.display = 'block';
            }

            function closeModal(modalId) {
                document.getElementById(modalId).style.display = 'none';
            }
        </script>
    <?php endif; ?>
</body>

</html>
