import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import VisitForm, { type VisitFormValues, type VisitPetOption, type VisitVeterinarianOption } from '@/components/visit-form';
import ClinicLayout from '@/layouts/clinic-layout';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, ClipboardPlus } from 'lucide-react';

interface Visit extends Omit<VisitFormValues, 'treatments_submitted'> {
    id: number;
    treatments: { treatment_given: string; cost: string }[];
}

export default function EditVisit({
    visit,
    pets,
    veterinarians,
}: {
    visit: Visit;
    pets: VisitPetOption[];
    veterinarians: VisitVeterinarianOption[];
}) {
    const initialValues: VisitFormValues = {
        pet_id: String(visit.pet_id),
        veterinarian_id: String(visit.veterinarian_id),
        examination_date: visit.examination_date,
        diagnosis: visit.diagnosis,
        procedure_cost: String(visit.procedure_cost),
        payment_status: visit.payment_status,
        treatments: visit.treatments.map((treatment) => ({
            treatment_given: treatment.treatment_given,
            cost: String(treatment.cost),
        })),
        treatments_submitted: true,
    };

    return (
        <ClinicLayout>
            <Head title="Edit medical visit" />
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
                        <CardTitle>Edit medical visit</CardTitle>
                        <CardDescription>Update the visit details and itemized treatments.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <VisitForm visitId={visit.id} initialValues={initialValues} pets={pets} veterinarians={veterinarians} />
                    </CardContent>
                </Card>
            </div>
        </ClinicLayout>
    );
}
