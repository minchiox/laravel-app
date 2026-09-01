import { Head, router } from '@inertiajs/react';

import ConfirmDeleteButton from '@/components/confirm-delete-button';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { difficultyBadgeVariant, difficultyLabels } from '@/lib/quiz-labels';
import type { SharedPageProps } from '@/types';
import type { LibraryRow } from '@/types/models';

interface LibraryListProps extends SharedPageProps {
    availableLibraries: LibraryRow[];
}

export default function LibraryList({ auth, nav, flash, availableLibraries }: LibraryListProps) {
    const user = auth.user!;

    return (
        <AppLayout user={user} nav={nav} flash={flash}>
            <Head title="Librerie" />

            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-semibold tracking-tight">Elenco librerie</h1>
                {user.isTeacher && (
                    <Button asChild>
                        <a href={nav.libraryCreate}>Crea nuova libreria</a>
                    </Button>
                )}
            </div>

            {availableLibraries.length === 0 ? (
                <p className="text-muted-foreground text-sm">Non ci sono ancora librerie.</p>
            ) : (
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nome</TableHead>
                            <TableHead>Materia</TableHead>
                            <TableHead>Difficoltà</TableHead>
                            <TableHead>Creata il</TableHead>
                            {user.isTeacher && <TableHead className="text-right">Azioni</TableHead>}
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {availableLibraries.map((library) => (
                            <TableRow key={library.id}>
                                <TableCell className="whitespace-normal">{library.library_name}</TableCell>
                                <TableCell>{library.library_subject}</TableCell>
                                <TableCell>
                                    <Badge variant={difficultyBadgeVariant[library.library_difficulty]}>
                                        {difficultyLabels[library.library_difficulty]}
                                    </Badge>
                                </TableCell>
                                <TableCell>{new Date(library.created_at).toLocaleDateString('it-IT')}</TableCell>
                                {user.isTeacher && (
                                    <TableCell className="text-right">
                                        <div className="flex justify-end gap-2">
                                            <Button asChild variant="outline" size="sm">
                                                <a href={route('libraryquiz.index', library.id)}>Aggiungi quiz</a>
                                            </Button>
                                            <Button asChild variant="outline" size="sm">
                                                <a href={route('library.quiz', library.id)}>Quiz</a>
                                            </Button>
                                            <Button asChild variant="outline" size="sm">
                                                <a href={route('library.edit', library.id)}>Modifica</a>
                                            </Button>
                                            <ConfirmDeleteButton
                                                description={`La libreria "${library.library_name}" verrà eliminata definitivamente.`}
                                                onConfirm={() => router.delete(route('library.destroy', library.id))}
                                            />
                                        </div>
                                    </TableCell>
                                )}
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            )}
        </AppLayout>
    );
}
