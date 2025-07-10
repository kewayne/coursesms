<?php
/**
 * Library of functions for the Course SMS plugin.
 *
 * @package   local_coursessms
 * @copyright 2025 Me
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extends the course navigation with a link to the plugin pages.
 *
 * @param navigation_node $navigation The course navigation object.
 * @param stdClass $course The course object.
 * @param context_course $context The course context.
 */
function local_coursessms_extend_navigation_course(core_course\navigation\node $navigation, stdClass $course, context_course $context): void {
    if (has_capability('local/coursessms:sendsms', $context) || has_capability('local/coursessms:viewlog', $context)) {
        $url = new moodle_url('/local/coursessms/index.php', ['id' => $course->id]);
        $navigation->add(
            get_string('coursessms', 'local_coursessms'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            'coursessms',
            new pix_icon('i/sms', '')
        );
    }
}
