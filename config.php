<?php

define('BASE_PATH', dirname(__FILE__));
define('CORE_PATH', BASE_PATH . '/core');
define('UPLOADS_PATH', BASE_PATH . '/uploads');

define('BASE_URL', '/');
define('PUBLIC_URL', BASE_URL . 'public');
define('UPLOADS_URL', BASE_URL . 'uploads');

define('DB_DRIVER', 'sqlite');
define('DB_FILE', BASE_PATH . '/sql/database.sqlite');

define('SITE_NAME', 'ODaCo');
define('SITE_VERSION', '1.0');

define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/avi', 'video/mkv']);

