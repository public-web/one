/**
 * Permission-related type definitions
 */

export interface RoleBasicData {
    id: number;
    name: string;
}

export interface PermissionData {
    id: number;
    name: string;
    display_name: string;
    guard_name: string;
    roles?: RoleBasicData[];
    roles_count: number;
    users_count: number;
    created_at: string;
}

export interface PermissionFormData {
    name: string;
}

export interface PermissionsPageProps {
    permissions: PermissionData[];
    roles: RoleBasicData[];
    filters?: {
        search?: string;
        category?: string;
        has_roles?: boolean;
        has_users?: boolean;
        role?: string;
    };
}
