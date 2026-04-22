-- =========================================
-- AMEND CURRENT DB TO MATCH CURRENT FILES
-- =========================================

-- 1) audit_log: keep compatible with current PHP actions
-- Your current table already works with VARCHAR(50),
-- so this step is optional. Use it only if you want stricter values.
ALTER TABLE `audit_log`
  MODIFY `action` ENUM(
    'INSERT',
    'UPDATE',
    'DELETE',
    'ARCHIVE_INSERT',
    'ARCHIVE_DELETE',
    'LOGIN_SUCCESS',
    'LOGIN_FAILURE',
    'LOGOUT'
  ) NOT NULL;

-- 2) Add extra helpful indexes
ALTER TABLE `form`
  ADD KEY `idx_form_updated_at` (`updated_at`);

ALTER TABLE `terminate`
  ADD KEY `idx_terminate_created_at` (`created_at`);

-- 3) Make sure text/file columns are large enough
ALTER TABLE `form`
  MODIFY `filename` TEXT DEFAULT NULL,
  MODIFY `remarks` TEXT DEFAULT NULL,
  MODIFY `rent` VARCHAR(120) NOT NULL;

ALTER TABLE `terminate`
  MODIFY `filename` TEXT DEFAULT NULL,
  MODIFY `remarks` TEXT DEFAULT NULL,
  MODIFY `rent` VARCHAR(120) DEFAULT NULL;

-- 4) Keep no_end_date available for future use
ALTER TABLE `form`
  MODIFY `no_end_date` TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE `terminate`
  MODIFY `no_end_date` TINYINT(1) NOT NULL DEFAULT 0;

-- 5) Optional: if you want terminate rows to preserve source timestamps
ALTER TABLE `terminate`
  MODIFY `created_at` DATETIME DEFAULT NULL,
  MODIFY `updated_at` DATETIME DEFAULT NULL;