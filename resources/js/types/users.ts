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

/**
 * Laravel pagination link
 */
export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

/**
 * Laravel paginated response
 */
export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

/**
 * Filters for users list
 */
export interface UsersFilters {
    search?: string;
    role?: string;
    status?: string;
    expiring?: string;
    per_page?: number;
}

export interface UsersPageProps {
    users: User[] | PaginatedResponse<User>;
    availableRoles: Role[];
    filters?: UsersFilters;
}

/**
 * Inertia error response type
 */
export type InertiaErrors = Record<string, string | string[]>;
