# Content Scheduling System Setup

## Overview
Sistem scheduling memungkinkan admin untuk menjadwalkan publikasi artikel. Artikel akan dipublikasikan secara otomatis sesuai waktu yang ditentukan.

## Database Schema
### posts table additions:
- `status` ENUM('draft', 'published', 'scheduled') DEFAULT 'published'
- `scheduled_at` DATETIME NULL

## Features
1. **Draft Mode**: Artikel disimpan sebagai draft, tidak tampil di website
2. **Scheduled Publishing**: Artikel dijadwalkan untuk dipublikasikan pada waktu tertentu
3. **Immediate Publishing**: Artikel langsung dipublikasikan (default)

## Automatic Publishing Setup

### 1. Cron Job Configuration
Add this line to your crontab to run the publishing script every minute:

```bash
# Edit crontab
crontab -e

# Add this line:
* * * * * /usr/bin/php /var/www/araska.id/cron_publish_scheduled.php >> /var/log/araska_cron.log 2>&1
```

### 2. Manual Testing
Test the cron script manually:
```bash
cd /var/www/araska.id
php cron_publish_scheduled.php
```

### 3. Check Logs
Monitor the cron job execution:
```bash
tail -f /var/log/araska_cron.log
# or check the local log file:
tail -f /var/www/araska.id/storage/cron.log
```

## Admin Interface
1. **Create Post**: Select "Jadwalkan Publikasi" and set date/time
2. **Edit Post**: Change status between draft, published, or scheduled
3. **Post List**: View status indicators and scheduled times

## API Usage
### Model Methods:
- `$post->all()` - Returns only published posts and ready scheduled posts
- `$post->allForAdmin()` - Returns all posts including drafts and scheduled
- `$post->getScheduledPostsReady()` - Returns posts ready for publishing
- `$post->publishScheduledPosts()` - Publishes ready scheduled posts
- `$post->getByStatus($status)` - Returns posts by specific status

## Validation Rules
1. Scheduled date must be in the future
2. Draft posts are hidden from public
3. Scheduled posts become visible only when scheduled_at <= NOW()

## Security Notes
- CSRF protection on all form submissions
- Admin authentication required
- Input validation for dates and status values

## Performance
- Indexed columns: status, scheduled_at
- Cron job runs every minute but only processes ready posts
- Efficient queries with proper WHERE clauses
