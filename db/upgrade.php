<?php
/**
 * Upgrade logic for the Course SMS plugin.
 *
 * @package   local_coursessms
 * @copyright 2025 Me
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The main function that is called when the plugin is being upgraded.
 *
 * @param int $oldversion The currently installed version of the plugin.
 * @return bool
 */
function xmldb_local_coursessms_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    // In case of future upgrades, logic would be added here.
    // e.g. if ($oldversion < 2025071100) { ... }

    return true;
}
