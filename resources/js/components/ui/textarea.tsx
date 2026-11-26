import * as React from 'react';
import { cn } from '@/lib/utils';

const Textarea = React.forwardRef<HTMLTextAreaElement, React.TextareaHTMLAttributes<HTMLTextAreaElement>>(
    ({ className, ...props }, ref) => (
        <textarea
            className={cn(
                'flex min-h-[84px] w-full rounded-[6px] border border-[#d0d5dd] bg-white px-3 py-2 text-sm text-[#0a0a0a] transition-colors placeholder:text-[#667085] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1d5fbf] focus-visible:border-[#1d5fbf] disabled:cursor-not-allowed disabled:bg-[#f4f5f7] disabled:text-[#5b616e]',
                className,
            )}
            ref={ref}
            {...props}
        />
    ),
);
Textarea.displayName = 'Textarea';

export { Textarea };
