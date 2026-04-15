import ToastContainer from '@/components/ui/Toast';

export default function AuthLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <>
      <ToastContainer />
      <main className="min-h-screen flex items-center justify-center bg-brand-gray">
        {children}
      </main>
    </>
  );
}
