import { Head, router } from '@inertiajs/react';

import ConfirmDeleteButton from '@/components/confirm-delete-button';
import QuizTable from '@/components/quiz-table';
import AppLayout from '@/layouts/app-layout';
import type { SharedPageProps } from '@/types';
import type { LibraryRow, QuizRow } from '@/types/models';

interface LibraryQuizListProps extends SharedPageProps {
    library: LibraryRow;
    quizzes: QuizRow[];
}

export default function LibraryQuizList({ auth, nav, flash, library, quizzes }: LibraryQuizListProps) {
    const user = auth.user!;

    return (
        <AppLayout user={user} nav={nav} flash={flash}>
            <Head title={`Quiz — ${library.library_name}`} />

            <h1 className="mb-6 text-2xl font-semibold tracking-tight">Quiz nella libreria “{library.library_name}”</h1>

            <QuizTable
                quizzes={quizzes}
                emptyMessage="Questa libreria non ha ancora nessun quiz."
                renderActions={(quiz) => (
                    <ConfirmDeleteButton
                        title="Rimuovere il quiz dalla libreria?"
                        description={`"${quiz.question}" verrà scollegato da questa libreria (il quiz resta nel tuo archivio).`}
                        onConfirm={() =>
                            router.delete(route('library.quiz.destroy', { idlibrary: library.id, idquiz: quiz.id }))
                        }
                    >
                        Rimuovi
                    </ConfirmDeleteButton>
                )}
            />
        </AppLayout>
    );
}
