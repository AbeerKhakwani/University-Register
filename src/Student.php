<?php
class Student
{
    private $name;
    private $id;
    private $enroll_date;
    function __construct($initial_name, $initial_id = null, $initial_date)
    {
        $this->name        = $initial_name;
        $this->id          = $initial_id;
        $this->enroll_date = $initial_date;
    }
    function getName()
    {
        return $this->name;
    }
    function setName($new_name)
    {
        $this->name = (string) $new_name;
    }
    function getId()
    {
        return $this->id;
    }
    function setId($new_id)
    {
        $this->id = (int) $new_id;
    }
    function getDate()
    {
        return $this->enroll_date;
    }
    function setDate($new_date)
    {
        $this->enroll_date = (int) $new_date;
    }
    function save()
    {
        $statement = $GLOBALS['DB']->query("INSERT INTO students (name, enroll_date) VALUES ('{$this->getName()}', '{$this->getDate()}') RETURNING id;");
        $result    = $statement->fetch(PDO::FETCH_ASSOC);
        $this->setId($result['id']);
    }
    static function getAll()
    {
        $returned_students = $GLOBALS['DB']->query("SELECT * FROM students;");
        $array_student     = array();
        foreach ($returned_students as $student) {
            $name        = $student['name'];
            $id          = $student['id'];
            $enroll_date = $student['enroll_date'];
            $new_student = new Student($name, $id, $enroll_date);
            array_push($array_student, $new_student);
        }
        return $array_student;
    }
    static function deleteAll()
    {
        $GLOBALS['DB']->exec("DELETE FROM students *;");
        $GLOBALS['DB']->exec("DELETE FROM students_courses *;");
    }
    function addCourse($new_course)
    {
        $GLOBALS['DB']->exec("INSERT INTO students_courses (student_id, course_id) VALUES ({$this->getId()}, {$new_course->getId()});");
    }
    function getCourses()
    {
        $query      = $GLOBALS['DB']->query("SELECT courses.* FROM
            students JOIN students_courses ON (students.id = students_courses.student_id)
            JOIN courses ON (students_courses.course_id = courses.id)
            WHERE students.id = {$this->getId()};");




    //    $course_ids = $query->fetchAll(PDO::FETCH_ASSOC);
        // var_dump($query);

        $courses   = array();
        foreach ($query as $course) {
            // $course_id       = $id['course_id'];
            // $result          = $GLOBALS['DB']->query("SELECT * FROM courses WHERE id = {$course_id};");
            // $returned_course = $result->fetchAll(PDO::FETCH_ASSOC);
            //
            //

            $name          = $course['name'];
            $id            = $course['id'];
            $course_number = $course['course_number'];
            $new_course    = new Course($name, $id, $course_number);
            array_push($courses , $new_course);
        }
        return $courses ;
    }
    function update($new_name)
    {
        $GLOBALS['DB']->exec("UPDATE students SET name = '{$new_name}' WHERE id = {$this->getId()};");
        $this->setName($new_name);
    }
    function delete()
    {
        //delete any studentfrom the tasks table where their id matches the current one.
        $GLOBALS['DB']->exec("DELETE FROM students WHERE id = {$this->getId()};");
        //also delete any rows from the categories_tasks table where the task id is the current one.
        $GLOBALS['DB']->exec("DELETE FROM students_courses WHERE student_id = {$this->getId()};");
    }
    static function find($search_id)
    {
        $found_student = null;
        $student       = Student::getAll();
        foreach ($student as $student) {
            $student_id = $student->getId();
            if ($student_id == $search_id) {
                $found_student = $student;
            }
        }
        return $found_student;
    }
}
?>
