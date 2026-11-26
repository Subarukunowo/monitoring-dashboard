import * as React from 'react';
import { cva, type VariantProps } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const alertVariants = cva(
    'relative w-full rounded-[8px] border px-4 py-3 text-sm flex items-start gap-3',
    {
        variants: {
            variant: {
                default: 'bg-white border-[#e3e6ec] text-[#0a0a0a]',
                positive: 'bg-[#f2fbf5] border-[#bbf7d0] text-[#04250e]',
                destructive: 'bg-[#fff5f5] border-[#fecaca] text-[#831c1e]',
                warning: 'bg-[#fffbeb] border-[#fde68a] text-[#92400e]',
                info: 'bg-[#f4f8ff] border-[#bfdbfe] text-[#174ea6]',
            },
        },
        defaultVariants: {
            variant: 'default',
        },
    },
);

const Alert = React.forwardRef<
    HTMLDivElement,
    React.HTMLAttributes<HTMLDivElement> & VariantProps<typeof alertVariants>
>(({ className, variant, ...props }, ref) => (
    <div ref={ref} role="alert" className={cn(alertVariants({ variant }), className)} {...props} />
));
Alert.displayName = 'Alert';

const AlertTitle = React.forwardRef<HTMLHeadingElement, React.HTMLAttributes<HTMLHeadingElement>>(
    ({ className, ...props }, ref) => (
        <h5 ref={ref} className={cn('font-medium leading-tight', className)} {...props} />
    ),
);
AlertTitle.displayName = 'AlertTitle';

const AlertDescription = React.forwardRef<HTMLParagraphElement, React.HTMLAttributes<HTMLParagraphElement>>(
    ({ className, ...props }, ref) => (
        <div ref={ref} className={cn('text-sm leading-relaxed', className)} {...props} />
    ),
);
AlertDescription.displayName = 'AlertDescription';

export { Alert, AlertTitle, AlertDescription };
