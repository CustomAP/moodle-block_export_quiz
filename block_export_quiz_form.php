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


require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/questionlib.php');


/**
 * Form to export questions from the quiz.
 *
 *  @copyright  2019 onwards Ashish Pawar (github : CustomAP)
 *     @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_export_quiz_form extends moodleform {

    function definition(){
        $mform = $this->_form;

        $quizes = $this->_customdata['quiz'];
        $format = get_import_export_formats('export');

        $formats = array();

        foreach ($format as $shortname => $fileformatname) {
            $formats[$shortname] = $fileformatname;
        }

        // Quiz select.
        $mform->addElement('select', 'quiz', get_string('quiz', 'block_export_quiz'),
                $quizes);

        // Format select.
        $mform->addElement('select', 'format', get_string('format', 'block_export_quiz'),
                $formats);

        // Submit buttons.
        $this->add_action_buttons(false, get_string('export', 'block_export_quiz'));
    }
}
