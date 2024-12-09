<?php
session_start();

// Initialize tasks and tickets if not already set
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = ['IPVPN', 'EMAIL', 'PASSWORD', 'FIREWALL'];
}
if (!isset($_SESSION['tickets'])) {
    $_SESSION['tickets'] = [];
}

// Handle ticket submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_ticket'])) {
    $ticket = [
        'from' => $_POST['from'] ?? '',
        'to' => $_POST['to'] ?? '',
        'task' => $_POST['task'] ?? '',
        'issue_time' => date('Y-m-d H:i:s'),
        'status' => 'Open',
        'urgent' => isset($_POST['urgent']) ? 'Yes' : 'No',
        'description' => $_POST['description'] ?? '',
    ];
    $_SESSION['tickets'][] = $ticket;
}

// Handle new task addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $new_task = $_POST['new_task'] ?? '';
    if (!empty($new_task) && !in_array($new_task, $_SESSION['tasks'])) {
        $_SESSION['tasks'][] = $new_task;
    }
}

// Handle task completion
if (isset($_GET['complete'])) {
    $index = (int)$_GET['complete'];
    if (isset($_SESSION['tickets'][$index])) {
        $_SESSION['tickets'][$index]['status'] = 'Completed';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Ticket Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            text-align: center;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
        }
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .form-group label {
            width: 150px;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group textarea {
            resize: vertical;
            height: 80px;
        }
        .form-group .checkbox {
            width: auto;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1>Simple Ticket Management System</h1>

    <h2>Create a New Ticket</h2>
    <form method="POST">
        <input type="hidden" name="create_ticket" value="1">
        <div class="form-group">
            <label for="from">From:</label>
            <input type="text" id="from" name="from" required>
        </div>
        <div class="form-group">
            <label for="to">To:</label>
            <input type="text" id="to" name="to" required>
        </div>
        <div class="form-group">
            <label for="task">Task:</label>
            <select id="task" name="task" required>
                <?php foreach ($_SESSION['tasks'] as $task): ?>
                    <option value="<?= htmlspecialchars($task) ?>"><?= htmlspecialchars($task) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
        </div>
        <div class="form-group">
            <label class="checkbox">Urgent:</label>
            <input type="checkbox" name="urgent">
        </div>
        <button type="submit">Create Ticket</button>
    </form>

    <h2>Add New Task</h2>
    <form method="POST">
        <input type="hidden" name="add_task" value="1">
        <div class="form-group">
            <label for="new_task">New Task:</label>
            <input type="text" id="new_task" name="new_task" required>
        </div>
        <button type="submit">Add Task</button>
    </form>

    <h2>All Tickets</h2>
    <table>
        <thead>
            <tr>
                <th>From</th>
                <th>To</th>
                <th>Task</th>
                <th>Issue Raised Time</th>
                <th>Status</th>
                <th>Urgent</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['tickets'] as $index => $ticket): ?>
                <tr>
                    <td><?= htmlspecialchars($ticket['from']) ?></td>
                    <td><?= htmlspecialchars($ticket['to']) ?></td>
                    <td><?= htmlspecialchars($ticket['task']) ?></td>
                    <td><?= htmlspecialchars($ticket['issue_time']) ?></td>
                    <td><?= htmlspecialchars($ticket['status']) ?></td>
                    <td><?= htmlspecialchars($ticket['urgent']) ?></td>
                    <td><?= htmlspecialchars($ticket['description']) ?></td>
                    <td>
                        <?php if ($ticket['status'] === 'Open'): ?>
                            <a href="?complete=<?= $index ?>">Mark as Completed</a>
                        <?php else: ?>
                            Completed
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
