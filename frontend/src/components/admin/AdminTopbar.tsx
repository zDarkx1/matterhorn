'use client';

import { usePathname } from 'next/navigation';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Separator } from '@/components/ui/separator';
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { Switch } from '@/components/ui/switch';
import { Moon, Sun } from 'lucide-react';
import { useTheme } from '@/hooks/useTheme';

const pageTitles: Record<string, string> = {
  '/admin': 'Dashboard',
  '/admin/products': 'Kelola Produk',
  '/admin/rentals': 'Kelola Pesanan',
  '/admin/payments': 'Verifikasi Pembayaran',
  '/admin/users': 'Kelola Pelanggan',
};

export function AdminTopbar() {
  const pathname = usePathname();
  const { isDark, toggleTheme } = useTheme();

  const currentTitle = pageTitles[pathname] || 'Admin';

  return (
    <header className="flex h-16 shrink-0 items-center gap-2 border-b px-4 transition-[width,height] ease-linear">
      <div className="flex items-center gap-2 flex-1">
        <SidebarTrigger className="-ml-1" />
        <Separator orientation="vertical" className="mr-2 !h-4" />
        <Breadcrumb>
          <BreadcrumbList>
            <BreadcrumbItem className="hidden md:block">
              <BreadcrumbLink href="/admin">Admin</BreadcrumbLink>
            </BreadcrumbItem>
            {pathname !== '/admin' && (
              <>
                <BreadcrumbSeparator className="hidden md:block" />
                <BreadcrumbItem>
                  <BreadcrumbPage>{currentTitle}</BreadcrumbPage>
                </BreadcrumbItem>
              </>
            )}
          </BreadcrumbList>
        </Breadcrumb>
      </div>

      {/* Dark Mode Toggle */}
      <div className="flex items-center gap-2">
        <Sun className="h-4 w-4 text-muted-foreground" />
        <Switch
          checked={isDark}
          onCheckedChange={toggleTheme}
          className="data-[state=checked]:bg-brand-orange"
        />
        <Moon className="h-4 w-4 text-muted-foreground" />
      </div>
    </header>
  );
}
