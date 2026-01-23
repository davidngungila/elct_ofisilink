# Employee Number Standardization Guide

## Overview
This guide explains how to standardize all employee numbers to a consistent format.

## Recommended Format

**Sequential Format: EMP001, EMP002, EMP003, etc.**

### Why This Format?
- ✅ Simple and clean
- ✅ Easy to remember and reference
- ✅ Sequential and predictable
- ✅ No date dependencies
- ✅ No department dependencies
- ✅ Works well for small to medium organizations

### Alternative Format
If you prefer date-based format: `EMP20251107DU` (date + department code)
- Set `EMPLOYEE_ID_FORMAT=date-based` in `.env`

## How to Standardize Existing Employee Numbers

### Step 1: Review Current Data (Dry Run)
Run the command in dry-run mode to see what changes will be made:

```bash
php artisan employees:standardize-numbers --dry-run
```

This will show you:
- Which employee numbers will be changed
- What the new numbers will be
- Which ones are already in the correct format
- Any errors that might occur

### Step 2: Standardize All Employee Numbers
Once you're satisfied with the preview, run the actual command:

```bash
php artisan employees:standardize-numbers
```

This will:
- Update all employee numbers to sequential format (EMP001, EMP002, etc.)
- Preserve the order based on user ID
- Skip employees already in the correct format
- Show a detailed report of all changes

### Step 3: Verify Changes
After running the command, verify the changes in your database or employee list page.

## Command Options

```bash
# Dry run (preview changes without making them)
php artisan employees:standardize-numbers --dry-run

# Use date-based format instead of sequential
php artisan employees:standardize-numbers --format=date-based

# Verbose output (shows skipped employees)
php artisan employees:standardize-numbers --dry-run -v
```

## Example Output

```
Starting employee number standardization...

Found 15 users with employee numbers to standardize.

=== STANDARDIZATION RESULTS ===

Changes made: 12

+------------------+------------------+------------------+
| User Name        | Old Employee ID  | New Employee ID  |
+------------------+------------------+------------------+
| Ally Ally        | EMP006           | EMP001           |
| Benjamin Zacharia| EMP20251201IT    | EMP002           |
| Caroline Shija   | EMP003           | EMP003           |
| David Ngungila    | EMP007           | EMP004           |
| ...              | ...              | ...              |
+------------------+------------------+------------------+

Skipped (already in correct format): 3
Total processed: 15
```

## Important Notes

1. **Backup First**: Always backup your database before running this command
2. **Test Environment**: Test in a development/staging environment first
3. **Employee Order**: Numbers are assigned based on user ID order (oldest first)
4. **Uniqueness**: The command ensures all employee numbers remain unique
5. **No Duplicates**: If a number already exists, it will find the next available number

## Future Employee Numbers

After standardization, all new employees will automatically get sequential numbers:
- Next employee: EMP016
- Following: EMP017
- And so on...

The system will automatically find the next available sequential number.

## Troubleshooting

### Error: "Could not generate unique ID"
- This should not happen, but if it does, check for duplicate employee numbers
- Run: `SELECT employee_id, COUNT(*) FROM users WHERE employee_id IS NOT NULL GROUP BY employee_id HAVING COUNT(*) > 1;`

### Want to Revert?
- Restore from your database backup
- Or manually update employee numbers back to their original values

## Support

If you encounter any issues, check the logs:
```bash
tail -f storage/logs/laravel.log
```

