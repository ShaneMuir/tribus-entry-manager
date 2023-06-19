<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta name="charset" content="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width initial-scale=1">

    <?php wp_head(); ?>

    <title><?php wp_title(); ?></title>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
