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
 * Handles the form submission for sending SMS messages.
 *
 * @package   local_coursessms
 * @copyright 2025 Kewayne Davidson <admin.kewayne.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/group/lib.php');
require_once(__DIR__ . '/classes/form/send_form.php');

$courseid = required_param('id', PARAM_INT);
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($course->id);

require_login($course);
require_capability('local/coursessms:sendsms', $context);

$form = new \local_coursessms\form\send_form(
    new moodle_url('/local/coursessms/send_action.php'),
    ['courseid' => $courseid]
);

// Redirect if cancelled.
if ($form->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', ['id' => $courseid]));
}

$data = $form->get_data();

if (!$data) {
    redirect(new moodle_url('/local/coursessms/index.php', ['id' => $courseid]));
}

$users = [];
$targetid = 0;

switch ($data->targettype) {
    case 'role':
        $targetid = $data->roleid;
        $users = get_role_users($data->roleid, $context, false, 'u.id, u.firstname, u.lastname, u.phone1, u.phone2');
        break;

    case 'group':
        $targetid = $data->groupid;
        $groupmembers = groups_get_members($data->groupid);
        $userids = array_keys($groupmembers);

        if (!empty($userids)) {
            list($insql, $params) = $DB->get_in_or_equal($userids);
            $params[] = $courseid;

            $users = $DB->get_records_sql(
                "SELECT u.id, u.firstname, u.lastname, u.phone1, u.phone2
                FROM {user} u
                JOIN {user_enrolments} ue ON ue.userid = u.id
                JOIN {enrol} e ON e.id = ue.enrolid
                WHERE u.id $insql AND e.courseid = ?",
                $params
            );
        }
        break;

    case 'all':
    default:
        $users = get_enrolled_users($context, '', 0, 'u.id, u.firstname, u.lastname, u.phone1, u.phone2');
        break;
}

$smsmanager = \core\di::get(\core_sms\manager::class);
$successfulsends = [];
$failedsends = [];

$sendername = fullname($USER);
$courseshortname = $course->shortname;

foreach ($users as $user) {
    $phonenumber = trim($user->phone1 ?: $user->phone2);

    if (empty($phonenumber)) {
        $failedsends[] = $user->id;
        continue;
    }

    $personalizedmessage = str_replace(
        ['{sender}', '{coursename}', '{firstname}', '{lastname}'],
        [$sendername, $courseshortname, $user->firstname, $user->lastname],
        $data->messagecontent
    );

    try {
        $smsmanager->send(
            recipientnumber: $phonenumber,
            content: $personalizedmessage,
            component: 'local_coursessms',
            messagetype: 'coursemessage',
            recipientuserid: $user->id,
            issensitive: false,
            async: true
        );
        $successfulsends[] = $user->id;
    } catch (\Exception $e) {
        $failedsends[] = $user->id;
        \core\notification::error(get_string('sms_send_failed', 'local_coursessms', $user->id));
    }
}

// Log the operation.
$logrecord = (object)[
    'courseid' => $courseid,
    'senderid' => $USER->id,
    'messagecontent' => $data->messagecontent,
    'targettype' => $data->targettype,
    'targetid' => $targetid,
    'success_userids' => json_encode($successfulsends),
    'failed_userids' => json_encode($failedsends),
    'timecreated' => time(),
];

$logid = $DB->insert_record('local_coursessms_log', $logrecord);

redirect(new moodle_url('/local/coursessms/log.php', [
    'id' => $courseid,
    'logid' => $logid,
    'notify' => 1,
]));
