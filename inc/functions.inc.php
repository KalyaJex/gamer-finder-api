<?php

// function sanitizeInput(string $value) {
//     return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
// }

// function sanitizeEmail(string $email) {
//     return filter_var($email, FILTER_SANITIZE_EMAIL);
// }

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validateRequiredFields($data, array $requiredFields) {
    if (!isset($data)) {
        return [
            'isValid' => false,
            'missingFields' => $requiredFields
        ];
    }
    $missingFields = [];

    foreach ($requiredFields as $field) {
        if (!array_key_exists($field, $data)) {
            $missingFields[] = $field;
        }
    }

    return [
        'isValid' => empty($missingFields),
        'missingFields' => $missingFields
    ];
}

// function validatePassword(string $password, int $length, int $maxLength): bool {
//     $containsLetter = '/[a-zA-Z]/';
//     $containsNumber = '/[0-9]/';
//     $containsSpecialChar = '/[!@#\$%\^&\*\(\)\-_\=\+\{\}\[\]\|\\:;\"\'<>,\.\?\/~`]/';
//     return strlen($password) < $length && strlen($password) >= $maxLength && preg_match($containsLetter, $password) && preg_match($containsNumber, $password) && preg_match($containsSpecialChar, $password);
// }

// function validateUsername(string $username, int $maxLength): bool {
//     $pattern = '/^[a-zA-Z0-9_]+$/';
//     return strlen($username) >= $maxLength && preg_match($pattern, $username);
// }

