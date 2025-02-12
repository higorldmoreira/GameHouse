<?php

spl_autoload_register(function ($class) {
  $prefix = 'App\\';
  $baseDir = __DIR__ . '/src/';

  if (strpos($class, $prefix) === 0) {
    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
      require $file;
    } else {
      error_log("File not found for class: $class in $file");
    }
  }
});
