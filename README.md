# Tribus CF7 Entry Manager

The Tribus CF7 Entry Manager is a WordPress plugin that allows you to save Contact Form 7 form entries into a database table and manage them in the admin panel.

## Features

- Save form entries into a database table
- Manage form entries in the WordPress admin panel
- Display form submissions in an HTML table
- Approve or decline pending submissions
- Send approval and refusal emails to end users
- Export approved entries to CSV

## Installation

1. Download the plugin.
2. Upload the plugin to your WordPress site.
3. Activate the plugin in the WordPress admin panel.

## Requirements

- WordPress version 6.2 or higher
- Contact Form 7 plugin version 5.7.7 or higher

## Usage

1. After activating the plugin, a new admin menu page called "CF7 Entries" will be added under the "Contact Form 7 Entries" menu.
2. On the "CF7 Entries" page, you can view and manage all form submissions in an HTML table.
3. Use the buttons in the "Action" column to approve or decline pending submissions.
4. Customize the email notifications sent to end users in the `tribus_send_cf7_approval_email()` and `tribus_send_cf7_refusal_email()` functions.
5. Export approved entries to CSV on the "Approved Entries CSV" sub-menu page.

## Contributing

We welcome contributions to the Tribus CF7 Entry Manager plugin. If you find any issues or have suggestions for improvements, please open a new issue or submit a pull request on our [GitHub repository](https://github.com/ShaneMuir/tribus-entry-manager).

### TODOs
Ideally the email notification needs to be managed and editable to the end users reqs,
so we need to re-work this a little as developer knowledge is currently req to edit and amend
email notifications.

## License

The Tribus CF7 Entry Manager plugin is licensed under the [MIT License](https://opensource.org/licenses/MIT).
