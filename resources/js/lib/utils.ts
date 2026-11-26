import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function formatRupiah(value: number | null | undefined): string {
    if (value === null || value === undefined || Number.isNaN(value)) {
        return '—';
    }

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
}

export function formatDateId(dateStr: string | null | undefined): string {
    if (!dateStr) {
        return '—';
    }
    const parsed = new Date(dateStr);
    if (Number.isNaN(parsed.getTime())) {
        return dateStr;
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(parsed);
}
