import { MapPin, Clock, Phone } from 'lucide-react';

export default function AboutPage() {
  return (
    <section className="py-12 bg-white min-h-screen">
      <div className="max-w-4xl mx-auto px-4">
        <h1 className="font-display font-bold text-4xl uppercase mb-8 flex items-center gap-3">
          <span className="w-1 h-8 bg-brand-orange block" />
          Tentang Matterhorn
        </h1>

        <div className="prose prose-sm max-w-none mb-12">
          <p className="text-lg text-gray-600 leading-relaxed">
            <strong>Matterhorn.co</strong> adalah penyedia jasa sewa peralatan outdoor dan camping premium di Bandung.
            Kami menyediakan berbagai alat hiking, camping, dan outdoor adventure dengan kualitas terjamin,
            kondisi steril, dan harga terjangkau.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
          {/* Location */}
          <div className="border border-gray-200 p-6">
            <div className="w-12 h-12 bg-brand-orange/10 flex items-center justify-center mb-4">
              <MapPin className="w-6 h-6 text-brand-orange" />
            </div>
            <h3 className="font-display font-bold uppercase text-sm mb-2">Lokasi Toko</h3>
            <p className="text-sm text-gray-600">
              Jl. Setiabudhi No. 123<br />
              Bandung, Jawa Barat 40154
            </p>
          </div>

          {/* Hours */}
          <div className="border border-gray-200 p-6">
            <div className="w-12 h-12 bg-brand-orange/10 flex items-center justify-center mb-4">
              <Clock className="w-6 h-6 text-brand-orange" />
            </div>
            <h3 className="font-display font-bold uppercase text-sm mb-2">Jam Operasional</h3>
            <div className="text-sm text-gray-600 space-y-1">
              <p>Senin - Sabtu: 09:00 - 21:45</p>
              <p>Minggu: Tutup</p>
            </div>
          </div>

          {/* Contact */}
          <div className="border border-gray-200 p-6">
            <div className="w-12 h-12 bg-brand-orange/10 flex items-center justify-center mb-4">
              <Phone className="w-6 h-6 text-brand-orange" />
            </div>
            <h3 className="font-display font-bold uppercase text-sm mb-2">Hubungi Kami</h3>
            <div className="text-sm text-gray-600 space-y-1">
              <p>WhatsApp: 0812-xxxx-xxxx</p>
              <p>Email: info@matterhorn.co</p>
            </div>
          </div>
        </div>

        {/* How it works */}
        <div className="bg-brand-black text-white p-8 md:p-12">
          <h2 className="font-display font-bold text-2xl uppercase mb-8">Cara Menyewa</h2>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
            {[
              { step: '01', title: 'Pilih Alat', desc: 'Browse katalog dan pilih peralatan yang Anda butuhkan.' },
              { step: '02', title: 'Checkout', desc: 'Masukkan ke keranjang dan lakukan checkout online.' },
              { step: '03', title: 'Ambil di Toko', desc: 'Datang ke toko untuk mengambil peralatan Anda.' },
              { step: '04', title: 'Kembalikan', desc: 'Kembalikan alat ke toko setelah selesai digunakan.' },
            ].map((item) => (
              <div key={item.step}>
                <span className="font-display font-bold text-4xl text-brand-orange">{item.step}</span>
                <h4 className="font-display font-bold uppercase text-sm mt-2 mb-1">{item.title}</h4>
                <p className="text-gray-400 text-xs">{item.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}
