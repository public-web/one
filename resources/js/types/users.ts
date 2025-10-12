/**
 * User-related type definitions
 */

export interface Role {
    id: number;
    name: string;
}

export interface UserRole {
    name: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    active: boolean;
    expires_at?: string;
    require_2fa: boolean;
    roles: UserRole[];
    deleted_at?: string | null;
}

export interface UserFormData {
    name: string;
    email: string;
    active: boolean;
    expires_at: string;
    require_2fa: boolean;
    role: string;
}

export interface UserSubmitData extends Record<string, any> {
    name: string;
    email: string;
    active: boolean;
    expires_at: string | null;
    require_2fa: boolean;
    role: string;
}

export interface UsersPageProps {
    users: User[];
    availableRoles: Role[];
}

/**
 * Inertia error response type
 */
export type InertiaErrors = Record<string, string | string[]>;
