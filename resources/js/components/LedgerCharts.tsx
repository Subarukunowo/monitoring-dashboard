import * as React from 'react';

interface TrendPoint {
    label: string;
    count: number;
}

function niceTicks(maxVal: number, segments = 4): number[] {
    const safeMax = Math.max(maxVal, 4);
    const rawStep = safeMax / segments;
    const mag = 10 ** Math.floor(Math.log10(rawStep));
    const step = [1, 2, 2.5, 5, 10].map((m) => m * mag).find((s) => s >= rawStep) || 10 * mag;
    const top = Math.ceil(safeMax / step) * step;
    const ticks: number[] = [];
    for (let v = 0; v <= top + 1e-9; v += step) {
        ticks.push(Math.round(v));
    }
    if (ticks[ticks.length - 1] <= maxVal) {
        ticks.push(ticks[ticks.length - 1] + step);
    }

    return ticks;
}

export function MonthlyIncidentChart({ data }: { data: TrendPoint[] }) {
    const [hoverIdx, setHoverIdx] = React.useState<number | null>(null);
    const maxVal = Math.max(...data.map((d) => d.count), 0);
    const ticks = niceTicks(maxVal, 4);
    const yMax = ticks[ticks.length - 1] || 4;

    const width = 560;
    const height = 210;
    const padL = 36;
    const padR = 12;
    const padT = 16;
    const padB = 28;
    const plotW = width - padL - padR;
    const plotH = height - padT - padB;
    const barSlot = data.length > 0 ? plotW / data.length : plotW;
    const barW = Math.min(44, Math.max(18, barSlot * 0.56));

    const yPos = (val: number) => padT + plotH - (val / yMax) * plotH;

    return (
        <div className="relative w-full">
            <svg
                viewBox={`0 0 ${width} ${height}`}
                className="w-full h-[210px] overflow-visible tabular-nums"
                role="img"
                aria-label="Grafik tren jumlah insiden 6 bulan terakhir"
            >
                {ticks.map((tick) => {
                    const y = yPos(tick);

                    return (
                        <g key={tick}>
                            <line
                                x1={padL}
                                x2={width - padR}
                                y1={y}
                                y2={y}
                                stroke={tick === 0 ? '#d0d5dd' : '#eef0f4'}
                                strokeWidth={1}
                                shapeRendering="crispEdges"
                            />
                            <text
                                x={padL - 8}
                                y={y + 4}
                                textAnchor="end"
                                fontSize={12}
                                fill="#5b616e"
                            >
                                {tick}
                            </text>
                        </g>
                    );
                })}

                {data.map((point, idx) => {
                    const cx = padL + barSlot * idx + barSlot / 2;
                    const x = cx - barW / 2;
                    const barH = (point.count / yMax) * plotH;
                    const y = padT + plotH - barH;
                    const isLatest = idx === data.length - 1;
                    const isHovered = hoverIdx === idx;

                    return (
                        <g
                            key={point.label}
                            onMouseEnter={() => setHoverIdx(idx)}
                            onMouseLeave={() => setHoverIdx(null)}
                            className="cursor-default"
                        >
                            <rect
                                x={x}
                                y={y}
                                width={barW}
                                height={Math.max(barH, point.count > 0 ? 2 : 0)}
                                fill={isLatest || isHovered ? '#1d5fbf' : '#94b3e0'}
                            />
                            {isHovered && (
                                <text
                                    x={cx}
                                    y={Math.max(padT - 2, y - 6)}
                                    textAnchor="middle"
                                    fontSize={12}
                                    fontWeight={600}
                                    fill="#0a0a0a"
                                >
                                    {point.count}
                                </text>
                            )}
                            <text
                                x={cx}
                                y={height - 6}
                                textAnchor="middle"
                                fontSize={12}
                                fill="#5b616e"
                            >
                                {point.label}
                            </text>
                        </g>
                    );
                })}
            </svg>
        </div>
    );
}

interface AreaDistPoint {
    id: number;
    nama_area: string;
    total: number;
    open: number;
}

export function AreaWorkloadList({
    data,
    onSelectArea,
}: {
    data: AreaDistPoint[];
    onSelectArea?: (areaId: number) => void;
}) {
    const maxTotal = Math.max(...data.map((d) => d.total), 1);

    if (data.length === 0) {
        return <p className="text-sm text-[#5b616e] py-6 text-center">Belum ada data area.</p>;
    }

    return (
        <div className="space-y-3 tabular-nums">
            {data.map((area) => {
                const widthPct = Math.max((area.total / maxTotal) * 100, area.total > 0 ? 4 : 0);

                return (
                    <button
                        key={area.id}
                        type="button"
                        onClick={() => onSelectArea?.(area.id)}
                        className="w-full text-left group cursor-pointer"
                    >
                        <div className="flex items-baseline justify-between text-sm mb-1.5">
                            <span className="font-medium text-[#0a0a0a] group-hover:text-[#1d5fbf] transition-colors">
                                {area.nama_area}
                            </span>
                            <span className="text-xs text-[#5b616e]">
                                <strong className="font-semibold text-[#0a0a0a]">{area.total}</strong> total
                                {area.open > 0 ? ` · ${area.open} aktif` : ' · 0 aktif'}
                            </span>
                        </div>
                        <div className="h-2 w-full rounded-[2px] bg-[#eef0f4] overflow-hidden">
                            <div
                                className="h-full bg-[#1d5fbf] transition-[width]"
                                style={{ width: `${widthPct}%` }}
                            />
                        </div>
                    </button>
                );
            })}
        </div>
    );
}
