-- Manual creation of incidents table
-- Run this in MySQL if migrations fail

USE ifmms_zar;

CREATE TABLE IF NOT EXISTS `incidents` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `severity` enum('P1','P2','P3','P4') DEFAULT 'P3',
  `status` enum('New','Acknowledged','Dispatched','In Progress','Waiting','Resolved','Closed','On Hold','Cancelled','Duplicate') DEFAULT 'New',
  `reported_by_user_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_to_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vehicle_identifier` varchar(255) DEFAULT NULL,
  `odometer` int(10) UNSIGNED DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `dtc_codes` json DEFAULT NULL,
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `dispatched_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `sla_response_due_at` timestamp NULL DEFAULT NULL,
  `sla_resolution_due_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `incidents_status_severity_index` (`status`,`severity`),
  KEY `incidents_assigned_to_user_id_index` (`assigned_to_user_id`),
  KEY `incidents_reported_by_user_id_index` (`reported_by_user_id`),
  KEY `incidents_reported_by_user_id_foreign` (`reported_by_user_id`),
  KEY `incidents_assigned_to_user_id_foreign` (`assigned_to_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create incident_updates table
CREATE TABLE IF NOT EXISTS `incident_updates` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `incident_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('comment','status','assignment','system') DEFAULT 'comment',
  `body` text DEFAULT NULL,
  `data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `incident_updates_incident_id_foreign` (`incident_id`),
  KEY `incident_updates_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add to migrations table so Laravel knows it's been run
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_08_16_083422_create_incidents_table', 1),
('2025_08_16_100024_create_incident_updates_table', 1);