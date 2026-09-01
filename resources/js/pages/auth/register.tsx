import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';

import FlashAlerts from '@/components/flash-alerts';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { SharedPageProps } from '@/types';

export default function Register({ nav, flash }: SharedPageProps) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
    });

    function submit(e: FormEvent) {
        e.preventDefault();
        post(route('register.custom'));
    }

    return (
        <div className="flex min-h-screen flex-col items-center justify-center gap-8 px-6">
            <Head title="Registrati" />

            <a href={nav.dashboard} className="flex items-center gap-2">
                <img src="/logo/Mexamlogo.png" alt="MEXAM" className="h-10 w-auto" />
            </a>

            <div className="w-full max-w-sm">
                <FlashAlerts flash={flash} />

                <Card>
                    <CardHeader>
                        <CardTitle>Registrati</CardTitle>
                        <CardDescription>Crea un nuovo account studente.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="name">Nome</Label>
                                <Input id="name" autoFocus value={data.name} onChange={(e) => setData('name', e.target.value)} />
                                {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="email">Email</Label>
                                <Input id="email" value={data.email} onChange={(e) => setData('email', e.target.value)} />
                                {errors.email && <p className="text-sm text-destructive">{errors.email}</p>}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="password">Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                />
                                {errors.password && <p className="text-sm text-destructive">{errors.password}</p>}
                            </div>

                            <Button type="submit" className="w-full" disabled={processing}>
                                Registrati
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <p className="mt-4 text-center text-sm text-muted-foreground">
                    Hai già un account?{' '}
                    <a href={nav.login} className="font-medium text-primary hover:underline">
                        Accedi
                    </a>
                </p>
            </div>
        </div>
    );
}
