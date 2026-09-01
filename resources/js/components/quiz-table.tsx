import type { ReactNode } from 'react';

import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { difficultyBadgeVariant, difficultyLabels } from '@/lib/quiz-labels';
import type { QuizRow } from '@/types/models';

interface QuizTableProps {
    quizzes: QuizRow[];
    renderActions?: (quiz: QuizRow) => ReactNode;
    emptyMessage?: string;
}

export default function QuizTable({ quizzes, renderActions, emptyMessage = 'Nessun quiz trovato.' }: QuizTableProps) {
    if (quizzes.length === 0) {
        return <p className="text-muted-foreground text-sm">{emptyMessage}</p>;
    }

    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Domanda</TableHead>
                    <TableHead>Risposta</TableHead>
                    <TableHead>Difficoltà</TableHead>
                    <TableHead>Materia</TableHead>
                    <TableHead>Punti</TableHead>
                    <TableHead>Creato il</TableHead>
                    {renderActions && <TableHead className="text-right">Azioni</TableHead>}
                </TableRow>
            </TableHeader>
            <TableBody>
                {quizzes.map((quiz) => (
                    <TableRow key={quiz.id}>
                        <TableCell className="max-w-xs whitespace-normal">{quiz.question}</TableCell>
                        <TableCell>{quiz.answer_text ?? (quiz.answer ? 'Vero' : 'Falso')}</TableCell>
                        <TableCell>
                            <Badge variant={difficultyBadgeVariant[quiz.difficulty]}>{difficultyLabels[quiz.difficulty]}</Badge>
                        </TableCell>
                        <TableCell>{quiz.subject}</TableCell>
                        <TableCell>{quiz.points}</TableCell>
                        <TableCell>{new Date(quiz.created_at).toLocaleDateString('it-IT')}</TableCell>
                        {renderActions && (
                            <TableCell className="text-right">
                                <div className="flex justify-end gap-2">{renderActions(quiz)}</div>
                            </TableCell>
                        )}
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}
