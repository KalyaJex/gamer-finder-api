<?php

function sanitizeInput(string $value) {
  return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function sanitizeEmail(string $email) {
  return filter_var($email, FILTER_SANITIZE_EMAIL);
}