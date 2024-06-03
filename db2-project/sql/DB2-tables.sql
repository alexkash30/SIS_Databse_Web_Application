create table account
	(email		varchar(50),
	 password	varchar(20) not null,
	 type		varchar(20),
	 primary key(email)
	);


create table department
	(dept_name	varchar(100), 
	 location	varchar(100), 
	 primary key (dept_name)
	);

create table instructor
	(instructor_id		varchar(10),
	 instructor_name	varchar(50) not null,
	 title 			varchar(30),
	 dept_name		varchar(100), 
	 email			varchar(50) not null,
	 primary key (instructor_id)
	);


create table student
	(student_id		varchar(10), 
	 name			varchar(20) not null, 
	 email			varchar(50) not null,
	 dept_name		varchar(100),
	 primary key (student_id),
	 foreign key (dept_name) references department (dept_name)
		on delete set null
	);

create table PhD
	(student_id			varchar(10), 
	 qualifier			varchar(30), 
	 proposal_defense_date		date,
	 dissertation_defense_date	date, 
	 primary key (student_id),
	 foreign key (student_id) references student (student_id)
		on delete cascade
	);

create table master
	(student_id		varchar(10), 
	 total_credits		int,
	 primary key (student_id),
	 foreign key (student_id) references student (student_id)
		on delete cascade
	);

create table undergraduate
	(student_id		varchar(10), 
	 total_credits		int,
	 class_standing		varchar(10)
		check (class_standing in ('Freshman', 'Sophomore', 'Junior', 'Senior')), 
	hold			varchar(4)
	 	check (hold in ('HOLD')), 	
	 primary key (student_id),
	 foreign key (student_id) references student (student_id)
		on delete cascade
	);

create table classroom
	(classroom_id 		varchar(8),
	 building		varchar(40) not null,
	 room_number		varchar(7) not null,
	 capacity		numeric(4,0),
	 primary key (classroom_id)
	);

create table time_slot
	(time_slot_id		varchar(8),
	 day			varchar(10) not null,
	 start_time		time not null,
	 end_time		time not null,
	 primary key (time_slot_id)
	);

create table course
	(course_id		varchar(20), 
	 course_name		varchar(50) not null, 
	 credits		numeric(2,0) check (credits > 0),
	 primary key (course_id)
	);

create table section
	(course_id		varchar(20),
	 section_id		varchar(10), 
	 semester		varchar(6)
			check (semester in ('Fall', 'Winter', 'Spring', 'Summer')), 
	 year			numeric(4,0) check (year > 1990 and year < 2100), 
	 instructor_id		varchar(10),
	 classroom_id   	varchar(8),
	 time_slot_id		varchar(8),	
	 primary key (course_id, section_id, semester, year),
	 foreign key (course_id) references course (course_id)
		on delete cascade,
	 foreign key (instructor_id) references instructor (instructor_id)
		on delete set null,
	 foreign key (time_slot_id) references time_slot(time_slot_id)
		on delete set null
	);

create table prereq
	(course_id		varchar(20), 
	 prereq_id		varchar(20) not null,
	 primary key (course_id, prereq_id),
	 foreign key (course_id) references course (course_id)
		on delete cascade,
	 foreign key (prereq_id) references course (course_id)
	);

create table advise
	(instructor_id		varchar(10),
	 student_id		varchar(10),
	 start_date		date not null,
	 end_date		date,
	 primary key (instructor_id, student_id),
	 foreign key (instructor_id) references instructor (instructor_id)
		on delete  cascade,
	 foreign key (student_id) references PhD (student_id)
		on delete cascade
);

create table advise_undergraduate
	(instructor_id		varchar(10),
	 student_id		varchar(10),
	 start_date		date not null,
	 end_date		date,
	 primary key (instructor_id, student_id),
	 foreign key (instructor_id) references instructor (instructor_id)
		on delete  cascade,
	 foreign key (student_id) references undergraduate (student_id)
		on delete cascade
);

create table TA
	(student_id		varchar(10),
	 course_id		varchar(20),
	 section_id		varchar(10), 
	 semester		varchar(6),
	 year			numeric(4,0),
	 primary key (student_id, course_id, section_id, semester, year),
	 foreign key (student_id) references PhD (student_id)
		on delete cascade,
	 foreign key (course_id, section_id, semester, year) references 
	     section (course_id, section_id, semester, year)
		on delete cascade
);

create table masterGrader
	(student_id		varchar(10),
	 course_id		varchar(20),
	 section_id		varchar(10), 
	 semester		varchar(6),
	 year			numeric(4,0),
	 primary key (student_id, course_id, section_id, semester, year),
	 foreign key (student_id) references master (student_id)
		on delete cascade,
	 foreign key (course_id, section_id, semester, year) references 
	     section (course_id, section_id, semester, year)
		on delete cascade
);

create table undergraduateGrader
	(student_id		varchar(10),
	 course_id		varchar(20),
	 section_id		varchar(10), 
	 semester		varchar(6),
	 year			numeric(4,0),
	 primary key (student_id, course_id, section_id, semester, year),
	 foreign key (student_id) references undergraduate (student_id)
		on delete cascade,
	 foreign key (course_id, section_id, semester, year) references 
	     section (course_id, section_id, semester, year)
		on delete cascade
);

create table take
	(student_id		varchar(10), 
	 course_id		varchar(20),
	 section_id		varchar(10), 
	 semester		varchar(6),
	 year			numeric(4,0),
	 grade		    	varchar(2)
		check (grade in ('A+', 'A', 'A-','B+', 'B', 'B-','C+', 'C', 'C-','D+', 'D', 'D-','F')), 
	 primary key (student_id, course_id, section_id, semester, year),
	 foreign key (course_id, section_id, semester, year) references 
	     section (course_id, section_id, semester, year)
		on delete cascade,
	 foreign key (student_id) references student (student_id)
		on delete cascade
	);

create table semester
	(
		semester		varchar(6) check (semester in ('Fall', 'Winter', 'Spring', 'Summer')), 
	 	year			numeric(4,0) check (year > 1990 and year < 2100),
		status			varchar(10) check (status in ('Past', 'Current', 'Future')), 
		primary key (semester, year)
	);

insert into department (dept_name, location) value ('Miner School of Computer & Information Sciences', 'Dandeneau Hall, 1 University Avenue, Lowell, MA 01854');
insert into department (dept_name, location) value ('Chemistry', 'Olney Hall, 265 South Riverside Street, Lowell, MA 01854');
insert into department (dept_name, location) value ('Mathematics & Statistics', 'Southwick Hall, 1 University Avenue, Lowell, MA 01854');
insert into department (dept_name, location) value ('Political Science', 'Dugan Hall, 883 Broadway Street, Lowell, MA 01854');
insert into department (dept_name, location) value ('Accounting', 'Manning school of Business, 72 University Ave, Lowell, MA 01854');

insert into account (email, password, type) values ('admin@uml.edu', '123456', 'admin');
insert into account (email, password, type) values ('dbadams@cs.uml.edu', '123456', 'instructor');
insert into account (email, password, type) values ('slin@cs.uml.edu', '123456', 'instructor');
insert into account (email, password, type) values ('Yelena_Rykalova@uml.edu', '123456', 'instructor');
insert into account (email, password, type) values ('Johannes_Weis@uml.edu', '123456', 'instructor');
insert into account (email, password, type) values ('Charles_Wilkes@uml.edu', '123456', 'instructor');

insert into account(email, password, type) values ('student1@student.uml.edu', '123456', 'student');
insert into account(email, password, type) values ('student2@student.uml.edu', '123456', 'student');
insert into account(email, password, type) values ('student3@student.uml.edu', '123456', 'student');
insert into account(email, password, type) values ('student4@student.uml.edu', '123456', 'student');
insert into account(email, password, type) values ('student5@student.uml.edu', '123456', 'student');
insert into account(email, password, type) values ('student6@student.uml.edu', '123456', 'student');
insert into account(email, password, type) values ('student7@student.uml.edu', '123456', 'student');
insert into account(email, password, type) values ('student8@student.uml.edu', '123456', 'student');
insert into account(email, password, type) values ('student9@student.uml.edu', '123456', 'student');
insert into account(email, password, type) values ('student10@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student11@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student12@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student13@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student14@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student15@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student16@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student17@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student18@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student19@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student20@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student21@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student22@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student23@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student24@student.uml.edu', '123456', 'student');
insert into account (email, password, type) values ('student25@student.uml.edu', '123456', 'student');



insert into student (student_id, name, email, dept_name) values (100, 'Phil', 'student1@student.uml.edu', 'Miner School of Computer & Information Sciences');
insert into student (student_id, name, email, dept_name) values (101, 'Steve', 'student2@student.uml.edu', 'Miner School of Computer & Information Sciences');
insert into student (student_id, name, email, dept_name) values (102, 'Ryan', 'student3@student.uml.edu', 'Miner School of Computer & Information Sciences');
insert into student (student_id, name, email, dept_name) values (103, 'Dave', 'student4@student.uml.edu', 'Miner School of Computer & Information Sciences');
insert into student (student_id, name, email, dept_name) values (104, 'Buck', 'student5@student.uml.edu', 'Miner School of Computer & Information Sciences');
insert into student (student_id, name, email, dept_name) values (105, 'Bill', 'student6@student.uml.edu', 'Miner School of Computer & Information Sciences');
insert into student (student_id, name, email, dept_name) values (106, 'John', 'student7@student.uml.edu', 'Miner School of Computer & Information Sciences');
insert into student (student_id, name, email, dept_name) values (107, 'Emily', 'student8@student.uml.edu', 'Miner School of Computer & Information Sciences');
insert into student (student_id, name, email, dept_name) values (108, 'Sarah', 'student9@student.uml.edu', 'Miner School of Computer & Information Sciences');
insert into student (student_id, name, email, dept_name) values (109, 'Michael', 'student10@student.uml.edu', 'Miner School of Computer & Information Sciences');
insert into student (student_id, name, email, dept_name) values (110, 'Jessica', 'student11@student.uml.edu', 'Chemistry');
insert into student (student_id, name, email, dept_name) values (111, 'Alex', 'student12@student.uml.edu', 'Miner School of Computer & Information Sciences');
insert into student (student_id, name, email, dept_name) values (112, 'Emma', 'student13@student.uml.edu', 'Accounting');
insert into student (student_id, name, email, dept_name) values (113, 'Oliver', 'student14@student.uml.edu', 'Political Science');
insert into student (student_id, name, email, dept_name) values (114, 'Sophia', 'student15@student.uml.edu', 'Chemistry');
insert into student (student_id, name, email, dept_name) values (115, 'John', 'student16@student.uml.edu', 'Mathematics & Statistics');
insert into student (student_id, name, email, dept_name) values (116, 'Emily', 'student17@student.uml.edu', 'Miner School of Computer & Information Sciences');
insert into student (student_id, name, email, dept_name) values (117, 'Sarah', 'student18@student.uml.edu', 'Political Science');
insert into student (student_id, name, email, dept_name) values (118, 'Michael', 'student19@student.uml.edu', 'Chemistry');
insert into student (student_id, name, email, dept_name) values (119, 'Jessica', 'student20@student.uml.edu', 'Accounting');
insert into student (student_id, name, email, dept_name) values (120, 'Alex', 'student21@student.uml.edu', 'Mathematics & Statistics');
insert into student (student_id, name, email, dept_name) values (121, 'Emma', 'student22@student.uml.edu', 'Miner School of Computer & Information Sciences');
insert into student (student_id, name, email, dept_name) values (122, 'Oliver', 'student23@student.uml.edu', 'Political Science');
insert into student (student_id, name, email, dept_name) values (123, 'Sophia', 'student24@student.uml.edu', 'Chemistry');
insert into student (student_id, name, email, dept_name) values (124, 'William', 'student25@student.uml.edu', 'Accounting');


insert into undergraduate (student_id, total_credits, class_standing) values (100, 3, 'Freshman');
insert into undergraduate (student_id, total_credits, class_standing) values (101, 3, 'Freshman');
insert into undergraduate (student_id, total_credits, class_standing) values (102, 3, 'Freshman');
insert into undergraduate (student_id, total_credits, class_standing) values (103, 3, 'Freshman');
insert into undergraduate (student_id, total_credits, class_standing) values (104, 3, 'Freshman');
insert into undergraduate (student_id, total_credits, class_standing) values (105, 3, 'Freshman');
insert into undergraduate (student_id, total_credits, class_standing) values (106, 3, 'Freshman');
insert into undergraduate (student_id, total_credits, class_standing) values (107, 3, 'Freshman');
insert into undergraduate (student_id, total_credits, class_standing) values (108, 3, 'Freshman');
insert into undergraduate (student_id, total_credits, class_standing) values (109, 3, 'Freshman');


insert into master (student_id, total_credits) values (110, 3);
insert into master (student_id, total_credits) values (111, 0);
insert into master (student_id, total_credits) values (112, 0);
insert into master (student_id, total_credits) values (113, 0);
insert into master (student_id, total_credits) values (114, 0);
insert into master (student_id, total_credits) values (115, 0);
insert into master (student_id, total_credits) values (116, 0);
insert into master (student_id, total_credits) values (117, 0);
insert into master (student_id, total_credits) values (118, 0);
insert into master (student_id, total_credits) values (119, 0);

insert into PhD (student_id) values (120);
insert into PhD (student_id) values (121);
insert into PhD (student_id) values (122);
insert into PhD (student_id) values (123);
insert into PhD (student_id) values (124);


-- COMP 
insert into course (course_id, course_name, credits) values ('COMP1010', 'Computing I', 3);
insert into course (course_id, course_name, credits) values ('COMP1020', 'Computing II', 3);
insert into course (course_id, course_name, credits) values ('COMP2010', 'Computing III', 3);
insert into course (course_id, course_name, credits) values ('COMP2040', 'Computing IV', 3);
-- -- CHEM
-- insert into course (course_id, course_name, credits) values ('CHEM1210', 'Chemistry I', 3);
-- insert into course (course_id, course_name, credits) values ('CHEM1220', 'Chemistry II', 3);
-- insert into course (course_id, course_name, credits) values ('CHEM2210', 'Organic Chemistry I', 3);
-- insert into course (course_id, course_name, credits) values ('CHEM1210', 'Organic Chemistry Laboratory IA', 1);
-- -- MATH
-- insert into course (course_id, course_name, credits) values ('MATH1310', 'Calculus I', 3);
-- insert into course (course_id, course_name, credits) values ('MATH1320', 'Calculus II', 3);
-- insert into course (course_id, course_name, credits) values ('MATH2310', 'Calculus III', 3);
-- insert into course (course_id, course_name, credits) values ('MATH2210', 'Introduction to Linear Algebra', 3); --prereq MATH.1310
-- -- POLI SCI
-- insert into course (course_id, course_name, credits) values ('POLI1100', 'Introduction to Polotics', 3);
-- insert into course (course_id, course_name, credits) values ('POLI1750', 'Introduction to Enviromental Polotics', 3);
-- insert into course (course_id, course_name, credits) values ('POLI2001', 'Comparative Enviromental Polotics', 3);--prereq POLI.1750
-- insert into course (course_id, course_name, credits) values ('POLI2220', 'Polotics of the Internet', 3);
-- --ACCOUNTING
-- insert into course (course_id, course_name, credits) values ('ACCT2010', 'Accounting/Financial', 3);
-- insert into course (course_id, course_name, credits) values ('ACCT2020', 'Accounting/Managerial', 3);--prereq ACCT.2010
-- insert into course (course_id, course_name, credits) values ('ACCT3100', 'Corporate Financial Reporting I', 3);--prereq ACCT.2010
-- insert into course (course_id, course_name, credits) values ('ACCT3200', 'Corporate Financial Reporting II', 3);--prereq ACCT.3100

insert into instructor (instructor_id, instructor_name, title, dept_name, email) value ('1', 'David Adams', 'Teaching Professor', 'Miner School of Computer & Information Sciences','dbadams@cs.uml.edu');
insert into instructor (instructor_id, instructor_name, title, dept_name, email) value ('2', 'Sirong Lin', 'Associate Teaching Professor', 'Miner School of Computer & Information Sciences','slin@cs.uml.edu');
insert into instructor (instructor_id, instructor_name, title, dept_name, email) value ('3', 'Yelena Rykalova', 'Associate Teaching Professor', 'Miner School of Computer & Information Sciences', 'Yelena_Rykalova@uml.edu');
insert into instructor (instructor_id, instructor_name, title, dept_name, email) value ('4', 'Johannes Weis', 'Assistant Teaching Professor', 'Miner School of Computer & Information Sciences','Johannes_Weis@uml.edu');
insert into instructor (instructor_id, instructor_name, title, dept_name, email) value ('5', 'Tom Wilkes', 'Assistant Teaching Professor', 'Miner School of Computer & Information Sciences','Charles_Wilkes@uml.edu');


insert into classroom (classroom_id, building, room_number, capacity) value ('1', 'Shea hall', '310', 25);
insert into classroom (classroom_id, building, room_number, capacity) value ('2', 'Shea hall', '208', 30);
insert into classroom (classroom_id, building, room_number, capacity) value ('3', 'Falmouth hall', '316', 25);
insert into classroom (classroom_id, building, room_number, capacity) value ('4', 'Falmouth hall', '114', 25);
insert into classroom (classroom_id, building, room_number, capacity) value ('5', 'Olsen hall', '510', 40);
insert into classroom (classroom_id, building, room_number, capacity) value ('6', 'Ball hall', '212', 35);
insert into classroom (classroom_id, building, room_number, capacity) value ('7', 'Onley hall', '150', 450);
-- insert into classroom (classroom_id, building, room_number, capacity) value ('8', 'Pulichino Tong Business Center', '140', 50);
-- insert into classroom (classroom_id, building, room_number, capacity) value ('9', 'Pulichino Tong Business Center', '216', 30);
-- insert into classroom (classroom_id, building, room_number, capacity) value ('10', 'Pulichino Tong Business Center', '318', 25);
-- insert into classroom (classroom_id, building, room_number, capacity) value ('11', 'Dugan Hall', '132', 35);
-- insert into classroom (classroom_id, building, room_number, capacity) value ('12', 'Dugan Hall', '210', 40);
-- insert into classroom (classroom_id, building, room_number, capacity) value ('13', 'Coburn Hall', '110', 50);


insert into time_slot (time_slot_id, day, start_time, end_time) value ('TS1', 'MoWeFr', '11:00:00', '11:50:00');
insert into time_slot (time_slot_id, day, start_time, end_time) value ('TS2', 'MoWeFr', '12:00:00', '12:50:00');
insert into time_slot (time_slot_id, day, start_time, end_time) value ('TS3', 'MoWeFr', '13:00:00', '13:50:00');
insert into time_slot (time_slot_id, day, start_time, end_time) value ('TS4', 'TuTh', '11:00:00', '12:15:00');
insert into time_slot (time_slot_id, day, start_time, end_time) value ('TS5', 'TuTh', '12:30:00', '13:45:00');


-- COMP
insert into section (course_id, section_id, semester, year,instructor_id,classroom_id,time_slot_id) value ('COMP1010', 'Section101', 'Fall', 2023, 1,1,'TS1');
insert into section (course_id, section_id, semester, year,instructor_id,classroom_id,time_slot_id) value ('COMP1010', 'Section102', 'Fall', 2023, 2,3,'TS3');
insert into section (course_id, section_id, semester, year,instructor_id,classroom_id,time_slot_id) value ('COMP2010', 'Section101', 'Fall', 2023,1,2,'TS2');
insert into section (course_id, section_id, semester, year,instructor_id,classroom_id,time_slot_id) value ('COMP2010', 'Section102', 'Fall', 2023,3,3,'TS1');

insert into section (course_id, section_id, semester, year,instructor_id,classroom_id,time_slot_id) value ('COMP1010', 'Section101', 'Spring', 2024,1,5,'TS1');
insert into section (course_id, section_id, semester, year,instructor_id,classroom_id,time_slot_id) value ('COMP1010', 'Section102', 'Spring', 2024,2,3,'TS2');
insert into section (course_id, section_id, semester, year,instructor_id,classroom_id,time_slot_id) value ('COMP1020', 'Section101', 'Spring', 2024,4,2,'TS1');
insert into section (course_id, section_id, semester, year,instructor_id,classroom_id,time_slot_id) value ('COMP2040', 'Section101', 'Spring', 2024,3,4,'TS4');
insert into section (course_id, section_id, semester, year,instructor_id,classroom_id,time_slot_id) value ('COMP2040', 'Section102', 'Spring', 2024,2,1,'TS3');

insert into section (course_id, section_id, semester, year) value ('COMP1010', 'Section101', 'Fall', 2024);
insert into section (course_id, section_id, semester, year) value ('COMP1010', 'Section102', 'Fall', 2024);
insert into section (course_id, section_id, semester, year) value ('COMP2010', 'Section101', 'Fall', 2024);
insert into section (course_id, section_id, semester, year) value ('COMP2010', 'Section102', 'Fall', 2024);

-- -- CHEM
-- insert into section (course_id, section_id, semester, year) value ('CHEM1210', 'Section101', 'Fall', 2023);
-- insert into section (course_id, section_id, semester, year) value ('CHEM1210', 'Section102', 'Fall', 2023);
-- insert into section (course_id, section_id, semester, year) value ('CHEM1210', 'Section103', 'Fall', 2023);
-- insert into section (course_id, section_id, semester, year) value ('CHEM1220', 'Section101', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('CHEM1220', 'Section102', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('CHEM2210', 'Section201', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('CHEM2210', 'Section202', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('CHEM1210', 'Section101', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('CHEM1210', 'Section102', 'Spring', 2024);
-- -- MATH
-- insert into section (course_id, section_id, semester, year) value ('MATH1310', 'Section101', 'Fall', 2023);
-- insert into section (course_id, section_id, semester, year) value ('MATH1310', 'Section102', 'Fall', 2023);
-- insert into section (course_id, section_id, semester, year) value ('MATH1320', 'Section101', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('MATH1320', 'Section102', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('MATH1320', 'Section103', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('MATH2310', 'Section201', 'Fall', 2024);
-- insert into section (course_id, section_id, semester, year) value ('MATH2310', 'Section202', 'Fall', 2024);
-- insert into section (course_id, section_id, semester, year) value ('MATH2210', 'Section101', 'Fall', 2023);
-- insert into section (course_id, section_id, semester, year) value ('MATH2210', 'Section102', 'Fall', 2023);
-- insert into section (course_id, section_id, semester, year) value ('MATH2210', 'Section101', 'Fall', 2024);
-- -- POLI SCI
-- insert into section (course_id, section_id, semester, year) value ('POLI1100', 'Section101', 'Fall', 2023);
-- insert into section (course_id, section_id, semester, year) value ('POLI1100', 'Section101', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('POLI1750', 'Section101', 'Fall', 2023);
-- insert into section (course_id, section_id, semester, year) value ('POLI1750', 'Section102', 'Fall', 2023);
-- insert into section (course_id, section_id, semester, year) value ('POLI2001', 'Section101', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('POLI2001', 'Section102', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('POLI2220', 'Section101', 'Fall', 2023);
-- insert into section (course_id, section_id, semester, year) value ('POLI2220', 'Section101', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('POLI2220', 'Section101', 'Fall', 2024);
-- -- ACCT
-- insert into section (course_id, section_id, semester, year) value ('ACCT2010', 'Section101', 'Fall', 2023);
-- insert into section (course_id, section_id, semester, year) value ('ACCT2010', 'Section102', 'Fall', 2023);
-- insert into section (course_id, section_id, semester, year) value ('ACCT2020', 'Section101', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('ACCT2020', 'Section102', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('ACCT3100', 'Section101', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('ACCT3100', 'Section102', 'Spring', 2024);
-- insert into section (course_id, section_id, semester, year) value ('ACCT3200', 'Section101', 'Fall', 2024);
-- insert into section (course_id, section_id, semester, year) value ('ACCT3200', 'Section101', 'Fall', 2024);


-- Advise
insert into advise (instructor_id, student_id, start_date, end_date) value ('1', '120', '2024-3-19', '2024-05-01');
insert into advise (instructor_id, student_id, start_date, end_date) value ('2', '120', '2024-3-19', '2024-05-01');
insert into advise (instructor_id, student_id, start_date, end_date) value ('3', '121', '2024-3-19', '2024-05-01');
insert into advise (instructor_id, student_id, start_date, end_date) value ('4', '122', '2024-3-19', '2024-05-01');
insert into advise (instructor_id, student_id, start_date, end_date) value ('5', '123', '2024-3-19', '2024-05-01');



insert into semester(semester, year, status) value ('Fall', 2023, 'Past');
insert into semester(semester, year, status) value ('Spring', 2024, 'Current');
insert into semester(semester, year, status) value ('Fall', 2024, 'Future');
insert into semester(semester, year, status) value ('Spring', 2025, 'Future');
insert into semester(semester, year, status) value ('Fall', 2025, 'Future');

insert into prereq(course_id, prereq_id) value ('COMP1020', 'COMP1010');
insert into prereq(course_id, prereq_id) value ('COMP2010', 'COMP1020');
insert into prereq(course_id, prereq_id) value ('COMP2040', 'COMP2010');

-- insert into prereq(course_id, prereq_id) value ('CHEM1220', 'CHEM1210');

-- insert into prereq(course_id, prereq_id) value ('MATH1320', 'MATH1310');
-- insert into prereq(course_id, prereq_id) value ('MATH2310', 'MATH1320');
-- insert into prereq(course_id, prereq_id) value ('MATH2210', 'MATH1310');

-- insert into prereq(course_id, prereq_id) value ('POLI2001', 'POLI1750');

-- insert into prereq(course_id, prereq_id) value ('ACCT2020', 'ACCT2010');
-- insert into prereq(course_id, prereq_id) value ('ACCT3100', 'ACCT2010');
-- insert into prereq(course_id, prereq_id) value ('ACCT3200', 'ACCT3100');

-- Showing GPA
insert into take(student_id, course_id, section_id, semester, year, grade) values (100, 'COMP1010', 'Section101', 'Fall', 2023, 'A');
insert into take(student_id, course_id, section_id, semester, year, grade) values (101, 'COMP1010', 'Section101', 'Fall', 2023, 'B+');
insert into take(student_id, course_id, section_id, semester, year, grade) values (102, 'COMP1010', 'Section101', 'Fall', 2023, 'B');
insert into take(student_id, course_id, section_id, semester, year, grade) values (103, 'COMP1010', 'Section101', 'Fall', 2023, 'A-');
insert into take(student_id, course_id, section_id, semester, year, grade) values (104, 'COMP1010', 'Section101', 'Fall', 2023, 'A');
insert into take(student_id, course_id, section_id, semester, year, grade) values (105, 'COMP1010', 'Section101', 'Fall', 2023, 'B+');
insert into take(student_id, course_id, section_id, semester, year, grade) values (106, 'COMP1010', 'Section101', 'Fall', 2023, 'B');
insert into take(student_id, course_id, section_id, semester, year, grade) values (107, 'COMP1010', 'Section101', 'Fall', 2023, 'A-');
insert into take(student_id, course_id, section_id, semester, year, grade) values (108, 'COMP1010', 'Section101', 'Fall', 2023, 'A');
insert into take(student_id, course_id, section_id, semester, year, grade) values (109, 'COMP1010', 'Section101', 'Fall', 2023, 'B+');

insert into take(student_id, course_id, section_id, semester, year, grade) values (110, 'COMP1010', 'Section102', 'Fall', 2023, 'A');


-- Satisfies 6 TA
insert into take(student_id, course_id, section_id, semester, year) values (100, 'COMP1020', 'Section101', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (101, 'COMP1020', 'Section101', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (102, 'COMP1020', 'Section101', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (103, 'COMP1020', 'Section101', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (104, 'COMP1020', 'Section101', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (105, 'COMP1020', 'Section101', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (106, 'COMP1020', 'Section101', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (107, 'COMP1020', 'Section101', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (108, 'COMP1020', 'Section101', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (109, 'COMP1020', 'Section101', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (110, 'COMP1020', 'Section101', 'Spring', 2024);



-- Satisfies 7 Grader
insert into take(student_id, course_id, section_id, semester, year) values (111, 'COMP1010', 'Section101', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (112, 'COMP1010', 'Section101', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (113, 'COMP1010', 'Section101', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (114, 'COMP1010', 'Section101', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (115, 'COMP1010', 'Section101', 'Spring', 2024);

-- Satisfies 7 Grader can't be for two classes
insert into take(student_id, course_id, section_id, semester, year) values (116, 'COMP1010', 'Section102', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (117, 'COMP1010', 'Section102', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (118, 'COMP1010', 'Section102', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (119, 'COMP1010', 'Section102', 'Spring', 2024);
insert into take(student_id, course_id, section_id, semester, year) values (120, 'COMP1010', 'Section102', 'Spring', 2024);


-- Satisfies 8 PhD Students
insert into take(student_id, course_id, section_id, semester, year, grade) values (121, 'COMP1010', 'Section101', 'Fall', 2023, 'A');
insert into take(student_id, course_id, section_id, semester, year, grade) values (122, 'COMP1010', 'Section101', 'Fall', 2023, 'A');
insert into take(student_id, course_id, section_id, semester, year, grade) values (123, 'COMP1010', 'Section101', 'Fall', 2023, 'A');



-- Satisfies 6
-- insert into take(student_id, course_id, section_id, semester, year, grade) values (100, 'COMP1010', 'Section101', 'Fall', 2023, 'A');
-- insert into take(student_id, course_id, section_id, semester, year, grade) values (101, 'COMP1010', 'Section101', 'Fall', 2023, 'A-');
-- insert into take(student_id, course_id, section_id, semester, year, grade) values (102, 'COMP1010', 'Section101', 'Fall', 2023, 'B+');
-- insert into take(student_id, course_id, section_id, semester, year, grade) values (103, 'COMP1010', 'Section101', 'Fall', 2023, 'B');
-- insert into take(student_id, course_id, section_id, semester, year, grade) values (104, 'COMP1010', 'Section101', 'Fall', 2023, 'C');

-- insert into take(student_id, course_id, section_id, semester, year, grade) values (110, 'COMP1010', 'Section101', 'Fall', 2023, 'A');
-- insert into take(student_id, course_id, section_id, semester, year, grade) values (111, 'COMP1010', 'Section101', 'Fall', 2023, 'A-');
-- insert into take(student_id, course_id, section_id, semester, year, grade) values (112, 'COMP1010', 'Section101', 'Fall', 2023, 'B+');

-- -- Satisfies 7
-- insert into take(student_id, course_id, section_id, semester, year) values (100, 'POLI1100', 'Section101', 'Spring', 2024);
-- insert into take(student_id, course_id, section_id, semester, year) values (101, 'POLI1100', 'Section101', 'Spring', 2024);
-- insert into take(student_id, course_id, section_id, semester, year) values (102, 'POLI1100', 'Section101', 'Spring', 2024);
-- insert into take(student_id, course_id, section_id, semester, year) values (103, 'POLI1100', 'Section101', 'Spring', 2024);
-- insert into take(student_id, course_id, section_id, semester, year) values (104, 'POLI1100', 'Section101', 'Spring', 2024);
-- insert into take(student_id, course_id, section_id, semester, year) values (105, 'POLI1100', 'Section101', 'Spring', 2024);
-- insert into take(student_id, course_id, section_id, semester, year) values (106, 'POLI1100', 'Section101', 'Spring', 2024);
-- insert into take(student_id, course_id, section_id, semester, year) values (107, 'POLI1100', 'Section101', 'Spring', 2024);
-- insert into take(student_id, course_id, section_id, semester, year) values (108, 'POLI1100', 'Section101', 'Spring', 2024);
-- insert into take(student_id, course_id, section_id, semester, year) values (109, 'POLI1100', 'Section101', 'Spring', 2024);

-- insert into take(student_id, course_id, section_id, semester, year, grade) values (110, 'POLI1100', 'Section101', 'Fall', 2023, 'A');