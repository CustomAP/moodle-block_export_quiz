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
// use question_edit_contexts;
use question_bank;
use moodle_url;
use block_export_quiz\export\dependencies\format_handler;;

class block_export_quiz_export extends format_handler
{
    
    /**
     * returns 
     * @param int $quizid
     * @param int $courseid
     * @param string $format
     * @return mixed
     */
    public function export_quiz_questions(int $quizid, int $courseid, string $format)
    {
        global $DB, $PAGE,$CFG;

        if (isset($courseid)) {
            require_login($courseid);
            $thiscontext = context_course::instance($courseid);
            $urlparams['courseid'] = $courseid;
        } else {
            print_error('missingcourseorcmid', 'question');
        }
        
        require_sesskey();

        // Load the necessary data.
        // $contexts = new question_edit_contexts($thiscontext);
        $questiondata = array();
        if ($questions = $DB->get_records('quiz_slots', array('quizid' => $quizid))) {
            foreach ($questions as $question) {
                array_push($questiondata, question_bank::load_question_data($question->questionid));
            }
        }

        /**
         * Check if the Quiz is visible to the user only then display it :
         * Teacher can choose to hide the quiz from the students in that case it should not be visible to students
         */
        $modinfo = get_fast_modinfo($courseid);
        $cm = $modinfo->instances["quiz"][$quizid];
        if(!$cm->uservisible)
            print_error('noaccess', 'block_export_quiz');

        // Initialise $PAGE.
        $nexturl = new moodle_url('/question/type/stack/questiontestrun.php', $urlparams);
        $PAGE->set_url('/blocks/export_quiz/exportnew.php', $urlparams);
        $PAGE->set_heading(get_string('pluginname','block_export_quiz'));
        $PAGE->set_pagelayout('admin');

        // // Check if the question format is readable, if yes import it : This way support is added for any third-party question format installed.
        // if (!is_readable($CFG->dirroot . "/question/format/{$format}/format.php")) {
        //     print_error('unknowformat', '', '', $format);
        // } else {
        //     require_once($CFG->dirroot . "/question/format/{$format}/format.php");
        // }

        // // Set up the export format.
        // $classname = 'qformat_' . $format;
        // $qformat = new $classname();
        // $qformat->setContexts($contexts->having_one_edit_tab_cap('export'));
        // $qformat->setCourse($COURSE);
        // $qformat->setCattofile(false);
        // $qformat->setContexttofile(false);
        // $qformat->setQuestions($questiondata);

        $choosenFormat = $this->get_file_format($thiscontext,$format,$questiondata);

        // Get quiz name to assign it to file name used for exporting.
        $filename = $cm->name. $choosenFormat->export_file_extension();

        // Pre-processing the export.
        if (!$choosenFormat->exportpreprocess()) {
            send_file_not_found();
        }

        /* Actual export process to get the converted string
        * Check capabilites set to false since already checks done for quiz availability
        * This also adds the functionality of exporting the quiz for the students
        */
        if (!$content = $choosenFormat->exportprocess(false)) {
            send_file_not_found();
        }

        send_file($content, $filename, 0, 0, true, true, $choosenFormat->mime_type());
    }

}
