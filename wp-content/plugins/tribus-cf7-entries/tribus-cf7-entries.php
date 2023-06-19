<?php
/*
Plugin Name: Tribus CF7 Entry Manager
Plugin URI: https://tribusdigital.com/
Description: Save CF7 form entries into a database table and manage them in the admin panel.
Version: 1.0.0
Author: Shane Muirhead
License: GPL2
*/

if (!defined('ABSPATH')) {
	exit; // Check if accessed directly
}


function tribus_cf7_entries_check_dependencies_on_activation() {
	/**
	 * This plugin depends on CF7 to be able to work
     * so this just checks if CF7 plugin is installed
     * and activated.
	 */
    if ( ! is_plugin_active('contact-form-7/wp-contact-form-7.php') ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );

        wp_die('Sorry, but this plugin requires Contact Form 7 to be installed and activated for it to work!');
    }
}
register_activation_hook( __FILE__, 'tribus_cf7_entries_check_dependencies_on_activation' );


function tribus_cf7_entry_manager_create_table() {
	/**
	 * Create the necessary database tables
     * req for this plugin to work.
     * Tables are created based on
     * form fields found in the CF7 form
     * instance.
	 */
	global $wpdb;
	$table_name = $wpdb->prefix . 'tribus_cf7_entries';

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
    ";

	$cf7_forms = get_posts(array(
		'post_type' => 'wpcf7_contact_form',
		'posts_per_page' => -1,
	));

	foreach ($cf7_forms as $form) {
		$cf7 = WPCF7_ContactForm::get_instance($form->ID);

		$form_fields_data = $cf7->scan_form_tags();
		$total_fields = count($form_fields_data);

		for ($i = 0; $i < $total_fields - 1; $i++) {
			$form_field = $form_fields_data[$i];
			$field_name = sanitize_key($form_field['name']);
			$field_name = str_replace('-', '_', $field_name);
			$sql .= "$field_name VARCHAR(255) NOT NULL,";
		}
	}

	$sql .= "
        status ENUM('pending', 'approved', 'declined') NOT NULL DEFAULT 'pending',
        email_sent TINYINT(1) NOT NULL DEFAULT 0, 
        PRIMARY KEY (id)
    ) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

register_activation_hook(__FILE__, 'tribus_cf7_entry_manager_create_table');


function tribus_cf7_entries_enqueue_scripts() {
	/**
	 * Add JS/CSS to the WP admin.
	 */
	wp_enqueue_script( 'tribus-cf7-entries', plugin_dir_url( __FILE__ ) . 'js/tribus-cf7-entries.js', array(), '1.0', true );
    wp_enqueue_style('tribus-cf7-entries', plugin_dir_url(__FILE__) . 'css/tribus-cf7-entries.css');
}

add_action( 'admin_enqueue_scripts', 'tribus_cf7_entries_enqueue_scripts' );

function tribus_cf7_entries_enqueue_frontend_scripts() {
	/**
	 * Adds JS/CSS to the frontend of our app.
	 */
    wp_enqueue_style('tribus-cf7-entries-frontend-style', plugin_dir_url( __FILE__ ) . 'css/tribus-cf7-entries-frontend.css');
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
}

add_action('wp_enqueue_scripts', 'tribus_cf7_entries_enqueue_frontend_scripts');


function tribus_cf7_entry_manager_save_entry($contact_form) {
	/**
	 * On mail sent save the form submission into
     * the database.
     * @hook_name: wpcf7_mail_sent
     * Depending on the req we use mail sent here
     * to save submission if the form was sent successfully
     * if no outgoing email is req you could use wpcf7_before_send_mail.
	 */
	global $wpdb;
	$table_name = $wpdb->prefix . 'tribus_cf7_entries';

	$submission = WPCF7_Submission::get_instance();

	if ($submission) {
		$data = $submission->get_posted_data();

		$entry_data = array();
		foreach ($contact_form->scan_form_tags() as $tag) {
			$field_name = $tag->name;
			$field_name_underscored = str_replace('-', '_', $field_name);

			if (isset($data[$field_name])) {
				$entry_data[$field_name_underscored] = $data[$field_name];
			}
		}

		$entry_data['status'] = 'pending';

		$wpdb->insert( $table_name, $entry_data );
	}
}
add_action('wpcf7_mail_sent', 'tribus_cf7_entry_manager_save_entry', 10, 1);


function tribus_cf7_entries_menu() {
	/**
	 * Admin Menu stuff.
	 */
    add_menu_page(
        'Contact Form 7 Entries',
        'CF7 Entries',
        'manage_options',
        'cf7-entries',
        'tribus_cf7_entries_page',
        'dashicons-email',
        30
    );

    add_submenu_page(
        'cf7-entries',
        'Approved Entries CSV',
        'Approved Entries CSV',
        'manage_options',
        'cf7-approved-entries-csv',
        'tribus_cf7_entries_submenu_page'
    );
}
add_action('admin_menu', 'tribus_cf7_entries_menu');


function tribus_cf7_entries_page() {
	/**
	 * Create an HTML table with all form submissions
     * listed so site user can accept or decline
     * submissions.
	 */
	global $wpdb;

	$table_name = $wpdb->prefix . 'tribus_cf7_entries';

	$entries = $wpdb->get_results(
		"SELECT * FROM $table_name"
	);

	// Get all Contact Form 7 forms
	$cf7_forms = get_posts(array(
		'post_type' => 'wpcf7_contact_form',
		'posts_per_page' => -1,
	));

	// Get the form fields
	$cf7 = WPCF7_ContactForm::get_instance($cf7_forms[0]->ID);
	$form_fields_data = $cf7->scan_form_tags();

	// Extract the field names
	$field_names = array_map(function($field) {
		return $field['name'];
	}, $form_fields_data);

	?>

    <div class="wrap">
        <h1 class="tribus-cf7-entries-title">Contact Form 7 Entries</h1>

		<?php if ($entries): ?>
            <table class="widefat">

                <thead>
                <tr>
					<?php foreach ($field_names as $field_name): ?>
                        <th><?php echo esc_html($field_name); ?></th>
					<?php endforeach; ?>
                    <th>Status</th>
                    <th>Email Sent</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
				<?php foreach ($entries as $entry) : ?>
                    <tr>
						<?php foreach ($field_names as $field_name):?>
			            <?php $field_name_underscored = str_replace('-', '_', $field_name); ?>

                            <td><?php echo isset($entry->{$field_name_underscored}) ? esc_html($entry->{$field_name_underscored}) : ''; ?></td>
						<?php endforeach; ?>
                        <td class="status-cell"><?php echo esc_html(ucfirst($entry->status)); ?></td>
                        <td class="email-sent-cell">
							<?php if ($entry->email_sent == 1) : ?>
                                <span>✅</span>
							<?php else : ?>
                                <span>❌</span>
							<?php endif; ?>
                        </td>
                        <td class="action-buttons">
							<?php if ($entry->status === 'pending') : ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" class="cf7-approve-entry-form">
                                    <input type="hidden" name="cf7_entry_id" value="<?php echo esc_attr($entry->id); ?>">
                                    <input type="hidden" name="cf7_entry_action" value="approve">
									<?php wp_nonce_field('cf7_approve_entry', 'cf7_approve_entry_nonce'); ?>
                                    <button type="submit" class="button button-primary cf7-approve-button">Approve</button>
                                </form>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" class="cf7-decline-entry-form">
                                    <input type="hidden" name="cf7_entry_id" value="<?php echo esc_attr($entry->id); ?>">
                                    <input type="hidden" name="cf7_entry_action" value="decline">
									<?php wp_nonce_field('cf7_decline_entry', 'cf7_decline_entry_nonce'); ?>
                                    <button type="submit" class="button button-secondary cf7-decline-button">Decline</button>
                                </form>
							<?php endif; ?>
                        </td>
                    </tr>
				<?php endforeach; ?>
                </tbody>

            </table>
		<?php else: ?>
            <p>No entries found!</p>
		<?php endif; ?>

    </div>

	<?php
}


function tribus_send_cf7_email($to, $subject, $message) {
	/**
	 * Sends an email.
	 */
    require_once (ABSPATH . 'wp-includes/PHPMailer/PHPMailer.php');
    require_once (ABSPATH . 'wp-includes/PHPMailer/SMTP.php');

    $mail = new PHPMailer\PHPMailer\PHPMailer();

    $mail->isSMTP();
    $mail->Host = WORDPRESS_SMTP_HOST;
    $mail->Port = WORDPRESS_SMTP_PORT;
    $mail->SMTPAuth = WORDPRESS_SMTP_AUTH;
    $mail->SMTPSecure = WORDPRESS_SMTP_SECURE;
    $mail->SMTPAutoTLS = false;
    $mail->Username = WORDPRESS_SMTP_USERNAME;
    $mail->Password = WORDPRESS_SMTP_PASSWORD;

    $mail->setFrom('your_email@example.com', 'Your Name');
    $mail->addAddress($to);

    // Set the subject and body
    $mail->Subject = $subject;
    $mail->Body = $message;

    if (!$mail->send()) {
        return false;
    }

    return true;
}

function tribus_send_cf7_approval_email() {
	/**
	 * Send an approval notification email
     * to the end user.
	 */
    $entry_id = absint( $_POST['cf7_entry_id'] );

    global $wpdb;
    $table_name = $wpdb->prefix . 'tribus_cf7_entries';

    $wpdb->update(
        $table_name,
        array(
            'status' => 'approved',
            'email_sent' => 1
        ),
        array( 'id' => $entry_id )
    );

    $entry = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $entry_id
        )
    );

    $to = $entry->your_email;
    $subject = "Your submission has been approved";
    $message = "You will receive your refund within 3-5 working days.";

    $email_sent = tribus_send_cf7_email( $to, $subject, $message );

    if ($email_sent) {
        wp_send_json_success( 'Email sent successfully.' );
    } else {
        wp_send_json_error( 'Failed to send email.' );
    }
}

function tribus_send_cf7_refusal_email() {
	/**
	 * Send an refusal notification email
	 * to the end user.
	 */
    $entry_id = absint( $_POST['cf7_entry_id'] );

    global $wpdb;
    $table_name = $wpdb->prefix . 'tribus_cf7_entries';

    $wpdb->update(
        $table_name,
        array(
            'status' => 'declined',
            'email_sent' => 1
        ),
        array('id' => $entry_id)
    );

    $entry = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $entry_id
        )
    );

    $to = $entry->your_email;
    $subject = "Your submission has been declined";
    $message = "Don't hesitate to get in touch.";

    $email_sent = tribus_send_cf7_email($to, $subject, $message);

    if ($email_sent) {
        wp_send_json_success( 'Email sent successfully.' );
    } else {
        wp_send_json_error( 'Failed to send email.' );
    }
}

add_action('wp_ajax_cf7_approve_entry', 'tribus_send_cf7_approval_email'); // Register approval ajax
add_action('wp_ajax_cf7_refuse_entry', 'tribus_send_cf7_refusal_email'); // Register refusal ajax


function tribus_cf7_entries_submenu_page() {
	renderSubMenuPage(); // Method to invoke another method ;)
}

function renderSubMenuPage() {
	/**
	 * Add sub menu page so that end user
     * can export all approved database entries
     * to CSV.
	 */
	?>
    <div class="wrap">
        <h1 class="approved-entries-title">Approved Entries CSV</h1>
		<?php
		if (isset($_GET['success']) && $_GET['success'] === 'csv_downloaded') {
			echo '<p class="success">CSV downloaded successfully.</p>';
		}
		?>
        <form method="post" id="download_form" action="<?php echo admin_url('admin.php?page=cf7-approved-entries-csv'); ?>">
            <input type="hidden" name="download_csv" value="1">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <input type="submit" class="button-primary" value="Download CSV">
			<?php
			if (isset($_GET['error']) && $_GET['error'] === 'invalid_password') {
				echo '<p class="error">Invalid password. Please try again.</p>';
			}
			?>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let pwdField = document.getElementById("password");
            pwdField.value = ""; // Clear field
            pwdField.focus(); // Focus field
        });
    </script>
	<?php
}

function download_csv() {
	/**
	 * Downloads all approved entries as CSV
	 */
	ob_start();

	global $wpdb;

	$fileName = 'approved_entries.csv';
	$table_name = $wpdb->prefix . 'tribus_cf7_entries';
	$entries = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'approved'", ARRAY_A);

	$csvHeaders = array_keys($entries[0]);

	$csvData = implode(',', $csvHeaders) . "\n";

	foreach ($entries as $entry) {
		$csvData .= implode(',', $entry) . "\n";
	}

	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=' . $fileName);

	echo $csvData;

	ob_end_flush();

	exit();
}

function tribus_cf7_entries_admin_init() {
	/**
	 * Checks the password and allows download of CSV data
     * if password is incorrect doesn't allow download
     * of sensitive data.
	 */
	if (isset($_POST['download_csv']) && isset($_POST['password'])) {
		$password = $_POST['password'];
		if ($password === 'your_password') {
			download_csv();
			wp_redirect(admin_url('admin.php?page=cf7-approved-entries-csv&success=csv_downloaded'));
		} else {
			$url = add_query_arg('error', 'invalid_password', admin_url('admin.php?page=cf7-approved-entries-csv'));
			wp_redirect($url);
		}
        exit();
	}
}

add_action('admin_init', 'tribus_cf7_entries_admin_init');
