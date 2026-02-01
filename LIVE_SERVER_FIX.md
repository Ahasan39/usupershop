# Fix for "Please provide a valid cache path" Error

The error `InvalidArgumentException: Please provide a valid cache path` occurs because the required **storage folders** are missing on your live server. These folders are often excluded from Git or deployment zips because they are empty.

## Solution

Run the following commands in your cPanel Terminal (while inside the `public_html` directory) to recreate the missing structure:

```bash
# 1. Create the missing framework directories
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing

# 2. Create bootstrap cache directory if missing
mkdir -p bootstrap/cache

# 3. Set correct permissions (make them writable)
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# 4. Clear cache to verify the fix
php artisan optimize:clear
```

After running these commands, your site should work, and the SEO updates will be live.
