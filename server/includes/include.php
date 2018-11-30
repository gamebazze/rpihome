<?php

spl_autoload_register(function ($class_name) {
    include_once __DIR__ . "/classes/class." . $class_name . '.php';
});

require_once __DIR__ . "/functions/sessions.php";
include_once __DIR__ . "/functions/helpers.php";