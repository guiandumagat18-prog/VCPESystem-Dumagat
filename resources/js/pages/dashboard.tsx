import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import ClinicLayout from '@/layouts/clinic-layout';
import { Head, Link } from '@inertiajs/react';
import { CalendarDays, ClipboardList, PawPrint, Plus, Stethoscope, Users } from 'lucide-react';

interface DashboardStats {
    pets: number;
    owners: number;
    veterinarians: number;
    visits: number;
}

interface RecentVisit {
    id: number;
    examination_date: string;
    diagnosis: string;
    procedure_cost: string;
    payment_status: 'pending' | 'paid' | 'cancelled';
    pet: { name: string };
    veterinarian: { name: string };
}

const statCards = [
    { key: 'pets', label: 'Registered pets', icon: PawPrint },
    { key: 'owners', label: 'Pet owners', icon: Users },
    { key: 'veterinarians', label: 'Veterinarians', icon: Stethoscope },
    { key: 'visits', label: 'Medical visits', icon: ClipboardList },
] as const;

function formatDate(value: string): string {
    const date = value.slice(0, 10);
    return new Date(`${date}T12:00:00`).toLocaleDateString();
}

function formatCurrency(value: string): string {
    return Number(value).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
}

export default function Dashboard({ stats, recentVisits }: { stats: DashboardStats; recentVisits: RecentVisit[] }) {
    return (
        <ClinicLayout>
            <Head title="Dashboard" />
            <div className="space-y-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div className="space-y-1">
                        <h1 className="text-3xl font-semibold tracking-tight">Dashboard</h1>
                        <p className="text-muted-foreground text-sm">An overview of your clinic’s records.</p>
                    </div>
                    <Button asChild>
                        <Link href={route('visits.create')}>
                            <Plus aria-hidden="true" />
                            Record a visit
                        </Link>
                    </Button>
                </div>

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    {statCards.map(({ key, label, icon: Icon }) => (
                        <Card key={key}>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardDescription>{label}</CardDescription>
                                <Icon className="text-muted-foreground size-4" aria-hidden="true" />
                            </CardHeader>
                            <CardContent>
                                <CardTitle className="text-2xl">{stats[key].toLocaleString()}</CardTitle>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                <Card>
                    <CardHeader className="flex flex-row items-center justify-between gap-4">
                        <div className="space-y-1">
                            <CardTitle>Recent medical visits</CardTitle>
                            <CardDescription>The latest five visits recorded in the system.</CardDescription>
                        </div>
                        <Button variant="outline" size="sm" asChild>
                            <Link href={route('visits.index')}>View all</Link>
                        </Button>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Date</TableHead>
                                    <TableHead>Pet</TableHead>
                                    <TableHead className="hidden md:table-cell">Veterinarian</TableHead>
                                    <TableHead className="hidden lg:table-cell">Diagnosis</TableHead>
                                    <TableHead className="text-right">Cost</TableHead>
                                    <TableHead className="hidden text-right sm:table-cell">Payment</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {recentVisits.length ? (
                                    recentVisits.map((visit) => (
                                        <TableRow key={visit.id}>
                                            <TableCell className="whitespace-nowrap">
                                                <CalendarDays className="text-muted-foreground mr-2 inline size-4" aria-hidden="true" />
                                                {formatDate(visit.examination_date)}
                                            </TableCell>
                                            <TableCell className="font-medium">{visit.pet.name}</TableCell>
                                            <TableCell className="hidden md:table-cell">{visit.veterinarian.name}</TableCell>
                                            <TableCell className="hidden max-w-xs truncate lg:table-cell">{visit.diagnosis}</TableCell>
                                            <TableCell className="text-right whitespace-nowrap">{formatCurrency(visit.procedure_cost)}</TableCell>
                                            <TableCell className="hidden text-right capitalize sm:table-cell">{visit.payment_status}</TableCell>
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={6} className="text-muted-foreground h-24 text-center">
                                            No medical visits have been recorded yet.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </ClinicLayout>
    );
}
