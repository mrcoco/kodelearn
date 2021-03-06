<?php defined('SYSPATH') or die('No direct script access.');

class Model_Exam extends ORM {

    protected $_belongs_to = array('course' => array(), 'event' => array(), 'examgroup' => array());

    protected $_has_many = array('examresult' => array('model' => 'examresult'));

    public function validator($data) {
        return Validation::factory($data)
            ->rule('name', 'not_empty')
            ->rule('room_id', 'not_empty')
            ->rule('examgroup_id', 'not_empty')
            ->rule('total_marks', 'not_empty')
            ->rule('total_marks', 'digit')
            ->rule('passing_marks', 'not_empty')
            ->rule('date', 'date')
            ->rule('date', 'not_empty')
            ->rule('from', 'not_empty')
            ->rule('to', 'not_empty')
            ->rule('from', 'Model_Exam::time_check', array(':value',':to'))
            ->rule('passing_marks', 'Model_Exam::marks_check', array(':value',':total_marks'))
            ->rule('passing_marks', 'digit')
            ->rule('name', 'min_length', array(':value', 3));
    }    

    /**
     * Method to get the students appearing for an exam
     * @param int $exam_id
     * @return array (values = user_ids of the students)
     */
    public function get_students($exam_id) {
        $exam = ORM::factory('exam', $exam_id);
        $users = $exam->course->users->find_all();
        return $users;
    }

    public function __toString() {
        return ucfirst($this->name);
    }

    /**
     * Method to return an anchor tag with exam name the text and 
     * link to the exam details page
     */
    public function toLink() {
        if (Acl::instance()->is_allowed('exam_edit')) {
            $url = Url::site('exam/edit/id/');
        } else {
            $url = Url::site('exam');
        }
        return Html::anchor($url, (string)$this);
    }

    /**
     * Method to return the formatted date of the exam
     */
    public function format_scheduled_date() {
        return date('Y-m-d', $this->event->eventstart);
    }

    /**
     * Method to return the formatted start time of the exam
     */
    public function format_starttime() {
        return date('H:i:s', $this->event->eventstart);
    }

    /**
     * Method to return the formatted end time of the exam
     */
    public function format_endtime() {
        return date('H:i:s', $this->event->eventend);
    }

    /**
     * Method to return the name of the room where the exam is going to be held
     */
    public function location() {
        return $this->event->room;
    }    

    public static function time_check($from, $to = NULL) {
        $s_from = strtotime($from);
        
        $s_to = strtotime($to);
        
        if($s_from > $s_to){
            return false;
        } else {
            return true;
        }
    }
    
    public static function marks_check($passing_marks, $total_marks = NULL) {
        
        if($passing_marks > $total_marks){
            return false;
        } else {
            return true;
        }
    }
    
    public static function exams_total($filters=array()) {
       
        $exam = ORM::factory('exam')    
            ->join('events','left')
            ->on('events.id','=','exams.event_id');
        if (isset($filters['filter_name'])) {
            $exam->where('exams.name', 'LIKE', '%' . $filters['filter_name'] . '%');
        }        
        if (isset($filters['filter_passing_marks'])) {
            $exam->where('exams.passing_marks', 'LIKE', '%' . $filters['filter_passing_marks'] . '%');
        } 
        if (isset($filters['filter_total_marks'])) {
            $exam->where('exams.total_marks', 'LIKE', '%' . $filters['filter_total_marks'] . '%');
        } 
        if (isset($filters['filter_reminder'])) {
            if($filters['filter_reminder'] == "yes" || $filters['filter_reminder'] == "Yes" || $filters['filter_reminder'] == "YES" || $filters['filter_reminder'] == "ye" || $filters['filter_reminder'] == "Ye" || $filters['filter_reminder'] == "YE" || $filters['filter_reminder'] == "Y" || $filters['filter_reminder'] == "y") {
                $exam->where('exams.reminder', '=', '1');
            } else if ($filters['filter_reminder'] == "no" || $filters['filter_reminder'] == "No" || $filters['filter_reminder'] == "NO" || $filters['filter_reminder'] == "N" || $filters['filter_reminder'] == "n") {
                $exam->where('exams.reminder', '=', '0');
            }
            
        }       
        
        return $exam->count_all();
    }
    
    public static function exams($filters=array()) {
       
        $exam = ORM::factory('exam')
            ->join('events','left')
            ->on('events.id','=','exams.event_id');
        if (isset($filters['filter_name'])) {
            $exam->where('exams.name', 'LIKE', '%' . $filters['filter_name'] . '%');
        }        
        if (isset($filters['filter_passing_marks'])) {
            $exam->where('exams.passing_marks', 'LIKE', '%' . $filters['filter_passing_marks'] . '%');
        } 
        if (isset($filters['filter_total_marks'])) {
            $exam->where('exams.total_marks', 'LIKE', '%' . $filters['filter_total_marks'] . '%');
        }
        if (isset($filters['filter_reminder'])) {
            if($filters['filter_reminder'] == "yes" || $filters['filter_reminder'] == "Yes" || $filters['filter_reminder'] == "YES" || $filters['filter_reminder'] == "ye" || $filters['filter_reminder'] == "Ye" || $filters['filter_reminder'] == "YE" || $filters['filter_reminder'] == "Y" || $filters['filter_reminder'] == "y") {
                $exam->where('exams.reminder', '=', '1');
            } else if ($filters['filter_reminder'] == "no" || $filters['filter_reminder'] == "No" || $filters['filter_reminder'] == "NO" || $filters['filter_reminder'] == "N" || $filters['filter_reminder'] == "n") {
                $exam->where('exams.reminder', '=', '0');
            }
            
        }
        
        $exam->group_by('id'); 
              
        if (isset($filters['sort'])) {
            $exam->order_by($filters['sort'], Arr::get($filters, 'order', 'ASC'));
        }
        if (isset($filters['limit'])) {
            $exam->limit($filters['limit'])
                ->offset(Arr::get($filters, 'offset', 0));            
        }
        return $exam->find_all();
    }
    
    public static function exams_total_grading_period($filters=array()) {
       
        $exam = ORM::factory('exam')
            ->join('events','left')
            ->on('events.id','=','exams.event_id')
            ->join('examgroups','left')
            ->on('examgroups.id','=','exams.examgroup_id');
        $exam->where('examgroups.name', 'LIKE', '%' . $filters['filter_grading_period'] . '%');
        
        return $exam->count_all();
    }
    
    public static function exams_grading_period($filters=array()) {
       
        $exam = ORM::factory('exam')
            ->join('events','left')
            ->on('events.id','=','exams.event_id')
            ->join('examgroups','left')
            ->on('examgroups.id','=','exams.examgroup_id');
        $exam->where('examgroups.name', 'LIKE', '%' . $filters['filter_grading_period'] . '%');
        
        $exam->group_by('exams.id'); 
              
        if (isset($filters['sort'])) {
            $exam->order_by($filters['sort'], Arr::get($filters, 'order', 'ASC'));
        }
        if (isset($filters['limit'])) {
            $exam->limit($filters['limit'])
                ->offset(Arr::get($filters, 'offset', 0));            
        }
        return $exam->find_all();
    }
    
    public static function exams_total_date($filters=array()) {
       
        $exam = ORM::factory('exam')
            ->join('events','left')
            ->on('events.id','=','exams.event_id');
        $exam->where('events.eventstart', 'between', array($filters['sdate'], $filters['edate']));
        
        return $exam->count_all();
    }
    
    public static function exams_date($filters=array()) {
       
        $exam = ORM::factory('exam')
            ->join('events','left')
            ->on('events.id','=','exams.event_id');
        $exam->where('events.eventstart', 'between', array($filters['sdate'], $filters['edate']));
        
        $exam->group_by('exams.id'); 
              
        if (isset($filters['sort'])) {
            $exam->order_by($filters['sort'], Arr::get($filters, 'order', 'ASC'));
        }
        if (isset($filters['limit'])) {
            $exam->limit($filters['limit'])
                ->offset(Arr::get($filters, 'offset', 0));            
        }
        return $exam->find_all();
    }
    
    public static function exams_total_course($filters=array()) {
       
        $exam = ORM::factory('exam')
            ->join('events','left')
            ->on('events.id','=','exams.event_id')
            ->join('courses','left')
            ->on('courses.id','=','exams.course_id');
        $exam->where('courses.name', 'LIKE', '%' . $filters['filter_course'] . '%');
        
        return $exam->count_all();
    }
    
    public static function exams_course($filters=array()) {
       
        $exam = ORM::factory('exam')
            ->join('events','left')
            ->on('events.id','=','exams.event_id')
            ->join('courses','left')
            ->on('courses.id','=','exams.course_id');
        $exam->where('courses.name', 'LIKE', '%' . $filters['filter_course'] . '%');
        
        $exam->group_by('exams.id'); 
              
        if (isset($filters['sort'])) {
            $exam->order_by($filters['sort'], Arr::get($filters, 'order', 'ASC'));
        }
        if (isset($filters['limit'])) {
            $exam->limit($filters['limit'])
                ->offset(Arr::get($filters, 'offset', 0));            
        }
        return $exam->find_all();
    }
    
    public static function send_exam_reminder() {
        $todays_date = strtotime(date('d-m-Y G:i:s'));
        $exam = ORM::factory('exam')
                ->select('events.eventtype','events.eventstart')
                ->join('events', 'left')
                ->on('events.id','=','exams.event_id');
        $exam->where('exams.reminder', '=', '1')
             ->where('events.eventstart', '>', $todays_date);
        $exams = $exam->find_all();

        foreach($exams as $exam_result) {
            if($exam_result->reminder_days == 0) {
                $students = $exam->get_students($exam_result->id);
                $exam->send_exam_reminder_email($students,$exam_result);       
                
            } else {
                $reminder_days_to_time = (86400 * $exam_result->reminder_days);  
                $time = $exam_result->eventstart - $reminder_days_to_time;
                $date = date('d-m-Y', $time);
                $current_date = date('d-m-Y');
                if($date == $current_date){
                    $students = $exam->get_students($exam_result->id);
                    $exam->send_exam_reminder_email($students,$exam_result);
                }
                  
            }
            
        }
        
    }
    
    public function send_exam_reminder_email($students,$exam_result) {
        foreach($students as $student) {
            $file = "exam_reminder_email";
            $data =array(
                '{user_name}'   => $student->firstname ." ". $student->lastname,
                '{exam_name}'   => $exam_result->name,
                '{date}'        => date('d-m-Y', $exam_result->eventstart),
                '{time}'        => date('g:i:s a', $exam_result->eventstart),
            ); 
            Email::send_mail($student->email, $file, $data);
               
        }
           
    }
    
    public static function get_course_exams_count($course) {
        $count_exam = ORM::factory('exam');
        $count_exam = $count_exam->where('course_id', '=', $course->id)->count_all();       
        return "<a href='".Url::site('exam')."/index/course_id/".$course->id."' target='_blank'>".$count_exam." exams</a>"; 
    }
    
}