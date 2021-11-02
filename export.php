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
 * This file contains the Export Quiz: exporting the file related code.
 *
 * Once user requested to export a quiz, their request will be direct here, and URL parsed and,
 * Paramaters extracted and passed to the export class.
 *
 * @package    block_export_quiz
 * @copyright  2019 onwards Ashish Pawar (github : CustomAP)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use block_export_quiz\export\block_export_quiz_export;

require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format.php');

require_login();

// Get the parameters from the URL.
$quizid = required_param('id', PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$format = required_param('format', PARAM_ALPHANUMEXT);

// Check if user have the required permission to export.
if (!has_capability('block/export_quiz:view', CONTEXT_COURSE::instance($courseid))) {
    throw new moodle_exception(get_string('nopermission', 'block_export_quiz'));
}

// If user have permission, we start proceed with the quiz export request.
$initexportclass = new block_export_quiz_export;
$initexportclass->export_quiz_questions($quizid, $courseid, $format);
