import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import type { SharedPageProps } from '@/types';

export default function ExamCreate({ auth, nav, flash }: SharedPageProps) {
    const user = auth.user!;

    const { data, setData, post, processing, errors } = useForm({
        exam_name: '',
        startAt: '',
        dueAt: '',
    });

    function submit(e: FormEvent) {
        e.preventDefault();
        post(route('exam.store'));
    }

    return (
        <AppLayout user={user} nav={nav} flash={flash}>
            <Head title="Crea esame" />

            <Card className="mx-auto max-w-xl">
                <CardHeader>
                    <CardTitle>Crea un nuovo esame</CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="exam_name">Nome</Label>
                            <Input id="exam_name" value={data.exam_name} onChange={(e) => setData('exam_name', e.target.value)} />
                            {errors.exam_name && <p className="text-sm text-destructive">{errors.exam_name}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="startAt">Inizio</Label>
                            <Input
                                id="startAt"
                                type="datetime-local"
                                value={data.startAt}
                                onChange={(e) => setData('startAt', e.target.value)}
                            />
                            {errors.startAt && <p className="text-sm text-destructive">{errors.startAt}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="dueAt">Fine</Label>
                            <Input
                                id="dueAt"
                                type="datetime-local"
                                value={data.dueAt}
                                onChange={(e) => setData('dueAt', e.target.value)}
                            />
                            {errors.dueAt && <p className="text-sm text-destructive">{errors.dueAt}</p>}
                        </div>

                        <Button type="submit" disabled={processing}>
                            Crea esame
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
