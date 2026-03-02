<?php
// Language initialization file
// This file handles language selection and loading

// Check if language is set in session, otherwise use default
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en'; // Default language
}

// Get current language from session
$current_language = $_SESSION['language'];

// Check if user requested a language change
if (isset($_GET['lang'])) {
    $requested_lang = $_GET['lang'];
    
    // Only allow supported languages
    $supported_languages = array('en', 'fr', 'rw');
    
    if (in_array($requested_lang, $supported_languages)) {
        $_SESSION['language'] = $requested_lang;
        $current_language = $requested_lang;
    }
}

// Load the appropriate language file
$language_file = __DIR__ . '/../languages/' . $current_language . '.php';

if (file_exists($language_file)) {
    include($language_file);
} else {
    // Fallback to English if language file doesn't exist
    include(__DIR__ . '/../languages/en.php');
}

// Function to get language string
function __($key) {
    global $lang;
    return isset($lang[$key]) ? $lang[$key] : $key;
}
?>
