<?php
// Start the session to store the current directory
session_start();

// Initialize the current directory
if (!isset($_SESSION['current_dir'])) {
    $_SESSION['current_dir'] = getcwd(); // Start with the current working directory
}

// Handle directory change request
if (isset($_GET['dir'])) {
    $new_dir = realpath($_SESSION['current_dir'] . DIRECTORY_SEPARATOR . $_GET['dir']);
    if (is_dir($new_dir)) {
        $_SESSION['current_dir'] = $new_dir;
    } else {
        echo "<p style='color: red;'>Invalid directory.</p>";
    }
}

// Handle "Go Up" request
if (isset($_GET['up'])) {
    $parent_dir = realpath($_SESSION['current_dir'] . DIRECTORY_SEPARATOR . '..');
    if ($parent_dir !== false && is_dir($parent_dir)) {
        $_SESSION['current_dir'] = $parent_dir;
    }
}

// Handle file upload
if (isset($_FILES['file_to_upload'])) {
    $upload_file = $_SESSION['current_dir'] . DIRECTORY_SEPARATOR . basename($_FILES['file_to_upload']['name']);
    if (move_uploaded_file($_FILES['file_to_upload']['tmp_name'], $upload_file)) {
        echo "<p style='color: green;'>File uploaded successfully.</p>";
    } else {
        echo "<p style='color: red;'>File upload failed.</p>";
    }
}

// Handle file download
if (isset($_GET['download'])) {
    $file_path = $_SESSION['current_dir'] . DIRECTORY_SEPARATOR . $_GET['download'];
    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        echo "<p style='color: red;'>File not found.</p>";
    }
}

// Handle bulk download (zip the entire directory)
if (isset($_GET['bulk_download'])) {
    $current_dir = $_SESSION['current_dir'];
    $zip_file_name = basename($current_dir) . '.zip';
    $zip_file_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zip_file_name;

    // Create a new zip archive
    $zip = new ZipArchive();
    if ($zip->open($zip_file_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($current_dir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $file_path = $file->getRealPath();
                $relative_path = substr($file_path, strlen($current_dir) + 1);
                $zip->addFile($file_path, $relative_path);
            }
        }

        $zip->close();

        // Send the zip file to the browser
        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zip_file_name . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($zip_file_path));
        readfile($zip_file_path);

        // Delete the temporary zip file
        unlink($zip_file_path);
        exit;
    } else {
        echo "<p style='color: red;'>Failed to create zip file.</p>";
    }
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $file_path = $_SESSION['current_dir'] . DIRECTORY_SEPARATOR . $_GET['delete'];
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            echo "<p style='color: green;'>File deleted successfully.</p>";
        } else {
            echo "<p style='color: red;'>Failed to delete file.</p>";
        }
    } else {
        echo "<p style='color: red;'>File not found.</p>";
    }
}

// Handle file creation
if (isset($_POST['create_file'])) {
    $file_name = $_POST['file_name'];
    $file_path = $_SESSION['current_dir'] . DIRECTORY_SEPARATOR . $file_name;
    if (!file_exists($file_path)) {
        $file_content = $_POST['file_content'] ?? '';
        if (file_put_contents($file_path, $file_content) !== false) {
            echo "<p style='color: green;'>File created successfully.</p>";
        } else {
            echo "<p style='color: red;'>Failed to create file.</p>";
        }
    } else {
        echo "<p style='color: red;'>File already exists.</p>";
    }
}

// Handle file editing
if (isset($_POST['save_file'])) {
    $file_path = $_SESSION['current_dir'] . DIRECTORY_SEPARATOR . $_POST['file_name'];
    if (file_exists($file_path)) {
        file_put_contents($file_path, $_POST['file_content']);
        echo "<p style='color: green;'>File saved successfully.</p>";
    } else {
        echo "<p style='color: red;'>File not found.</p>";
    }
}

// Get the list of files and directories in the current directory
$current_dir = $_SESSION['current_dir'];
$files = scandir($current_dir);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP File Manager</title>
    <style>
        /* Dark Theme Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #1e1e1e;
            color: #e0e0e0;
        }
        h1 {
            color: #ffffff;
        }
        p {
            color: #e0e0e0;
        }
        .file-list {
            list-style-type: none;
            padding: 0;
        }
        .file-list li {
            padding: 10px;
            background-color: #2d2d2d;
            margin-bottom: 5px;
            border: 1px solid #444;
            border-radius: 5px;
        }
        .file-list li a {
            text-decoration: none;
            color: #4dabf7;
        }
        .file-list li a:hover {
            text-decoration: underline;
        }
        .file-actions {
            margin-top: 10px;
        }
        .file-actions a {
            margin-right: 10px;
            color: #ff6b6b;
        }
        .file-actions a:hover {
            color: #ff8787;
        }
        textarea {
            width: 100%;
            height: 300px;
            background-color: #2d2d2d;
            color: #e0e0e0;
            border: 1px solid #444;
            border-radius: 5px;
            padding: 10px;
        }
        .create-file-form {
            margin-bottom: 20px;
            background-color: #2d2d2d;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #444;
        }
        .create-file-form label {
            display: block;
            margin-bottom: 5px;
            color: #e0e0e0;
        }
        .create-file-form input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            background-color: #3d3d3d;
            color: #e0e0e0;
            border: 1px solid #444;
            border-radius: 5px;
        }
        .create-file-form button {
            padding: 8px 15px;
            background-color: #4dabf7;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .create-file-form button:hover {
            background-color: #6bc0ff;
        }
        .upload-form {
            margin-bottom: 20px;
            background-color: #2d2d2d;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #444;
        }
        .upload-form input[type="file"] {
            margin-bottom: 10px;
        }
        .upload-form button {
            padding: 8px 15px;
            background-color: #4dabf7;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .upload-form button:hover {
            background-color: #6bc0ff;
        }
        .editor-form {
            background-color: #2d2d2d;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #444;
        }
        .editor-form button {
            padding: 8px 15px;
            background-color: #4dabf7;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .editor-form button:hover {
            background-color: #6bc0ff;
        }
    </style>
</head>
<body>
    <h1>PHP File Manager</h1>
    <p>Current Directory: <?php echo $current_dir; ?></p>

    <!-- Go Up Button -->
    <p><a href="?up=1" style="color: #4dabf7;">Go Up</a></p>

    <!-- Bulk Download Button -->
    <p><a href="?bulk_download=1" style="color: #4dabf7;">Download All Files as ZIP</a></p>

    <!-- File Upload Form -->
    <div class="upload-form">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="file_to_upload" required>
            <button type="submit">Upload File</button>
        </form>
    </div>

    <!-- Create File Form -->
    <div class="create-file-form">
        <h2>Create a New File</h2>
        <form method="post">
            <label for="file_name">File Name:</label>
            <input type="text" name="file_name" id="file_name" required>
            <br>
            <label for="file_content">File Content (optional):</label>
            <br>
            <textarea name="file_content" id="file_content"></textarea>
            <br>
            <button type="submit" name="create_file">Create File</button>
        </form>
    </div>

    <!-- List of Files and Directories -->
    <ul class="file-list">
        <?php
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue; // Skip . and ..
            $file_path = $current_dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($file_path)) {
                echo "<li><a href='?dir=$file'>[DIR] $file</a></li>";
            } else {
                echo "<li>
                        <a href='?download=$file'>$file</a>
                        <span class='file-actions'>
                            <a href='?edit=$file'>Edit</a>
                            <a href='?delete=$file' onclick='return confirm(\"Are you sure you want to delete this file?\")'>Delete</a>
                        </span>
                      </li>";
            }
        }
        ?>
    </ul>

    <!-- File Editor -->
    <?php
    if (isset($_GET['edit'])) {
        $file_path = $current_dir . DIRECTORY_SEPARATOR . $_GET['edit'];
        if (file_exists($file_path)) {
            $file_content = htmlspecialchars(file_get_contents($file_path));
            echo "<div class='editor-form'>
                    <h2>Editing: $_GET[edit]</h2>
                    <form method='post'>
                        <input type='hidden' name='file_name' value='$_GET[edit]'>
                        <textarea name='file_content'>$file_content</textarea>
                        <br>
                        <button type='submit' name='save_file'>Save</button>
                    </form>
                  </div>";
        } else {
            echo "<p style='color: red;'>File not found.</p>";
        }
    }
    ?>
</body>
</html>