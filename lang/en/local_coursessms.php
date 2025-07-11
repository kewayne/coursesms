<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * English Language strings.
 *
 * @package   local_coursessms
 * @copyright 2025 Kewayne Davidson <admin.kewayne.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['back_to_course'] = 'Back to course';
$string['back_to_log'] = 'Back to SMS Log';
$string['character_count'] = 'Character count: {$a}';
$string['coursessms'] = 'Course SMS';
$string['coursessms:sendsms'] = 'Send SMS to course participants';
$string['coursessms:viewlog'] = 'View course SMS logs';
$string['coursessms_nav'] = 'Course SMS';
$string['error_no_gateway'] = 'There are no SMS gateways configured or available. Please contact the site administrator.';
$string['error_no_target'] = 'You must select a valid role, group, or cohort.';
$string['log_details_title'] = 'SMS Batch Details';
$string['log_failed_sends'] = 'Failed Sends';
$string['log_failed_to_send_to'] = 'Could not send to ({$a}) - No phone number';
$string['log_message'] = 'Message';
$string['log_no_logs'] = 'There is no history of sent SMS for this course.';
$string['log_none'] = 'None found';
$string['log_recipient_details'] = 'View detailed recipient list';
$string['log_recipients'] = 'Recipients';
$string['log_sent_by'] = 'Sent by';
$string['log_sent_on'] = 'Date sent';
$string['log_successful_sends'] = 'Successful Sends';
$string['log_successfully_sent_to'] = 'Successfully sent to ({$a})';
$string['log_target'] = 'Target';
$string['log_view_details'] = 'View details';
$string['message_content'] = 'Message content';
$string['message_content_help'] = 'Enter the content of your SMS. The message will be truncated if it exceeds the gateway limit (typically 480 characters).';
$string['message_heading'] = 'Message';
$string['messages_queued'] = 'Your message has been queued for sending.';
$string['nogateway_enabled_message'] = 'There are currently no SMS gateways enabled. Please configure one from the <a href="{$a}">Gateway Management page</a>.';
$string['placeholder_info'] = 'You can use placeholders in the message: {sender} = sender full name, {coursename} = course short name, {firstname} = recipient first name, {lastname} = recipient last name.';
$string['pluginname'] = 'Course Send SMS';
$string['privacy:metadata:local_coursessms_log'] = 'Stores logs of SMS messages sent from courses.';
$string['privacy:metadata:local_coursessms_log:failed_userids'] = 'The list of user IDs who failed to receive the message.';
$string['privacy:metadata:local_coursessms_log:messagecontent'] = 'The SMS message content.';
$string['privacy:metadata:local_coursessms_log:senderid'] = 'The ID of the user who sent the message.';
$string['privacy:metadata:local_coursessms_log:success_userids'] = 'The list of user IDs who received the message.';
$string['privacy:metadata:local_coursessms_log:targetid'] = 'The ID of the role, group, or cohort targeted.';
$string['privacy:metadata:local_coursessms_log:targettype'] = 'The type of target (e.g., role, group, cohort).';
$string['privacy:metadata:local_coursessms_log:timecreated'] = 'The time the message was created.';
$string['reason_no_phone'] = 'No phone number on profile';
$string['select_cohort'] = 'Select a cohort';
$string['select_group'] = 'Select a group';
$string['select_role'] = 'Select a role';
$string['send_button'] = 'Send SMS';
$string['sendsms'] = 'Send SMS';
$string['sendsms_page_title'] = 'Send SMS to course participants';
$string['setup_gateway_button'] = 'Set up Gateway';
$string['sms_send_failed'] = 'Failed to queue SMS for user with ID: {$a}';
$string['smslog'] = 'SMS Log';
$string['smslog_page_title'] = 'Log of sent SMS';
$string['tab_sendsms'] = 'Send SMS';
$string['tab_smslog'] = 'SMS Log';
$string['target_all'] = 'All participants';
$string['target_cohort'] = 'Cohort';
$string['target_group'] = 'Group';
$string['target_heading'] = 'Select Recipients';
$string['target_heading_help'] = 'Choose who should receive the SMS. You can target all participants, or filter by a specific role, group, or cohort within this course.';
$string['target_role'] = 'Role';
$string['target_type'] = 'Target by';
