import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { difficultyLabels } from '@/lib/quiz-labels';
import type { SharedPageProps } from '@/types';
import type { Difficulty, LibraryRow } from '@/types/models';

interface LibraryEditProps extends SharedPageProps {
    library: LibraryRow;
}

export default function LibraryEdit({ auth, nav, flash, library }: LibraryEditProps) {
    const user = auth.user!;

    const { data, setData, put, processing, errors } = useForm({
        library_name: library.library_name,
        library_subject: library.library_subject,
        library_difficulty: library.library_difficulty,
    });

    function submit(e: FormEvent) {
        e.preventDefault();
        put(route('library.update', library.id));
    }

    return (
        <AppLayout user={user} nav={nav} flash={flash}>
            <Head title="Modifica libreria" />

            <Card className="mx-auto max-w-xl">
                <CardHeader>
                    <CardTitle>Modifica libreria</CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="library_name">Nome</Label>
                            <Input
                                id="library_name"
                                value={data.library_name}
                                onChange={(e) => setData('library_name', e.target.value)}
                            />
                            {errors.library_name && <p className="text-sm text-destructive">{errors.library_name}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="library_subject">Materia</Label>
                            <Input
                                id="library_subject"
                                value={data.library_subject}
                                onChange={(e) => setData('library_subject', e.target.value)}
                            />
                            {errors.library_subject && <p className="text-sm text-destructive">{errors.library_subject}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="library_difficulty">Difficoltà</Label>
                            <Select
                                value={data.library_difficulty}
                                onValueChange={(value: Difficulty) => setData('library_difficulty', value)}
                            >
                                <SelectTrigger id="library_difficulty" className="w-full">
                                    <SelectValue placeholder="Seleziona un livello" />
                                </SelectTrigger>
                                <SelectContent>
                                    {(Object.keys(difficultyLabels) as Difficulty[]).map((level) => (
                                        <SelectItem key={level} value={level}>
                                            {difficultyLabels[level]}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.library_difficulty && (
                                <p className="text-sm text-destructive">{errors.library_difficulty}</p>
                            )}
                        </div>

                        <Button type="submit" disabled={processing}>
                            Salva modifiche
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
