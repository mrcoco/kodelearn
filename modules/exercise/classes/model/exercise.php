<?php defined('SYSPATH') or die('No direct script access.');

class Model_Exercise extends ORM {

    protected $_has_many = array(
        'results' => array(
            'model' => 'exerciseresult', 
        )
    );

    public function validator($data) {
        return Validation::factory($data)
            ->rule('title', 'not_empty');
    }

    /**
     * Method to get the number of questions in this exercise
     * @return int num_questions
     */
    public function num_questions() {
        return ORM::factory('exercisequestion')
            ->where('exercise_id', ' = ', $this->id)
            ->count_all();
    }

    /**
     * MEthod to get the questions selected for this exercise
     * @return 
     */
    public function questions() {
        return ORM::factory('exercisequestion')
            ->where('exercise_id', ' = ', $this->id)
            ->find_all();
    }

    /**
     * Get the total marks for exercise = Sum of marks of all questions
     */
    public function marks() {
        return array_sum($this->questions()->as_array(null, 'marks'));
    }

    /**
     * Method to add questions to an exercise
     * @param Array $zipped_data will be a zipped array of individual column arrays (question_id, marks)
     *        eg: array((1, 10), (2, 34)) marks for question 1 = 10, marks for question 2 = 34 etc.
     * @return Model_Exercise
     */
    public function add_questions($zipped_data) {
        foreach ($zipped_data as $ques) {
            $assoc = ORM::factory('exercisequestion');
            $assoc->exercise_id = $this->id;
            $assoc->question_id = $ques[0];
            $assoc->marks = $ques[1];
            $assoc->save();
        }            
        return $this;
    }

    /**
     * Method to delete all questions from an exercise
     * It will not actually delete the questions but just disassociate them from the exercise
     * @return Model_Exercise
     */
    public function delete_questions() {
        DB::delete('exercisequestions')
            ->where('exercise_id', ' = ', $this->id)
            ->execute();
        return $this;
    }

    /**
     * Method to check if the user has attempted the exercise atleast once
     */
    public function is_attempted() {
        return $this->results
            ->where('user_id', ' = ', Auth::instance()->get_user()->id)
            ->count_all();
    }

    /**
     * Method to get the attempts of the current user
     */
    public function attempts() {
        return $this->results
            ->where('user_id', ' = ', Auth::instance()->get_user()->id)
            ->find_all();
    }
}
