# DB2-Project: Student Information System
Alex Kashian, Daniel Olen, Nathan Wright

## Building
### Prerequisites
* xampp software is installed and started

### Installing
* Create a database from the phpmyadmin interface named `DB2`
* Use the SQL Button on phpmyadmin and paste the contents of `sql/DB2-tables.sql` in and click go.
* Copy `db2-project` folder into htdocs folder of your xampp installation
  * If on linux with xampp installed in the default location you can run `sudo make copy`

### Running
* Have xampp started `/opt/lampp/lampp start`
* Navigate to http://localhost/db2-project/

### Running Android
* Open `db2projectandroid` in Android Studio
* Modify `app/src/main/res/values/strings.xml` url to point to public ip address
* Launch Application using AVD

# Phase 2
## Info
### Student types:
| Type      | Min Credits |
| --------- | ----------- |
| Freshman  | 0           |
| Sophomore | 30          |
| Junior    | 60          |
| Senior    | 90          |

### Tasks:

1. [X] A student can create an account and modify their information later. (The accounts for admin and instructors are created in advance.) (10 points) 
2. [X] The admin will be able to create a new course section and appoint instructor to teach the section. Every course section is scheduled to meet at a specific time slot, with a limit of two sections per time slot. Each instructor teaches one or two sections per semester. Should an instructor be assigned two sections, the two sections must be scheduled in consecutive time slots. (10 points) 
3. [X] A student can browse all the courses offered in the current semester and can register for a specific section of a course if they satisfy the prerequisite conditions and there is available space in the section. (Assume each section is limited to 15 students). (10 points) 
4. [X] A student can view a list of all courses they have taken and are currently taking, along with the total number of credits earned and their cumulative GPA. (10 points) 
5. [X] Instructors have access to records of all course sections they have taught, including names of current semester's enrolled students and the names and grades of students from past semesters. (10 points) 
6. [X] Teaching Assistants (TAs), who are PhD students, will be assigned by the admin to sections with more than 10 students. A PhD student is eligible to be a TA for only one section. (10 points) 
7. [X] Grader positions for sections with 5 to 10 students will be assigned by the admin with either MS students or undergraduate students who have got A- or A in the course. If there are more than one qualified candidates, the admin will choose one as the grader. A student may serve as a grader for only one section. (10 points) 
8. [X] The admin or instructor can appoint one or two instructors as advisor(s) for PhD students, including a start date, and optional end date. The advisor will be able to view the course history of their advisees, and update their advisees’ information. (10 points)
9. [X] Assign and edit classrooms, make sure classes aren't overbooked.
10.  [X] Undergraduate students can be assigned advisors. The advisor will be able to view the course history of their advisees, and add a hold to their account preventing them from registering in classes


### Tables with Inserts:
1. [X] account
2. [X] department
3. [X] instructor
4. [X] student
5. [X] PhD
6. [X] master
7. [X] undergraduate
8. [X] classroom
9. [X] time_slot
10. [X] course
11. [X] section
12. [X] prereq
13. [ ] advise
14. [ ] TA
15. [ ] masterGrader
16. [ ] undergraduateGrader
17. [ ] take
20. [X] semester

# Phase 1
### Tasks:
1. [X] A student can create an account.
2. [X] A student can browse all the courses offered in the current semester and can register for a specific section of a course if there is available space in the section. (Assume each section is limited to 15 students).
3. [X] A student can view a list of all courses they have taken and are currently taking.
4. [X] Instructors have access to records of all course sections they have taught, including names of current semester's enrolled students and the names and grades of students from past semesters.
5. [X] Undergraduate students can be assigned advisors. The advisor will be able to view the course history of their advisees, and add a hold to their account preventing them from registering in classes
 