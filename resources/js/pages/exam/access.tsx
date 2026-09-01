import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import AppLayout from '@/layouts/app-layout';
import type { SharedPageProps } from '@/types';

interface AccessQuiz {
    id: number;
    question: string;
    type: 'open' | 'close';
}

interface ExamAccessProps extends SharedPageProps {
    exam: { id: number; exam_name: string };
    quizzes: AccessQuiz[];
}

export default function ExamAccess({ auth, nav, flash, exam, quizzes }: ExamAccessProps) {
    const user = auth.user!;

    const initialData: Record<string, string> = { exam_id: String(exam.id) };
    for (const quiz of quizzes) {
        initialData[quiz.type === 'open' ? `answer_text${quiz.id}` : `answer${quiz.id}`] = '';
    }

    const { data, setData, post, processing } = useForm(initialData);

    function submit(e: FormEvent) {
        e.preventDefault();
        post(route('store.user.answer'));
    }

    return (
        <AppLayout user={user} nav={nav} flash={flash}>
            <Head title={exam.exam_name} />

            <h1 className="mb-6 text-2xl font-semibold tracking-tight">{exam.exam_name}</h1>

            <form onSubmit={submit} className="space-y-4">
                {quizzes.map((quiz, index) => (
                    <Card key={quiz.id}>
                        <CardHeader>
                            <CardTitle className="text-base">
                                {index + 1}. {quiz.question}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {quiz.type === 'close' ? (
                                <RadioGroup
                                    required
                                    value={data[`answer${quiz.id}`]}
                                    onValueChange={(value) => setData(`answer${quiz.id}`, value)}
                                >
                                    <div className="flex items-center gap-2">
                                        <RadioGroupItem value="1" id={`answer${quiz.id}-true`} />
                                        <Label htmlFor={`answer${quiz.id}-true`} className="font-normal">
                                            Vero
                                        </Label>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <RadioGroupItem value="0" id={`answer${quiz.id}-false`} />
                                        <Label htmlFor={`answer${quiz.id}-false`} className="font-normal">
                                            Falso
                                        </Label>
                                    </div>
                                </RadioGroup>
                            ) : (
                                <Input
                                    required
                                    value={data[`answer_text${quiz.id}`]}
                                    onChange={(e) => setData(`answer_text${quiz.id}`, e.target.value)}
                                />
                            )}
                        </CardContent>
                    </Card>
                ))}

                <Button type="submit" disabled={processing}>
                    Consegna
                </Button>
            </form>
        </AppLayout>
    );
}
