-- mysql
USE spbvkurse_main;

CREATE TABLE IF NOT EXISTS users(
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    full_name VARCHAR(255),
    avatar_url TEXT,
    role ENUM('reader', 'author', 'moderator', 'admin') DEFAULT 'reader',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    last_login TIMESTAMP NULL DEFAULT NULL,
    is_blocked BOOLEAN DEFAULT false
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS subscriptions(
    id SERIAL PRIMARY KEY,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    plan ENUM('monthly', 'yearly'),
    status ENUM('active', 'expired', 'cancelled'),
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP GENERATED ALWAYS AS (
        CASE plan
            WHEN 'monthly' THEN started_at + INTERVAL 1 MONTH
            WHEN 'yearly' THEN started_at + INTERVAL 1 YEAR
        END
    ) STORED,
    auto_renew BOOLEAN DEFAULT true,
    payment_method VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES `spbvkurse_main`.users(id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS payments(
    id SERIAL PRIMARY KEY,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    subscription_id BIGINT(20) UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'RUB',
    status ENUM('pending', 'succeeded', 'failed', 'refunded'),
    payment_gateway VARCHAR(20),
    payment_gateway_id BIGINT(20) UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES `spbvkurse_main`.users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS categories(
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE,
    description TEXT,
    parent_id BIGINT(20) UNSIGNED,
    sort_order INT,
    is_active BOOLEAN DEFAULT true
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS tags(
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE,
    slug VARCHAR(50) UNIQUE
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS articles(
    id SERIAL PRIMARY KEY,
    author_id BIGINT(20) UNSIGNED NOT NULL,
    category_id BIGINT(20) UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    content TEXT,
    excerpt TEXT,
    cover_image_url TEXT,
    status ENUM('draft', 'pending', 'published', 'rejected'),
    is_breaking BOOLEAN DEFAULT false,
    is_premium BOOLEAN DEFAULT false,
    views_count INT DEFAULT 0,
    likes_count INT DEFAULT 0,
    comments_count INT DEFAULT 0,
    published_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    moderated_by BIGINT(20) UNSIGNED,
    moderated_at TIMESTAMP NULL DEFAULT NULL,
    rejection_reason TEXT,
    FOREIGN KEY (author_id) REFERENCES `spbvkurse_main`.users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (moderated_by) REFERENCES `spbvkurse_main`.users(id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS article_tags(
    article_id BIGINT(20) UNSIGNED NOT NULL,
    tag_id BIGINT(20) UNSIGNED NOT NULL,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE RESTRICT ON UPDATE CASCADE 
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS article_images(
    id SERIAL PRIMARY KEY,
    article_id BIGINT(20) UNSIGNED NOT NULL,
    image_url TEXT,
    thumbnail_url TEXT,
    caption VARCHAR(255),
    sort_order INT,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS external_sources(
    id SERIAL PRIMARY KEY,
    name VARCHAR(100),
    base_url VARCHAR(255),
    api_key VARCHAR(255) COMMENT 'the api key should be encrupted',
    update_interval_minutes INT DEFAULT 30 COMMENT 'intervals between reads at which new data from the source should be fetched',
    is_active BOOLEAN DEFAULT true,
    last_sync_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS external_articles(
    id SERIAL PRIMARY KEY,
    source_id BIGINT(20) UNSIGNED NOT NULL,
    category_id BIGINT(20) UNSIGNED,
    external_id VARCHAR(255),
    title VARCHAR(255),
    content TEXT,
    url TEXT,
    image_url TEXT,
    published_at TIMESTAMP NULL DEFAULT NULL,
    fetched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    author_name VARCHAR(100),
    title_hash VARCHAR(64),
    INDEX (title_hash),
    FOREIGN KEY (source_id) REFERENCES external_sources(id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS user_likes(
    id SERIAL PRIMARY KEY,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    article_id BIGINT(20) UNSIGNED,
    external_article_id BIGINT(20) UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES `spbvkurse_main`.users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (external_article_id) REFERENCES external_sources(id) ON DELETE SET NULL ON UPDATE CASCADE
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS comments(
    id SERIAL PRIMARY KEY,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    article_id BIGINT(20) UNSIGNED,
    external_article_id BIGINT(20) UNSIGNED,
    parent_id BIGINT(20) UNSIGNED,
    FOREIGN KEY (user_id) REFERENCES `spbvkurse_main`.users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (external_article_id) REFERENCES external_sources(id) ON DELETE SET NULL ON UPDATE CASCADE
)ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS notifications(
    id SERIAL PRIMARY KEY,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    type ENUM('new_article', 'breaking_news', 'moderation_result', 'subscription_expiring', 'daily_digest'),
    title VARCHAR(255),
    content TEXT,
    data JSON,
    is_read BOOLEAN DEFAULT false,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    channel ENUM('in_app', 'push', 'email'),
    FOREIGN KEY (user_id) REFERENCES `spbvkurse_main`.users(id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS author_followers(
    id SERIAL PRIMARY KEY,
    follower_id BIGINT(20) UNSIGNED NOT NULL,
    author_id BIGINT(20) UNSIGNED NOT NULL,
    FOREIGN KEY (follower_id) REFERENCES `spbvkurse_main`.users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (author_id) REFERENCES `spbvkurse_main`.users(id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS user_notification_settings(
    id SERIAL PRIMARY KEY,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    push_enabled BOOLEAN DEFAULT true,
    email_enabled BOOLEAN DEFAULT true,
    daily_digest BOOLEAN DEFAULT true,
    notify_on_followed_authors BOOLEAN DEFAULT true,
    notify_on_breaking BOOLEAN DEFAULT true,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES `spbvkurse_main`.users(id) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS user_reading_history(
    id SERIAL PRIMARY KEY,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    article_id BIGINT(20) UNSIGNED,
    external_article_id BIGINT(20) UNSIGNED,
    read_at TIMESTAMP NULL DEFAULT NULL,
    read_percentage INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES `spbvkurse_main`.users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE SET NULL ON UPDATE CASCADE
)ENGINE=INNODB;

-- триггеры
DELIMITER $$

-- INSERT TRIGGERS
CREATE TRIGGER on_article_insert
BEFORE INSERT ON articles
FOR EACH ROW
BEGIN
    IF NOT ((NEW.moderated_at IS NOT NULL AND NEW.moderated_by IS NOT NULL) OR
        (NEW.moderated_at IS NULL AND NEW.moderated_by IS NULL)
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Both moderated_at and moderated_by should either be null or not null';
    END IF;
END$$

CREATE TRIGGER urh_check_article_type
BEFORE INSERT ON user_reading_history
FOR EACH ROW
BEGIN
    IF NOT ((NEW.article_id IS NOT NULL AND NEW.external_article_id IS NULL) OR
        (NEW.article_id IS NULL AND NEW.external_article_id IS NOT NULL)
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'CONSTRAINT VIOLATION: either one of article_id or external_article_id can be not null, not both at the same time';
    END IF; 
END$$

CREATE TRIGGER check_like_type
BEFORE INSERT ON user_likes
FOR EACH ROW
BEGIN
    IF NOT ((NEW.article_id IS NOT NULL AND NEW.external_article_id IS NULL) OR
        (NEW.article_id IS NULL AND NEW.external_article_id IS NOT NULL)
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'CONSTRAINT VIOLATION: either one of article_id or external_article_id can be not null, not both at the same time';
    END IF; 
END$$

CREATE TRIGGER comment_parent
BEFORE INSERT ON comments
FOR EACH ROW
BEGIN
    DECLARE max_id BIGINT UNSIGNED;
    
    SELECT MAX(id) INTO max_id FROM comments;
    
    IF max_id IS NOT NULL THEN
        SET NEW.parent_id = max_id;
    END IF;
END$$

-- UPDATE TRIGGERS
CREATE TRIGGER on_article_update
BEFORE UPDATE ON articles
FOR EACH ROW
BEGIN
    IF NOT ((NEW.moderated_at IS NOT NULL AND NEW.moderated_by IS NOT NULL) OR
        (NEW.moderated_at IS NULL AND NEW.moderated_by IS NULL)
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Both moderated_at and moderated_by should either be null or not null';
    END IF;
END$$

CREATE TRIGGER urh_check_article_type_update
BEFORE UPDATE ON user_reading_history
FOR EACH ROW
BEGIN
    IF NOT ((NEW.article_id IS NOT NULL AND NEW.external_article_id IS NULL) OR
        (NEW.article_id IS NULL AND NEW.external_article_id IS NOT NULL)
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'CONSTRAINT VIOLATION: either one of article_id or external_article_id can be not null, not both at the same time';
    END IF; 
END$$

CREATE TRIGGER check_like_type_update
BEFORE UPDATE ON user_likes
FOR EACH ROW
BEGIN
    IF NOT ((NEW.article_id IS NOT NULL AND NEW.external_article_id IS NULL) OR
        (NEW.article_id IS NULL AND NEW.external_article_id IS NOT NULL)
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'CONSTRAINT VIOLATION: either one of article_id or external_article_id can be not null, not both at the same time';
    END IF; 
END$$

DELIMITER ;