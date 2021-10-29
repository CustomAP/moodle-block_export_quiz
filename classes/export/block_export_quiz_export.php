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
 * @package    block_export_quiz
 * @copyright  2019 onwards Ashish Pawar (github : CustomAP)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_export_quiz\export;

use context_course;
use question_bank;
use moodle_url;
use block_export_quiz\export\dependencies\format_handler;
use moodle_exception;

/**
 * This class handles quiz export requests
 */
class block_export_quiz_export extends format_handler
{
    /**
     * based on the provided on run env, this methdo eitehr return files for download or return TRUE for unit testing
     * @param int $quizid
     * @param int $courseid
     * @param string $format
     * @param bool $isunittesting
     * @return mixed
     */
    public function export_quiz_questions(int $quizid, int $courseid, string $format, bool $isunittesting = false) {
        global $DB, $CFG, $PAGE;

        if (isset($courseid)) {

            if (!$isunittesting) {

                require_login($courseid);
            }
            $thiscontext = context_course::instance($courseid);
            $urlparams['courseid'] = $courseid;
        } else {
            throw new moodle_exception(get_string('missingcourseid', 'block_export_quiz'));
        }

        if (!$isunittesting) {

            require_sesskey();

        }

        // Load the necessary data.
        $questiondata = array();
        if ($questions = $DB->get_records('quiz_slots', array('quizid' => $quizid))) {
            foreach ($questions as $question) {
                array_push($questiondata, question_bank::load_question_data($question->questionid));
            }
        }

        // Check if the Quiz is visible to the user only then display it.
        // Teacher can choose to hide the quiz from the students in that case it should not be visible to students.
        $modinfo = get_fast_modinfo($courseid);
        $cm = $modinfo->instances["quiz"][$quizid];
        if (!$cm->uservisible) {
            throw new moodle_exception(get_string('noaccess', 'block_export_quiz'));
        }

        // Initialise $PAGE.
        $nexturl = new moodle_url('/question/type/stack/questiontestrun.php', $urlparams);
        $PAGE->set_url('/blocks/export_quiz/export.php', $urlparams);
        $PAGE->set_heading(get_string('pluginname', 'block_export_quiz'));
        $PAGE->set_pagelayout('admin');

        // Get instance of the requested file format class.
        $choosenformat = $this->get_file_format($thiscontext, $format, $questiondata);
        // Get quiz name to assign it to file name used for exporting.
        $filename = $cm->name. $choosenformat->export_file_extension();
        // Pre-processing the export.
        if (!$choosenformat->exportpreprocess()) {
            send_file_not_found();
        }

        /* Actual export process to get the converted string
        * Check capabilites set to false since already checks done for quiz availability
        * This also adds the functionality of exporting the quiz for the students
        */
        if (!$content = $choosenformat->exportprocess(false)) {
            send_file_not_found();
        }

        if ($isunittesting) {
            return true;
        }

        if (!$isunittesting) {
            send_file($content, $filename, 0, 0, true, true, $choosenformat->mime_type());
        }
    }

}
