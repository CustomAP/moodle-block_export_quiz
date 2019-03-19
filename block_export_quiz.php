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

class block_export_quiz extends block_base{

	/**
     * Initialise the block.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_export_quiz');
    }

    /**
	 * Should be only visible in a particular course	
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
        global $COURSE, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = get_string('footer', 'block_export_quiz');

        $courseid = $this->page->course->id;
        
        // Create unordered list of quizes in particular course
        if ($quizes = $DB->get_records('quiz', array('course' => $courseid))) {
			$this->content->text .= html_writer::start_tag('ul');
		foreach ($quizes as $quiz) {

                /**
                 * Check if the Quiz is visible to the user only then display it : 
                 * Teacher can choose to hide the quiz from the students in that case it should not be visible to students
                 */
                $modinfo = get_fast_modinfo($this->page->course);
                $cm = $modinfo->get_cm($DB->get_record('course_modules', array('module' => 16, 'instance' => $quiz->id))->id);
                if(!$cm->uservisible)
                    continue;

				$pageurl = new moodle_url('/blocks/export_quiz/export.php',
					array('courseid' => $COURSE->id,
						'id' => $quiz->id));
				$this->content->text .= html_writer::start_tag('li');
				$this->content->text .= html_writer::link($pageurl, $quiz->name);
				$this->content->text .= html_writer::end_tag('li');
			}
			$this->content->text .= html_writer::end_tag('ul');
		}

		return $this->content;
    }
}