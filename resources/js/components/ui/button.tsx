import * as React from 'react';
import { Slot } from '@radix-ui/react-slot';
import { cva, type VariantProps } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const buttonVariants = cva(
    'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-[6px] text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1d5fbf] focus-visible:ring-offset-1 disabled:pointer-events-none disabled:opacity-50 cursor-pointer [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0',
    {
        variants: {
            variant: {
                default: 'bg-[#141414] text-white hover:bg-[#2a2a2a]',
                destructive: 'bg-[#c4242a] text-white hover:bg-[#a61d22]',
                outline: 'border border-[#d0d5dd] bg-white text-[#0a0a0a] hover:bg-[#f4f5f7]',
                secondary: 'bg-[#e7eaef] text-[#141414] hover:bg-[#dce0e7]',
                ghost: 'text-[#5b616e] hover:bg-[#e9ecf1] hover:text-[#0a0a0a]',
                link: 'text-[#1d5fbf] underline-offset-4 hover:underline',
            },
            size: {
                default: 'h-9 px-3.5 py-2',
                sm: 'h-8 px-3 text-xs',
                lg: 'h-10 px-5',
                icon: 'h-8 w-8',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
);

export interface ButtonProps
    extends React.ButtonHTMLAttributes<HTMLButtonElement>,
        VariantProps<typeof buttonVariants> {
    asChild?: boolean;
}

const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
    ({ className, variant, size, asChild = false, ...props }, ref) => {
        const Comp = asChild ? Slot : 'button';

        return (
            <Comp
                className={cn(buttonVariants({ variant, size, className }))}
                ref={ref}
                {...props}
            />
        );
    },
);
Button.displayName = 'Button';

export { Button, buttonVariants };
