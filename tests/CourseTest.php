<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */
    require_once "src/Student.php";
    require_once "src/Course.php";
    $DB = new PDO('pgsql:host=localhost;dbname=registrar');
    class CourseTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Course::deleteAll();
            //Student::deleteAll();
        }
        //Initialize a Course with a name and be able to get it back out of the object using getName().
        function testGetName()
        {
            //Arrange
            $name = "Kitchen chores";
            $number = 909098;
            $id=3;
            $test_class = new Course($name,$id, $number);
            //No need to save here because we are communicating with the object only and not the database.
            //Act
            $result = $test_class->getName();
            //Assert
            $this->assertEquals($name, $result);
        }
        function testSetName()
        { //can I change the name in the object with setName() after initializing it?
            //Arrange
            $name = "Kitchen chores";
            $number = 909098;
            $id=3;
            $test_class = new Course($name,$id, $number);
            //No need to save here because we are communicating with the object only and not the database.
            //Act
            $test_class->setName("Home chores");
            $result = $test_class->getName();
            //Assert
            $this->assertEquals("Home chores", $result);
        }
        //Next, let's add the Id property to our Course class. Like any other property it needs a getter and setter.
        //Create a Course with the id in the constructor and be able to get the id back out.
        function testGetId()
        {
            //Arrange
            $id = 1;
            $number = 909098;
            $name = "Kitchen chores";
            $test_class = new Course($name, $id, $number);
            //Act
            $result = $test_class->getId();
            //Assert
            $this->assertEquals(1, $result);
        }
        //Create a Course with the id in the constructor and be able to change its value, and then get the new id out.
        function testSetId()
        {
            //Arrange
            $id = 1;
            $number = 909098;
            $name = "Kitchen chores";
            $test_class = new Course($name, $id, $number);
            //Act
            $test_class->setId(2);
            //Assert
            $result = $test_class->getId();
            $this->assertEquals(2, $result);
        }
    //    CREATE - save method stores all object data in categories table.
        function testSave()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $number = 909098;
            $test_class = new Course($name, $id, $number);
            $test_class->save();
            //Act
            $result = Course::getAll();
            var_dump($result);
            //Assert
            $this->assertEquals($test_class, $result[0]);
        }
        //This test makes sure that after saving not only are the id's equal, they are not null.
        function testSaveSetsId()
        {
            //Arrange
            $name = "Work stuff";
            $number = 200808;
            $id = 1;
            $test_class = new Course($name, $id, $number);
            //Act
            //save it. Id should be assigned in database, then stored in object.
            $test_class->save();
            //Assert
            //That id in the object should be numeric (not null)
            $this->assertEquals(true, is_numeric($test_class->getId()));
        }
        //READ - All categories
        //This method should return an array of all Course objects from the categories table.
        // //Since it isn't specifically for only one Course, it is for all, it should be a static method.
        function testGetAll()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $number = 200808;
            $test_class = new Course($name, $id, $number);
            $test_class->save();
            $name2 = "Home stuff";
            $id2 = 2;
            $number2 = 200808;
            $test_class2 = new Course($name2, $id2,$number2);
            $test_class2->save();
            //Act
            $result = Course::getAll();
            //Assert
            $this->assertEquals([$test_class, $test_class2], $result);
        }
        // //DELETE - All categories
        // //Since this also deals with more than one Course it should be a static method.
        function testDeleteAll()
        {
            //Arrange
            //We need some categories saved into the database so that we can make sure our deleteAll method removes them all.
            $name = "Wash the dog";
            $id = 1;
            $number = 200808;
            $test_class = new Course($name, $id, $number);
            $test_class->save();
            $name2 = "Water the lawn";
            $id2 = 2;
            $number2 = 200808;
            $test_class2 = new Course($name2, $id2, $number2);
            $test_class2->save();
            //Act
            //Delete categories.
            Course::deleteAll();
            //Assert
            //Now when we call getAll, we should get an empty array because we deleted all categories.
            $result = Course::getAll();
            $this->assertEquals([], $result);
        }
        // //Test for find class method.
        // //Static method to select a class using its id number as input.
        function testFind()
        {
            //Arrange
            //Create and save 2 categories.
            $name = "Wash the dog";
            $id = 1;
            $number = 200808;
            $test_class = new Course($name, $id, $number);
            $test_class->save();
            $name2 = "Home stuff";
            $id2 = 2;
            $number2 = 200808;
            $test_class2 = new Course($name2, $id2, $number);
            $test_class2->save();
            //Act
            //search using the id of the first class.
            $result = Course::find($test_class->getId());
            //Assert
            //if the search function works then result should be the first class.
            //ids are unique so this method will always return a single instance.
            //no need for returning an array like in getAll.
            $this->assertEquals($test_class, $result);
        }
        function testUpdate()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $number = 200808;
            $test_class = new Course($name, $id, $number);
            $test_class->save();
            $new_name = "Home stuff";
            //Act
            $test_class->update($new_name);
            //Assert
            $this->assertEquals("Home stuff", $test_class->getName());
        }
        function testDeleteCategory()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $number = 200808;
            $test_class = new Course($name, $id, $number);
            $test_class->save();
            $name2 = "Home stuff";
            $number2 = 200576808;
            $id2 = 2;
            $test_class2 = new Course($name2, $id2, $number2);
            $test_class2->save();
            //Act
            $test_class->delete();
            //Assert
            $this->assertEquals([$test_class2], Course::getAll());
        }
        //This addStudent() method will assign a Student object to the current Course object by saving their ids in the join table.
        // function testAddStudent()
        // {
        //     //Arrange
        //     //We need a class and a student saved
        //     $name = "Work stuff";
        //     $id = 1;
        //
        //     $test_class = new Course($name, $id, $number);
        //     $test_class->save();
        //     $description = "File reports";
        //     $id2 = 2;
        //     $test_student = new Student($description, $id2);
        //     $test_student->save();
        //     //Act
        //     //call add student method on test class to assign test student to it.
        //     //add student method takes an entire Student object as input and assigns it to the class the method has been called on.
        //     $test_class->addStudent($test_student);
        //     //Assert
        //     //now if we get the students associated with the class we should get the one we assigned back
        //     //our test student should be returned in an array because there can be more than one student associated with a class.
        //     //we still need to write the getStudents() method to get the students associated with the class,
        //     //but now we know how we want to use it.
        //     $this->assertEquals($test_class->getStudents(), [$test_student]);
        // }
        // //Now we write a test for the getStudents method since we need it to be able to test the Add Student method.
        // function testGetStudents()
        // {
        //     //Arrange
        //     //start with a class
        //     $name = "Home stuff";
        //     $id = 1;
        //     $test_class = new Course($name, $id, $number);
        //     $test_class->save();
        //     //create 2 students to assign to it.
        //     $description = "Wash the dog";
        //     $id2 = 2;
        //     $test_student = new Student($description, $id2);
        //     $test_student->save();
        //     $description2 = "Take out the trash";
        //     $id3 = 3;
        //     $test_student2 = new Student($description2, $id3);
        //     $test_student2->save();
        //     //Act
        //     //add both students to the class
        //     $test_class->addStudent($test_student);
        //     $test_class->addStudent($test_student2);
        //     //Assert
        //     //we should get both of them back when we call getStudents on the test class.
        //     $this->assertEquals($test_class->getStudents(), [$test_student, $test_student2]);
        // }
        // //When we call delete on a class it should delete all mention of that class from both the categories table and the join table.
        // //if we delete the class 'work stuff' and then later ask the 'file reports' student which categories it belongs to,
        // //we wouldn't want it to tell us it is assigned to one that doesn't exist anymore.
        // //we don't want to delete the student, just any mention of the class it was associated with in the join table.
        // function testDelete()
        // {
        //     //Arrange
        //     $name = "Work stuff";
        //     $id = 1;
        //     $test_class = new Course($name, $id, $number);
        //     $test_class->save();
        //     $description = "File reports";
        //     $id2 = 2;
        //     $test_student = new Student($description, $id2);
        //     $test_student->save();
        //     //Act
        //     $test_class->addStudent($test_student);
        //     $test_class->delete();
        //     //Assert
        //     $this->assertEquals([], $test_student->getCourse());
        // }
    }
?>
