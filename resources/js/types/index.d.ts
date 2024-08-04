export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
}

export interface Task{
    id: number;
    name: string;
    completed: boolean;
    completed_at: Date;
    created_at: Date;
    updated_at: Date;
}

export interface Note{
    id: number;
    content: string;
    created_at: Date;
    updated_at: Date;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
};
