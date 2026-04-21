# 📋 Attendance Marking Guide - School ERP

## ⚠️ Important: Teacher Assignment

Each timetable is assigned to a specific teacher. **You can only mark attendance for classes assigned to you.**

---

## 🔐 Login Credentials for Testing

### John Teacher (ID: 3)
- **Email:** teacher@schoolerp.com
- **Password:** password
- **Assigned Classes:** FY-B Monday classes (including timetable ID 131)

### Nikita Shinde (ID: 15)
- **Email:** nikitashinde01598@gmail.com
- **Password:** password
- **Assigned Classes:** Various divisions

### Other Teachers
- **Email:** amanda.wilson@schoolerp.com
- **Password:** password

- **Email:** david.lee@schoolerp.com
- **Password:** password

---

## 📝 How to Mark Attendance

### Step 1: Login as the Correct Teacher
1. Go to: http://127.0.0.1:8000/login
2. Login with teacher credentials (e.g., `teacher@schoolerp.com` / `password`)

### Step 2: Navigate to Attendance
1. Go to: http://127.0.0.1:8000/teacher/attendance
2. You will see today's scheduled classes

### Step 3: Select a Class
1. Find the class you want to mark attendance for
2. Click **"Mark Attendance"** button
3. **Note:** If you don't see the "Mark Attendance" button, that class is not assigned to you

### Step 4: Mark Attendance
1. For each student, select:
   - ✅ **Present** (green button)
   - ❌ **Absent** (red button)
   - ⚠️ **Late** (yellow button)
2. Add optional remarks if needed
3. Click **"Submit Attendance"** at the bottom

### Step 5: Confirmation
- You should see a success message: "Attendance marked successfully for [Division Name]"
- The attendance is now saved in the database

---

## ❌ Common Errors and Solutions

### Error: "You are not authorized to mark attendance for this class"
**Cause:** You're trying to mark attendance for a class assigned to another teacher.

**Solution:** 
- Login as the teacher assigned to that timetable
- OR contact admin to reassign the timetable to you

### Error: "Failed to mark attendance: [error message]"
**Possible Causes:**
- Invalid student ID
- Invalid status value
- Database connection issue

**Solution:**
- Refresh the page and try again
- Check that all students have a status selected
- Contact admin if the problem persists

### Error: Page redirects without saving
**Possible Causes:**
- Session expired
- CSRF token mismatch
- Date is in the past or future

**Solution:**
- Login again
- Make sure you're marking attendance for today's date
- Clear browser cache and try again

---

## 🔍 Check Attendance Status

### For Teachers:
1. Go to: http://127.0.0.1:8000/teacher/attendance
2. Classes with marked attendance show: "✓ Marked" badge
3. Classes without attendance show: "◌ Pending" badge

### View History:
1. Click **"History"** button
2. Select division and date range
3. View all attendance records

---

## 📊 Database Verification

### Check if attendance was saved:
```sql
-- Check attendance for specific timetable
SELECT * FROM attendance WHERE timetable_id = 131;

-- Count attendance records
SELECT COUNT(*) FROM attendance WHERE timetable_id = 131 AND date = CURDATE();

-- View attendance with student names
SELECT a.id, s.first_name, s.last_name, a.status, a.date 
FROM attendance a 
JOIN students s ON a.student_id = s.id 
WHERE a.timetable_id = 131;
```

---

## 🛠️ Admin: Reassign Timetables

If a teacher cannot mark attendance for their class:

### Option 1: Update via Database
```sql
-- Assign timetable to teacher (replace TEACHER_ID with actual ID)
UPDATE timetables SET teacher_id = TEACHER_ID WHERE id = TIMETABLE_ID;
```

### Option 2: Use Admin Panel
1. Login as admin
2. Go to Timetable Management
3. Edit the timetable
4. Change the assigned teacher
5. Save changes

---

## 📞 Support

If you're still having issues:
1. Check the error message displayed
2. Verify you're logged in as the correct teacher
3. Check browser console for JavaScript errors
4. Contact system admin

---

**Last Updated:** 2026-03-23  
**System:** School ERP  
**Module:** Teacher Attendance
