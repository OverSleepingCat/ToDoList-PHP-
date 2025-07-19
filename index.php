<?php
$filename = "tasks.txt";

// Add a new task
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['task'])) {
    $task = trim($_POST['task']);
    if (!empty($task)) {
        file_put_contents($filename, "0|$task" . PHP_EOL, FILE_APPEND);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Toggle done status
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    $lines = file($filename, FILE_IGNORE_NEW_LINES);
    if (isset($lines[$id])) {
        $parts = explode('|', $lines[$id], 2);
        if (count($parts) === 2) {
            $done = $parts[0] == "1" ? "0" : "1";
            $text = $parts[1];
            $lines[$id] = "$done|$text";
            file_put_contents($filename, implode(PHP_EOL, $lines) . PHP_EOL);
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Delete a task
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $lines = file($filename, FILE_IGNORE_NEW_LINES);
    if (isset($lines[$id])) {
        unset($lines[$id]);
        file_put_contents($filename, implode(PHP_EOL, $lines) . PHP_EOL);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>To-Do List with Checkboxes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background: #f0f0f0;
        }
        h1 {
            margin-top: 30px;
        }
        form {
            margin: 20px;
        }
        input[type="text"] {
            padding: 8px;
            width: 200px;
        }
        button {
            padding: 8px 12px;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            background: #fff;
            margin: 8px auto;
            padding: 10px;
            width: 320px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        li form {
            margin: 0;
        }
        span.done {
            text-decoration: line-through;
            color: gray;
        }
    </style>
</head>
<body>
    <h1>✅ To-Do List with Checkboxes</h1>
    <form method="post">
        <input type="text" name="task" placeholder="Enter new task..." required>
        <button type="submit">Add Task</button>
    </form>

    <ul>
        <?php
        if (file_exists($filename)) {
            $tasks = file($filename, FILE_IGNORE_NEW_LINES);
            foreach ($tasks as $index => $line) {
                $parts = explode('|', $line, 2);
                if (count($parts) < 2) continue;

                $done = $parts[0];
                $text = $parts[1];
                $checked = $done == "1" ? "checked" : "";
                $class = $done == "1" ? "done" : "";

                echo "<li>
                        <form method='get' style='display:inline'>
                            <input type='hidden' name='toggle' value='$index'>
                            <input type='checkbox' onChange='this.form.submit()' $checked>
                        </form>
                        <span class='$class'>" . htmlspecialchars($text) . "</span>
                        <a href='?delete=$index' onclick='return confirm(\"Delete this task?\")'>❌</a>
                      </li>";
            }
        }
        ?>
    </ul>
</body>
</html>
