import { Head, router } from '@inertiajs/react';

import ConfirmDeleteButton from '@/components/confirm-delete-button';
import QuizTable from '@/components/quiz-table';
import AppLayout from '@/layouts/app-layout';
import type { SharedPageProps } from '@/types';
import type { ExamRow, QuizRow } from '@/types/models';

interface ExamQuizListProps extends SharedPageProps {
    exam: ExamRow;
    quizzes: QuizRow[];
}

export default function ExamQuizList({ auth, nav, flash, exam, quizzes }: ExamQuizListProps) {
    const user = auth.user!;

    return (
        <AppLayout user={user} nav={nav} flash={flash}>
            <Head title={`Quiz — ${exam.exam_name}`} />

            <h1 className="mb-6 text-2xl font-semibold tracking-tight">Quiz nell'esame “{exam.exam_name}”</h1>

            <QuizTable
                quizzes={quizzes}
                emptyMessage="Questo esame non ha ancora nessun quiz."
                renderActions={(quiz) => (
                    <ConfirmDeleteButton
                        title="Rimuovere il quiz dall'esame?"
                        description={`"${quiz.question}" verrà scollegato da questo esame (il quiz resta nel tuo archivio).`}
                        onConfirm={() => router.delete(route('exam.quiz.destroy', { idexam: exam.id, idquiz: quiz.id }))}
                    >
                        Rimuovi
                    </ConfirmDeleteButton>
                )}
            />
        </AppLayout>
    );
}
