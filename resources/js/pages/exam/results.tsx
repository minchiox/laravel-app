import { Head } from '@inertiajs/react';

import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import type { SharedPageProps } from '@/types';

interface ExamResultsProps extends SharedPageProps {
    exam: { id: number; exam_name: string };
    users: { id: number; name: string; user_points: number | null }[];
}

export default function ExamResults({ auth, nav, flash, exam, users }: ExamResultsProps) {
    const user = auth.user!;

    return (
        <AppLayout user={user} nav={nav} flash={flash}>
            <Head title={`Risultati — ${exam.exam_name}`} />

            <h1 className="mb-6 text-2xl font-semibold tracking-tight">Risultati di “{exam.exam_name}”</h1>

            {users.length === 0 ? (
                <p className="text-muted-foreground text-sm">Nessuno studente ha ancora consegnato questo esame.</p>
            ) : (
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Studente</TableHead>
                            <TableHead>Punteggio</TableHead>
                            <TableHead className="text-right">Azioni</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {users.map((student) => (
                            <TableRow key={student.id}>
                                <TableCell>{student.name}</TableCell>
                                <TableCell>{student.user_points ?? '—'}</TableCell>
                                <TableCell className="text-right">
                                    <div className="flex justify-end gap-2">
                                        <Button asChild variant="outline" size="sm">
                                            <a href={route('display.users.answer', { iduser: student.id, idexam: exam.id })}>
                                                Risultato
                                            </a>
                                        </Button>
                                        <Button asChild variant="outline" size="sm">
                                            <a
                                                href={route('print.exam', { idexam: exam.id, iduser: student.id })}
                                                target="_blank"
                                                rel="noreferrer"
                                            >
                                                Stampa
                                            </a>
                                        </Button>
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
