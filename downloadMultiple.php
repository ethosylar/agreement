<?php
session_start();

if (!isset($_SESSION['department'])) {
    echo "<script>
            alert('You must be logged in to view this page.');
            window.location.href='login.html';
          </script>";
    exit();
}

if (isset($_POST['files']) && !empty($_POST['files'])) {
    $files = $_POST['files'];
    $zip = new ZipArchive();
    $zipFilename = "downloads_" . time() . ".zip";

    if ($zip->open($zipFilename, ZipArchive::CREATE) !== TRUE) {
        exit("Unable to create zip file.");
    }

    // Add each file to the ZIP archive
    foreach ($files as $file) {
        $filePath = 'uploads/' . $file;
        if (file_exists($filePath)) {
            $zip->addFile($filePath, $file);
        }
    }

    $zip->close();

    // Force download the ZIP file
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zipFilename) . '"');
    header('Content-Length: ' . filesize($zipFilename));
    flush();
    readfile($zipFilename);

    // Delete the temporary ZIP file after download
    unlink($zipFilename);
    exit();
} else {
    echo "<script>alert('No files selected.'); window.location.href='dashboard.php';</script>";
}
?>
