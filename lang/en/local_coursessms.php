<?php
/**
 * English language strings for the Course SMS plugin.
 *
 * @package   local_coursessms
 * @copyright 2025 Me
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Course SMS';
$string['coursessms'] = 'Course SMS';
$string['sendsms'] = 'Send SMS';
$string['sendsms_page_title'] = 'Send SMS to course participants';
$string['smslog'] = 'SMS Log';
$string['smslog_page_title'] = 'Log of sent SMS';

// Capabilities.
$string['coursessms:sendsms'] = 'Send SMS to course participants';
$string['coursessms:viewlog'] = 'View course SMS logs';

// Form strings.
$string['target_heading'] = 'Select Recipients';
$string['target_help'] = 'Choose who should receive the SMS. You can target all participants, or filter by a specific role, group, or cohort within this course.';
$string['target_type'] = 'Target by';
$string['target_all'] = 'All participants';
$string['target_role'] = 'Role';
$string['target_group'] = 'Group';
$string['target_cohort'] = 'Cohort';
$string['select_role'] = 'Select a role';
$string['select_group'] = 'Select a group';
$string['select_cohort'] = 'Select a cohort';
$string['message_heading'] = 'Message';
$string['message_content'] = 'Message content';
$string['message_content_help'] = 'Enter the content of your SMS. The message will be truncated if it exceeds the gateway limit (typically 480 characters).';
$string['character_count'] = 'Character count: {$a}';
$string['send_button'] = 'Send SMS';
$string['error_no_target'] = 'You must select a valid role, group, or cohort.';
$string['error_no_gateway'] = 'There are no SMS gateways configured or available. Please contact the site administrator.';

// Log strings.
$string['log_sent_by'] = 'Sent by';
$string['log_sent_on'] = 'Date sent';
$string['log_message'] = 'Message';
$string['log_recipients'] = 'Recipients';
$string['log_view_details'] = 'View details';
$string['log_details_title'] = 'SMS Batch Details';
$string['log_successfully_sent_to'] = 'Successfully sent to ({$a})';
$string['log_failed_to_send_to'] = 'Could not send to ({$a}) - No phone number';
$string['log_no_logs'] = 'There is no history of sent SMS for this course.';
$string['back_to_log'] = 'Back to SMS Log';
$string['back_to_course'] = 'Back to course';
