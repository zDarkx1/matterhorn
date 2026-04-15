'use client';

import { useState } from 'react';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { CreditCard, Banknote, QrCode, Check } from 'lucide-react';

export type PaymentMethod = 'cash' | 'qris';

interface PaymentMethodModalProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSelect: (method: PaymentMethod) => void;
}

const METHODS: { value: PaymentMethod; label: string; desc: string; icon: React.ReactNode }[] = [
  {
    value: 'cash',
    label: 'Bayar di Toko (Cash)',
    desc: 'Bayar langsung saat pengambilan barang',
    icon: <Banknote className="w-8 h-8 text-green-600" />,
  },
  {
    value: 'qris',
    label: 'QRIS',
    desc: 'Scan QR Code untuk pembayaran digital',
    icon: <QrCode className="w-8 h-8 text-blue-600" />,
  },
];

export function PaymentMethodModal({ open, onOpenChange, onSelect }: PaymentMethodModalProps) {
  const [selected, setSelected] = useState<PaymentMethod | null>(null);

  const handleConfirm = () => {
    if (selected) {
      onSelect(selected);
      onOpenChange(false);
    }
  };

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>Pilih Metode Pembayaran</DialogTitle>
        </DialogHeader>

        <div className="-mx-4 px-4 max-h-[70vh] overflow-y-auto">
          <div className="space-y-3 py-2">
            {METHODS.map((m) => (
              <button
                key={m.value}
                onClick={() => setSelected(m.value)}
                className={`w-full flex items-center gap-4 p-4 border rounded-lg transition-all text-left ${
                  selected === m.value
                    ? 'border-brand-orange bg-orange-50 ring-1 ring-brand-orange'
                    : 'border-gray-200 hover:border-gray-300'
                }`}
              >
                <div className="flex-shrink-0">{m.icon}</div>
                <div className="flex-1">
                  <p className="font-semibold text-sm">{m.label}</p>
                  <p className="text-xs text-muted-foreground">{m.desc}</p>
                </div>
                <div className={`w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors ${
                  selected === m.value ? 'border-brand-orange bg-brand-orange' : 'border-gray-300'
                }`}>
                  {selected === m.value && <Check className="w-3.5 h-3.5 text-white" />}
                </div>
              </button>
            ))}
          </div>
        </div>

        <div className="flex items-center justify-between pt-2 border-t">
          <div>
            <p className="text-xs text-muted-foreground">Metode Pembayaran</p>
            <p className="font-semibold text-sm">
              {selected ? METHODS.find(m => m.value === selected)?.label : 'Belum dipilih'}
            </p>
          </div>
          <Button
            onClick={handleConfirm}
            disabled={!selected}
            className="bg-brand-orange hover:bg-orange-700 text-white"
          >
            Bayar
          </Button>
        </div>
      </DialogContent>
    </Dialog>
  );
}
