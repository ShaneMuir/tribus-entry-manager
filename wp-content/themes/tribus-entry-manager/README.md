# Tribus CF7 Entry Manager Theme

The Tribus CF7 Entry Manager Theme is a simple WordPress theme designed to work seamlessly with the Tribus CF7 Entry Manager plugin. It provides an index page that renders a form shortcode directly in the template file.

## Usage

1. Install and activate the Tribus CF7 Entry Manager plugin.
2. Activate the Tribus CF7 Entry Manager Theme.
3. Add Cf7 shortcode to themes index.php

## Theme Configuration

### Local SMTP for Debugging

When developing the Tribus CF7 Entry Manager plugin, you may want to test email functionality using a local SMTP server (I've been using mailgun). To enable local SMTP for debugging:

1. Open the `functions.php` file located in the theme's directory.
2. Locate the following code block:

    ```php
    // SMTP Settings (for debugging)
    const WORDPRESS_SMTP_AUTH = false;
    const WORDPRESS_SMTP_SECURE = '';
    const WORDPRESS_SMTP_HOST = 'localhost';
    const WORDPRESS_SMTP_PORT = '1025';
    const WORDPRESS_SMTP_USERNAME = null;
    const WORDPRESS_SMTP_PASSWORD = null;
    ```

3. Update the values of `WORDPRESS_SMTP_AUTH`, `WORDPRESS_SMTP_SECURE`, `WORDPRESS_SMTP_HOST`, `WORDPRESS_SMTP_PORT`, `WORDPRESS_SMTP_USERNAME`, and `WORDPRESS_SMTP_PASSWORD` with your local SMTP server configuration.

## Customisation

The Tribus CF7 Entry Manager Theme provides basic styling and layout for the index page. You can customize the appearance of the page by modifying the theme's CSS file located in the `css` directory.

## Contributing

We welcome contributions to the Tribus CF7 Entry Manager Theme. If you find any issues or have suggestions for improvements, please open a new issue or submit a pull request on our [GitHub repository](https://github.com/ShaneMuir/tribus-entry-manager).

## License

The Tribus CF7 Entry Manager Theme is licensed under the [MIT License](https://opensource.org/licenses/MIT).
