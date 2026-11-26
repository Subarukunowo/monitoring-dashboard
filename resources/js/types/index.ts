export interface AuthUser {
    id: number;
    name: string;
    email: string | null;
    role: 'admin' | 'user' | string;
    is_admin: boolean;
}

export interface NavMeta {
    overdueCount: number;
    readyToCloseCount: number;
    unreadMessagesCount: number;
}

export interface PageProps {
    auth: {
        user: AuthUser | null;
    };
    flash: {
        success?: string | null;
        error?: string | null;
    };
    navMeta: NavMeta | null;
    currentPath: string;
    todayLabel: string;
    [key: string]: unknown;
}

export interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}
