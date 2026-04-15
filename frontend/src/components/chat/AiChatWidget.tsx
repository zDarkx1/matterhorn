'use client';

import { useState, useRef, useEffect } from 'react';
import { MessageCircle, X, Send } from 'lucide-react';

interface ChatMessage {
  role: 'user' | 'assistant' | 'system';
  content: string;
}

interface UserData {
  firstName: string;
  lastName: string;
  email: string;
}

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';

export default function AiChatWidget() {
  const [isOpen, setIsOpen] = useState(false);
  const [userData, setUserData] = useState<UserData | null>(null);
  const [messages, setMessages] = useState<ChatMessage[]>([]);
  const [input, setInput] = useState('');
  const [loading, setLoading] = useState(false);
  const [regForm, setRegForm] = useState({ firstName: '', lastName: '', email: '' });
  const messagesEndRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  // Load data from localStorage on mount
  useEffect(() => {
    try {
      const saved = localStorage.getItem('matterhorn_user');
      if (saved) setUserData(JSON.parse(saved));
      const history = localStorage.getItem('matterhorn_history');
      if (history) setMessages(JSON.parse(history));
    } catch {
      localStorage.removeItem('matterhorn_user');
      localStorage.removeItem('matterhorn_history');
    }
  }, []);

  // Auto-scroll to bottom
  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages, loading]);

  // Focus input when chat opens
  useEffect(() => {
    if (isOpen && userData) {
      setTimeout(() => inputRef.current?.focus(), 300);
    }
  }, [isOpen, userData]);

  const handleRegister = (e: React.FormEvent) => {
    e.preventDefault();
    const data: UserData = {
      firstName: regForm.firstName,
      lastName: regForm.lastName,
      email: regForm.email,
    };
    setUserData(data);
    localStorage.setItem('matterhorn_user', JSON.stringify(data));
    setTimeout(() => inputRef.current?.focus(), 100);
  };

  const sendMessage = async () => {
    const text = input.trim();
    if (!text || loading) return;

    const userMsg: ChatMessage = { role: 'user', content: text };
    const newMessages = [...messages, userMsg];
    setMessages(newMessages);
    setInput('');
    setLoading(true);

    try {
      const response = await fetch(`${API_URL}/ai/chat`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({
          message: text,
          history: newMessages.slice(-5),
        }),
      });

      const data = await response.json();

      if (data.choices?.[0]?.message?.content) {
        const aiMsg: ChatMessage = { role: 'assistant', content: data.choices[0].message.content };
        const updated = [...newMessages, aiMsg];
        setMessages(updated);
        localStorage.setItem('matterhorn_history', JSON.stringify(updated));
      } else {
        const errMsg: ChatMessage = { role: 'system', content: 'Maaf, server AI sedang sibuk.' };
        setMessages([...newMessages, errMsg]);
      }
    } catch {
      const errMsg: ChatMessage = { role: 'system', content: 'Gagal terhubung. Cek koneksi / Refresh halaman.' };
      setMessages([...newMessages, errMsg]);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed bottom-6 right-6 md:right-24 z-50 flex flex-col items-end gap-4 font-sans">

      {/* ── Chat Box ── */}
      {isOpen && (
        <div
          className="w-[340px] md:w-[380px] bg-white shadow-2xl flex flex-col overflow-hidden border border-gray-200 animate-in slide-in-from-bottom-5 fade-in duration-300"
          style={{ height: '550px', maxHeight: '80vh' }}
        >
          {/* Header */}
          <div className="bg-brand-black text-white p-4 flex justify-between items-center select-none flex-shrink-0">
            <div className="flex items-center gap-3">
              <div className="w-8 h-8 bg-white rounded-full flex items-center justify-center overflow-hidden">
                <span className="font-display font-bold text-brand-black text-sm">M</span>
              </div>
              <div>
                <h4 className="font-bold text-sm tracking-wide">Matterhorn Care</h4>
                <div className="flex items-center gap-1">
                  <span className="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse" />
                  <p className="text-xs text-gray-400">Online</p>
                </div>
              </div>
            </div>
            <button onClick={() => setIsOpen(false)} className="text-gray-400 hover:text-white transition">
              <X className="w-6 h-6" />
            </button>
          </div>

          {/* Registration View */}
          {!userData ? (
            <div className="flex-1 p-6 md:p-8 flex flex-col justify-center bg-white overflow-y-auto">
              <div className="text-center mb-6">
                <div className="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                  <MessageCircle className="w-6 h-6 text-gray-400" />
                </div>
                <h5 className="font-bold text-black text-lg mb-1">Halo, Petualang!</h5>
                <p className="text-xs text-gray-500 leading-relaxed">
                  Isi data diri sebentar ya, biar kami tahu harus panggil Kakak siapa.
                </p>
              </div>

              <form onSubmit={handleRegister} className="space-y-3">
                <div className="grid grid-cols-2 gap-2">
                  <input
                    type="text"
                    placeholder="Nama Depan"
                    value={regForm.firstName}
                    onChange={(e) => setRegForm({ ...regForm, firstName: e.target.value })}
                    className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs focus:bg-white focus:border-black focus:ring-0 outline-none transition"
                    required
                  />
                  <input
                    type="text"
                    placeholder="Belakang"
                    value={regForm.lastName}
                    onChange={(e) => setRegForm({ ...regForm, lastName: e.target.value })}
                    className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs focus:bg-white focus:border-black focus:ring-0 outline-none transition"
                    required
                  />
                </div>
                <input
                  type="email"
                  placeholder="Email Aktif"
                  value={regForm.email}
                  onChange={(e) => setRegForm({ ...regForm, email: e.target.value })}
                  className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs focus:bg-white focus:border-black focus:ring-0 outline-none transition"
                  required
                />
                <button
                  type="submit"
                  className="w-full bg-brand-black text-white font-bold py-3 rounded-lg text-xs uppercase tracking-wider hover:bg-gray-800 transition shadow-lg mt-2"
                >
                  Mulai Chatting
                </button>
              </form>
            </div>
          ) : (
            /* Chat Interface */
            <div className="flex-1 flex flex-col bg-gray-50 relative min-h-0">
              {/* Messages */}
              <div className="flex-1 overflow-y-auto p-4 space-y-4 min-h-0">
                <div className="text-center mt-2">
                  <span className="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Hari ini</span>
                </div>

                {/* Welcome message */}
                <div className="flex items-end gap-2">
                  <div className="w-6 h-6 bg-brand-black rounded-full flex-shrink-0 flex items-center justify-center text-white text-[10px] font-bold">
                    M
                  </div>
                  <div className="bg-white p-3 rounded-2xl rounded-bl-none shadow-sm text-sm text-gray-700 max-w-[85%] border border-gray-100">
                    Halo! Selamat datang di Matterhorn Care. Ada yang bisa dibantu soal peralatan camping?
                  </div>
                </div>

                {/* Chat history */}
                {messages.map((msg, i) => (
                  <div key={i} className={msg.role === 'user' ? 'flex justify-end' : msg.role === 'system' ? 'text-center text-xs text-red-600 my-2' : 'flex items-end gap-2'}>
                    {msg.role === 'assistant' && (
                      <div className="w-6 h-6 bg-white border border-gray-200 rounded-full flex-shrink-0 flex items-center justify-center">
                        <span className="text-[10px] font-bold text-brand-black">M</span>
                      </div>
                    )}
                    {msg.role === 'user' ? (
                      <div className="bg-brand-black text-white px-4 py-3 rounded-2xl rounded-br-none text-sm max-w-[80%] shadow-md">
                        {msg.content}
                      </div>
                    ) : msg.role === 'assistant' ? (
                      <div className="bg-white p-3.5 rounded-2xl rounded-bl-none shadow-sm text-sm text-gray-800 max-w-[85%] border border-gray-100 leading-relaxed whitespace-pre-line">
                        {msg.content}
                      </div>
                    ) : (
                      <span>{msg.content}</span>
                    )}
                  </div>
                ))}

                {/* Loading indicator */}
                {loading && (
                  <div className="flex items-end gap-2 pl-2">
                    <div className="bg-gray-200 p-2 rounded-2xl rounded-bl-none w-12 flex items-center justify-center gap-1">
                      <span className="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" />
                      <span className="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce [animation-delay:100ms]" />
                      <span className="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce [animation-delay:200ms]" />
                    </div>
                  </div>
                )}

                <div ref={messagesEndRef} />
              </div>

              {/* Input */}
              <div className="p-3 bg-brand-black flex-shrink-0">
                <div className="relative">
                  <input
                    ref={inputRef}
                    type="text"
                    value={input}
                    onChange={(e) => setInput(e.target.value)}
                    onKeyDown={(e) => { if (e.key === 'Enter') sendMessage(); }}
                    className="w-full bg-white text-black pl-4 pr-12 py-3 rounded-full text-sm outline-none focus:ring-2 focus:ring-gray-500 transition"
                    placeholder="Tulis pesan..."
                    autoComplete="off"
                  />
                  <button
                    onClick={sendMessage}
                    disabled={loading || !input.trim()}
                    className="absolute right-1 top-1/2 -translate-y-1/2 w-8 h-8 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition disabled:opacity-50"
                  >
                    <Send className="w-4 h-4" />
                  </button>
                </div>
                <div className="text-center mt-1">
                  <p className="text-[9px] text-gray-500">Powered by Matterhorn AI</p>
                </div>
              </div>
            </div>
          )}
        </div>
      )}

      {/* ── Toggle Button ── */}
      {!isOpen && (
        <button
          onClick={() => setIsOpen(true)}
          className="bg-brand-black text-white flex items-center gap-3 px-6 py-3.5 rounded-full shadow-2xl hover:bg-gray-900 transition-all duration-300 transform hover:scale-105 group border border-gray-800"
        >
          <div className="relative">
            <MessageCircle className="w-6 h-6" />
            <span className="absolute -top-1 -right-1 w-3 h-3 bg-red-500 border-2 border-brand-black rounded-full animate-pulse" />
          </div>
          <span className="font-medium text-sm tracking-wide">Chat</span>
        </button>
      )}
    </div>
  );
}
