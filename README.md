# MDID ConNect System
A classroom reservation system constructed in 2019 by Tze-Chieh (Jay) Chou from MDID Class of 2021, with the main objective of digitalizing and improving the experience of the classroom reservation process within the school.

To test the system, please refer to [the following link](https://mdidconnect.herokuapp.com).

Use the following credentials to login:
```
Test User:
  Username: test
  Password: test
Test Admin:
  Username: admin
  Password: admin
```

Click [here](https://youtu.be/Vyr0e-Eu3rg) for a video demo.

## Overview & Sitemap
![Image](https://i.imgur.com/99sKynj.png)

### Database Design

#### Datatable "classrooms"
| Field | Type |
|---|---|
|id|INT|
|name|VARCHAR|
|description|VARCHAR|
|timesReserved (dynamically updated)|INT|

#### Datatable "reservations"
| Field | Type |
|---|---|
|r_id|INT|
|sentBy (stores user ID)|VARCHAR|
|room|VARCHAR|
|date|VARCHAR|
|periods|VARCHAR|
|purpose|TEXT|
|personnel|TEXT|
|status (1 for approved, 0 for pending, -1 for disapproved)|INT|
|message|TEXT|

#### Datatable "users"
| Field | Type |
|---|---|
|class|VARCHAR|
|name|VARCHAR|
|ID|VARCHAR|
|pwd|VARCHAR|

## Technical Information
**Main languages used:**
* Frontend: HTML, CSS, Javascript
* Backend: PHP, MySQL

**Frameworks & Libraries Used:**
jQuery, Bootstrap 3 (with parts of Bootstrap 4 integrated)

**Current Server:** Heroku (a free website-hosting service)

## Pages and features (non-display pages are listed in *italics*)
  * All pages are designed to be responsive to changes in viewport widths, so the website can display properly on all devices.

### Homepage (index.php)
  * Shows top 3 reserved classrooms, basic information about the website and a "contact us" section for feedback and comments.
  * Classroom availability can be viewed by clicking on the picture of the classroom, which displays the availability in a modal box as a pop-up.
  * Feedback and comments are sent via email to the website creator. (Currently only works locally, needs to be fixed for the server.)
  
![Image](https://i.imgur.com/204DoFP.png)
![Image](https://i.imgur.com/WmSjToZ.png)

### Available Rooms (rooms.php)
  * Filters (searching of a specific term / location) can be applied to help with finding the classroom.
  * Classroom availability can be viewed by clicking on the picture of the classroom, which displays the availability in a modal box as a pop-up.
  * Clicking on "reserve" directly links to the Reserve Classroom page if user is logged in, with the classroom filled in, or otherwise links to the login page.
  
![Image](https://i.imgur.com/Rw4NVx8.png)
Availability is shown in a modal box as pop-up.
![Image](https://i.imgur.com/5T8CWix.png)

### Login (login.php)
  * Shows red error message if username or password is incorrect.
![Image](https://i.imgur.com/FoHIMn7.png)

### Reserve Classroom (reserve.php) [Requires Login]
  * Shows red error message if any required field is empty.
  * Date can be filled using the DateTimePicker plugin.
![Image](https://i.imgur.com/uzv4imm.png)

### Reservation History (history.php) [Requires Login]
  * Reservation record can be filtered by the date (future/past/all), status (pending/approved/disapproved), or by typing in specific phrases.
  * Table can be sorted by clicking on the column headers, using the Table Sorter plugin.
  * Clicking on the record brings up a modal box to show more details (for disapproved requests, this shows the message left by the admin).
![Image](https://i.imgur.com/Dl03w5y.png)

### View Requests (approve.php) [Admin Only]
  * Reservation record can be filtered by the date (future/past/all), status (pending/approved/disapproved), or by typing in specific phrases.
  * Table can be sorted by clicking on the column headers, using the Table Sorter plugin.
  * Clicking on the record brings up a modal box to show more details.
  * Clicking on the classroom brings up a modal box to show the availability of that classroom for reference.
  * Disapproving a classroom brings up a prompt for a reason/message to the user.
![Image](https://i.imgur.com/GfELpRw.png)

### Edit Classroom Info (editInfo.php) [Admin Only]
  * Allows admin to edit existing classroom informations (name/location/image/weekly schedule), add new classrooms or delete classrooms.
  * Files can be selected from computer and uploaded to the server to replace existing images.
![Image](https://i.imgur.com/ulYwdU5.png)

### Edit Account Info (editAccount.php) [Admin Only]
  * Student account info (name/class/password) can be modified by clicking on the row.
  * Passwords are hidden unless they are selected in editing mode.
  * Users can be filtered by account type (Teacher/Admin/Student) or by name.
![Image](https://i.imgur.com/5vLiZiT.png)

---
### *Logout* (logout.php)
  *Triggered when a user clicks on the "logout" button, clearing session and returning to the homepage.*
### *Backend functions & actions* (servers.php)
  *Contains backend database operations for inserting, updating and deleting requests/users/classrooms as well as code for automatic emails.*
### *Database Configurations* (db.php)

## Plugins
### Directory for Display & Schedule Images (img)
### Bootstrap (Bootstrap & MDB-Free_4)
### Datetime Picker (bower_components)
### Table Sorter (tablesorter-master)
### Automatic Emailer (PHPMailer-master)
  *Works locally but currently does not function in the server*

## Other Files
### Custom Scripts (scripts.js)
### Custom Stylesheet (styles.css)
### Login with Google (login2.php)
  *Currently under construction*
### Test for Google Login (testGoogle.php)
  *For testing purposes only*

## Future Improvements
While the system is currently in maintenance, several functions could be added before it becomes available and ready for practical use.
1. **Login method:** In order to alleviate the burden of having to maintain account information every year, Google APIs will be incorporated to allow users to login with school Emails, which automatically contains necessary information such as email and name.
2. **Automated Email notifications:** While the Email function works locally, it is currently down on the server. Email notifications will be sent to users when their requests have been approved/disapproved and to admins when a new request has been made.
3. **Implementation of Google Calendar API:** The Google Calendar API allows events to be added to the Google Calendar, and by integrating it with the MDID Connect System, admin can create events once reservation requests are approved, automatically inviting the user and involved personnel to the event, etc. The availability pop-up of each classroom can also be replaced with the Google Calendar Plugin so that scheduled events and classroom reservations can be displayed together.
4. **Export reservation data to CSV:** This could be useful to the admin if they wish to keep a record of reservation data every year and clear the data, etc.
