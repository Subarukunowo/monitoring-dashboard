import * as React from 'react';
import { cn } from '@/lib/utils';

const Table = React.forwardRef<HTMLTableElement, React.HTMLAttributes<HTMLTableElement>>(
    ({ className, ...props }, ref) => (
        <div className="relative w-full overflow-x-auto">
            <table
                ref={ref}
                className={cn('w-full caption-bottom text-sm tabular-nums', className)}
                {...props}
            />
        </div>
    ),
);
Table.displayName = 'Table';

const TableHeader = React.forwardRef<HTMLTableSectionElement, React.HTMLAttributes<HTMLTableSectionElement>>(
    ({ className, ...props }, ref) => (
        <thead ref={ref} className={cn('[&_tr]:border-b [&_tr]:border-[#e3e6ec]', className)} {...props} />
    ),
);
TableHeader.displayName = 'TableHeader';

const TableBody = React.forwardRef<HTMLTableSectionElement, React.HTMLAttributes<HTMLTableSectionElement>>(
    ({ className, ...props }, ref) => (
        <tbody ref={ref} className={cn('[&_tr:last-child]:border-0', className)} {...props} />
    ),
);
TableBody.displayName = 'TableBody';

const TableRow = React.forwardRef<HTMLTableRowElement, React.HTMLAttributes<HTMLTableRowElement>>(
    ({ className, ...props }, ref) => (
        <tr
            ref={ref}
            className={cn(
                'border-b border-[#eef0f4] transition-colors hover:bg-[#f7f8fa] data-[state=selected]:bg-[#e9ecf1]',
                className,
            )}
            {...props}
        />
    ),
);
TableRow.displayName = 'TableRow';

const TableHead = React.forwardRef<HTMLTableCellElement, React.ThHTMLAttributes<HTMLTableCellElement>>(
    ({ className, ...props }, ref) => (
        <th
            ref={ref}
            className={cn(
                'h-9 px-3 text-left align-middle text-xs font-medium text-[#5b616e] whitespace-nowrap [&:has([role=checkbox])]:pr-0',
                className,
            )}
            {...props}
        />
    ),
);
TableHead.displayName = 'TableHead';

const TableCell = React.forwardRef<HTMLTableCellElement, React.TdHTMLAttributes<HTMLTableCellElement>>(
    ({ className, ...props }, ref) => (
        <td
            ref={ref}
            className={cn('px-3 py-3 align-middle text-sm text-[#0a0a0a] [&:has([role=checkbox])]:pr-0', className)}
            {...props}
        />
    ),
);
TableCell.displayName = 'TableCell';

export { Table, TableHeader, TableBody, TableHead, TableRow, TableCell };
