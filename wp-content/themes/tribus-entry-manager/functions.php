<?php

// Use PHPMailer instead of wp_mail (locally)
add_action('wp_mail_failed', 'action_wp_mail_failed', 10, 1);
add_action('phpmailer_init', function ($phpmailer) {

$phpmailer->isSMTP();
$phpmailer->SMTPAuth = WORDPRESS_SMTP_AUTH;
$phpmailer->SMTPSecure = WORDPRESS_SMTP_SECURE;
$phpmailer->SMTPAutoTLS = false;
$phpmailer->Host = WORDPRESS_SMTP_HOST;
$phpmailer->Port = WORDPRESS_SMTP_PORT;

$phpmailer->Username = WORDPRESS_SMTP_USERNAME;
$phpmailer->Password = WORDPRESS_SMTP_PASSWORD;
});

// Putting these here since nothing is sensitive
const WORDPRESS_SMTP_AUTH = false;
const WORDPRESS_SMTP_SECURE = '';
const WORDPRESS_SMTP_HOST = 'localhost';
const WORDPRESS_SMTP_PORT = '1025';
const WORDPRESS_SMTP_USERNAME = null;
const WORDPRESS_SMTP_PASSWORD = null;

add_theme_support( 'title-tag' );
