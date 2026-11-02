import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import VisitForm, { type VisitPetOption, type VisitVeterinarianOption } from '@/components/visit-form';
import ClinicLayout from '@/layouts/clinic-layout';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, ClipboardPlus } from 'lucide-react';

export default function CreateVisit({ pets, veterinarians }: { pets: VisitPetOption[]; veterinarians: VisitVeterinarianOption[] }) {
    return (
        <ClinicLayout>
            <Head title="Record a visit" />
            <div className="mx-auto max-w-3xl space-y-6">
                <Button variant="ghost" size="sm" asChild>
                    <Link href={route('visits.index')}>
                        <ArrowLeft aria-hidden="true" />
                        Back to visits
                    </Link>
                </Button>

                <Card>
                    <CardHeader>
                        <div className="bg-muted mb-2 flex size-10 items-center justify-center rounded-lg">
                            <ClipboardPlus className="size-5" aria-hidden="true" />
                        </div>
                        <CardTitle>Record a medical visit</CardTitle>
                        <CardDescription>Enter the patient, examination, diagnosis, and any treatments.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <VisitForm pets={pets} veterinarians={veterinarians} />
                    </CardContent>
                </Card>
            </div>
        </ClinicLayout>
    );
}
