import { Head, router } from '@inertiajs/react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import type { SharedPageProps } from '@/types';

interface ResultQuiz {
    id: number;
    question: string;
}

interface ResultAnswer {
    quiz_id: number;
    answer: boolean | null;
    answer_text: string | null;
}

interface ExamResultsUserProps extends SharedPageProps {
    exam: { id: number; exam_name: string };
    quizzes: ResultQuiz[];
    userAnswer: ResultAnswer[];
    userId: number;
}

export default function ExamResultsUser({ auth, nav, flash, exam, quizzes, userAnswer, userId }: ExamResultsUserProps) {
    const user = auth.user!;

    function answerFor(quizId: number) {
        return userAnswer.find((a) => a.quiz_id === quizId) ?? null;
    }

    function correct() {
        router.post(route('display.users.answerP'), { exam_id: exam.id, user_id: userId });
    }

    return (
        <AppLayout user={user} nav={nav} flash={flash}>
            <Head title={`Compito — ${exam.exam_name}`} />

            <h1 className="mb-6 text-2xl font-semibold tracking-tight">Compito svolto — “{exam.exam_name}”</h1>

            <div className="space-y-4">
                {quizzes.map((quiz, index) => {
                    const given = answerFor(quiz.id);

                    return (
                        <Card key={quiz.id}>
                            <CardHeader>
                                <CardTitle className="text-base">
                                    {index + 1}. {quiz.question}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                {given === null ? (
                                    <p className="text-muted-foreground text-sm">Nessuna risposta.</p>
                                ) : (
                                    <p className="text-sm">
                                        Risposta data:{' '}
                                        <span className="font-medium">
                                            {given.answer_text ?? (given.answer ? 'Vero' : 'Falso')}
                                        </span>
                                    </p>
                                )}
                            </CardContent>
                        </Card>
                    );
                })}
            </div>

            <Button className="mt-6" onClick={correct}>
                Correggi
            </Button>
        </AppLayout>
    );
}
