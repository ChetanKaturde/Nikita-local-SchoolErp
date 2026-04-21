# 🎓 SCHOOL ERP - FEATURE DECISION DOCUMENT

**Purpose:** Decide ALL features before MVP definition  
**Date:** March 31, 2026  
**Status:** **DISCUSSION REQUIRED**  

---

## 📋 HOW TO USE THIS DOCUMENT

This is a **decision-making tool**. For each feature:
1. ✅ **Keep** - Feature is needed, keep as-is
2. ⚠️ **Modify** - Feature is needed but needs changes
3. ❌ **Remove** - Feature is not needed, delete it
4. ➕ **Add** - Missing feature that should be added

**Decision Criteria:**
- Is this essential for a School ERP?
- Do schools actually use this?
- Is this over-engineered?
- Can this be simplified?

---

## 🏫 MODULE 1: USER ROLES & ACCESS

### Current Roles Implemented:
```
1. Super Admin (system owner)
2. Admin (college administrator)
3. Principal (head of institution)
4. Teacher (faculty member)
5. Student (enrolled student)
6. Accountant (finance staff)
7. Office Staff (administrative staff)
8. Librarian (library staff)
9. HOD Commerce
10. HOD Science
11. HOD Management
12. HOD Arts
```

### Discussion Points:

**Q1: Do we need ALL 12 roles?**

**My Recommendation:**
```
✅ Keep (7 roles):
1. Super Admin - System owner/IT company
2. Admin - College principal/manager
3. Principal - Academic head
4. Teacher - Faculty
5. Student - Enrolled student
6. Accountant - Finance
7. Librarian - Library

❌ Remove/Merge (5 roles):
8. Office Staff → Merge with Admin
9. HOD Commerce → Merge with Teacher (add HOD checkbox)
10. HOD Science → Merge with Teacher (add HOD checkbox)
11. HOD Management → Merge with Teacher (add HOD checkbox)
12. HOD Arts → Merge with Teacher (add HOD checkbox)
```

**Reasoning:**
- HODs are just teachers with additional permissions
- Office staff does admin tasks
- Simpler role structure = easier maintenance

**Q2: Should we add Parent role?**

**Options:**
- ❌ No - Parents don't need system access
- ✅ Yes - View child's attendance, fees, results
- ⚠️ Maybe later - Phase 2 feature

---

## 🎓 MODULE 2: STUDENT MANAGEMENT

### Current Features (from StudentController):

**Student CRUD:**
```
✅ Create Student (with photo, documents)
✅ Read Student List (with filters, search, sort)
✅ Read Student Detail (with fee history)
✅ Update Student
✅ Delete Student
```

**Student Fields Captured:**
```
Personal:
- First, Middle, Last Name
- Date of Birth
- Gender (Male/Female/Other)
- Blood Group
- Religion
- Category (General/OBC/SC/ST/VJNT/NT/EWS)
- Aadhar Number
- Photo

Contact:
- Mobile Number
- Email
- Current Address
- Permanent Address

Academic:
- Program (Standard)
- Division
- Academic Session
- Academic Year (FY/SY/TY)
- Admission Number
- Roll Number
- Student Status (Active/Inactive/Graduated/Transferred)

Documents:
- Photo
- Signature
- 12th Marksheet
- Caste Certificate
```

**Guardian Management:**
```
✅ Add Guardian (Father/Mother/Other)
✅ Guardian Details (Name, Mobile, Email, Occupation)
✅ Multiple Guardians per Student
```

### Discussion Points:

**Q3: Are all student fields necessary?**

**My Recommendation:**
```
✅ Essential (Keep):
- Name, DOB, Gender
- Mobile, Email
- Address
- Program, Division, Session
- Admission Number, Roll Number
- Photo
- Guardian (at least 1)

⚠️ Optional but Keep:
- Blood Group (useful for emergencies)
- Religion (for demographic reports)
- Category (for scholarship/quota)
- Aadhar (for Indian compliance)

❌ Consider Removing:
- Middle Name (many don't have)
- Permanent Address (same as current usually)
```

**Q4: Should student admission be multi-step wizard?**

**Current:** Single 408-line form  
**Alternative:** 4-step wizard
1. Personal Details
2. Academic Details
3. Guardian Details
4. Documents Upload

**My Recommendation:** ✅ **Yes, make it wizard** - Better UX

**Q5: Bulk student operations needed?**

**Options:**
- ✅ Bulk Upload (Excel import)
- ✅ Bulk Promote (move to next year)
- ✅ Bulk Transfer (change division)
- ✅ Bulk Delete (with confirmation)
- ❌ None - Manual only

---

## 👨‍🏫 MODULE 3: TEACHER MANAGEMENT

### Current Features (from TeacherController):

**Teacher CRUD:**
```
✅ Create Teacher (with auto password)
✅ Read Teacher List
✅ Read Teacher Detail
✅ Update Teacher
✅ Delete Teacher
```

**Teacher Fields:**
```
- Name
- Email
- Password (auto-generated or manual)
- Department
- Phone
- Photo
- Role (teacher + HOD options)
```

### Discussion Points:

**Q6: Should teachers have subject allocation?**

**Current:** Teachers have department  
**Missing:** Which subjects they can teach

**My Recommendation:** ✅ **Add subject allocation**
- Teacher can teach multiple subjects
- Helps in timetable creation
- Helps in marks entry

**Q7: Should teachers have qualification/experience fields?**

**Options:**
- ❌ No - HR system would handle this
- ✅ Yes - Basic fields (Qualification, Experience, Specialization)

**My Recommendation:** ✅ **Add basic fields** - Useful for reports

**Q8: Teacher dashboard features?**

**Current:**
- View assigned divisions
- View students
- Mark attendance
- Enter marks

**Missing:**
- Leave application
- View timetable
- Download reports

**My Recommendation:** ✅ **Add all missing features**

---

## 📚 MODULE 4: ACADEMIC STRUCTURE

### Current Structure:

```
Department (Commerce, Science, Arts)
    ↓
Program (B.Com, B.Sc, BA)
    ↓
Division (FY-A, SY-B, TY-C)
    ↓
Students
```

**Additional Structure:**
```
Academic Session (2024-25, 2025-26)
Academic Year (FY, SY, TY)
Subjects (per Program)
Time Slots (for timetable)
Rooms (for classes)
```

### Discussion Points:

**Q9: Is Department → Program → Division structure correct?**

**My Recommendation:** ✅ **Yes, but simplify**
- Department: Commerce, Science, Arts
- Program: B.Com, B.Sc, BA (3 years)
- Division: A, B, C (not FY-A, just A)
- Academic Year handles FY/SY/TY

**Q10: Should we have "Standard" instead of "Program"?**

**For School (1-12):** Use "Standard"  
**For College (FY/SY/TY):** Use "Program"

**My Recommendation:** ⚠️ **Support both**
- Field name: `program_standard`
- Type selector: School/College
- Shows "Standard 1-12" or "Program FY/SY/TY"

**Q11: Subject allocation to Program?**

**Current:** Subjects belong to Program  
**My Recommendation:** ✅ **Keep as-is**

**Q12: Should we have "Batch" system?**

**For:** Better grouping (Morning/Evening batches)  
**Against:** Adds complexity

**My Recommendation:** ❌ **No batch** - Division is enough

---

## 📅 MODULE 5: ATTENDANCE SYSTEM

### Current Features:

**Attendance Types:**
```
✅ Daily Attendance (Present/Absent/Late)
✅ Teacher-marked Attendance (via timetable)
✅ Admin-marked Attendance
✅ Attendance Reports
✅ Holiday Integration
```

**Attendance Fields:**
```
- Student
- Division
- Date
- Status (Present/Absent/Late)
- Marked By (Teacher)
- Remarks
- IP Address
- Check-in Time
- Check-out Time
```

### Discussion Points:

**Q13: Is daily attendance enough?**

**Options:**
- ✅ Daily only (one per day per student)
- ⚠️ Period-wise (multiple per day)
- ❌ Lecture-wise (per subject)

**My Recommendation:** ✅ **Daily only** - Simpler, most schools use this

**Q14: Should we have attendance percentage threshold?**

**Current:** 75% minimum (hardcoded)  
**My Recommendation:** ✅ **Make configurable** (Admin can set 60-90%)

**Q15: Bulk attendance import?**

**Options:**
- ✅ Excel import (for bulk upload)
- ❌ Manual only

**My Recommendation:** ❌ **Manual only** - Teachers mark daily

**Q16: Attendance regularization?**

**Scenario:** Student was absent but should be present (error correction)

**My Recommendation:** ✅ **Add edit capability** (with audit log)

---

## 🕐 MODULE 6: TIMETABLE SYSTEM

### Current Features:

**Timetable Capabilities:**
```
✅ Create Timetable (manual entry)
✅ Grid View (weekly view)
✅ Teacher Timetable
✅ Division Timetable
✅ Room Allocation
✅ Time Slot Management
✅ Holiday Checking
✅ Conflict Detection
✅ Import/Export (Excel/PDF)
✅ Copy to Next Session
```

**Timetable Fields:**
```
- Division
- Teacher
- Subject
- Day of Week
- Time Slot
- Room
- Type (Lecture/Practical/Break)
```

### Discussion Points:

**Q17: Is manual timetable creation enough?**

**Options:**
- ✅ Manual (current)
- ⚠️ Auto-generate (AI-based)
- ❌ Template-based

**My Recommendation:** ✅ **Manual only** - Auto-generation is complex

**Q18: Should we have "Free Period" tracking?**

**Current:** Break time exists  
**Missing:** Teacher's free periods

**My Recommendation:** ✅ **Add free period slot** - Useful for substitute arrangement

**Q19: Substitute teacher arrangement?**

**Scenario:** Regular teacher absent, assign substitute

**My Recommendation:** ⚠️ **Phase 2 feature** - Not critical for MVP

---

## 💰 MODULE 7: FEE MANAGEMENT

### Current Features:

**Fee Structure:**
```
✅ Fee Heads (Tuition, Exam, Library, Lab, Sports, etc.)
✅ Fee Structures (per Program/Division)
✅ Installment System (1-12 installments)
✅ Discount System
✅ Scholarship Integration
```

**Fee Collection:**
```
✅ Manual Payment Collection
✅ Razorpay Online Payment
✅ Payment Receipts (PDF)
✅ Outstanding Tracking
✅ Payment History
```

**Fee Reports:**
```
✅ Collection Report
✅ Outstanding Report
✅ Defaulter List
✅ Student-wise Ledger
```

### Discussion Points:

**Q20: Is installment system necessary?**

**Current:** 1-12 installments supported  
**My Recommendation:** ✅ **Keep** - Very useful for Indian schools

**Q21: Should we have late fee/penalty?**

**Options:**
- ✅ Auto-calculate late fee after due date
- ❌ Manual only

**My Recommendation:** ✅ **Add late fee** - Configurable % per day

**Q22: Fee concession/discount approval workflow?**

**Scenario:** Principal gives 50% discount, needs approval trail

**My Recommendation:** ✅ **Add approval workflow** - Principal approves, accountant applies

**Q23: Sibling discount?**

**Scenario:** 2nd child gets 10% off

**My Recommendation:** ⚠️ **Phase 2** - Not critical

**Q24: Transport fee integration?**

**Options:**
- ✅ Add Transport as fee head
- ❌ Separate transport system

**My Recommendation:** ✅ **Add as fee head** - Simpler

---

## 📖 MODULE 8: LIBRARY MANAGEMENT

### Current Features:

**Library Operations:**
```
✅ Book CRUD (ISBN, Title, Author, Publisher)
✅ Book Categorization (Subject, Category)
✅ Book Issuance (to students)
✅ Book Return
✅ Fine Calculation (₹5/day default)
✅ Available Copies Tracking
```

### Discussion Points:

**Q25: Is basic library management enough?**

**My Recommendation:** ✅ **Yes for MVP** - Don't need advanced features

**Q26: Should we have book reservation?**

**Scenario:** Student reserves book before it's returned

**My Recommendation:** ❌ **No** - Adds complexity

**Q27: Should we have e-books?**

**My Recommendation:** ❌ **No** - Phase 3 feature

**Q28: Fine waiver capability?**

**Scenario:** Librarian waives fine for genuine reason

**My Recommendation:** ✅ **Add waiver** (with reason log)

---

## 📝 MODULE 9: EXAMINATION & RESULTS

### Current Features:

**Examination Management:**
```
✅ Exam Creation (Mid-sem, End-sem, Unit Test)
✅ Subject-wise Exams
✅ Marks Entry (by teacher)
✅ Marks Approval (by HOD/Principal)
✅ Grade Calculation
✅ Result Generation
```

**Marks System:**
```
✅ Theory Marks
✅ Practical Marks
✅ Internal Assessment
✅ External Exam
✅ Total Marks
✅ Percentage
✅ Grade (A+, A, B+, B, C, D, F)
✅ Pass/Fail
```

### Discussion Points:

**Q29: Is current grading system sufficient?**

**Current:** A+ (80-100), A (70-80), etc.  
**My Recommendation:** ✅ **Make configurable** - Different boards have different systems

**Q30: Should we have CGPA system?**

**Options:**
- ✅ Yes (for college)
- ❌ No (only percentage for school)

**My Recommendation:** ⚠️ **Both** - Configurable (Percentage/CGPA)

**Q31: ATKT (Allowed To Keep Terms) system?**

**Scenario:** Student can move to next year with 2-3 backlogs

**My Recommendation:** ✅ **Add ATKT rules** - Common in Indian colleges

**Q32: Backlog tracking?**

**Scenario:** Student has 3 backlogs, needs to retake exams

**My Recommendation:** ✅ **Track backlogs** - Essential for college

**Q33: Consolidated marksheet?**

**Current:** Subject-wise marks  
**Missing:** All semesters in one marksheet

**My Recommendation:** ✅ **Generate consolidated marksheet** - Critical for job applications

---

## 🎓 MODULE 10: PROMOTION & TRANSFER

### Current Features:

**Promotion System:**
```
✅ Promotion Logic (API only)
✅ Eligibility Checking (attendance, marks)
✅ Bulk Promotion
✅ Promotion History
✅ Rollback Capability
```

**Transfer System:**
```
✅ Transfer Logic (API only)
✅ TC Generation
✅ Transfer History
```

### Discussion Points:

**Q34: Should promotion be automatic or manual?**

**Options:**
- ✅ Automatic (based on rules)
- ⚠️ Manual (admin decides)
- ❌ Semi-automatic (suggest + approve)

**My Recommendation:** ⚠️ **Semi-automatic** - System suggests, admin approves

**Q35: What should be promotion criteria?**

**Options:**
- Attendance only (75%+)
- Marks only (pass all subjects)
- Both attendance + marks
- Manual (no criteria)

**My Recommendation:** ⚠️ **Configurable** - Admin sets rules

**Q36: Transfer Certificate workflow?**

**Steps:**
1. Parent applies for TC
2. School clears dues (library, fees)
3. Principal approves
4. TC generated

**My Recommendation:** ✅ **Implement full workflow** - Essential

---

## 🏥 MODULE 11: HEALTH & WELLNESS

### Current Features:
```
❌ None
```

### Discussion Points:

**Q37: Should we have health records?**

**Fields:**
- Blood group (already in student)
- Allergies
- Medical conditions
- Emergency contacts

**My Recommendation:** ⚠️ **Basic health info only** - Allergies, emergency contact

**Q38: Should we track sick leaves separately?**

**My Recommendation:** ❌ **No** - Regular leave system is enough

---

## 🚌 MODULE 12: TRANSPORT SYSTEM

### Current Features:
```
❌ None
```

### Discussion Points:

**Q39: Should we have transport management?**

**Features:**
- Bus routes
- Bus stops
- Route allocation to students
- Transport fee

**My Recommendation:** ❌ **No for MVP** - Can be fee head only

---

## 🏠 MODULE 13: HOSTEL MANAGEMENT

### Current Features:
```
❌ None
```

### Discussion Points:

**Q40: Should we have hostel management?**

**Features:**
- Room allocation
- Mess/washing
- Hostel fee
- Warden assignment

**My Recommendation:** ❌ **No** - Most day schools don't need this

---

## 📊 MODULE 14: REPORTS & ANALYTICS

### Current Features:

**Reports Available:**
```
✅ Attendance Report
✅ Fee Collection Report
✅ Outstanding Report
✅ Student List Report
✅ Marksheet
✅ Library Issue Report
```

### Discussion Points:

**Q41: What additional reports are needed?**

**Suggestions:**
- ✅ Student Strength Report (class-wise)
- ✅ Teacher Attendance Report
- ✅ Fee Defaulter List
- ✅ Result Analysis (pass %)
- ✅ Revenue Report
- ❌ Advanced Analytics (Phase 3)

**Q42: Export formats?**

**Options:**
- ✅ PDF
- ✅ Excel
- ❌ CSV (Excel is enough)

**My Recommendation:** ✅ **PDF + Excel only**

---

## 🔔 MODULE 15: NOTIFICATIONS

### Current Features:

**Notifications:**
```
✅ Student Notifications (in-app)
✅ Teacher Notifications (in-app)
❌ Email Notifications
❌ SMS Notifications
❌ Push Notifications
```

### Discussion Points:

**Q43: Should we have email notifications?**

**Scenarios:**
- Fee due reminder
- Attendance low alert
- Exam schedule
- Result declared

**My Recommendation:** ✅ **Add email** - Essential for communication

**Q44: Should we have SMS notifications?**

**My Recommendation:** ❌ **No** - Costly, email is enough

**Q45: WhatsApp integration?**

**My Recommendation:** ❌ **No** - Phase 3 feature

---

## ⚙️ MODULE 16: SETTINGS & CONFIGURATION

### Current Features:

**Settings:**
```
✅ Academic Rules (pass %, min attendance)
⚠️ System Settings (partial)
❌ SMTP Configuration (requires .env edit)
❌ Payment Gateway Config (requires .env edit)
❌ Branding Config (logo, colors)
```

### Discussion Points:

**Q46: What settings should be configurable via UI?**

**My Recommendation:**
```
✅ Configurable via UI:
- Pass percentage
- Minimum attendance
- Late fee %
- Fine per day
- School name
- School logo
- Contact info
- Email settings
- Razorpay settings

❌ Keep in .env only:
- Database credentials
- App key
- Debug mode
```

---

## 🎨 MODULE 17: UI/UX FEATURES

### Current Features:

**UI Features:**
```
✅ Responsive Design
✅ Bootstrap 5
✅ Sidebar Navigation
✅ Search & Filters
✅ Pagination
✅ Sort by Columns
❌ Dark Mode
❌ Excel Export Button
❌ Bulk Actions
❌ Multi-step Forms
```

### Discussion Points:

**Q47: What UI features are essential?**

**My Recommendation:**
```
✅ Essential:
- Search & Filters
- Pagination
- Sort by Columns
- Excel Export
- Bulk Actions (delete, promote)

❌ Not Essential:
- Dark Mode
- Keyboard Shortcuts
- Touch-friendly (desktop-first)
```

---

## 🔐 MODULE 18: SECURITY & PERMISSIONS

### Current Features:

**Security:**
```
✅ Password Hashing (bcrypt)
✅ CSRF Protection
✅ Role-based Access (Spatie)
✅ Permission System
✅ Session Management
✅ Rate Limiting (login only)
```

### Discussion Points:

**Q48: What additional security is needed?**

**My Recommendation:**
```
✅ Add:
- Rate limiting on all POST endpoints
- Password strength validation
- Session timeout (30 min)
- Audit log (who did what)
- IP whitelist (for admin)

❌ Not Needed:
- 2FA (overkill for school)
- Biometric login
- CAPTCHA (annoying)
```

---

## 📱 MODULE 19: MOBILE ACCESS

### Current Features:
```
✅ Responsive Web (mobile-friendly)
❌ Mobile App
❌ PWA (Progressive Web App)
```

### Discussion Points:

**Q49: Should we have mobile app?**

**My Recommendation:** ❌ **No** - Responsive web is enough for now

**Q50: Should we have PWA?**

**My Recommendation:** ❌ **No** - Phase 3 feature

---

## 🧪 MODULE 20: TESTING & QA

### Current Features:
```
✅ Unit Tests (16 files)
✅ Feature Tests (some)
❌ Browser Tests (Dusk)
❌ Performance Tests
❌ Security Audit
```

### Discussion Points:

**Q51: What testing is essential?**

**My Recommendation:**
```
✅ Essential:
- Unit tests for services
- Feature tests for critical workflows
- Manual testing checklist

❌ Not Essential:
- Browser tests (Dusk)
- Performance tests
- Load testing
```

---

## 📋 MVP DEFINITION WORKSHEET

### After discussing all features above, let's define MVP:

**MVP = Minimum Viable Product**

**Question:** What are the 10 features WITHOUT which the system cannot launch?

**My MVP Recommendation:**

1. **Student Management** (CRUD + Documents + Guardian)
2. **Teacher Management** (CRUD + Subject Allocation)
3. **Department/Program/Division** (Academic Structure)
4. **Attendance System** (Daily marking + Reports)
5. **Timetable** (Manual creation + View)
6. **Fee Management** (Structure + Collection + Receipts)
7. **Examination** (Create + Marks Entry)
8. **Result** (Marksheet Generation)
9. **Library** (Book Issue/Return)
10. **User Roles** (Admin/Teacher/Student/Accountant/Librarian)

**NOT in MVP (Phase 2):**
- Promotion/Transfer workflow
- ATKT system
- Email notifications
- Advanced reports
- Bulk operations
- Multi-step forms

**NOT in MVP (Phase 3):**
- Mobile app
- WhatsApp integration
- Transport management
- Hostel management
- Advanced analytics
- Dark mode

---

## 🎯 DECISION SUMMARY TABLE

| Module | MVP? | Priority | Effort | Decision |
|--------|------|----------|--------|----------|
| Student Management | ✅ | P0 | Medium | Keep |
| Teacher Management | ✅ | P0 | Medium | Keep |
| Academic Structure | ✅ | P0 | Low | Keep |
| Attendance | ✅ | P0 | Low | Keep |
| Timetable | ✅ | P0 | Medium | Keep |
| Fee Management | ✅ | P0 | High | Keep |
| Examination | ✅ | P0 | Medium | Keep |
| Result | ✅ | P0 | Medium | Keep |
| Library | ✅ | P0 | Low | Keep |
| User Roles | ✅ | P0 | Low | Keep |
| Promotion/Transfer | ❌ | P1 | High | Phase 2 |
| ATKT System | ❌ | P1 | High | Phase 2 |
| Email Notifications | ❌ | P1 | Medium | Phase 2 |
| Bulk Operations | ❌ | P2 | Low | Phase 2 |
| Advanced Reports | ❌ | P2 | Medium | Phase 2 |
| Mobile App | ❌ | P3 | High | Phase 3 |

---

## 📝 YOUR DECISIONS NEEDED

Please review and decide on:

### Role Structure:
1. Reduce from 12 to 7 roles? ✅/❌
2. Add Parent role? ✅/❌

### Student Management:
3. Remove middle name field? ✅/❌
4. Make admission form wizard? ✅/❌
5. Add bulk upload? ✅/❌

### Teacher Management:
6. Add subject allocation? ✅/❌
7. Add qualification fields? ✅/❌

### Academic Structure:
8. Support both School/College? ✅/❌

### Attendance:
9. Daily only (not period-wise)? ✅/❌
10. Configurable threshold? ✅/❌

### Timetable:
11. Manual only (no auto-gen)? ✅/❌
12. Add free period tracking? ✅/❌

### Fee Management:
13. Keep installment system? ✅/❌
14. Add late fee? ✅/❌
15. Add discount approval? ✅/❌

### Library:
16. Basic only (no reservation)? ✅/❌
17. Add fine waiver? ✅/❌

### Examination:
18. Configurable grading? ✅/❌
19. Add CGPA option? ✅/❌
20. Add ATKT rules? ✅/❌
21. Add backlog tracking? ✅/❌
22. Generate consolidated marksheet? ✅/❌

### Promotion/Transfer:
23. Semi-automatic promotion? ✅/❌
24. Configurable criteria? ✅/❌
25. Full TC workflow? ✅/❌

### Health:
26. Add basic health info? ✅/❌

### Transport:
27. Skip for MVP? ✅/❌

### Hostel:
28. Skip entirely? ✅/❌

### Reports:
29. PDF + Excel only? ✅/❌
30. What additional reports?

### Notifications:
31. Add email? ✅/❌
32. Skip SMS? ✅/❌

### Settings:
33. What to make configurable?

### UI/UX:
34. Skip dark mode? ✅/❌
35. Add Excel export? ✅/❌
36. Add bulk actions? ✅/❌

### Security:
37. What additional security?

### Mobile:
38. Skip mobile app? ✅/❌

### Testing:
39. What testing is essential?

---

## 🎯 NEXT STEPS

After you make decisions:

1. **Finalize MVP features** (10 must-haves)
2. **Define Phase 2** (nice-to-haves)
3. **Define Phase 3** (future enhancements)
4. **Create cleanup plan** (delete unused features)
5. **Start implementation**

---

**Tell me your decisions, then I'll create:**
1. MVP Definition Document
2. Phase 2 Plan
3. Phase 3 Plan
4. Cleanup Checklist
5. Implementation Roadmap
