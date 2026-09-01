import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import type { SharedPageProps } from '@/types';
import type { LibraryRow, QuizRow } from '@/types/models';

interface LibraryAddQuizProps extends SharedPageProps {
    library: LibraryRow;
    availableQuiz: QuizRow[];
}

export default function LibraryAddQuiz({ auth, nav, flash, library, availableQuiz }: LibraryAddQuizProps) {
    const user = auth.user!;

    const { data, setData, post, processing, errors } = useForm({
        library_id: String(library.id),
        quiz_id: '',
    });

    function submit(e: FormEvent) {
        e.preventDefault();
        post(route('libraryquiz.store'));
    }

    return (
        <AppLayout user={user} nav={nav} flash={flash}>
            <Head title={`Aggiungi quiz — ${library.library_name}`} />

            <Card className="mx-auto max-w-xl">
                <CardHeader>
                    <CardTitle>Aggiungi un quiz a “{library.library_name}”</CardTitle>
                    <CardDescription>Scegli tra i quiz del tuo archivio.</CardDescription>
                </CardHeader>
                <CardContent>
                    {availableQuiz.length === 0 ? (
                        <p className="text-muted-foreground text-sm">Non hai ancora creato nessun quiz.</p>
                    ) : (
                        <form onSubmit={submit} className="space-y-4">
                            <Select value={data.quiz_id} onValueChange={(value) => setData('quiz_id', value)}>
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Seleziona un quiz" />
                                </SelectTrigger>
                                <SelectContent>
                                    {availableQuiz.map((quiz) => (
                                        <SelectItem key={quiz.id} value={String(quiz.id)}>
                                            {quiz.question}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.quiz_id && <p className="text-sm text-destructive">{errors.quiz_id}</p>}

                            <Button type="submit" disabled={processing || data.quiz_id === ''}>
                                Aggiungi quiz
                            </Button>
                        </form>
                    )}
                </CardContent>
            </Card>
        </AppLayout>
    );
}
