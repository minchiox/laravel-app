import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { difficultyLabels } from '@/lib/quiz-labels';
import type { SharedPageProps } from '@/types';
import type { Difficulty, QuizRow } from '@/types/models';

interface QuizEditProps extends SharedPageProps {
    quiz: QuizRow;
}

export default function QuizEdit({ auth, nav, flash, quiz }: QuizEditProps) {
    const user = auth.user!;

    const { data, setData, put, processing, errors } = useForm({
        question: quiz.question,
        'answer-type': (quiz.answer_text !== null ? 'open' : 'close') as 'open' | 'close',
        answer: (quiz.answer === null ? '' : quiz.answer ? '1' : '0') as '' | '0' | '1',
        answer_text: quiz.answer_text ?? '',
        subject: quiz.subject,
        difficulty: quiz.difficulty as Difficulty,
        points: String(quiz.points),
    });

    function submit(e: FormEvent) {
        e.preventDefault();
        put(route('quiz.update', quiz.id));
    }

    return (
        <AppLayout user={user} nav={nav} flash={flash}>
            <Head title="Modifica quiz" />

            <Card className="mx-auto max-w-xl">
                <CardHeader>
                    <CardTitle>Modifica quiz</CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="question">Domanda</Label>
                            <Input id="question" value={data.question} onChange={(e) => setData('question', e.target.value)} />
                            {errors.question && <p className="text-sm text-destructive">{errors.question}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="answer-type">Tipo di risposta</Label>
                            <Select
                                value={data['answer-type']}
                                onValueChange={(value: 'open' | 'close') => setData('answer-type', value)}
                            >
                                <SelectTrigger id="answer-type" className="w-full">
                                    <SelectValue placeholder="Seleziona un tipo" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="close">Vero/Falso</SelectItem>
                                    <SelectItem value="open">Risposta aperta</SelectItem>
                                </SelectContent>
                            </Select>
                            {errors['answer-type'] && <p className="text-sm text-destructive">{errors['answer-type']}</p>}
                        </div>

                        {data['answer-type'] === 'close' && (
                            <div className="space-y-2">
                                <Label>Risposta corretta</Label>
                                <RadioGroup value={data.answer} onValueChange={(value: '0' | '1') => setData('answer', value)}>
                                    <div className="flex items-center gap-2">
                                        <RadioGroupItem value="1" id="answer-true" />
                                        <Label htmlFor="answer-true" className="font-normal">
                                            Vero
                                        </Label>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <RadioGroupItem value="0" id="answer-false" />
                                        <Label htmlFor="answer-false" className="font-normal">
                                            Falso
                                        </Label>
                                    </div>
                                </RadioGroup>
                                {errors.answer && <p className="text-sm text-destructive">{errors.answer}</p>}
                            </div>
                        )}

                        {data['answer-type'] === 'open' && (
                            <div className="space-y-2">
                                <Label htmlFor="answer_text">Risposta corretta</Label>
                                <Input
                                    id="answer_text"
                                    value={data.answer_text}
                                    onChange={(e) => setData('answer_text', e.target.value)}
                                />
                                {errors.answer_text && <p className="text-sm text-destructive">{errors.answer_text}</p>}
                            </div>
                        )}

                        <div className="space-y-2">
                            <Label htmlFor="subject">Materia</Label>
                            <Input id="subject" value={data.subject} onChange={(e) => setData('subject', e.target.value)} />
                            {errors.subject && <p className="text-sm text-destructive">{errors.subject}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="difficulty">Difficoltà</Label>
                            <Select value={data.difficulty} onValueChange={(value: Difficulty) => setData('difficulty', value)}>
                                <SelectTrigger id="difficulty" className="w-full">
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
                            {errors.difficulty && <p className="text-sm text-destructive">{errors.difficulty}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="points">Punti</Label>
                            <Input
                                id="points"
                                type="number"
                                min={1}
                                value={data.points}
                                onChange={(e) => setData('points', e.target.value)}
                            />
                            {errors.points && <p className="text-sm text-destructive">{errors.points}</p>}
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
