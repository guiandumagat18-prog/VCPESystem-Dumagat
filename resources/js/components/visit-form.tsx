import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/react';
import { Plus, Trash2 } from 'lucide-react';
import { type FormEventHandler } from 'react';

export interface VisitPetOption {
    id: number;
    name: string;
    owner: { name: string } | null;
}

export interface VisitVeterinarianOption {
    id: number;
    name: string;
}

export type VisitTreatment = {
    treatment_given: string;
    cost: string;
};

export type VisitFormValues = {
    pet_id: string;
    veterinarian_id: string;
    examination_date: string;
    diagnosis: string;
    procedure_cost: string;
    payment_status: 'pending' | 'paid' | 'cancelled';
    treatments: VisitTreatment[];
    treatments_submitted: boolean;
};

interface VisitFormProps {
    pets: VisitPetOption[];
    veterinarians: VisitVeterinarianOption[];
    initialValues?: VisitFormValues;
    visitId?: number;
}

const fieldClassName =
    'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm';

function today(): string {
    const now = new Date();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${now.getFullYear()}-${month}-${day}`;
}

export default function VisitForm({ pets, veterinarians, initialValues, visitId }: VisitFormProps) {
    const { data, setData, post, put, processing, errors } = useForm<VisitFormValues>(
        initialValues ?? {
            pet_id: '',
            veterinarian_id: '',
            examination_date: today(),
            diagnosis: '',
            procedure_cost: '0',
            payment_status: 'pending',
            treatments: [],
            treatments_submitted: false,
        },
    );
    const errorFor = (field: string) => Object.entries(errors).find(([key]) => key === field)?.[1];

    const submit: FormEventHandler = (event) => {
        event.preventDefault();
        if (visitId === undefined) {
            post(route('visits.store'));
            return;
        }

        put(route('visits.update', visitId));
    };

    const updateTreatment = (index: number, field: keyof VisitTreatment, value: string) => {
        setData(
            'treatments',
            data.treatments.map((treatment, treatmentIndex) => (treatmentIndex === index ? { ...treatment, [field]: value } : treatment)),
        );
    };

    return (
        <form onSubmit={submit} className="space-y-5">
            <div className="grid gap-5 sm:grid-cols-2">
                <div className="grid gap-2">
                    <Label htmlFor="pet_id">Pet</Label>
                    <select
                        id="pet_id"
                        className={fieldClassName}
                        value={data.pet_id}
                        onChange={(event) => setData('pet_id', event.target.value)}
                        aria-invalid={Boolean(errors.pet_id)}
                        required
                    >
                        <option value="" disabled>
                            Select a pet
                        </option>
                        {pets.map((pet) => (
                            <option key={pet.id} value={pet.id}>
                                {pet.name} — {pet.owner?.name ?? 'Unknown owner'}
                            </option>
                        ))}
                    </select>
                    <InputError message={errors.pet_id} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="veterinarian_id">Veterinarian</Label>
                    <select
                        id="veterinarian_id"
                        className={fieldClassName}
                        value={data.veterinarian_id}
                        onChange={(event) => setData('veterinarian_id', event.target.value)}
                        aria-invalid={Boolean(errors.veterinarian_id)}
                        required
                    >
                        <option value="" disabled>
                            Select a veterinarian
                        </option>
                        {veterinarians.map((veterinarian) => (
                            <option key={veterinarian.id} value={veterinarian.id}>
                                {veterinarian.name}
                            </option>
                        ))}
                    </select>
                    <InputError message={errors.veterinarian_id} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="examination_date">Examination date</Label>
                    <Input
                        id="examination_date"
                        type="date"
                        value={data.examination_date}
                        onChange={(event) => setData('examination_date', event.target.value)}
                        aria-invalid={Boolean(errors.examination_date)}
                        required
                    />
                    <InputError message={errors.examination_date} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="payment_status">Payment status</Label>
                    <select
                        id="payment_status"
                        className={fieldClassName}
                        value={data.payment_status}
                        onChange={(event) => setData('payment_status', event.target.value as VisitFormValues['payment_status'])}
                        aria-invalid={Boolean(errors.payment_status)}
                        required
                    >
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <InputError message={errors.payment_status} />
                </div>
            </div>

            <div className="grid gap-2">
                <Label htmlFor="diagnosis">Diagnosis</Label>
                <textarea
                    id="diagnosis"
                    className={`${fieldClassName} h-auto min-h-28`}
                    value={data.diagnosis}
                    onChange={(event) => setData('diagnosis', event.target.value)}
                    aria-invalid={Boolean(errors.diagnosis)}
                    required
                    minLength={5}
                />
                <InputError message={errors.diagnosis} />
            </div>

            <div className="grid gap-2 sm:max-w-xs">
                <Label htmlFor="procedure_cost">Procedure cost</Label>
                <Input
                    id="procedure_cost"
                    type="number"
                    min="0"
                    step="0.01"
                    value={data.procedure_cost}
                    onChange={(event) => setData('procedure_cost', event.target.value)}
                    aria-invalid={Boolean(errors.procedure_cost)}
                    required
                />
                <InputError message={errors.procedure_cost} />
                {data.treatments.length > 0 && (
                    <p className="text-muted-foreground text-xs">The total will be calculated from the treatment costs when saved.</p>
                )}
            </div>

            <div className="space-y-3 border-t pt-5">
                <div className="flex items-center justify-between gap-3">
                    <div>
                        <h3 className="font-medium">Treatments</h3>
                        <p className="text-muted-foreground text-sm">Optional itemized treatments and costs.</p>
                    </div>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        onClick={() => {
                            setData('treatments_submitted', true);
                            setData('treatments', [...data.treatments, { treatment_given: '', cost: '0' }]);
                        }}
                    >
                        <Plus aria-hidden="true" />
                        Add treatment
                    </Button>
                </div>

                {data.treatments.map((treatment, index) => (
                    <div key={index} className="grid gap-3 rounded-lg border p-4 sm:grid-cols-[1fr_12rem_auto] sm:items-end">
                        <div className="grid gap-2">
                            <Label htmlFor={`treatment-${index}`}>Treatment</Label>
                            <Input
                                id={`treatment-${index}`}
                                value={treatment.treatment_given}
                                onChange={(event) => updateTreatment(index, 'treatment_given', event.target.value)}
                                aria-invalid={Boolean(errorFor(`treatments.${index}.treatment_given`))}
                                maxLength={255}
                                required
                            />
                            <InputError message={errorFor(`treatments.${index}.treatment_given`)} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor={`treatment-cost-${index}`}>Cost</Label>
                            <Input
                                id={`treatment-cost-${index}`}
                                type="number"
                                min="0"
                                step="0.01"
                                value={treatment.cost}
                                onChange={(event) => updateTreatment(index, 'cost', event.target.value)}
                                aria-invalid={Boolean(errorFor(`treatments.${index}.cost`))}
                                required
                            />
                            <InputError message={errorFor(`treatments.${index}.cost`)} />
                        </div>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            aria-label={`Remove treatment ${index + 1}`}
                            onClick={() => {
                                setData('treatments_submitted', true);
                                setData(
                                    'treatments',
                                    data.treatments.filter((_, treatmentIndex) => treatmentIndex !== index),
                                );
                            }}
                        >
                            <Trash2 aria-hidden="true" />
                        </Button>
                    </div>
                ))}
            </div>

            <div className="flex justify-end gap-3 border-t pt-5">
                <Button type="button" variant="outline" asChild>
                    <a href={route('visits.index')}>Cancel</a>
                </Button>
                <Button type="submit" disabled={processing || pets.length === 0 || veterinarians.length === 0}>
                    {processing ? 'Saving…' : visitId === undefined ? 'Record visit' : 'Save changes'}
                </Button>
            </div>
            {(pets.length === 0 || veterinarians.length === 0) && (
                <p className="text-destructive text-sm">
                    {pets.length === 0 ? 'Add a pet before recording a visit. ' : ''}
                    {veterinarians.length === 0 ? 'Add a veterinarian before recording a visit.' : ''}
                </p>
            )}
        </form>
    );
}
