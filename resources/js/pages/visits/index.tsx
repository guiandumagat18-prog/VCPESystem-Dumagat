import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import ClinicLayout from '@/layouts/clinic-layout';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { CheckCircle2, ChevronLeft, ChevronRight, ClipboardList, Pencil, Plus } from 'lucide-react';

interface Visit {
    id: number;
    examination_date: string;
    diagnosis: string;
    procedure_cost: string;
    payment_status: 'pending' | 'paid' | 'cancelled';
    pet: { name: string; owner: string | null };
    veterinarian: string;
}

interface PaginatedVisits {
    data: Visit[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

function formatCurrency(value: string): string {
    return Number(value).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
}

export default function VisitsIndex({ visits }: { visits: PaginatedVisits }) {
    const { flash } = usePage<SharedData>().props;

    return (
        <ClinicLayout>
            <Head title="Medical visits" />
            <div className="space-y-6">
                {flash.success && (
                    <Alert>
                        <CheckCircle2 className="size-4" />
                        <AlertTitle>Saved</AlertTitle>
                        <AlertDescription>{flash.success}</AlertDescription>
                    </Alert>
                )}

                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div className="space-y-1">
                        <h1 className="text-3xl font-semibold tracking-tight">Medical visits</h1>
                        <p className="text-muted-foreground text-sm">Review visit records, diagnoses, and payments.</p>
                    </div>
                    <Button asChild>
                        <Link href={route('visits.create')}>
                            <Plus aria-hidden="true" />
                            Record a visit
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Visit records</CardTitle>
                        <CardDescription>
                            {visits.total} {visits.total === 1 ? 'visit' : 'visits'} recorded
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Date</TableHead>
                                    <TableHead>Pet / owner</TableHead>
                                    <TableHead className="hidden md:table-cell">Veterinarian</TableHead>
                                    <TableHead className="hidden lg:table-cell">Diagnosis</TableHead>
                                    <TableHead>Payment</TableHead>
                                    <TableHead className="text-right">Cost</TableHead>
                                    <TableHead className="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {visits.data.length > 0 ? (
                                    visits.data.map((visit) => (
                                        <TableRow key={visit.id}>
                                            <TableCell className="whitespace-nowrap">{visit.examination_date}</TableCell>
                                            <TableCell>
                                                <div className="font-medium">{visit.pet.name}</div>
                                                <div className="text-muted-foreground text-xs">{visit.pet.owner ?? 'Unknown owner'}</div>
                                            </TableCell>
                                            <TableCell className="hidden md:table-cell">{visit.veterinarian}</TableCell>
                                            <TableCell className="hidden max-w-xs truncate lg:table-cell">{visit.diagnosis}</TableCell>
                                            <TableCell>
                                                <Badge variant={visit.payment_status === 'paid' ? 'default' : 'secondary'} className="capitalize">
                                                    {visit.payment_status}
                                                </Badge>
                                            </TableCell>
                                            <TableCell className="text-right whitespace-nowrap">{formatCurrency(visit.procedure_cost)}</TableCell>
                                            <TableCell className="text-right">
                                                <Button variant="ghost" size="icon" asChild>
                                                    <Link href={route('visits.edit', visit.id)} aria-label={`Edit visit for ${visit.pet.name}`}>
                                                        <Pencil aria-hidden="true" />
                                                    </Link>
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={7} className="text-muted-foreground h-24 text-center">
                                            <ClipboardList className="mx-auto mb-2 size-5" aria-hidden="true" />
                                            No medical visits have been recorded yet.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <div className="mt-4 flex flex-col gap-3 border-t pt-4 sm:flex-row sm:items-center sm:justify-between">
                            <p className="text-muted-foreground text-sm">
                                {visits.from && visits.to ? `Showing ${visits.from}–${visits.to} of ${visits.total} visits` : 'No visits to show'}
                            </p>
                            <div className="flex items-center gap-2">
                                {visits.prev_page_url ? (
                                    <Button variant="outline" size="sm" asChild>
                                        <Link href={visits.prev_page_url}>
                                            <ChevronLeft aria-hidden="true" />
                                            Previous
                                        </Link>
                                    </Button>
                                ) : (
                                    <Button variant="outline" size="sm" disabled>
                                        <ChevronLeft aria-hidden="true" />
                                        Previous
                                    </Button>
                                )}
                                <span className="text-muted-foreground px-2 text-sm">
                                    Page {visits.current_page} of {visits.last_page}
                                </span>
                                {visits.next_page_url ? (
                                    <Button variant="outline" size="sm" asChild>
                                        <Link href={visits.next_page_url}>
                                            Next
                                            <ChevronRight aria-hidden="true" />
                                        </Link>
                                    </Button>
                                ) : (
                                    <Button variant="outline" size="sm" disabled>
                                        Next
                                        <ChevronRight aria-hidden="true" />
                                    </Button>
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </ClinicLayout>
    );
}
