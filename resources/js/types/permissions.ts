/**
 * Permission-related type definitions
 */

export interface PermissionData {
    id: number;
    name: string;
    display_name: string;
    guard_name: string;
    roles_count: number;
    users_count: number;
    created_at: string;
}

export interface PermissionFormData {
    name: string;
}

export interface PermissionsPageProps {
    permissions: PermissionData[];
}
