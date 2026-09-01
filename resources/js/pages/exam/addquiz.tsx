import { Head, router } from '@inertiajs/react';
import axios from 'axios';
import { useEffect, useState } from 'react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { difficultyBadgeVariant, difficultyLabels } from '@/lib/quiz-labels';
import type { SharedPageProps } from '@/types';
import type { Difficulty, ExamRow, LibraryRow } from '@/types/models';

interface PickableQuiz {
    id: number;
    question: string;
    subject: string;
    difficulty: Difficulty;
    points: number;
}

interface ExamAddQuizProps extends SharedPageProps {
    exam: ExamRow;
    availableLibraries: LibraryRow[];
}

export default function ExamAddQuiz({ auth, nav, flash, exam, availableLibraries }: ExamAddQuizProps) {
    const user = auth.user!;

    const [libraryId, setLibraryId] = useState(availableLibraries[0] ? String(availableLibraries[0].id) : '');
    const [quizzes, setQuizzes] = useState<PickableQuiz[]>([]);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        if (!libraryId) {
            setQuizzes([]);
            return;
        }

        setLoading(true);
        axios
            .get<PickableQuiz[]>(route('libraries.quiz.exam', libraryId))
            .then((response) => setQuizzes(response.data))
            .finally(() => setLoading(false));
    }, [libraryId]);

    function addQuiz(quizId: number) {
        router.post(route('examquiz.store'), { exam_id: exam.id, quiz_id: quizId });
    }

    return (
        <AppLayout user={user} nav={nav} flash={flash}>
            <Head title={`Aggiungi quiz — ${exam.exam_name}`} />

            <Card>
                <CardHeader>
                    <CardTitle>Aggiungi un quiz a “{exam.exam_name}”</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                    {availableLibraries.length === 0 ? (
                        <p className="text-muted-foreground text-sm">Non hai ancora creato nessuna libreria.</p>
                    ) : (
                        <>
                            <div className="max-w-sm space-y-2">
                                <Select value={libraryId} onValueChange={setLibraryId}>
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="Seleziona una libreria" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {availableLibraries.map((library) => (
                                            <SelectItem key={library.id} value={String(library.id)}>
                                                {library.library_name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>

                            {loading ? (
                                <p className="text-muted-foreground text-sm">Caricamento…</p>
                            ) : quizzes.length === 0 ? (
                                <p className="text-muted-foreground text-sm">Questa libreria non ha quiz.</p>
                            ) : (
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Domanda</TableHead>
                                            <TableHead>Difficoltà</TableHead>
                                            <TableHead>Materia</TableHead>
                                            <TableHead>Punti</TableHead>
                                            <TableHead className="text-right">Azioni</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {quizzes.map((quiz) => (
                                            <TableRow key={quiz.id}>
                                                <TableCell className="max-w-xs whitespace-normal">{quiz.question}</TableCell>
                                                <TableCell>
                                                    <Badge variant={difficultyBadgeVariant[quiz.difficulty]}>
                                                        {difficultyLabels[quiz.difficulty]}
                                                    </Badge>
                                                </TableCell>
                                                <TableCell>{quiz.subject}</TableCell>
                                                <TableCell>{quiz.points}</TableCell>
                                                <TableCell className="text-right">
                                                    <Button variant="outline" size="sm" onClick={() => addQuiz(quiz.id)}>
                                                        Aggiungi
                                                    </Button>
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            )}
                        </>
                    )}
                </CardContent>
            </Card>
        </AppLayout>
    );
}
