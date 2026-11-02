import { Button } from '@/components/ui/button';
import { type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { LogOut, PawPrint } from 'lucide-react';

export default function ClinicLayout({ children }: { children: React.ReactNode }) {
    const { auth } = usePage<SharedData>().props;

    return (
        <div className="bg-muted/30 min-h-screen">
            <header className="bg-background border-b">
                <div className="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
                    <Link href={route('dashboard')} className="flex items-center gap-2 font-semibold tracking-tight">
                        <PawPrint className="size-5" aria-hidden="true" />
                        <span>Vet Clinic</span>
                    </Link>
                    <div className="flex items-center gap-2">
                        <Button variant="ghost" asChild>
                            <Link href={route('dashboard')}>Dashboard</Link>
                        </Button>
                        <Button variant="ghost" asChild>
                            <Link href={route('pets.index')}>Pets</Link>
                        </Button>
                        <Button variant="ghost" asChild>
                            <Link href={route('visits.index')}>Visits</Link>
                        </Button>
                        <span className="text-muted-foreground hidden text-sm sm:inline">{auth.user.name}</span>
                        <Button variant="outline" size="sm" asChild>
                            <Link href={route('logout')} method="post" as="button">
                                <LogOut aria-hidden="true" />
                                <span className="hidden sm:inline">Log out</span>
                            </Link>
                        </Button>
                    </div>
                </div>
            </header>
            <main className="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10">{children}</main>
        </div>
    );
}
