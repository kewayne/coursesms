<?php
/**
 * Main page for sending SMS messages in a course.
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

// Check if any SMS gateways are enabled.
$smsmanager = \core_sms\manager::instance();
if (!$smsmanager->has_gateways()) {
    throw new \moodle_exception('error_no_gateway', 'local_coursessms');
}

$PAGE->set_url('/local/coursessms/index.php', ['id' => $course->id]);
$PAGE->set_title(get_string('sendsms_page_title', 'local_coursessms'));
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);
$PAGE->navbar->add(get_string('pluginname', 'local_coursessms'));

$form = new \local_coursessms\form\send_form(
    new moodle_url('/local/coursessms/send_action.php'),
    ['courseid' => $courseid]
);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', ['id' => $courseid]));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('sendsms_page_title', 'local_coursessms'));

// Display tabs
$tabrows = [];
$row = [];
$row[] = new \core\output\tab(
    get_string('sendsms', 'local_coursessms'),
    new moodle_url('/local/coursessms/index.php', ['id' => $courseid]),
    true
);
if (has_capability('local/coursessms:viewlog', $context)) {
    $row[] = new \core\output\tab(
        get_string('smslog', 'local_coursessms'),
        new moodle_url('/local/coursessms/log.php', ['id' => $courseid]),
        false
    );
}
$tabrows[] = $row;
echo $OUTPUT->tabtree($tabrows);


$form->display();

// Javascript for the character counter.
$PAGE->requires->js_init_code(<<<EOD
const messageTextarea = document.getElementById('id_messagecontent');
const charCountDisplay = document.getElementById('id_charcount');
if (messageTextarea && charCountDisplay) {
    const updateCount = () => {
        const count = messageTextarea.value.length;
        charCountDisplay.textContent = 'Character count: ' + count;
    };
    messageTextarea.addEventListener('input', updateCount);
    updateCount(); // Initial count.
}
EOD
);


echo $OUTPUT->footer();
