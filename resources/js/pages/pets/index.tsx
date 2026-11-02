import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import ClinicLayout from '@/layouts/clinic-layout';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { CheckCircle2, ChevronLeft, ChevronRight, Pencil, Plus } from 'lucide-react';

interface PetOwner {
    name: string;
    phone: string | null;
}

interface Pet {
    id: number;
    name: string;
    species: string;
    breed: string | null;
    owner: PetOwner | null;
}

interface PaginatedPets {
    data: Pet[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

export default function PetsIndex({ pets }: { pets: PaginatedPets }) {
    const { flash } = usePage<SharedData>().props;

    return (
        <ClinicLayout>
            <Head title="Pets" />
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
                        <h1 className="text-3xl font-semibold tracking-tight">Pets</h1>
                        <p className="text-muted-foreground text-sm">Manage pets and their owners.</p>
                    </div>
                    <Button asChild>
                        <Link href={route('pets.create')}>
                            <Plus aria-hidden="true" />
                            Add pet
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Pet directory</CardTitle>
                        <CardDescription>
                            {pets.total} {pets.total === 1 ? 'pet' : 'pets'} registered
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Species</TableHead>
                                    <TableHead>Breed</TableHead>
                                    <TableHead>Owner</TableHead>
                                    <TableHead className="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {pets.data.length > 0 ? (
                                    pets.data.map((pet) => (
                                        <TableRow key={pet.id}>
                                            <TableCell className="font-medium">{pet.name}</TableCell>
                                            <TableCell>
                                                <Badge variant="secondary">{pet.species}</Badge>
                                            </TableCell>
                                            <TableCell>{pet.breed || <span className="text-muted-foreground">Not specified</span>}</TableCell>
                                            <TableCell>
                                                <div>{pet.owner?.name ?? 'Unknown owner'}</div>
                                                {pet.owner?.phone && <div className="text-muted-foreground text-xs">{pet.owner.phone}</div>}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Button variant="ghost" size="icon" asChild>
                                                    <Link href={route('pets.edit', pet.id)} aria-label={`Edit ${pet.name}`}>
                                                        <Pencil aria-hidden="true" />
                                                    </Link>
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={5} className="text-muted-foreground h-24 text-center">
                                            No pets have been added yet.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <div className="mt-4 flex flex-col gap-3 border-t pt-4 sm:flex-row sm:items-center sm:justify-between">
                            <p className="text-muted-foreground text-sm">
                                {pets.from && pets.to ? `Showing ${pets.from}–${pets.to} of ${pets.total} pets` : 'No pets to show'}
                            </p>
                            <div className="flex items-center gap-2">
                                {pets.prev_page_url ? (
                                    <Button variant="outline" size="sm" asChild>
                                        <Link href={pets.prev_page_url}>
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
                                    Page {pets.current_page} of {pets.last_page}
                                </span>
                                {pets.next_page_url ? (
                                    <Button variant="outline" size="sm" asChild>
                                        <Link href={pets.next_page_url}>
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
