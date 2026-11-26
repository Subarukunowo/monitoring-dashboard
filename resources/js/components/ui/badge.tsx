import * as React from 'react';
import { cva, type VariantProps } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const badgeVariants = cva(
    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium whitespace-nowrap transition-colors',
    {
        variants: {
            variant: {
                default: 'bg-[#141414] text-white',
                secondary: 'bg-[#e7eaef] text-[#3d434d]',
                positive: 'bg-[#dcfae5] text-[#04250e]',
                negative: 'bg-[#ffedea] text-[#831c1e]',
                warning: 'bg-[#fffbeb] text-[#92400e]',
                info: 'bg-[#e8f0fe] text-[#174ea6]',
                outline: 'border border-[#d0d5dd] bg-white text-[#3d434d]',
            },
        },
        defaultVariants: {
            variant: 'secondary',
        },
    },
);

export interface BadgeProps
    extends React.HTMLAttributes<HTMLSpanElement>,
        VariantProps<typeof badgeVariants> {}

function Badge({ className, variant, ...props }: BadgeProps) {
    return <span className={cn(badgeVariants({ variant }), className)} {...props} />;
}

export { Badge, badgeVariants };
