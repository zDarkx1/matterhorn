import type { Metadata } from "next";
import { Toaster } from "@/components/ui/sonner";
import "./globals.css";

export const metadata: Metadata = {
  title: {
    default: "Matterhorn.co | Adventure Gear Rental",
    template: "%s | Matterhorn.co",
  },
  description:
    "Sewa peralatan outdoor & camping premium di Bandung. Tenda, carrier, sleeping bag, dan perlengkapan hiking lainnya dengan harga terjangkau.",
  keywords: ["sewa alat camping", "rental outdoor", "tenda bandung", "carrier", "sleeping bag"],
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="id" className="scroll-smooth">
      <head>
        <link
          href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap"
          rel="stylesheet"
        />
      </head>
      <body className="min-h-screen flex flex-col">
        {children}
        <Toaster />
      </body>
    </html>
  );
}
