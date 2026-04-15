'use client';

import { useUIStore } from '@/stores/useUIStore';
import { X, CheckCircle, AlertCircle, Info } from 'lucide-react';

const ICON_MAP = {
  success: CheckCircle,
  error: AlertCircle,
  info: Info,
};

const COLOR_MAP = {
  success: 'bg-green-50 border-green-200 text-green-800',
  error: 'bg-red-50 border-red-200 text-red-800',
  info: 'bg-blue-50 border-blue-200 text-blue-800',
};

export default function ToastContainer() {
  const { toasts, removeToast } = useUIStore();

  if (toasts.length === 0) return null;

  return (
    <div className="fixed top-24 right-4 z-[100] flex flex-col gap-2 max-w-sm">
      {toasts.map((toast) => {
        const Icon = ICON_MAP[toast.type];
        return (
          <div
            key={toast.id}
            className={`flex items-start gap-3 px-4 py-3 border shadow-lg animate-in slide-in-from-right-5 fade-in duration-300 ${COLOR_MAP[toast.type]}`}
          >
            <Icon className="w-5 h-5 flex-shrink-0 mt-0.5" />
            <p className="text-sm flex-1">{toast.message}</p>
            <button onClick={() => removeToast(toast.id)} className="flex-shrink-0 hover:opacity-70 transition">
              <X className="w-4 h-4" />
            </button>
          </div>
        );
      })}
    </div>
  );
}
