# Documentation for Tribus CF7 Entry Manager

This plugin allows you to save Contact Form 7 form entries into a database table and manage them in the admin panel.

## Installation

1. Download the plugin.
2. Upload the plugin to your WordPress site.
3. Activate the plugin in the WordPress admin panel.

## Requirements

This plugin requires the Contact Form 7 plugin to be installed and activated for it to work. If Contact Form 7 is not installed or activated, the plugin will be deactivated.

## Database Table Creation

When the plugin is activated, it creates a database table to store the form entries. The table is created based on the form fields found in the Contact Form 7 form instances. Each field becomes a column in the table.

## Enqueue Scripts and Styles

The plugin enqueues JavaScript and CSS files in the WordPress admin panel and the frontend of the website.

## Saving Form Entries

The plugin hooks into the `wpcf7_mail_sent` action to save form submissions into the database table. The submission data is retrieved using the `WPCF7_Submission::get_instance()` method and then inserted into the database table.

## Admin Menu

The plugin adds an admin menu page called "CF7 Entries" under the "Contact Form 7 Entries" menu. On this page, all the form submissions are displayed in an HTML table. Each row represents a form submission, and the columns represent the form fields. The status of each submission and whether an email has been sent are also displayed. The admin user can approve or decline pending submissions by clicking the corresponding buttons in the "Action" column.

## Approval and Refusal Emails

When an admin user approves or declines a submission, the plugin sends an email notification to the end user. The email content and recipients can be customized in the `tribus_send_cf7_approval_email()` and `tribus_send_cf7_refusal_email()` functions.

## Exporting Approved Entries to CSV

The plugin adds a sub-menu page called "Approved Entries CSV" under the "Contact Form 7 Entries" menu. On this page, an admin user can download a CSV file containing all the approved entries. To download the CSV file, the admin user must enter a password. If the password is valid, the plugin generates the CSV file and initiates the download.

## Plugin Configuration

There are no specific configuration options for this plugin. The plugin functions are defined in the main plugin file, `tribus-cf7-entry-manager.php`. The required files for sending emails are included using the `require_once` function.

## Conclusion

The Tribus CF7 Entry Manager plugin provides a solution for saving Contact Form 7 form entries into a database table and managing them in the WordPress admin panel. It offers features like database table creation, form entry saving, email notifications, and exporting entries to CSV.

