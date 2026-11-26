import { Link } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import type { Paginated } from '@/types';

export function PaginationBar<T>({ paginated }: { paginated: Paginated<T> }) {
    if (paginated.last_page <= 1) {
        return (
            <div className="flex items-center justify-between pt-3 text-xs text-[#5b616e] tabular-nums">
                <span>Menampilkan {paginated.total} data</span>
            </div>
        );
    }

    const prev = paginated.links[0];
    const next = paginated.links[paginated.links.length - 1];
    const middle = paginated.links.slice(1, -1);

    return (
        <div className="flex flex-col gap-2 pt-4 sm:flex-row sm:items-center sm:justify-between tabular-nums">
            <span className="text-xs text-[#5b616e]">
                Menampilkan {paginated.from ?? 0}–{paginated.to ?? 0} dari {paginated.total} data
            </span>
            <div className="flex items-center gap-1">
                {prev?.url ? (
                    <Link
                        href={prev.url}
                        className="inline-flex h-8 items-center gap-1 rounded-[6px] border border-[#d0d5dd] bg-white px-2.5 text-xs font-medium text-[#0a0a0a] hover:bg-[#f4f5f7]"
                    >
                        <ChevronLeft className="h-3.5 w-3.5" />
                        Sebelumnya
                    </Link>
                ) : (
                    <span className="inline-flex h-8 items-center gap-1 rounded-[6px] border border-[#e3e6ec] bg-[#f4f5f7] px-2.5 text-xs text-[#667085]">
                        <ChevronLeft className="h-3.5 w-3.5" />
                        Sebelumnya
                    </span>
                )}

                {middle.map((link, idx) =>
                    link.url ? (
                        <Link
                            key={idx}
                            href={link.url}
                            className={`inline-flex h-8 min-w-8 items-center justify-center rounded-[6px] px-2 text-xs font-medium ${
                                link.active
                                    ? 'bg-[#141414] text-white'
                                    : 'border border-[#d0d5dd] bg-white text-[#0a0a0a] hover:bg-[#f4f5f7]'
                            }`}
                        >
                            {link.label.replace(/&laquo;|&raquo;/g, '').trim()}
                        </Link>
                    ) : (
                        <span key={idx} className="px-1.5 text-xs text-[#667085]">
                            …
                        </span>
                    ),
                )}

                {next?.url ? (
                    <Link
                        href={next.url}
                        className="inline-flex h-8 items-center gap-1 rounded-[6px] border border-[#d0d5dd] bg-white px-2.5 text-xs font-medium text-[#0a0a0a] hover:bg-[#f4f5f7]"
                    >
                        Berikutnya
                        <ChevronRight className="h-3.5 w-3.5" />
                    </Link>
                ) : (
                    <span className="inline-flex h-8 items-center gap-1 rounded-[6px] border border-[#e3e6ec] bg-[#f4f5f7] px-2.5 text-xs text-[#667085]">
                        Berikutnya
                        <ChevronRight className="h-3.5 w-3.5" />
                    </span>
                )}
            </div>
        </div>
    );
}
