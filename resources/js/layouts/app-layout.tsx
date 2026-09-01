import { router } from '@inertiajs/react';
import { ChevronDownIcon } from 'lucide-react';
import type { PropsWithChildren } from 'react';

import FlashAlerts from '@/components/flash-alerts';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { AuthUser, Nav, SharedPageProps } from '@/types';

interface AppLayoutProps {
    user: AuthUser;
    nav: Nav;
    flash: SharedPageProps['flash'];
}

function NavMenu({ label, children }: PropsWithChildren<{ label: string }>) {
    return (
        <DropdownMenu>
            <DropdownMenuTrigger className="flex items-center gap-1 text-muted-foreground outline-none hover:text-foreground focus-visible:text-foreground">
                {label}
                <ChevronDownIcon className="size-3.5" />
            </DropdownMenuTrigger>
            <DropdownMenuContent align="start">{children}</DropdownMenuContent>
        </DropdownMenu>
    );
}

export default function AppLayout({ user, nav, flash, children }: PropsWithChildren<AppLayoutProps>) {
    return (
        <div className="min-h-screen bg-background">
            <header className="border-b">
                <div className="mx-auto flex h-16 max-w-5xl items-center justify-between px-6">
                    <a href={nav.dashboard} className="flex items-center gap-2">
                        <img src="/logo/Mexamlogo.png" alt="MEXAM" className="h-8 w-auto" />
                    </a>

                    <nav className="flex items-center gap-6 text-sm">
                        {user.isTeacher && (
                            <NavMenu label="Quiz">
                                <DropdownMenuItem asChild>
                                    <a href={nav.quizList}>Elenco quiz</a>
                                </DropdownMenuItem>
                                <DropdownMenuItem asChild>
                                    <a href={nav.quizCreate}>Crea quiz</a>
                                </DropdownMenuItem>
                            </NavMenu>
                        )}

                        <NavMenu label="Librerie">
                            {user.isTeacher && (
                                <>
                                    <DropdownMenuItem asChild>
                                        <a href={nav.libraryCreate}>Crea libreria</a>
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                </>
                            )}
                            <DropdownMenuItem asChild>
                                <a href={nav.libraryList}>Elenco librerie</a>
                            </DropdownMenuItem>
                        </NavMenu>

                        <NavMenu label="Esami">
                            {user.isTeacher && (
                                <>
                                    <DropdownMenuItem asChild>
                                        <a href={nav.examCreate}>Crea esame</a>
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                </>
                            )}
                            <DropdownMenuItem asChild>
                                <a href={nav.examList}>Elenco esami</a>
                            </DropdownMenuItem>
                        </NavMenu>

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

            <main className="mx-auto max-w-5xl px-6 py-8">
                <FlashAlerts flash={flash} />
                {children}
            </main>
        </div>
    );
}
