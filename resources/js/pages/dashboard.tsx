import { Head } from '@inertiajs/react';

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import type { SharedPageProps } from '@/types';

export default function Dashboard({ auth, nav, flash }: SharedPageProps) {
    // La rotta e' dietro il middleware 'auth': auth.user esiste sempre qui.
    const user = auth.user!;

    return (
        <AppLayout user={user} nav={nav}>
            <Head title="Dashboard" />

            {flash.success && (
                <div className="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">
                    {flash.success}
                </div>
            )}
            {flash.error && (
                <div className="mb-6 rounded-md border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm text-destructive">
                    {flash.error}
                </div>
            )}

            <h1 className="mb-1 text-2xl font-semibold tracking-tight">Ciao, {user.name}</h1>
            <p className="mb-8 text-muted-foreground">
                {user.isTeacher ? 'Gestisci quiz, librerie ed esami dei tuoi studenti.' : 'Qui trovi gli esami e le librerie a cui hai accesso.'}
            </p>

            <div className="grid gap-4 sm:grid-cols-2">
                {user.isTeacher && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Quiz</CardTitle>
                            <CardDescription>Crea e gestisci le domande da riusare negli esami.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <a href={nav.quizCreate} className="text-sm font-medium text-primary hover:underline">
                                Crea un nuovo quiz &rarr;
                            </a>
                        </CardContent>
                    </Card>
                )}

                <Card>
                    <CardHeader>
                        <CardTitle>Librerie</CardTitle>
                        <CardDescription>Raccolte di quiz organizzate per argomento.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <a href={nav.libraryList} className="text-sm font-medium text-primary hover:underline">
                            Vai alle librerie &rarr;
                        </a>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Esami</CardTitle>
                        <CardDescription>
                            {user.isTeacher ? "Crea, correggi e stampa gli esami dei tuoi studenti." : 'Svolgi gli esami assegnati.'}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <a href={nav.examList} className="text-sm font-medium text-primary hover:underline">
                            Vai agli esami &rarr;
                        </a>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
