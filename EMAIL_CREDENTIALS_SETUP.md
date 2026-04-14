# Email Credentials Setup Guide

## Overview
The system now supports sending login credentials via email to students, teachers, and staff members.

## What Was Implemented

### 1. **SMTP Configuration** (`.env`)
- Changed from `MAIL_MAILER=log` to `MAIL_MAILER=smtp`
- Configured for Outlook/Hotmail SMTP
- Settings:
  ```
  MAIL_MAILER=smtp
  MAIL_HOST=smtp-mail.outlook.com
  MAIL_PORT=587
  MAIL_ENCRYPTION=tls
  ```

### 2. **Email Class** (`app/Mail/SendCredentials.php`)
- Created mailable class for sending credentials
- Includes user information, password, user type, and login URL
- Uses Markdown template for professional email formatting

### 3. **Email Template** (`resources/views/emails/send-credentials.blade.php`)
- Professional email template with:
  - Welcome message
  - Login credentials (email & password)
  - User type designation
  - Login button
  - Security notes and best practices

### 4. **Controller Method** (`app/Http/Controllers/Web/AdminController.php`)
- Added `sendCredentials()` method
- Validates admin authentication
- Determines user type (student/teacher/staff)
- Sends email via configured SMTP
- Returns success/error messages

### 5. **Route** (`routes/web.php`)
- Added route: `POST /admin/credentials/send-credentials/{userId}`
- Protected by admin role middleware

### 6. **UI Updates** (`resources/views/admin/credentials.blade.php`)
- Added "Send Credentials" button (paper plane icon) to all user types
- JavaScript function to handle sending
- Works for Students, Staff, and Teachers tabs

## Required Configuration

### Step 1: Update `.env` with Your Outlook Credentials

Open your `.env` file and update these lines:

```env
MAIL_USERNAME=your-outlook-email@example.com
MAIL_PASSWORD=your-outlook-app-password
MAIL_FROM_ADDRESS=your-outlook-email@example.com
```

**Important:** 
- Use your full Outlook email address for `MAIL_USERNAME`
- Use an **App Password** (not your regular password)
- You can generate an app password from your Microsoft account security settings
- `MAIL_FROM_ADDRESS` should match your `MAIL_USERNAME`

### Step 2: Generate Outlook App Password

1. Go to your Microsoft account security settings
2. Enable two-factor authentication if not already enabled
3. Go to "App passwords" section
4. Click "Create a new app password"
5. Copy the generated password
6. Use this in your `.env` file as `MAIL_PASSWORD`

### Step 3: Clear Configuration Cache

After updating `.env`, run:
```bash
php artisan config:clear
php artisan cache:clear
```

Or restart your Laravel server if using `php artisan serve`.

## How to Use

1. **Login as Admin** to the ERP system
2. **Navigate to** User Credentials page
3. **Select a user** from Students, Staff, or Teachers tab
4. **Click the paper plane icon** (📧 Send Credentials)
5. **Confirm** the email address in the popup
6. **Success!** Email will be sent with login credentials

## Email Content

The email includes:
- Welcome message with user's name
- Login email address
- Temporary password
- User type (Student/Teacher/Staff)
- Direct login button link
- Security recommendations

## Testing

To test the email functionality:

1. Make sure you've completed Step 1-3 above
2. Go to Admin > Credentials
3. Find a user with a generated password
4. Click the send credentials button
5. Check the recipient email inbox (and spam folder)

## Troubleshooting

### Email Not Sending?
- Check `.env` has correct Outlook credentials
- Verify app password is valid
- Ensure `MAIL_FROM_ADDRESS` matches `MAIL_USERNAME`
- Check Laravel logs: `storage/logs/laravel.log`

### Common Errors

**"Failed to send credentials"**
- Check your Outlook app password
- Verify SMTP settings are correct
- Check firewall/antivirus isn't blocking port 587

**"No temporary password found"**
- Reset the user's password first
- This generates a temp password that can be emailed

### Testing Without Real Email

You can temporarily switch to mail logging for testing:
```env
MAIL_MAILER=log
```
Emails will be saved to `storage/logs/laravel.log` instead of being sent.

## Security Notes

- Only admins can send credentials
- Passwords are sent in plain text (temporary passwords)
- Users should change password on first login
- Email is encrypted in transit via TLS
- Consider implementing password reset tokens for enhanced security

## Future Enhancements

Potential improvements:
- Batch send credentials to multiple users
- Email templates customization via admin panel
- Email delivery tracking
- Automatic password expiry notifications
- HTML email preview before sending
