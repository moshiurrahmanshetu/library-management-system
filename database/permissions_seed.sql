--
-- Seed default permissions.
-- These permissions support current and future modules.
--

INSERT INTO permissions (name, description) VALUES
    ('dashboard.view', 'View dashboard'),

    ('books.view', 'View books'),
    ('books.create', 'Create books'),
    ('books.edit', 'Edit books'),
    ('books.delete', 'Delete books'),

    ('members.view', 'View members'),
    ('members.create', 'Create members'),
    ('members.edit', 'Edit members'),
    ('members.delete', 'Delete members'),

    ('users.view', 'View users'),
    ('users.create', 'Create users'),
    ('users.edit', 'Edit users'),
    ('users.delete', 'Delete users'),

    ('roles.view', 'View roles'),
    ('roles.create', 'Create roles'),
    ('roles.edit', 'Edit roles'),
    ('roles.delete', 'Delete roles'),

    ('permissions.view', 'View permissions'),
    ('permissions.create', 'Create permissions'),
    ('permissions.edit', 'Edit permissions'),
    ('permissions.delete', 'Delete permissions'),

    ('role_permissions.view', 'View role permissions'),
    ('role_permissions.edit', 'Edit role permissions'),

    ('reports.view', 'View reports'),

    ('settings.view', 'View settings')
ON DUPLICATE KEY UPDATE
    description = VALUES(description);
