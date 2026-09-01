import { Head, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';

import ConfirmDeleteButton from '@/components/confirm-delete-button';
import QuizTable from '@/components/quiz-table';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { difficultyLabels } from '@/lib/quiz-labels';
import type { SharedPageProps } from '@/types';
import type { Difficulty, QuizRow } from '@/types/models';

interface QuizIndexProps extends SharedPageProps {
    quizzes: QuizRow[];
    filters: {
        question?: string;
        difficulty?: Difficulty;
        subject?: string;
    };
}

export default function QuizIndex({ auth, nav, flash, quizzes, filters }: QuizIndexProps) {
    const user = auth.user!;

    const [question, setQuestion] = useState(filters.question ?? '');
    const [difficulty, setDifficulty] = useState<'' | Difficulty>(filters.difficulty ?? '');
    const [subject, setSubject] = useState(filters.subject ?? '');

    function search(e: FormEvent) {
        e.preventDefault();
        router.get(
            route('quiz.search'),
            { question, difficulty, subject },
            { preserveState: true, replace: true },
        );
    }

    return (
        <AppLayout user={user} nav={nav} flash={flash}>
            <Head title="Quiz" />

            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-semibold tracking-tight">Archivio quiz</h1>
                <Button asChild>
                    <a href={nav.quizCreate}>Crea nuovo quiz</a>
                </Button>
            </div>

            <Card className="mb-6">
                <CardHeader>
                    <CardTitle className="text-base">Cerca</CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={search} className="grid gap-4 sm:grid-cols-4 sm:items-end">
                        <div className="space-y-2">
                            <Label htmlFor="search-question">Domanda</Label>
                            <Input id="search-question" value={question} onChange={(e) => setQuestion(e.target.value)} />
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="search-difficulty">Difficoltà</Label>
                            <Select value={difficulty} onValueChange={(value: '' | Difficulty) => setDifficulty(value)}>
                                <SelectTrigger id="search-difficulty" className="w-full">
                                    <SelectValue placeholder="Tutte" />
                                </SelectTrigger>
                                <SelectContent>
                                    {(Object.keys(difficultyLabels) as Difficulty[]).map((level) => (
                                        <SelectItem key={level} value={level}>
                                            {difficultyLabels[level]}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="search-subject">Materia</Label>
                            <Input id="search-subject" value={subject} onChange={(e) => setSubject(e.target.value)} />
                        </div>

                        <Button type="submit" variant="outline">
                            Cerca
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <QuizTable
                quizzes={quizzes}
                emptyMessage="Nessun quiz corrisponde ai filtri selezionati."
                renderActions={(quiz) => (
                    <>
                        <Button asChild variant="outline" size="sm">
                            <a href={route('quiz.edit', quiz.id)}>Modifica</a>
                        </Button>
                        <ConfirmDeleteButton
                            description={`Il quiz "${quiz.question}" verrà eliminato definitivamente.`}
                            onConfirm={() => router.delete(route('quiz.destroy', quiz.id))}
                        />
                    </>
                )}
            />
        </AppLayout>
    );
}
