import { Head, useForm } from '@inertiajs/react';
import type { ChangeEvent, FormEvent } from 'react';
import { useEffect, useState } from 'react';

import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import type { SharedPageProps } from '@/types';

interface ProfileUser {
    name: string;
    email: string;
    phone: string | null;
    city: string | null;
    avatarUrl: string | null;
}

interface ProfileProps extends SharedPageProps {
    profileUser: ProfileUser;
}

export default function Profile({ auth, nav, flash, profileUser }: ProfileProps) {
    const user = auth.user!;

    const { data, setData, post, processing, errors } = useForm<{
        name: string;
        email: string;
        phone: string;
        city: string;
        current_password: string;
        password: string;
        confirm_password: string;
        avatar: File | null;
    }>({
        name: profileUser.name,
        email: profileUser.email,
        phone: profileUser.phone ?? '',
        city: profileUser.city ?? '',
        current_password: '',
        password: '',
        confirm_password: '',
        avatar: null,
    });

    const [avatarPreview, setAvatarPreview] = useState<string | null>(null);

    useEffect(() => {
        return () => {
            if (avatarPreview) {
                URL.revokeObjectURL(avatarPreview);
            }
        };
    }, [avatarPreview]);

    function onAvatarChange(e: ChangeEvent<HTMLInputElement>) {
        const file = e.target.files?.[0] ?? null;
        setData('avatar', file);

        if (avatarPreview) {
            URL.revokeObjectURL(avatarPreview);
        }
        setAvatarPreview(file ? URL.createObjectURL(file) : null);
    }

    function submit(e: FormEvent) {
        e.preventDefault();
        post(route('user.profile.store'), { forceFormData: true });
    }

    return (
        <AppLayout user={user} nav={nav} flash={flash}>
            <Head title="Profilo" />

            <Card className="mx-auto max-w-xl">
                <CardHeader>
                    <CardTitle>Il tuo profilo</CardTitle>
                    <CardDescription>Aggiorna i tuoi dati e, se vuoi, la password.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="flex items-center gap-4">
                            <Avatar className="size-16">
                                {(avatarPreview ?? profileUser.avatarUrl) && (
                                    <AvatarImage src={avatarPreview ?? profileUser.avatarUrl ?? undefined} alt={profileUser.name} />
                                )}
                                <AvatarFallback>{profileUser.name.charAt(0).toUpperCase()}</AvatarFallback>
                            </Avatar>
                            <div className="space-y-2">
                                <Label htmlFor="avatar">Avatar</Label>
                                <Input id="avatar" type="file" accept="image/jpeg,image/png,image/webp" onChange={onAvatarChange} />
                                {errors.avatar && <p className="text-sm text-destructive">{errors.avatar}</p>}
                            </div>
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="name">Nome</Label>
                            <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} />
                            {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="email">Email</Label>
                            <Input id="email" value={data.email} onChange={(e) => setData('email', e.target.value)} />
                            {errors.email && <p className="text-sm text-destructive">{errors.email}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="phone">Telefono</Label>
                            <Input id="phone" value={data.phone} onChange={(e) => setData('phone', e.target.value)} />
                            {errors.phone && <p className="text-sm text-destructive">{errors.phone}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="city">Città</Label>
                            <Input id="city" value={data.city} onChange={(e) => setData('city', e.target.value)} />
                            {errors.city && <p className="text-sm text-destructive">{errors.city}</p>}
                        </div>

                        <div className="space-y-2 border-t pt-4">
                            <Label htmlFor="current_password">Password attuale</Label>
                            <Input
                                id="current_password"
                                type="password"
                                value={data.current_password}
                                onChange={(e) => setData('current_password', e.target.value)}
                                required={data.password !== ''}
                            />
                            <p className="text-muted-foreground text-sm">Richiesta solo per cambiare la password.</p>
                            {errors.current_password && <p className="text-sm text-destructive">{errors.current_password}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="password">Nuova password</Label>
                            <Input
                                id="password"
                                type="password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                            />
                            {errors.password && <p className="text-sm text-destructive">{errors.password}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="confirm_password">Conferma nuova password</Label>
                            <Input
                                id="confirm_password"
                                type="password"
                                value={data.confirm_password}
                                onChange={(e) => setData('confirm_password', e.target.value)}
                                required={data.password !== ''}
                            />
                            {errors.confirm_password && <p className="text-sm text-destructive">{errors.confirm_password}</p>}
                        </div>

                        <Button type="submit" disabled={processing}>
                            Salva profilo
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
