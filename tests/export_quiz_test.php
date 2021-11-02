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

global $CFG;

use block_export_quiz\export\block_export_quiz_export;
use block_export_quiz\form\block_export_quiz_form;
use block_export_quiz\export\dependencies\format_handler;
use context_course;
use question_bank;
use stdClass;

/**
 * Export quiz block plugin tests units
 */
class local_export_quiz_test extends advanced_testcase
{
    /**
     * test if we are able to get instance of moodle form class
     * @return void
     */
    public function test_we_can_get_instance_of_moodle_form_api(): void {
        self::assertInstanceOf(block_export_quiz_form::class, new block_export_quiz_form);
    }

    /**
     * test if moodle Form API contains the required methods to render the form.
     * by checking if the render() method exists or not.
     * @return void
     */
    public function test_we_can_render_moodle_form(): void {
        $message = 'block_export_quiz_form class does not have method named : render()';
        self::assertTrue(method_exists(block_export_quiz_form::class, 'render'), $message);
    }

    /**
     * test if we are able to get instance of moodle form class
     * @return void
     */
    public function test_we_can_get_instance_of_export_class(): void {
        self::assertInstanceOf(block_export_quiz_export::class, new block_export_quiz_export);
    }

    /**
     * test if we are able to get trigger from the export question method
     * from the export class
     * @return void
     */
    public function test_we_can_trigger_the_export_questions_method(): void {
        global $DB;
        $this->resetAfterTest();

        $dg = $this->getDataGenerator();
        $quizgen = $dg->get_plugin_generator('mod_quiz');
        $course = $dg->create_course();
        $quiz1 = $quizgen->create_instance(['course' => $course->id, 'sumgrades' => 2]);

        $questgen = $dg->get_plugin_generator('core_question');
        $quizcat = $questgen->create_question_category();
        $question = $questgen->create_question('numerical', null, ['category' => $quizcat->id]);
        quiz_add_quiz_question($question->id, $quiz1);

        $exportquizclass = new block_export_quiz_export;
        $exportquizmethod = $exportquizclass->export_quiz_questions($quiz1->id, $quiz1->course, 'xhtml', true);

        $message = 'failed to return download object';
        self::assertTrue($exportquizmethod, $message);
    }

    /**
     * test if we are able to get instance of format_handler class
     * @return void
     */
    public function test_we_can_get_instance_of_format_handler_class(): void {
        self::assertInstanceOf(format_handler::class, new format_handler);
    }

    /**
     * test if we are able to get instance of format_handler class
     * @return void
     */
    public function test_we_can_get_instance_of_selected_export_format_class(): void {
        global $DB;
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        list($category, $course, $qcat, $questions) = $generator->setup_course_and_questions();

        $qcat1 = $generator->create_question_category(array(
            'name' => 'My category', 'sortorder' => 1, 'idnumber' => 'myqcat'));

        $quest1 = $generator->update_question($questions[0], null, ['idnumber' => 'myquest']);

        $quest3 = $generator->create_question('shortanswer', null,
        ['name' => 'sa1', 'category' => $qcat1->id, 'idnumber' => 'myquest_3']);

        question_move_questions_to_category([$quest1->id], $qcat1->id);

        $quest2 = $generator->update_question($questions[1], null, ['idnumber' => 'myquest']);

        question_move_questions_to_category([$quest2->id], $qcat1->id);

        $quest4 = $generator->create_question('shortanswer', null, ['name' => 'sa1', 'category' => $qcat1->id, 'idnumber' => '0']);

        $getcourse1context = context_course::instance($course->id);

        $questiondata = array();
        if ($questions = $DB->get_records('question', array('category' => $qcat1->id))) {
            foreach ($questions as $question) {
                array_push($questiondata, question_bank::load_question_data($question->id));
            }
        }

        $formathander = new format_handler;
        $qformat = $formathander->get_file_format($getcourse1context, 'xml', $questiondata);

        $message = 'failed to return object';

        self::assertIsObject($qformat, $message);
    }

}
