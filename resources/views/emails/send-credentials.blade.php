<x-mail::message>
# Welcome to {{ config('app.name') }}

Dear {{ $user->name ?? $user->first_name . ' ' . $user->last_name }},

Your account has been created successfully. Below are your login credentials:

## Login Credentials

**Email:** {{ $user->email }}

**Password:** {{ $password }}

## User Type: {{ ucfirst($userType) }}

Please keep these credentials secure and do not share them with anyone.

<x-mail::button :url="$loginUrl" color="primary">
Login to Your Account
</x-mail::button>

## Important Security Notes

- Change your password after first login
- Do not share your credentials with anyone
- Contact administrator if you suspect unauthorized access

If you have any questions, please contact your system administrator.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
