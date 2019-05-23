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
 * This file contains the Export Quiz Block.
 *
 * @package    block_export_quiz
 * @copyright  2019 onwards Ashish Pawar (github : CustomAP)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('block_export_quiz_form.php');

class block_export_quiz extends block_base{

    /**
     * Initialise the block.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_export_quiz');
    }

    /**
     * Should be only visible in a particular course and in the quiz modules
    */
    public function applicable_formats() {
        return array ('course-view' => true, 'mod-quiz' => true);
    }


    public function get_content_type() {
        return BLOCK_TYPE_TEXT;
    }

    /**
     * Return the content of this block.
     *
     * @return stdClass the content
     */
    public function get_content() {
        global $COURSE, $DB, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';

        $courseid = $this->page->course->id;

        $quiztags = array();
       
        /**
         * Adding quiz names and corresponding urls created in $quiztags array
         */
        $quizes = get_fast_modinfo($this->page->course)->instances['quiz'];
        foreach ($quizes as $quiz) {

                /**
                 * Check if the Quiz is visible to the user only then display it :
                 * Teacher can choose to hide the quiz from the students in that case it should not be visible to students
                 */
                if(!$quiz->uservisible)
                    continue;

                $pageurl = new moodle_url('/blocks/export_quiz/export.php',
                    array('courseid' => $COURSE->id,
                        'id' => $quiz->instance,
                        'sesskey' => sesskey()));

                $quiztags[(string)$pageurl] = $quiz->name;
        }
       
        // Export form
        $export_quiz_form = new block_export_quiz_form((string)$this->page->url, array('quiz' => $quiztags));

        $export_quiz_form->set_data('');

        $this->content->text = $export_quiz_form->render();

        if ($export_quiz_form->is_cancelled()) {
            // Do nothing
        } else if ($from_form = $export_quiz_form->get_data()) {
            $url = new moodle_url($from_form->quiz, array('format' => $from_form->format));

            // Don't allow force download for behat site, as pop-up can't be handled by selenium.
            if (!defined('BEHAT_SITE_RUNNING')) {
                $PAGE->requires->js_function_call('document.location.replace', array($url->out(false)), false, 1);
            }
        }

        return $this->content;
    }
}
