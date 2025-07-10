<?php
/**
 * Handles the form submission for sending SMS.
 *
 * @package   local_coursessms
 * @copyright 2025 Me
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
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

$data = $form->get_data();

if (!$data) {
    // Form validation failed, or it's not submitted. Redirect back.
    redirect(new moodle_url('/local/coursessms/index.php', ['id' => $courseid]));
}

// 1. Get the list of target users.
$userfields = 'id, firstname, lastname, phone1, phone2';
$users = [];
$targetid = 0;

switch ($data->targettype) {
    case 'role':
        $targetid = $data->roleid;
        $users = get_role_users($data->roleid, $context, false, $userfields);
        break;
    case 'group':
        $targetid = $data->groupid;
        $users = \groups_get_group_members($data->groupid, $userfields);
        break;
    case 'cohort':
        $targetid = $data->cohortid;
        $cohortusers = \core_cohort\get_cohort_members($data->cohortid);
        $enrolledusers = get_enrolled_users($context, '', 0, 'u.id, u.firstname, u.lastname, u.phone1, u.phone2');
        // Filter cohort members to only those enrolled in the current course.
        foreach($cohortusers as $cuser) {
            if (isset($enrolledusers[$cuser->id])) {
                $users[$cuser->id] = $enrolledusers[$cuser->id];
            }
        }
        break;
    case 'all':
    default:
        $users = get_enrolled_users($context, '', 0, 'u.id, u.firstname, u.lastname, u.phone1, u.phone2');
        break;
}

// 2. Iterate and send SMS.
$smsmanager = \core_sms\manager::instance();
$successfulsends = [];
$failedsends = [];

foreach ($users as $user) {
    $phonenumber = trim($user->phone1);
    if (empty($phonenumber)) {
        $phonenumber = trim($user->phone2);
    }

    if (empty($phonenumber)) {
        $failedsends[] = $user->id;
        continue;
    }

    try {
        $message = $smsmanager->send(
            recipientnumber: $phonenumber,
            content: $data->messagecontent,
            component: 'local_coursessms',
            messagetype: 'coursemessage',
            recipientuserid: $user->id,
            issensitive: false,
            async: true // Use async for bulk sending.
        );
        // We assume success if no exception is thrown. Status can be checked later.
        $successfulsends[] = $user->id;
    } catch (\Exception $e) {
        // If the send() method fails immediately, log it.
        $failedsends[] = $user->id;
        // Optionally log the exception for debugging.
        \core\notification::error("Failed to queue SMS for user {$user->id}: " . $e->getMessage());
    }
}

// 3. Log the batch operation to our custom table.
$logrecord = new \stdClass();
$logrecord->courseid = $courseid;
$logrecord->senderid = $USER->id;
$logrecord->messagecontent = $data->messagecontent;
$logrecord->targettype = $data->targettype;
$logrecord->targetid = $targetid;
$logrecord->success_userids = json_encode($successfulsends);
$logrecord->failed_userids = json_encode($failedsends);
$logrecord->timecreated = time();
$logid = $DB->insert_record('local_coursessms_log', $logrecord);

// 4. Redirect to the log page to show the results.
redirect(new moodle_url('/local/coursessms/log.php', ['id' => $courseid, 'logid' => $logid, 'notify' => 1]));
