import { Head } from '@inertiajs/react';

import { Button } from '@/components/ui/button';
import type { SharedPageProps } from '@/types';

interface WelcomeProps extends SharedPageProps {
    canRegister: boolean;
}

export default function Welcome({ nav, canRegister }: WelcomeProps) {
    return (
        <>
            <Head title="Benvenuto" />

            <div className="flex min-h-screen flex-col items-center justify-center gap-8 px-6 text-center">
                <img src="/logo/Mexamlogo.png" alt="MEXAM" className="h-16 w-auto" />

                <div className="max-w-md space-y-2">
                    <h1 className="text-2xl font-semibold tracking-tight">MEXAM</h1>
                    <p className="text-muted-foreground">
                        Crea esami, raccogli le risposte dei tuoi studenti e correggile da un&apos;unica piattaforma.
                    </p>
                </div>

                <div className="flex items-center gap-3">
                    <Button asChild>
                        <a href={nav.login}>Accedi</a>
                    </Button>
                    {canRegister && (
                        <Button asChild variant="outline">
                            <a href={nav.register}>Registrati</a>
                        </Button>
                    )}
                </div>
            </div>
        </>
    );
}
