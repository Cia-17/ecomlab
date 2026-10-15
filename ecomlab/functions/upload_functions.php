<?php
// Shared, SERVER-SIDE image upload checks.
// Used by add_product_action, update_product_action and the registration action.
//
// Why this is needed: $_FILES['x']['type'] and ['name'] are just text the
// browser sends - an attacker can set them to anything. So we never trust
// them. We look at the real file contents, and we choose the file extension
// ourselves, so an uploaded "shell.php" can never be saved as a .php file.

define('MAX_IMAGE_BYTES', 2 * 1024 * 1024);   // 2 MB

// Real image types we accept  =>  the extension WE give the saved file.
function allowed_image_types()
{
    return [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
}

// Validates one uploaded file and moves it into $destDir.
//
// Returns an array:
//   ['status' => 'none']                       no file was chosen (it is optional)
//   ['status' => 'ok',    'filename' => '..']  saved - store this filename in the DB
//   ['status' => 'error', 'error' => '..']     rejected - show this message
function save_uploaded_image($file, $destDir)
{
    // Nothing submitted at all
    if (!is_array($file) || !isset($file['error'])) {
        return ['status' => 'none'];
    }

    // Someone sending several files under one name is not a normal form
    if (is_array($file['error'])) {
        return ['status' => 'error', 'error' => 'Please upload a single image.'];
    }

    // 1. Did PHP receive the upload properly?
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['status' => 'none'];
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        return ['status' => 'error', 'error' => 'Image must be smaller than 2 MB.'];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['status' => 'error', 'error' => 'The upload failed. Please try again.'];
    }

    // 2. Must really be an HTTP upload (not a path the attacker made up)
    if (!is_uploaded_file($file['tmp_name'])) {
        return ['status' => 'error', 'error' => 'Invalid upload.'];
    }

    // 3. Size - measured on the server, not read from the browser's claim
    $size = filesize($file['tmp_name']);
    if ($size === false || $size === 0) {
        return ['status' => 'error', 'error' => 'The uploaded file is empty.'];
    }
    if ($size > MAX_IMAGE_BYTES) {
        return ['status' => 'error', 'error' => 'Image must be smaller than 2 MB.'];
    }

    // 4. Type - detected from the file's actual bytes
    $allowed = allowed_image_types();

    $info = @getimagesize($file['tmp_name']);      // false if it is not an image
    if ($info === false || !isset($info['mime'])) {
        return ['status' => 'error', 'error' => 'Only JPG, PNG, GIF and WEBP images are allowed.'];
    }
    $mime = $info['mime'];

    if (function_exists('finfo_open')) {           // second opinion, when available
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detected = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if ($detected !== $mime) {
            return ['status' => 'error', 'error' => 'Only JPG, PNG, GIF and WEBP images are allowed.'];
        }
    }

    if (!isset($allowed[$mime])) {
        return ['status' => 'error', 'error' => 'Only JPG, PNG, GIF and WEBP images are allowed.'];
    }

    // 5. Build our own filename. The extension comes from the detected type,
    //    never from what the user called the file.
    $filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $allowed[$mime];

    if (!is_dir($destDir) && !@mkdir($destDir, 0755, true)) {
        if (function_exists('log_error')) log_error("Upload folder missing: $destDir");
        return ['status' => 'error', 'error' => 'Could not save the image (upload folder is missing).'];
    }

    if (!move_uploaded_file($file['tmp_name'], rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $filename)) {
        if (function_exists('log_error')) log_error("move_uploaded_file failed for $destDir");
        return ['status' => 'error', 'error' => 'Could not save the image. Check the upload folder permissions.'];
    }
    @chmod(rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $filename, 0644);

    return ['status' => 'ok', 'filename' => $filename];
}
