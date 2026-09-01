import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';

import FlashAlerts from '@/components/flash-alerts';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { SharedPageProps } from '@/types';

export default function Login({ nav, flash }: SharedPageProps) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    function submit(e: FormEvent) {
        e.preventDefault();
        post(route('login.custom'));
    }

    return (
        <div className="flex min-h-screen flex-col items-center justify-center gap-8 px-6">
            <Head title="Accedi" />

            <a href={nav.dashboard} className="flex items-center gap-2">
                <img src="/logo/Mexamlogo.png" alt="MEXAM" className="h-10 w-auto" />
            </a>

            <div className="w-full max-w-sm">
                <FlashAlerts flash={flash} />

                <Card>
                    <CardHeader>
                        <CardTitle>Accedi</CardTitle>
                        <CardDescription>Entra con le tue credenziali MEXAM.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="email">Email</Label>
                                <Input id="email" autoFocus value={data.email} onChange={(e) => setData('email', e.target.value)} />
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

                            <div className="flex items-center gap-2">
                                <Checkbox
                                    id="remember"
                                    checked={data.remember}
                                    onCheckedChange={(checked) => setData('remember', checked === true)}
                                />
                                <Label htmlFor="remember" className="font-normal">
                                    Ricordami
                                </Label>
                            </div>

                            <Button type="submit" className="w-full" disabled={processing}>
                                Accedi
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <p className="mt-4 text-center text-sm text-muted-foreground">
                    Non hai un account?{' '}
                    <a href={nav.register} className="font-medium text-primary hover:underline">
                        Registrati
                    </a>
                </p>
            </div>
        </div>
    );
}
