<?php
/**
 * Displays the log of sent SMS messages.
 *
 * @package   local_coursessms
 * @copyright 2025 Me
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('id', PARAM_INT);
$logid = optional_param('logid', 0, PARAM_INT); // To view a specific log entry.
$notify = optional_param('notify', 0, PARAM_INT);

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($course->id);

require_login($course);
require_capability('local/coursessms:viewlog', $context);

$PAGE->set_url('/local/coursessms/log.php', ['id' => $course->id]);
$PAGE->set_title(get_string('smslog_page_title', 'local_coursessms'));
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);
$PAGE->navbar->add(get_string('pluginname', 'local_coursessms'));

if ($notify) {
    \core\notification::success('SMS messages have been queued for sending.');
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('smslog_page_title', 'local_coursessms'));

// Display tabs
$tabrows = [];
$row = [];
if (has_capability('local/coursessms:sendsms', $context)) {
    $row[] = new \core\output\tab(
        get_string('sendsms', 'local_coursessms'),
        new moodle_url('/local/coursessms/index.php', ['id' => $courseid]),
        false
    );
}
$row[] = new \core\output\tab(
    get_string('smslog', 'local_coursessms'),
    new moodle_url('/local/coursessms/log.php', ['id' => $courseid]),
    true
);
$tabrows[] = $row;
echo $OUTPUT->tabtree($tabrows);


if ($logid) {
    // --- Detailed view of a single log entry ---
    $log = $DB->get_record('local_coursessms_log', ['id' => $logid, 'courseid' => $courseid], '*', MUST_EXIST);
    $renderer = $PAGE->get_renderer('local_coursessms');
    echo $renderer->render_from_template('local_coursessms/log_template', ['log' => $log]);

} else {
    // --- List view of all logs for the course ---
    $logs = $DB->get_records('local_coursessms_log', ['courseid' => $courseid], 'timecreated DESC');

    if (empty($logs)) {
        echo $OUTPUT->notification(get_string('log_no_logs', 'local_coursessms'));
    } else {
        $table = new html_table();
        $table->head = [
            get_string('log_sent_on', 'local_coursessms'),
            get_string('log_sent_by', 'local_coursessms'),
            get_string('log_message', 'local_coursessms'),
            get_string('log_recipients', 'local_coursessms'),
            '',
        ];

        foreach ($logs as $log) {
            $sender = $DB->get_record('user', ['id' => $log->senderid], 'id, firstname, lastname');
            $messagepreview = \core_text::truncate($log->messagecontent, 50);
            $successcount = count(json_decode($log->success_userids));
            $failedcount = count(json_decode($log->failed_userids));
            $recipientinfo = "Success: {$successcount}, Failed: {$failedcount}";
            $detailsurl = new moodle_url('/local/coursessms/log.php', ['id' => $courseid, 'logid' => $log->id]);

            $row = new html_table_row();
            $row->cells[] = userdate($log->timecreated);
            $row->cells[] = fullname($sender);
            $row->cells[] = $messagepreview;
            $row->cells[] = $recipientinfo;
            $row->cells[] = $OUTPUT->action_link($detailsurl, get_string('log_view_details', 'local_coursessms'));
            $table->data[] = $row;
        }
        echo html_writer::table($table);
    }
}

echo $OUTPUT->footer();
