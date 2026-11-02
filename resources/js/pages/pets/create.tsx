import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import ClinicLayout from '@/layouts/clinic-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, PawPrint } from 'lucide-react';
import { type FormEventHandler } from 'react';

export default function CreatePet() {
    const { data, setData, post, processing, errors } = useForm({
        owner_name: '',
        name: '',
        species: '',
        breed: '',
    });

    const submit: FormEventHandler = (event) => {
        event.preventDefault();
        post(route('pets.store'));
    };

    return (
        <ClinicLayout>
            <Head title="Add a pet" />
            <div className="mx-auto max-w-2xl space-y-6">
                <Button variant="ghost" size="sm" asChild>
                    <Link href={route('pets.index')}>
                        <ArrowLeft aria-hidden="true" />
                        Back to pets
                    </Link>
                </Button>

                <Card>
                    <CardHeader>
                        <div className="mb-2 flex size-10 items-center justify-center rounded-lg bg-muted">
                            <PawPrint className="size-5" aria-hidden="true" />
                        </div>
                        <CardTitle>Add a pet</CardTitle>
                        <CardDescription>Enter the pet’s details and owner name.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="space-y-5">
                            <div className="grid gap-2">
                                <Label htmlFor="owner_name">Owner name</Label>
                                <Input
                                    id="owner_name"
                                    value={data.owner_name}
                                    onChange={(event) => setData('owner_name', event.target.value)}
                                    placeholder="Enter the owner’s name"
                                    autoComplete="name"
                                    aria-invalid={Boolean(errors.owner_name)}
                                    required
                                />
                                <InputError message={errors.owner_name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="name">Pet name</Label>
                                <Input
                                    id="name"
                                    value={data.name}
                                    onChange={(event) => setData('name', event.target.value)}
                                    placeholder="e.g. Mochi"
                                    autoComplete="off"
                                    aria-invalid={Boolean(errors.name)}
                                    required
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="species">Species</Label>
                                <Input
                                    id="species"
                                    value={data.species}
                                    onChange={(event) => setData('species', event.target.value)}
                                    placeholder="e.g. Dog"
                                    aria-invalid={Boolean(errors.species)}
                                    required
                                />
                                <InputError message={errors.species} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="breed">Breed</Label>
                                <Input
                                    id="breed"
                                    value={data.breed}
                                    onChange={(event) => setData('breed', event.target.value)}
                                    placeholder="e.g. Shih Tzu"
                                    aria-invalid={Boolean(errors.breed)}
                                />
                                <InputError message={errors.breed} />
                            </div>

                            <div className="flex justify-end gap-3 border-t pt-5">
                                <Button type="button" variant="outline" asChild>
                                    <Link href={route('pets.index')}>Cancel</Link>
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {processing ? 'Saving…' : 'Create pet'}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </ClinicLayout>
    );
}
