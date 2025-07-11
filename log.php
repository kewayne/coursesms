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
 * Log Page for view sms history.
 *
 * @package   local_coursessms
 * @copyright 2025 Kewayne Davidson <admin.kewayne.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('id', PARAM_INT);
$logid = optional_param('logid', 0, PARAM_INT);
$notify = optional_param('notify', 0, PARAM_INT);

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($course->id);

require_login($course);
require_capability('local/coursessms:viewlog', $context);

$PAGE->set_url('/local/coursessms/log.php', ['id' => $courseid]);
$PAGE->set_title(get_string('smslog_page_title', 'local_coursessms'));
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);
$PAGE->navbar->add(get_string('pluginname', 'local_coursessms'));

if ($notify) {
    \core\notification::success(get_string('messages_queued', 'local_coursessms'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('smslog_page_title', 'local_coursessms'));

$tabrows = [];
$row = [];

if (has_capability('local/coursessms:sendsms', $context)) {
    $row[] = new tabobject(
        'sendsms',
        new moodle_url('/local/coursessms/index.php', ['id' => $courseid]),
        get_string('sendsms', 'local_coursessms')
    );
}

$row[] = new tabobject(
    'smslog',
    new moodle_url('/local/coursessms/log.php', ['id' => $courseid]),
    get_string('smslog', 'local_coursessms')
);

$tabrows[] = $row;
print_tabs($tabrows, 'smslog');

if ($logid) {
    $log = $DB->get_record('local_coursessms_log', ['id' => $logid, 'courseid' => $courseid], '*', MUST_EXIST);
    $sender = $DB->get_record('user', ['id' => $log->senderid], 'id, firstname, lastname');

    echo $OUTPUT->box_start('generalbox');
    echo html_writer::tag('h4', get_string('log_details_title', 'local_coursessms'));
    echo html_writer::tag('p', get_string('log_sent_by', 'local_coursessms') . ': ' . fullname($sender));
    echo html_writer::tag('p', get_string('log_sent_on', 'local_coursessms') . ': ' . userdate($log->timecreated));
    echo html_writer::tag('p',
        get_string('log_message', 'local_coursessms') . ': ' .
        format_text($log->messagecontent, FORMAT_PLAIN)
    );

    $targetlabel = '';
    switch ($log->targettype) {
        case 'role':
            $role = $DB->get_record('role', ['id' => $log->targetid], '*', IGNORE_MISSING);
            $rolename = $role ? role_get_name($role, $context) : 'Unknown Role';
            $targetlabel = get_string('target_role', 'local_coursessms') . ': ' . $rolename;
            break;
        case 'group':
            $group = $DB->get_record('groups', ['id' => $log->targetid], '*', IGNORE_MISSING);
            $targetlabel = get_string('target_group', 'local_coursessms') . ': ' .
                ($group->name ?? get_string('unknown_group', 'local_coursessms'));
            break;
        case 'all':
        default:
            $targetlabel = get_string('target_all', 'local_coursessms');
            break;
    }

    echo html_writer::tag('p', get_string('log_target', 'local_coursessms') . ': ' . $targetlabel);

    $successids = json_decode($log->success_userids, true) ?? [];
    $failedids = json_decode($log->failed_userids, true) ?? [];

    echo html_writer::tag('p', get_string('log_recipients', 'local_coursessms') .
        ': ' . count($successids) . ' success, ' . count($failedids) . ' failed');

    echo html_writer::start_tag('details');
    echo html_writer::tag('summary', get_string('log_recipient_details', 'local_coursessms'));

    echo html_writer::tag('h5', get_string('log_successful_sends', 'local_coursessms'));
    if ($successids) {
        $table = new html_table();
        $table->head = ['Name', 'Phone'];
        foreach ($successids as $userid) {
            $user = $DB->get_record('user', ['id' => $userid], 'id, firstname, lastname, phone1, phone2');
            $phone = $user->phone1 ?? $user->phone2 ?? '-';
            $table->data[] = [fullname($user), $phone];
        }
        echo html_writer::table($table);
    } else {
        echo html_writer::tag('p', get_string('log_none', 'local_coursessms'));
    }

    echo html_writer::tag('h5', get_string('log_failed_sends', 'local_coursessms'));
    if ($failedids) {
        $table = new html_table();
        $table->head = ['Name', 'Phone', 'Reason'];
        foreach ($failedids as $userid) {
            $user = $DB->get_record('user', ['id' => $userid], 'id, firstname, lastname, phone1, phone2');
            $phone = $user->phone1 ?? $user->phone2 ?? '';
            $phonedisplay = $phone ?: '-';
            if ($phonedisplay === '-') {
                $reason = get_string('reason_no_phone', 'local_coursessms');
            } else {
                $reason = get_string('reason_unknown', 'local_coursessms');
            }
            $table->data[] = [fullname($user), $phonedisplay, $reason];
        }
        echo html_writer::table($table);
    } else {
        echo html_writer::tag('p', get_string('log_none', 'local_coursessms'));
    }

    echo html_writer::end_tag('details');
    echo $OUTPUT->box_end();

} else {
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
            $preview = mb_strimwidth($log->messagecontent, 0, 50, '...');
            $successcount = count(json_decode($log->success_userids, true));
            $failedcount = count(json_decode($log->failed_userids, true));
            $recipientinfo = "Success: {$successcount}, Failed: {$failedcount}";

            $url = new moodle_url('/local/coursessms/log.php', ['id' => $courseid, 'logid' => $log->id]);

            $row = new html_table_row();
            $row->cells[] = userdate($log->timecreated);
            $row->cells[] = fullname($sender);
            $row->cells[] = $preview;
            $row->cells[] = $recipientinfo;
            $row->cells[] = $OUTPUT->action_link($url, get_string('log_view_details', 'local_coursessms'));

            $table->data[] = $row;
        }

        echo html_writer::table($table);
    }
}

echo $OUTPUT->footer();

