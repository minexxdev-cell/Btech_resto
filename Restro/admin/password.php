<?php
/**
 * Hash Cracker - Attempts to find original value of a hash
 * Works with MD5, SHA1, and other algorithms
 */

// The hash you want to crack
$target_hash = "903b21879b4a60fc9103c3334e4f6f62cf6c3a2d";

// Detect hash type based on length
$hash_length = strlen($target_hash);
$hash_type = match($hash_length) {
    32 => 'md5',
    40 => 'sha1',
    64 => 'sha256',
    default => 'unknown'
};

echo "Hash Type Detected: " . strtoupper($hash_type) . "\n";
echo "Target Hash: $target_hash\n";
echo str_repeat("-", 50) . "\n\n";

// Method 1: Common passwords dictionary
echo "Method 1: Checking common passwords...\n"; 
$common_passwords = [
    'password', '123456', '12345678', 'admin', 'root', 'qwerty',
    'letmein', 'welcome', 'monkey', '1234567890', 'abc123',
    'password123', 'admin123', 'test', 'user', 'guest'
];

foreach ($common_passwords as $password) {
    $hashed = hash($hash_type, $password);
    if ($hashed === $target_hash) {
        echo "✓ FOUND! Original value: '$password'\n";
        exit;
    }
}
echo "✗ Not found in common passwords\n\n";

// Method 2: Brute force numeric values (0-9999)
echo "Method 2: Brute forcing numeric values (0-9999)...\n";
for ($i = 0; $i < 10000; $i++) {
    $hashed = hash($hash_type, (string)$i);
    if ($hashed === $target_hash) {
        echo "✓ FOUND! Original value: '$i'\n";
        exit;
    }
}
echo "✗ Not found in numeric range\n\n";

// Method 3: Try alphanumeric combinations (limited length)
echo "Method 3: Trying short alphanumeric combinations...\n";
$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
$max_length = 4; // Increase cautiously - gets exponentially slower

function bruteforce($chars, $max_length, $target, $hash_type, $current = '') {
    if (strlen($current) <= $max_length) {
        $hashed = hash($hash_type, $current);
        if ($hashed === $target) {
            echo "✓ FOUND! Original value: '$current'\n";
            return true;
        }
        
        if (strlen($current) < $max_length) {
            for ($i = 0; $i < strlen($chars); $i++) {
                if (bruteforce($chars, $max_length, $target, $hash_type, $current . $chars[$i])) {
                    return true;
                }
            }
        }
    }
    return false;
}

if (!bruteforce($chars, $max_length, $target_hash, $hash_type)) {
    echo "✗ Not found in alphanumeric combinations\n\n";
}

// Method 4: Use online rainbow tables (suggestion)
echo "\nMethod 4: Online Rainbow Table Lookup\n";
echo "Try these online services:\n";
echo "- https://crackstation.net/\n";
echo "- https://www.md5online.org/\n";
echo "- https://hashes.com/\n";
echo "- https://hashtoolkit.com/\n\n";

echo "Hash cracking completed. No match found with current methods.\n";
echo "Consider:\n";
echo "1. Using a larger dictionary/wordlist\n";
echo "2. Checking online rainbow tables\n";
echo "3. The original value may be complex or salted\n";
?>