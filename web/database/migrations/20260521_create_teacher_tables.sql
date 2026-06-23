-- Migration: create tables for teacher / professor panel features
-- Generated: 2026-05-21
-- Run this file in your MySQL database (e.g. via phpMyAdmin or `mysql` cli)

SET FOREIGN_KEY_CHECKS=0;

-- Teachers basic profile
CREATE TABLE IF NOT EXISTS `teachers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `telefone` VARCHAR(50) DEFAULT NULL,
  `cpf` VARCHAR(20) DEFAULT NULL,
  `sexo` CHAR(1) DEFAULT NULL,
  `curriculo` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_teachers_cpf` (`cpf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Per-teacher dashboard / panel summary
CREATE TABLE IF NOT EXISTS `teacher_panels` (
  `teacher_id` INT UNSIGNED NOT NULL,
  `proxima_sala` VARCHAR(255) DEFAULT NULL,
  `proxima_reuniao` VARCHAR(255) DEFAULT NULL,
  `creditos` INT DEFAULT 0,
  `novas_mensagens` INT DEFAULT 0,
  `rendimento_por_turma` JSON DEFAULT (JSON_ARRAY()),
  `horarios` JSON DEFAULT (JSON_ARRAY()),
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`teacher_id`),
  CONSTRAINT `fk_teacher_panels_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Teacher schedule entries (detailed horarios)
CREATE TABLE IF NOT EXISTS `teacher_horarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `teacher_id` INT UNSIGNED NOT NULL,
  `dia` VARCHAR(30) DEFAULT NULL,
  `inicio` TIME DEFAULT NULL,
  `fim` TIME DEFAULT NULL,
  `disciplina` VARCHAR(255) DEFAULT NULL,
  `turma` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_teacher_horarios_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rendimento por turma (per-teacher, per-class performance metrics)
CREATE TABLE IF NOT EXISTS `teacher_rendimentos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `teacher_id` INT UNSIGNED NOT NULL,
  `turma` VARCHAR(100) NOT NULL,
  `rendimento` DECIMAL(5,2) DEFAULT NULL,
  `periodo` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_tr_teacher` (`teacher_id`),
  CONSTRAINT `fk_teacher_rendimentos_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Programação / agenda (events)
CREATE TABLE IF NOT EXISTS `teacher_programacao` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `teacher_id` INT UNSIGNED NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `descricao` TEXT DEFAULT NULL,
  `data_inicio` DATETIME DEFAULT NULL,
  `data_fim` DATETIME DEFAULT NULL,
  `local` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_teacher_programacao_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mural de avisos
CREATE TABLE IF NOT EXISTS `teacher_mural` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `teacher_id` INT UNSIGNED NOT NULL,
  `titulo` VARCHAR(255) DEFAULT NULL,
  `mensagem` TEXT NOT NULL,
  `publicado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `visivel_para_turmas` JSON DEFAULT (JSON_ARRAY()),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_teacher_mural_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chat messages (simple structure)
CREATE TABLE IF NOT EXISTS `teacher_chat_messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `teacher_id` INT UNSIGNED NOT NULL,
  `user_from` VARCHAR(100) DEFAULT NULL,
  `user_to` VARCHAR(100) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `read` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_chat_teacher` (`teacher_id`),
  CONSTRAINT `fk_teacher_chat_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Link teachers to turmas (classes) if you don't already have a `turmas` table
CREATE TABLE IF NOT EXISTS `teacher_turmas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `teacher_id` INT UNSIGNED NOT NULL,
  `turma` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_tt_teacher` (`teacher_id`),
  CONSTRAINT `fk_teacher_turmas_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;

-- End of migration