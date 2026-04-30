# Role Management Guide

## Roles Overview

The application has the following predefined system roles:

### System Roles (Cannot be deleted)
1. **Admin** (`admin`)
   - Full system access
   - Manage users, roles, and features

2. **Siswa (Student)** (`siswa`)
   - Check-in/Check-out attendance
   - Fill daily logbook entries
   - View guidance notes

3. **Pembimbing PKL (Industry Supervisor)** (`pembimbing_pkl`)
   - Validate student attendance
   - Validate logbook entries
   - Provide guidance to students
   - View reports

4. **Pembimbing Perusahaan (Company Mentor)** (`pembimbing_perusahaan`) *NEW*
   - Validate student attendance
   - Validate logbook entries
   - Provide guidance to students
   - View reports

5. **Pembimbing Sekolah (School Supervisor)** (`pembimbing_sekolah`)
   - Provide guidance to students
   - Filter by class
   - View reports

6. **Kepala Jurusan (Head of Department)** (`kepala_jurusan`)
   - Weekly logbook review
   - Department filtering
   - View reports

7. **Wali Kelas (Homeroom Teacher)** (`wali_kelas`)
   - Filter by class
   - View reports

8. **Kepala Sekolah (School Principal)** (`kepala_sekolah`)
   - View all school data
   - View reports

9. **Kesiswaan (Student Affairs)** (`kesiswaan`)
   - View all student data
   - Class filtering
   - View reports

## Why Roles Cannot Be Deleted

There are two main reasons a role cannot be deleted:

### 1. System Roles Protection
System roles are protected because they are essential to the application. These cannot be deleted under any circumstances:
- Admin
- Siswa (Student)
- Pembimbing PKL (Industry Supervisor)
- Pembimbing Perusahaan (Company Mentor) **NEW**
- Pembimbing Sekolah (School Supervisor)
- Kepala Jurusan (Head of Department)
- Wali Kelas (Homeroom Teacher)
- Kepala Sekolah (School Principal)
- Kesiswaan (Student Affairs)

**Error message:** "Cannot delete system roles. This role is essential to the application."

### 2. Role Has Assigned Users
Even for custom (non-system) roles, you cannot delete a role if users are assigned to it. This prevents orphaning user accounts.

**Error message:** "Cannot delete role. X user(s) are assigned to this role."

To delete such a role, you must:
1. Reassign all users to a different role first
2. Then delete the empty role

## Changes Made

### New Role Added
- **Pembimbing Perusahaan (Company Mentor)** - Separated from Industry Supervisor to allow for distinct company mentor responsibilities

### Updated on
- Model: `app/Models/Role.php`
- Seeder: `database/seeders/RoleSeeder.php`
- Feature Seeder: `database/seeders/RoleFeatureSeeder.php`

## How to Reseed Roles

To apply the new role configuration:

```bash
# Reset and reseed the database with new roles
php artisan migrate:fresh --seed

# Or just reseed roles without losing data
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=RoleFeatureSeeder
```

## Managing Custom Roles

You can create additional custom roles through the admin panel at `/admin/roles`:
- Custom roles are deletable as long as no users are assigned to them
- You can modify custom role permissions and features
- System roles are read-only and cannot be modified or deleted
