import type { Difficulty } from '@/types/models';

export const difficultyLabels: Record<Difficulty, string> = {
    easy: 'Facile',
    medium: 'Media',
    hard: 'Difficile',
};

export const difficultyBadgeVariant: Record<Difficulty, 'secondary' | 'default' | 'destructive'> = {
    easy: 'secondary',
    medium: 'default',
    hard: 'destructive',
};
