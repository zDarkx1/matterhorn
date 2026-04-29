import ToastContainer from '@/components/ui/Toast';
import Navbar from '@/components/layout/Navbar';
import Footer from '@/components/layout/Footer';

export default function AuthLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <>
      <ToastContainer />
      <Navbar />
      <main className="flex-1 bg-white">
        {children}
      </main>
      <Footer />
    </>
  );
}
