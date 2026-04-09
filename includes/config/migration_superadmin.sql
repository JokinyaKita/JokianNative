-- Migration: add superadmin role and lock public registration to student-only flow
-- Run this on existing database if `superadmin` role is not yet available.

ALTER TABLE users
MODIFY COLUMN role ENUM('student','instructor','admin','superadmin') DEFAULT 'student';

-- Seed default superadmin account (insert or update if already exists)
-- Email    : superadmin@learninglite.local
-- Password : SuperAdmin123
INSERT INTO users (name, email, password_hash, role)
VALUES (
	'Super Admin',
	'superadmin@learninglite.local',
	'$2y$12$HkR2BJDnDRcV3oY/NY/J.Omah7Rfgc1Nef.eohH77NHJBWFytctiu',
	'superadmin'
)
ON DUPLICATE KEY UPDATE
	name = VALUES(name),
	password_hash = VALUES(password_hash),
	role = 'superadmin';
