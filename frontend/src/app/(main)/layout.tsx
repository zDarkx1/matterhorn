'use client';

import { useState, useEffect, useCallback } from 'react';
import Navbar from '@/components/layout/Navbar';
import Footer from '@/components/layout/Footer';
import AiChatWidget from '@/components/chat/AiChatWidget';
import { SessionExpiredModal } from '@/components/modals/AuthModals';

export default function MainLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const [sessionExpired, setSessionExpired] = useState(false);

  // Listen for custom session-expired event from API wrapper
  useEffect(() => {
    const handler = () => setSessionExpired(true);
    window.addEventListener('session-expired', handler);
    return () => window.removeEventListener('session-expired', handler);
  }, []);

  return (
    <>
      <Navbar />
      <main id="main-content" role="main" className="flex-1">{children}</main>
      <Footer />
      <AiChatWidget />
      <SessionExpiredModal open={sessionExpired} onOpenChange={setSessionExpired} />
    </>
  );
}
