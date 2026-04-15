import Link from 'next/link';

export default function Footer() {
  return (
    <footer className="bg-brand-black text-gray-400 py-16 border-t-4 border-brand-orange">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-12">

          {/* Brand */}
          <div className="col-span-1">
            <span className="font-display font-bold text-2xl text-white tracking-tighter uppercase mb-4 block">
              Matterhorn
            </span>
            <p className="text-sm leading-relaxed mb-6">
              Penyedia peralatan outdoor premium no. 1 di Bandung. Kami menjamin kualitas, kebersihan, dan keamanan setiap alat yang Anda sewa.
            </p>
            <div className="flex gap-4">
              <a href="#" className="w-8 h-8 bg-gray-700 hover:bg-brand-orange flex items-center justify-center text-white transition text-xs font-bold">IG</a>
              <a href="#" className="w-8 h-8 bg-gray-700 hover:bg-brand-orange flex items-center justify-center text-white transition text-xs font-bold">FB</a>
              <a href="#" className="w-8 h-8 bg-gray-700 hover:bg-brand-orange flex items-center justify-center text-white transition text-xs font-bold">YT</a>
            </div>
          </div>

          {/* Bantuan */}
          <div>
            <h4 className="text-white font-display font-bold uppercase tracking-wider mb-6 text-sm">Bantuan</h4>
            <ul className="space-y-3 text-sm">
              <li><Link href="#" className="hover:text-brand-orange transition">Cara Menyewa</Link></li>
              <li><Link href="#" className="hover:text-brand-orange transition">Syarat & Ketentuan</Link></li>
              <li><Link href="#" className="hover:text-brand-orange transition">Kebijakan Denda</Link></li>
              <li><Link href="#" className="hover:text-brand-orange transition">Konfirmasi Pembayaran</Link></li>
            </ul>
          </div>

          {/* Tentang */}
          <div>
            <h4 className="text-white font-display font-bold uppercase tracking-wider mb-6 text-sm">Tentang Kami</h4>
            <ul className="space-y-3 text-sm">
              <li><Link href="/about" className="hover:text-brand-orange transition">Lokasi Toko</Link></li>
              <li><Link href="#" className="hover:text-brand-orange transition">Program Member</Link></li>
              <li><Link href="#" className="hover:text-brand-orange transition">Karir</Link></li>
              <li><Link href="#" className="hover:text-brand-orange transition">Hubungi Kami</Link></li>
            </ul>
          </div>

          {/* Newsletter */}
          <div>
            <h4 className="text-white font-display font-bold uppercase tracking-wider mb-6 text-sm">Berlangganan</h4>
            <p className="text-xs mb-4">Dapatkan info promo terbaru.</p>
            <div className="flex">
              <input
                type="email"
                placeholder="Email Anda"
                className="w-full bg-brand-dark text-white px-4 py-2 text-sm outline-none border border-gray-700 focus:border-brand-orange transition"
              />
              <button className="bg-brand-orange text-white px-4 font-bold hover:bg-orange-700 transition">GO</button>
            </div>
          </div>
        </div>

        <div className="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center text-xs">
          <p>&copy; 2026 Matterhorn Outdoor Rental. All Rights Reserved.</p>
          <div className="flex gap-4 mt-4 md:mt-0">
            <span>Privacy Policy</span>
            <span>Terms of Service</span>
          </div>
        </div>
      </div>
    </footer>
  );
}
