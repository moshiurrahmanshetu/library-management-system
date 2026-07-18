--
-- Grant Super Admin (role_id = 1) all existing permissions.
-- This ensures the first/admin role always has full access.
--

INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions
ON DUPLICATE KEY UPDATE role_id = role_id;
