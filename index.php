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
 * Main page for sending SMS messages in a course.
 *
 * @package   local_coursessms
 * @copyright 2025 Kewayne Davidson <admin.kewayne.com>
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
$smsmanager = \core\di::get(\core_sms\manager::class);
$enabledgateways = $smsmanager->get_enabled_gateway_instances();
if (empty($enabledgateways)) {
    throw new \moodle_exception('error_no_gateway', 'local_coursessms');
}

// Set up the page.
$PAGE->set_url('/local/coursessms/index.php', ['id' => $courseid]);
$PAGE->set_title(get_string('sendsms_page_title', 'local_coursessms'));
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);
$PAGE->navbar->add(get_string('pluginname', 'local_coursessms'));

// Form instance.
$form = new \local_coursessms\form\send_form(
    new moodle_url('/local/coursessms/send_action.php'),
    ['courseid' => $courseid]
);

// Handle cancel action.
if ($form->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', ['id' => $courseid]));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('sendsms_page_title', 'local_coursessms'));

// Display navigation tabs.
$tabrows = [];
$row = [];

// Tab: Send SMS.
$row[] = new tabobject(
    'sendsms',
    new moodle_url('/local/coursessms/index.php', ['id' => $courseid]),
    get_string('tab_sendsms', 'local_coursessms')
);

// Tab: SMS Log.
if (has_capability('local/coursessms:viewlog', $context)) {
    $row[] = new tabobject(
        'smslog',
        new moodle_url('/local/coursessms/log.php', ['id' => $courseid]),
        get_string('tab_smslog', 'local_coursessms')
    );
}
$tabrows[] = $row;

print_tabs($tabrows, 'sendsms');

// Display the form.
$form->display();

echo $OUTPUT->footer();
