-- ============================================================
--  Base de datos: bdblogs  (MySQL 8 / MariaDB 10.6+)
--
--  Script equivalente a las migraciones de database/migrations.
--  Crea la base y todas las tablas, y registra las migraciones
--  como ya ejecutadas para que "php artisan migrate" no las repita.
--
--  ATENCIÓN: borra las tablas existentes antes de crearlas.
--
--  No carga datos. Después de ejecutarlo correr:
--      php artisan db:seed
--
--  Si se agrega o modifica una migración, actualizar este archivo.
-- ============================================================
CREATE DATABASE IF NOT EXISTS bdblogs
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE bdblogs;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS ratings, comments, posts, personal_access_tokens,
                     sessions, password_reset_tokens, cache, cache_locks,
                     users, migrations;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
--  Usuarios
--  role: 'user' | 'editor' | 'admin'
-- ------------------------------------------------------------
CREATE TABLE users (
  id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name                VARCHAR(255)    NOT NULL,
  email               VARCHAR(255)    NOT NULL,
  email_verified_at   TIMESTAMP       NULL DEFAULT NULL,
  password            VARCHAR(255)    NOT NULL,
  role                VARCHAR(255)    NOT NULL DEFAULT 'user',
  requested_editor_at TIMESTAMP       NULL DEFAULT NULL,
  remember_token      VARCHAR(100)    NULL DEFAULT NULL,
  created_at          TIMESTAMP       NULL DEFAULT NULL,
  updated_at          TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY users_email_unique (email),
  KEY users_role_index (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_reset_tokens (
  email      VARCHAR(255) NOT NULL,
  token      VARCHAR(255) NOT NULL,
  created_at TIMESTAMP    NULL DEFAULT NULL,
  PRIMARY KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sessions (
  id            VARCHAR(255)    NOT NULL,
  user_id       BIGINT UNSIGNED NULL DEFAULT NULL,
  ip_address    VARCHAR(45)     NULL DEFAULT NULL,
  user_agent    TEXT            NULL,
  payload       LONGTEXT        NOT NULL,
  last_activity INT             NOT NULL,
  PRIMARY KEY (id),
  KEY sessions_user_id_index (user_id),
  KEY sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Caché (CACHE_STORE=database)
-- ------------------------------------------------------------
CREATE TABLE cache (
  `key`      VARCHAR(255) NOT NULL,
  value      MEDIUMTEXT   NOT NULL,
  expiration BIGINT       NOT NULL,
  PRIMARY KEY (`key`),
  KEY cache_expiration_index (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cache_locks (
  `key`      VARCHAR(255) NOT NULL,
  owner      VARCHAR(255) NOT NULL,
  expiration BIGINT       NOT NULL,
  PRIMARY KEY (`key`),
  KEY cache_locks_expiration_index (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Posts
--  is_published: 0 = pendiente de revisión | 1 = publicado | 2 = rechazado
-- ------------------------------------------------------------
CREATE TABLE posts (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id          BIGINT UNSIGNED NOT NULL,
  title            VARCHAR(180)    NOT NULL,
  content          TEXT            NOT NULL,
  image_path       VARCHAR(255)    NULL DEFAULT NULL,
  is_published     TINYINT(1)      NOT NULL DEFAULT 1,
  published_at     TIMESTAMP       NULL DEFAULT NULL,
  rejection_reason TEXT            NULL,
  created_at       TIMESTAMP       NULL DEFAULT NULL,
  updated_at       TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY posts_user_id_created_at_index (user_id, created_at),
  KEY posts_is_published_published_at_index (is_published, published_at),
  CONSTRAINT posts_user_id_foreign FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Comentarios
-- ------------------------------------------------------------
CREATE TABLE comments (
  id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  post_id    BIGINT UNSIGNED NOT NULL,
  user_id    BIGINT UNSIGNED NOT NULL,
  content    TEXT            NOT NULL,
  created_at TIMESTAMP       NULL DEFAULT NULL,
  updated_at TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY comments_post_id_created_at_index (post_id, created_at),
  KEY comments_user_id_created_at_index (user_id, created_at),
  CONSTRAINT comments_post_id_foreign FOREIGN KEY (post_id)
    REFERENCES posts (id) ON DELETE CASCADE,
  CONSTRAINT comments_user_id_foreign FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Calificaciones (1 a 5). Un usuario califica una sola vez cada post.
-- ------------------------------------------------------------
CREATE TABLE ratings (
  id         BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  post_id    BIGINT UNSIGNED  NOT NULL,
  user_id    BIGINT UNSIGNED  NOT NULL,
  score      TINYINT UNSIGNED NOT NULL,
  created_at TIMESTAMP        NULL DEFAULT NULL,
  updated_at TIMESTAMP        NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY ratings_post_id_user_id_unique (post_id, user_id),
  KEY ratings_post_id_score_index (post_id, score),
  CONSTRAINT ratings_post_id_foreign FOREIGN KEY (post_id)
    REFERENCES posts (id) ON DELETE CASCADE,
  CONSTRAINT ratings_user_id_foreign FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Tokens de la API (Laravel Sanctum)
-- ------------------------------------------------------------
CREATE TABLE personal_access_tokens (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tokenable_type VARCHAR(255)    NOT NULL,
  tokenable_id   BIGINT UNSIGNED NOT NULL,
  name           TEXT            NOT NULL,
  token          VARCHAR(64)     NOT NULL,
  abilities      TEXT            NULL,
  last_used_at   TIMESTAMP       NULL DEFAULT NULL,
  expires_at     TIMESTAMP       NULL DEFAULT NULL,
  created_at     TIMESTAMP       NULL DEFAULT NULL,
  updated_at     TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY personal_access_tokens_token_unique (token),
  KEY personal_access_tokens_tokenable_type_tokenable_id_index (tokenable_type, tokenable_id),
  KEY personal_access_tokens_expires_at_index (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Registro de migraciones (para que artisan las dé por ejecutadas)
-- ------------------------------------------------------------
CREATE TABLE migrations (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  migration VARCHAR(255) NOT NULL,
  batch     INT          NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations (migration, batch) VALUES
  ('0001_01_01_000000_create_users_table', 1),
  ('0001_01_01_000001_create_cache_table', 1),
  ('2026_04_24_000003_add_role_to_users_table', 1),
  ('2026_04_24_000004_create_posts_table', 1),
  ('2026_04_24_000005_create_comments_table', 1),
  ('2026_04_24_000006_create_ratings_table', 1),
  ('2026_05_25_151027_add_rejection_reason_to_posts_table', 1),
  ('2026_05_26_000001_add_requested_editor_at_to_users_table', 1),
  ('2026_10_27_232224_create_personal_access_tokens_table', 1);
