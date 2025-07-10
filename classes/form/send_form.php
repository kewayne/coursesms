<?php
/**
 * Moodle form for sending an SMS.
 *
 * @package   local_coursessms
 * @copyright 2025 Me
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coursessms\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class send_form extends \moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        global $DB;
        $mform = $this->_form;
        $courseid = $this->_customdata['courseid'];
        $context = \context_course::instance($courseid);

        // --- Recipient Selection ---
        $mform->addElement('header', 'target_header', get_string('target_heading', 'local_coursessms'));

        $targetoptions = [
            'all' => get_string('target_all', 'local_coursessms'),
            'role' => get_string('target_role', 'local_coursessms'),
            'group' => get_string('target_group', 'local_coursessms'),
            'cohort' => get_string('target_cohort', 'local_coursessms'),
        ];
        $mform->addElement('select', 'targettype', get_string('target_type', 'local_coursessms'), $targetoptions);
        $mform->addHelpButton('targettype', 'target_heading', 'local_coursessms');

        // Role selector.
        $roles = get_definable_roles($context, 'moodle/course:view', true);
        $roleoptions = [];
        if (!empty($roles)) {
            foreach ($roles as $role) {
                $roleoptions[$role->id] = role_get_name($role, $context);
            }
        }
        $mform->addElement('select', 'roleid', get_string('select_role', 'local_coursessms'), $roleoptions);
        $mform->hideIf('roleid', 'targettype', 'neq', 'role');
        $mform->disabledIf('roleid', 'targettype', 'neq', 'role');

        // Group selector.
        $groups = \groups_get_all_groups($courseid);
        $groupoptions = [];
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $groupoptions[$group->id] = $group->name;
            }
        }
        $mform->addElement('select', 'groupid', get_string('select_group', 'local_coursessms'), $groupoptions);
        $mform->hideIf('groupid', 'targettype', 'neq', 'group');
        $mform->disabledIf('groupid', 'targettype', 'neq', 'group');


        // Cohort selector.
        $cohorts = $DB->get_records_sql("
            SELECT c.id, c.name
            FROM {cohort} c
            JOIN {cohort_members} cm ON cm.cohortid = c.id
            JOIN {user_enrolments} ue ON ue.userid = cm.userid
            JOIN {enrol} e ON e.id = ue.enrolid
            WHERE e.courseid = ? AND c.contextid != ?
            GROUP BY c.id, c.name
            ORDER BY c.name ASC
        ", [$courseid, \context_system::instance()->id]);
        $cohortoptions = [];
        if (!empty($cohorts)) {
            foreach ($cohorts as $cohort) {
                $cohortoptions[$cohort->id] = $cohort->name;
            }
        }
        $mform->addElement('select', 'cohortid', get_string('select_cohort', 'local_coursessms'), $cohortoptions);
        $mform->hideIf('cohortid', 'targettype', 'neq', 'cohort');
        $mform->disabledIf('cohortid', 'targettype', 'neq', 'cohort');

        // --- Message Content ---
        $mform->addElement('header', 'message_header', get_string('message_heading', 'local_coursessms'));
        $mform->addElement('textarea', 'messagecontent', get_string('message_content', 'local_coursessms'), 'wrap="virtual" rows="5" cols="50"');
        $mform->addHelpButton('messagecontent', 'message_content', 'local_coursessms');
        $mform->addRule('messagecontent', null, 'required', null, 'client');
        $mform->setType('messagecontent', PARAM_TEXT);

        // Add a character counter.
        $mform->addElement('static', 'charcount', '', get_string('character_count', 'local_coursessms', 0));

        // Hidden field for course ID.
        $mform->addElement('hidden', 'id', $courseid);
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons(true, get_string('send_button', 'local_coursessms'));
    }

    /**
     * Form validation.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['targettype'] === 'role' && empty($data['roleid'])) {
            $errors['roleid'] = get_string('error_no_target', 'local_coursessms');
        }
        if ($data['targettype'] === 'group' && empty($data['groupid'])) {
            $errors['groupid'] = get_string('error_no_target', 'local_coursessms');
        }
        if ($data['targettype'] === 'cohort' && empty($data['cohortid'])) {
            $errors['cohortid'] = get_string('error_no_target', 'local_coursessms');
        }

        return $errors;
    }
}
