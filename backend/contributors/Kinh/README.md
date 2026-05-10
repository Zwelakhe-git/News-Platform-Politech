# News API

## Installation
- Configure Redis and MySQL in `config.php`
- Run `composer install` if using search

## API
- POST `/api/like.php`
- GET `/api/view.php?post_id=1`
- POST `/api/comment.php`
- GET `/api/search.php?q=...`

## Job

Run cron:

`* * * * * /usr/bin/php /path/to/job/write_to_db.php`

If your project has a posts table with a different name, you need to change it in job/write_to_db.php.