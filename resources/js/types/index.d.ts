export interface AuthUser {
    id: number;
    name: string;
    email: string;
    isTeacher: boolean;
    avatarUrl: string | null;
}

export interface Nav {
    login: string;
    register: string;
    dashboard: string;
    profile: string;
    signout: string;
    quizList: string;
    quizCreate: string;
    libraryCreate: string;
    libraryList: string;
    examCreate: string;
    examList: string;
}

export interface SharedPageProps {
    auth: {
        user: AuthUser | null;
    };
    flash: {
        success: string | null;
        error: string | null;
    };
    nav: Nav;
    [key: string]: unknown;
}
