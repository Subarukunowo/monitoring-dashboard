import { Badge } from '@/components/ui/badge';

export function StatusBadge({ statusKerja, label }: { statusKerja?: string | null; label?: string | null }) {
    const map: Record<string, { variant: 'negative' | 'warning' | 'info' | 'positive' | 'secondary'; text: string }> = {
        pending: { variant: 'negative', text: 'Terbuka' },
        open: { variant: 'negative', text: 'Terbuka' },
        on_progress: { variant: 'warning', text: 'Sedang Dikerjakan' },
        on_hold: { variant: 'info', text: 'Ditunda' },
        completed: { variant: 'positive', text: 'Selesai' },
    };

    const resolved = (statusKerja && map[statusKerja]) || {
        variant: 'secondary' as const,
        text: label || 'Tidak Diketahui',
    };

    return <Badge variant={resolved.variant}>{label || resolved.text}</Badge>;
}

export function RabStatusBadge({ statusRab }: { statusRab?: string | null }) {
    const map: Record<string, { variant: 'secondary' | 'warning' | 'positive'; text: string }> = {
        draft: { variant: 'secondary', text: 'Draft' },
        diajukan: { variant: 'warning', text: 'Diajukan' },
        disetujui: { variant: 'positive', text: 'Disetujui' },
    };

    const resolved = (statusRab && map[statusRab]) || map.draft;

    return <Badge variant={resolved.variant}>{resolved.text}</Badge>;
}
