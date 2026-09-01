export type Difficulty = 'easy' | 'medium' | 'hard';

export interface QuizRow {
    id: number;
    question: string;
    answer: boolean | null;
    answer_text: string | null;
    subject: string;
    difficulty: Difficulty;
    points: number;
    created_at: string;
}

export interface LibraryRow {
    id: number;
    library_name: string;
    library_subject: string;
    library_difficulty: Difficulty;
    created_at: string;
}

export interface ExamRow {
    id: number;
    exam_name: string;
    startAt: string;
    dueAt: string;
    total_points: number | null;
    is_open?: boolean;
}
