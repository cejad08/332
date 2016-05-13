<?php

/* Database config */

$db_host        = 'localhost';
$db_user        = 'root';
$db_pass        = '';
$db_database    = 'university'; 

/* End config */


#$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_database); 
$mysqli = new mysqli($db_host, $db_user, $db_pass);
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
//navigation header
echo '<a href="/index.php">Home</a> -
<a href="/stud.php">Student Interface</a> -
<a href="/prof.php">Professor Interface</a>'."<br>";

//Drop db for testing
dropUniversityDB($mysqli,$db_database);

//create university database
if ($mysqli->select_db($db_database) === FALSE) 
{
	$sql = "CREATE DATABASE university;";
    if ($mysqli->query($sql) === TRUE) {
		echo "Database created successfully..."."<br>";
		$mysqli->select_db($db_database);	//select DB
		createUniversityTables($mysqli);	//create tables
		populateTables($mysqli);			//populate tables with required records
	} else {
		echo "Error creating database: " . "<br>". $conn->error;
	}
}
else
{
	echo "Database already created"."<br>";

}
//Close connection
$mysqli->close();

//function to drop database
function dropUniversityDB($mysqli,$db_database)
{
	$sql = 'DROP DATABASE '. $db_database;
	if ($mysqli->query($sql)) {
		echo "Database was successfully dropped\n"."<br>";
	} else {
		echo 'Error dropping database: ' . mysql_error() . "\n";
	}
}
//creates tables based on schema
function createUniversityTables($mysqli) {

    echo "<br>"."Creating Tables..."."<br>";
	
	//Create Professor table
	// sql to create table
	$sql = "CREATE TABLE Professor (
	ssn INT(9) UNSIGNED PRIMARY KEY, 
	fname VARCHAR(30) NOT NULL,
	lname VARCHAR(30) NOT NULL,
	street VARCHAR(50),
	city VARCHAR(50),
	state VARCHAR(2),
	phone_area INT(3),
	phone INT(7),
	salary DOUBLE(16,2),
	title VARCHAR(30),
	sex VARCHAR(1) NOT NULL
	)";
	if ($mysqli->query($sql) === TRUE) {
		echo "Table Professor created successfully..."."<br>";
	} else {
		echo "Error creating Professor table: " . $mysqli->error."<br>";
	}
	
	//Create Department table
	// sql to create table
	$sql = "CREATE TABLE Department (
	dept_no INT(5) UNSIGNED PRIMARY KEY, 
	dept_name VARCHAR(30) NOT NULL,
	phone_area INT(3),
	phone INT(7),
	location VARCHAR(30) NOT NULL,
	chair_ssn INT(9),
	FOREIGN KEY (chair_ssn) REFERENCES Professor(ssn)
	)";
	if ($mysqli->query($sql) === TRUE) {
		echo "Table Department created successfully..."."<br>";
	} else {
		echo "Error creating Department table: " . $mysqli->error."<br>";
	}
	
	//Create Course table
	// sql to create table
	$sql = "CREATE TABLE Course (
	course_no INT(3) UNSIGNED PRIMARY KEY, 
	course_name VARCHAR(30) NOT NULL,
	textbook VARCHAR(100),
	units INT(1) NOT NULL,
	dept_num INT(5) NOT NULL,
	FOREIGN KEY (dept_num) REFERENCES Department(dept_no)
	)";
	if ($mysqli->query($sql) === TRUE) {
		echo "Table Course created successfully..."."<br>";
	} else {
		echo "Error creating Course table: " . $mysqli->error."<br>";
	}
	
	//Create Section table
	// sql to create table
	$sql = "CREATE TABLE Section (
	section_no INT(5) UNSIGNED PRIMARY KEY, 
	class_room VARCHAR(30) NOT NULL,
	seats INT(6) NOT NULL,
	course_num INT(3) NOT NULL,
	FOREIGN KEY (course_num) REFERENCES Course(course_no),
	p_ssn INT(9),
	FOREIGN KEY (p_ssn) REFERENCES Professor(ssn)
	)";
	if ($mysqli->query($sql) === TRUE) {
		echo "Table Section created successfully..."."<br>";
	} else {
		echo "Error creating Section table: " . $mysqli->error."<br>";
	}
	
	//Create Student table
	// sql to create table
	$sql = "CREATE TABLE Student (
	campus_id INT(10) UNSIGNED PRIMARY KEY, 
	fname VARCHAR(30) NOT NULL,
	lname VARCHAR(30) NOT NULL,
	street VARCHAR(50),
	city VARCHAR(50),
	state VARCHAR(2),
	phone_area INT(3),
	phone INT(7),
	major_dept_no INT(5),
	FOREIGN KEY (major_dept_no) REFERENCES Department(dept_no),
	minor_dept_no INT(5),
	FOREIGN KEY (minor_dept_no) REFERENCES Department(dept_no)
	)";
	if ($mysqli->query($sql) === TRUE) {
		echo "Table Student created successfully..."."<br>";
	} else {
		echo "Error creating Student table: " . $mysqli->error."<br>";
	}
	
	
	//Create Degree table
	// sql to create table
	$sql = "CREATE TABLE Degree (
	degree_name VARCHAR(30) NOT NULL,
	p_ssn INT(9) UNSIGNED PRIMARY KEY, 
	FOREIGN KEY (p_ssn) REFERENCES Professor(ssn),
	year YEAR(4) NOT NULL,
	college VARCHAR(30) NOT NULL
	)";
	if ($mysqli->query($sql) === TRUE) {
		echo "Table Degree created successfully..."."<br>";
	} else {
		echo "Error creating Degree table: " . $mysqli->error."<br>";
	}
	
	//Create schedule table
	// sql to create table
	$sql = "CREATE TABLE Schedule (
	section_num INT(5) UNSIGNED PRIMARY KEY, 
	FOREIGN KEY (section_num) REFERENCES Section(section_no),
	day VARCHAR(5) NOT NULL,
	begin_time TIME NOT NULL,
	end_time TIME NOT NULL
	)";
	if ($mysqli->query($sql) === TRUE) {
		echo "Table Schedule created successfully..."."<br>";
	} else {
		echo "Error creating Schedule table: " . $mysqli->error."<br>";
	}
	
	//Create Prerequisite table
	//sql to create table
	$sql = "CREATE TABLE Prerequisite (
	prereq_course_num INT(3) UNSIGNED PRIMARY KEY,
	FOREIGN KEY (prereq_course_num) REFERENCES Course(course_no)
	)";
	if ($mysqli->query($sql) === TRUE) {
		echo "Table Prerequisite created successfully..."."<br>";
	} else {
		echo "Error creating Prerequisite table: " . $mysqli->error."<br>";
	}
	
	//Create Enrollment table
	//sql to create table
	$sql = "CREATE TABLE Enrollment (
	c_id INT(10) UNSIGNED PRIMARY KEY,
	FOREIGN KEY (c_id) REFERENCES Student(campus_id),
	sect_id INT(5) NOT NULL,
	FOREIGN KEY (sect_id) REFERENCES Student(section_no),
	grade CHAR(2)
	)";
	if ($mysqli->query($sql) === TRUE) {
		echo "Table Enrollment created successfully..."."<br>";
	} else {
		echo "Error creating Enrollment table: " . $mysqli->error."<br>";
	}
	
	echo "<br>";
}

function populateTables($mysqli) {
	echo "<br>"."Populating Tables..."."<br>";
	
	//Student 1
	$sql = "INSERT INTO Student (campus_id, fname, lname, street, city, state, phone_area, phone, major_dept_no, minor_dept_no)
	VALUES (12345690, 'John', 'Doe', '123 Fake Str.', 'Fake City', 'CA', '714', '7758755', '00001', '00002')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Student 1 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Student 2
	$sql = "INSERT INTO Student (campus_id, fname, lname, street, city, state, phone_area, phone, major_dept_no, minor_dept_no)
	VALUES (0000000000, 'Jane', 'Doe', '123 Fake Str.', 'Fake City ', 'CA', '714', '7758755', '00001', '00002')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Student 2 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Student 3
	$sql = "INSERT INTO Student (campus_id, fname, lname, street, city, state, phone_area, phone, major_dept_no, minor_dept_no)
	VALUES (0000000001, 'Dan', 'Dong', '123 Rock Str.', 'Rock City', 'CA', '711', '1231234', '00002', '00001')";

	if ($mysqli->query($sql) === TRUE) {
		echo "Student 3 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	
		//Student 2
	$sql = "INSERT INTO Student (campus_id, fname, lname, street, city, state, phone_area, phone, major_dept_no, minor_dept_no)
	VALUES (0000000002, 'Tom', 'Do', '123 A Str.', 'A City', 'CA', '710', '7753333', '00001', '00002')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Student 2 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
		//Student 3
	$sql = "INSERT INTO Student (campus_id, fname, lname, street, city, state, phone_area, phone, major_dept_no, minor_dept_no)
	VALUES (0000000003, 'Ron', 'Dork', '123 5th Str.', 'Fake City', 'CA', '714', '7118755', '00001', '00002')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Student 3 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
		//Student 4
	$sql = "INSERT INTO Student (campus_id, fname, lname, street, city, state, phone_area, phone, major_dept_no, minor_dept_no)
	VALUES (0000000004, 'Jackson', 'T', '123 Lol Str.', 'Lol City', 'CA', '755', '7755755', '00002', '00001')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Student 4 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
		//Student 5
	$sql = "INSERT INTO Student (campus_id, fname, lname, street, city, state, phone_area, phone, major_dept_no, minor_dept_no)
	VALUES (0000000005, 'Ko', 'Co', '123 Fake Str.', 'Fake City St', 'CA', '714', '7758755', '00001', '00002')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Student 5 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
		//Student 6
	$sql = "INSERT INTO Student (campus_id, fname, lname, street, city, state, phone_area, phone, major_dept_no, minor_dept_no)
	VALUES (0000000006, 'Po', 'Po', '123 9th Str.', 'Fake City', 'CA', '414', '1231235', '00001', '00002')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Student 6 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
		//Student 7
	$sql = "INSERT INTO Student (campus_id, fname, lname, street, city, state, phone_area, phone, major_dept_no, minor_dept_no)
	VALUES (0000000007, 'Jane', 'Doe', '123 Fake Str.', 'Fake City St', 'CA', '714', '7758755', '00001', '00002')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Student 7 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
		//Student 8
	$sql = "INSERT INTO Student (campus_id, fname, lname, street, city, state, phone_area, phone, major_dept_no, minor_dept_no)
	VALUES (0000000008, 'Ton', 'Lame', '123 Long Str.', 'Long City', 'CA', '543', '12365444', '00002', '00001')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Student 8 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
		//Student 9
	$sql = "INSERT INTO Student (campus_id, fname, lname, street, city, state, phone_area, phone, major_dept_no, minor_dept_no)
	VALUES (0000000009, 'Fon', 'Due', '123 Choco Str.', 'Choco City', 'CA', '714', '7758755', '00001', '00002')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Student 9 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
		//Student 10
	$sql = "INSERT INTO Student (campus_id, fname, lname, street, city, state, phone_area, phone, major_dept_no, minor_dept_no)
	VALUES (0000000010, 'Tenth', 'Student', '123 Fake Str.', 'Fake City St', 'CA', '714', '7758755', '00001', '00002')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Student 10 added successfully...". "<br>". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	
	//////Departments//////
	//Department 1
	$sql = "INSERT INTO Department (dept_no, dept_name, phone_area, phone, location, chair_ssn)
	VALUES (12345, 'Computer Science', '714', '7758755', 'North West Campus', '000000001')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Department 1 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Department 2
	$sql = "INSERT INTO Department (dept_no, dept_name, phone_area, phone, location, chair_ssn)
	VALUES (54321, 'Engineering', '700', '7711111', 'South West Campus', '000000002')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Department 2 added successfully...". "<br>". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	
	/////Professors/////
	//Professor 1
	$sql = "INSERT INTO Professor (ssn, fname, lname, street, city, state, phone_area, phone, salary, title, sex)
	VALUES (000000001, 'Super', 'Man', '123 Krypton Str', 'Crypt City', 'CA', '111', '1231234', 40000.00, 'Dr', 'M')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Professors 1 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}

	$sql = "INSERT INTO Degree (degree_name, p_ssn, year, college)
	VALUES ('Computer Science BS', 000000001, 2000, 'Cal State Fullerton')";
	if ($mysqli->query($sql) === TRUE) {
		//echo "Professors 1's degree added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Professor 2
	$sql = "INSERT INTO Professor (ssn, fname, lname, street, city, state, phone_area, phone, salary, title, sex)
	VALUES (000000002, 'Bat', 'Man', '123 Cave Str', 'Cave City', 'CA', '321', '1121113', 65457.01, 'Dr', 'F')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Professors 2 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	$sql = "INSERT INTO Degree (degree_name, p_ssn, year, college)
	VALUES ('Engineering BS', 000000002, 2000, 'Cal State Long Beach')";
	if ($mysqli->query($sql) === TRUE) {
		//echo "Professors 1's degree added successfully...". "<br>";
		echo "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Professor 3
	$sql = "INSERT INTO Professor (ssn, fname, lname, street, city, state, phone_area, phone, salary, title, sex)
	VALUES (000000003, 'Tony', 'Stark', '123 Ironman Str', 'Long City', 'CA', '211', '9876543211', 80000.00, 'Dr', 'M')";
	if ($mysqli->query($sql) === TRUE) {
		echo "Professors 3 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}

	$sql = "INSERT INTO Degree (degree_name, p_ssn, year, college)
	VALUES ('Electrical Engineering BS', 000000003, 2000, 'Massachussetts Institute of Technology')";
	if ($mysqli->query($sql) === TRUE) {
		//echo "Professors 3's degree added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	
	/////Courses//////
	//Course 1
	$sql = "INSERT INTO Course (course_no, course_name, textbook, units, dept_num)
	VALUES (442, 'Engineering Computer Things', 'Fundamentals Of Engineering Computer Things', 4, 54321)";
	if ($mysqli->query($sql) === TRUE) {
		echo "Course 1 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Course 2
	$sql = "INSERT INTO Course (course_no, course_name, textbook, units, dept_num)
	VALUES (315, 'Comp Sci Ethics', 'Fundamentals Of Ethics', 3, 12345)";
	if ($mysqli->query($sql) === TRUE) {
		echo "Course 2 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Course 3
	$sql = "INSERT INTO Course (course_no, course_name, textbook, units, dept_num)
	VALUES (362, 'Software Engineering', 'Fundamentals Of Software Engineering', 3, 54321)";
	if ($mysqli->query($sql) === TRUE) {
		echo "Course 3 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Course 4
	$sql = "INSERT INTO Course (course_no, course_name, textbook, units, dept_num)
	VALUES (323, 'Compilers and stuff', 'Fundamentals Of Compilers', 3, 12345)";
	if ($mysqli->query($sql) === TRUE) {
		echo "Course 4 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Section 1:Course 1_1
	$sql = "INSERT INTO Section (section_no, classroom, meeting_days, begin_time, end_time, num_seats, course_no, p_ssn"
	VALUES (442_1, '300CS', 'M,W', '8:00AM','9:15AM', '32',442,000000001 )
	if ($mysqli->query($sql) === TRUE) {
		echo "Section 1 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Section 2:Course 1_2
	$sql = "INSERT INTO Section (section_no, classroom, meeting_days, begin_time, end_time, num_seats, course_no, p_ssn"
	VALUES (442_2, '300CS', 'T,TH', '8:00AM','9:15AM', '32',442,000000001 )
	if ($mysqli->query($sql) === TRUE) {
		echo "Section 2 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Section 3:Course 2_1
	$sql = "INSERT INTO Section (section_no, classroom, meeting_days, begin_time, end_time, num_seats, course_no, p_ssn"
	VALUES (315_1, '101CS', 'M,W', '10:00AM','11:15AM', '35',315,000000001 )
	if ($mysqli->query($sql) === TRUE) {
		echo "Section 3 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Section 4: Course 2_2
	$sql = "INSERT INTO Section (section_no, classroom, meeting_days, begin_time, end_time, num_seats, course_no, p_ssn"
	VALUES (315_2, '101CS', 'T,TH', '10:00AM','11:15AM', '35',315,000000002 )
	if ($mysqli->query($sql) === TRUE) {
		echo "Section 4 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Section 5: Course 3_1
	$sql = "INSERT INTO Section (section_no, classroom, meeting_days, begin_time, end_time, num_seats, course_no, p_ssn"
	VALUES (362_1, '201CS', 'M,W', '12:00PM','1:15PM', '23',362,000000002 )
	if ($mysqli->query($sql) === TRUE) {
		echo "Section 5 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Section 6: Course 3_2
	$sql = "INSERT INTO Section (section_no, classroom, meeting_days, begin_time, end_time, num_seats, course_no, p_ssn"
	VALUES (362_2, '201CS', 'T,TH', '12:00PM','1:15PM', '23',362,000000002 )
	if ($mysqli->query($sql) === TRUE) {
		echo "Section 6 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Section 7: Course 4_1
	$sql = "INSERT INTO Section (section_no, classroom, meeting_days, begin_time, end_time, num_seats, course_no, p_ssn"
	VALUES (323_1, '401CS', 'M,W', '7:00PM','8:15PM', '27',323,000000003 )
	if ($mysqli->query($sql) === TRUE) {
		echo "Section 7 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}
	//Section 8: Course 4_2
	$sql = "INSERT INTO Section (section_no, classroom, meeting_days, begin_time, end_time, num_seats, course_no, p_ssn"
	VALUES (323_2, '401CS', 'T,TH', '7:00PM','8:15PM', '27',323,000000003 )
	if ($mysqli->query($sql) === TRUE) {
		echo "Section 8 added successfully...". "<br>";
	} else {
		echo "Error:". "<br>" . $mysqli->error;
	}	
	/////Need to populate the rest of the requirements on the paper////
}

?>

<html>
<head> <title>Student Interface - CPSC 332 Project</title> </head>
<body>
</body>
</html>
