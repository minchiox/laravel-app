import { Head, router } from '@inertiajs/react';

import ConfirmDeleteButton from '@/components/confirm-delete-button';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import type { SharedPageProps } from '@/types';
import type { ExamRow } from '@/types/models';

interface ExamListProps extends SharedPageProps {
    availableExam: ExamRow[];
}

export default function ExamList({ auth, nav, flash, availableExam }: ExamListProps) {
    const user = auth.user!;

    return (
        <AppLayout user={user} nav={nav} flash={flash}>
            <Head title="Esami" />

            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-semibold tracking-tight">Elenco esami</h1>
                {user.isTeacher && (
                    <Button asChild>
                        <a href={nav.examCreate}>Crea nuovo esame</a>
                    </Button>
                )}
            </div>

            {availableExam.length === 0 ? (
                <p className="text-muted-foreground text-sm">Non ci sono ancora esami.</p>
            ) : (
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nome</TableHead>
                            <TableHead>Inizio</TableHead>
                            <TableHead>Fine</TableHead>
                            <TableHead>Punti totali</TableHead>
                            <TableHead>Stato</TableHead>
                            <TableHead className="text-right">Azioni</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {availableExam.map((exam) => (
                            <TableRow key={exam.id}>
                                <TableCell className="whitespace-normal">{exam.exam_name}</TableCell>
                                <TableCell>{new Date(exam.startAt).toLocaleString('it-IT')}</TableCell>
                                <TableCell>{new Date(exam.dueAt).toLocaleString('it-IT')}</TableCell>
                                <TableCell>{exam.total_points ?? 0}</TableCell>
                                <TableCell>
                                    <Badge variant={exam.is_open ? 'default' : 'outline'}>
                                        {exam.is_open ? 'Aperto' : 'Chiuso'}
                                    </Badge>
                                </TableCell>
                                <TableCell className="text-right">
                                    <div className="flex flex-wrap justify-end gap-2">
                                        {user.isTeacher ? (
                                            <>
                                                <Button asChild variant="outline" size="sm">
                                                    <a href={route('examquiz.index', exam.id)}>Aggiungi quiz</a>
                                                </Button>
                                                <Button asChild variant="outline" size="sm">
                                                    <a href={route('exam.quiz', exam.id)}>Quiz</a>
                                                </Button>
                                                <Button asChild variant="outline" size="sm">
                                                    <a href={route('exam.edit', exam.id)}>Modifica</a>
                                                </Button>
                                                <Button asChild variant="outline" size="sm">
                                                    <a href={route('print.blankexam', exam.id)} target="_blank" rel="noreferrer">
                                                        Stampa
                                                    </a>
                                                </Button>
                                                <Button asChild variant="outline" size="sm">
                                                    <a href={route('show.users.results.index', exam.id)}>Risultati</a>
                                                </Button>
                                                <ConfirmDeleteButton
                                                    description={`L'esame "${exam.exam_name}" verrà eliminato definitivamente.`}
                                                    onConfirm={() => router.delete(route('exam.destroy', exam.id))}
                                                />
                                            </>
                                        ) : (
                                            <Button asChild size="sm">
                                                <a href={route('exam.access', exam.id)}>Partecipa</a>
                                            </Button>
                                        )}
                                    </div>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            )}
        </AppLayout>
    );
}
