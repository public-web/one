/**
 * Role-related type definitions
 */

export interface Permission {
    id: number;
    name: string;
    display_name: string;
}

export interface RoleData {
    id: number;
    name: string;
    guard_name: string;
    permissions_count: number;
    users_count: number;
    permissions: string[];
    created_at: string;
}

export interface RoleFormData {
    name: string;
    permissions: string[];
}

export interface RolesPageProps {
    roles: RoleData[];
    permissions: Permission[];
}
