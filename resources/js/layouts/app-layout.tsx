import { router } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';

import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { AuthUser, Nav } from '@/types';

interface AppLayoutProps {
    user: AuthUser;
    nav: Nav;
}

export default function AppLayout({ user, nav, children }: PropsWithChildren<AppLayoutProps>) {
    return (
        <div className="min-h-screen bg-background">
            <header className="border-b">
                <div className="mx-auto flex h-16 max-w-5xl items-center justify-between px-6">
                    <a href={nav.dashboard} className="flex items-center gap-2">
                        <img src="/logo/Mexamlogo.png" alt="MEXAM" className="h-8 w-auto" />
                    </a>

                    <nav className="flex items-center gap-6 text-sm">
                        {user.isTeacher && (
                            <>
                                <a href={nav.quizList} className="text-muted-foreground hover:text-foreground">
                                    Quiz
                                </a>
                                <a href={nav.libraryCreate} className="text-muted-foreground hover:text-foreground">
                                    Librerie
                                </a>
                                <a href={nav.examCreate} className="text-muted-foreground hover:text-foreground">
                                    Esami
                                </a>
                            </>
                        )}
                        <a href={nav.libraryList} className="text-muted-foreground hover:text-foreground">
                            Elenco librerie
                        </a>
                        <a href={nav.examList} className="text-muted-foreground hover:text-foreground">
                            Elenco esami
                        </a>

                        <DropdownMenu>
                            <DropdownMenuTrigger className="flex items-center gap-2 rounded-full outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                <Avatar>
                                    {user.avatarUrl && <AvatarImage src={user.avatarUrl} alt={user.name} />}
                                    <AvatarFallback>{user.name.charAt(0).toUpperCase()}</AvatarFallback>
                                </Avatar>
                                <span className="font-medium">{user.name}</span>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuLabel className="text-muted-foreground font-normal">{user.email}</DropdownMenuLabel>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem asChild>
                                    <a href={nav.dashboard}>Dashboard</a>
                                </DropdownMenuItem>
                                <DropdownMenuItem asChild>
                                    <a href={nav.profile}>Profilo</a>
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem onSelect={() => router.post(nav.signout)}>Esci</DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </nav>
                </div>
            </header>

            <main className="mx-auto max-w-5xl px-6 py-8">{children}</main>
        </div>
    );
}
