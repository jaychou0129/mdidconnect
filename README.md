# MDID ConNect System
A classroom reservation system constructed in 2019 by Tze-Chieh (Jay) Chou from MDID Class of 2021, with the main objective of digitalizing and improving the experience of the classroom reservation process within the school.

The system is currently in maintenance and will be available by June, 2021. To test the system, please refer to [the following link](https://mdidconnect.herokuapp.com).

Use the following credentials to login:
```
Test User:
  Username: test
  Password: test
Test Admin:
  Username: admin
  Password: admin
```

## Overview & Sitemap
![Image](https://i.imgur.com/mvgeORL.png)

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

## Pages and features (non-display pages are listed in *italics*)
### Homepage (index.php)
![Image](https://i.imgur.com/204DoFP.png)
![Image](https://i.imgur.com/WmSjToZ.png)
  * Shows top 3 reserved classrooms, basic information about the website and a "contact us" section for feedback and comments.
  * Classroom availability can be viewed by clicking on the picture of the classroom.
  * Feedback and comments are sent via email to the website creator. (Currently only works locally, needs to be fixed for the server.)

### Available Rooms (rooms.php)
![Image](https://i.imgur.com/Rw4NVx8.png)
Availability is shown in a modal box as pop-up.
![Image](https://i.imgur.com/5T8CWix.png)

### Login (login.php)
![Image](https://i.imgur.com/FoHIMn7.png)

### Reserve Classroom (reserve.php) [Requires Login]
![Image](https://i.imgur.com/uzv4imm.png)

### Reservation History (history.php) [Requires Login]
![Image](https://i.imgur.com/Dl03w5y.png)

### View Requests (approve.php) [Admin Only]
![Image](https://i.imgur.com/GfELpRw.png)

### Edit Classroom Info (editInfo.php) [Admin Only]
![Image](https://i.imgur.com/ulYwdU5.png)

### Edit Account Info (editAccount.php) [Admin Only]
![Image](https://i.imgur.com/5vLiZiT.png)

---
### *Logout* (logout.php)
### *Backend functions & actions* (servers.php) 
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
