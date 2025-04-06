<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bugs - Bug Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            margin: 0;
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

        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .bug-section {
            margin-bottom: 30px;
            display: none;
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

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .action-buttons {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <header>
        <!-- Back to Home Button -->
        <button onclick="window.location.href='?home'">Back to Home</button>

        <h1>Bug Management</h1>

        <!-- Logout Button -->
        <button onclick="window.location.href='?logout'">Logout</button>
    </header>

    <div class="container">
        <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
            <p>You must be logged in to view this page. <a href="?login">Login here</a>.</p>
        <?php else: ?>
            <div class="action-buttons">
                <a href="?bug/add">Add New Bug</a>
            </div>

            <!-- Dropdown for filter selection -->
            <div class="filter-section">
                <form>
                    <label for="filter">Filter bugs: </label>
                    <select id="filter" onchange="filterBugs()">
                        <option value="all">All Bugs by Project</option>
                        <option value="open">Open Bugs</option>
                        <option value="overdue">Overdue Bugs</option>
                        <option value="unassigned">Unassigned Bugs</option>
                    </select>
                </form>
            </div>

            <!-- All Bugs by Project -->
            <div id="all-bugs-section" class="bug-section">
                <h2>All Bugs by Project:</h2>
                <?php if (isset($groupedBugs) && count($groupedBugs) > 0): ?>
                    <?php foreach ($groupedBugs as $projectId => $bugs): ?>
                        <?php if (count($bugs) > 0): ?>
                            <h3>Grouped by Project: <?= htmlspecialchars($bugs[0]['projectName']) ?></h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Project</th>
                                        <th>Summary</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bugs as $bug): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($bug['id']) ?></td>
                                            <td><?= htmlspecialchars($bug['projectName']) ?></td>
                                            <td><?= htmlspecialchars($bug['summary']) ?></td>
                                            <td><?= htmlspecialchars($bug['statusName']) ?></td>
                                            <td><?= htmlspecialchars($bug['priorityName']) ?></td>
                                            <td>
                                                <a href="?bug/view/<?= htmlspecialchars($bug['id']) ?>">View</a>
                                                <?php if ($_SESSION['user_role'] === 'Admin' || $_SESSION['user_role'] === 'Manager' || $_SESSION['user_id'] == $bug['assignedToId']): ?>
                                                    <a href="?bug/update/<?= htmlspecialchars($bug['id']) ?>">Update</a>
                                                    <a href="javascript:void(0);" onclick="openDeleteModal(<?= htmlspecialchars($bug['id']) ?>)">Delete</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <h2>Grouped by Project: Unknown Project</h2>
                            <p>No bugs available for this project</p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No projects available</p>
                <?php endif; ?>
            </div>

            <!-- Open Bugs -->
            <div id="open-bugs-section" class="bug-section">
                <h2>Open Bugs:</h2>
                <?php if (isset($openBugs) && count($openBugs) > 0): ?>
                    <?php foreach ($openBugs as $projectId => $bugs): ?>
                        <?php if (count($bugs) > 0): ?>
                            <div style="margin-bottom: 30px;">
                                <h3>Project: <?= htmlspecialchars($bugs[0]['projectName']) ?></h3>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Project</th>
                                            <th>Summary</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bugs as $bug): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($bug['id']) ?></td>
                                                <td><?= htmlspecialchars($bug['projectName']) ?></td>
                                                <td><?= htmlspecialchars($bug['summary']) ?></td>
                                                <td><?= htmlspecialchars($bug['statusName']) ?></td>
                                                <td><?= htmlspecialchars($bug['priorityName']) ?></td>
                                                <td>
                                                    <a href="?bug/view/<?= htmlspecialchars($bug['id']) ?>">View</a>
                                                    <?php if ($_SESSION['user_role'] === 'Admin' || $_SESSION['user_role'] === 'Manager'): ?>
                                                        <a href="?bug/update/<?= htmlspecialchars($bug['id']) ?>">Update</a>
                                                        <a href="javascript:void(0);" onclick="openDeleteModal(<?= htmlspecialchars($bug['id']) ?>)">Delete</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No open bugs available</p>
                <?php endif; ?>
            </div>

            <!-- Overdue Bugs -->
            <div id="overdue-bugs-section" class="bug-section">
                <h2>Overdue Bugs:</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Project</th>
                            <th>Summary</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($overdueBugs) && count($overdueBugs) > 0): ?>
                            <?php foreach ($overdueBugs as $bug): ?>
                                <tr>
                                    <td><?= htmlspecialchars($bug['id']) ?></td>
                                    <td><?= htmlspecialchars($bug['projectName'] ?? 'Unknown Project') ?></td>
                                    <td><?= htmlspecialchars($bug['summary']) ?></td>
                                    <td><?= htmlspecialchars($bug['statusName'] ?? 'Unknown Status') ?></td>
                                    <td><?= htmlspecialchars($bug['priorityName'] ?? 'Unknown Priority') ?></td>
                                    <td>
                                        <a href="?bug/view/<?= htmlspecialchars($bug['id']) ?>">View</a>
                                        <?php if ($_SESSION['user_role'] === 'Admin' || $_SESSION['user_role'] === 'Manager'): ?>
                                            <a href="?bug/update/<?= htmlspecialchars($bug['id']) ?>">Update</a>
                                            <a href="javascript:void(0);" onclick="openDeleteModal(<?= htmlspecialchars($bug['id']) ?>)">Delete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No overdue bugs available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Unassigned Bugs -->
            <div id="unassigned-bugs-section" class="bug-section">
                <h2>Unassigned Bugs</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Project</th>
                            <th>Summary</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($unassignedBugs) && count($unassignedBugs) > 0): ?>
                            <?php foreach ($unassignedBugs as $bug): ?>
                                <tr>
                                    <td><?= htmlspecialchars($bug['id']) ?></td>
                                    <td><?= htmlspecialchars($bug['projectName'] ?? 'Unknown Project') ?></td>
                                    <td><?= htmlspecialchars($bug['summary']) ?></td>
                                    <td><?= htmlspecialchars($bug['statusName'] ?? 'Unknown Status') ?></td>
                                    <td><?= htmlspecialchars($bug['priorityName'] ?? 'Unknown Priority') ?></td>
                                    <td>
                                        <a href="?bug/view/<?= htmlspecialchars($bug['id']) ?>">View</a>
                                        <?php if ($_SESSION['user_role'] === 'Admin' || $_SESSION['user_role'] === 'Manager'): ?>
                                            <a href="?bug/update/<?= htmlspecialchars($bug['id']) ?>">Update</a>
                                            <a href="javascript:void(0);" onclick="openDeleteModal(<?= htmlspecialchars($bug['id']) ?>)">Delete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No unassigned bugs available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div id="deleteModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="document.getElementById('deleteModal').style.display='none'">&times;</span>
                    <h2>Confirm Delete</h2>
                    <p>Are you sure you want to delete this bug?</p>
                    <form id="deleteForm" action="" method="post">
                        <input type="hidden" name="confirmDelete" value="1">
                        <button type="submit">Yes, Delete</button>
                        <button type="button" onclick="document.getElementById('deleteModal').style.display='none'">Cancel</button>
                    </form>
                </div>
            </div>

            <script>
                function openDeleteModal(bugId) {
                    document.getElementById('deleteForm').action = "?bug/delete/" + bugId;
                    document.getElementById('deleteModal').style.display = 'block';
                }

                function filterBugs() {
                    const filter = document.getElementById("filter").value;
                    const sections = document.querySelectorAll(".bug-section");

                    // Hide all sections initially
                    sections.forEach(section => section.style.display = 'none');

                    // Show the selected section based on the filter value
                    if (filter === 'all') {
                        document.getElementById("all-bugs-section").style.display = 'block';
                    } else if (filter === 'open') {
                        document.getElementById("open-bugs-section").style.display = 'block';
                    } else if (filter === 'overdue') {
                        document.getElementById("overdue-bugs-section").style.display = 'block';
                    } else if (filter === 'unassigned') {
                        document.getElementById("unassigned-bugs-section").style.display = 'block';
                    }
                }

                // Display the default view
                document.getElementById("all-bugs-section").style.display = 'block';
            </script>
        <?php endif; ?>
    </div>
</body>

</html>
