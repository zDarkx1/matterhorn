'use client';

import * as React from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { motion, type Transition } from 'motion/react';
import { EmblaOptionsType, EmblaCarouselType } from 'embla-carousel';
import useEmblaCarousel from 'embla-carousel-react';
import Autoplay from 'embla-carousel-autoplay';
import { ChevronRight, ChevronLeft } from 'lucide-react';

// ─── Slide Data ──────────────────────────────────────────────
export interface HeroSlide {
  image: string;
  badge?: string;
  title: React.ReactNode;
  desc: string;
  cta: string;
  ctaHref: string;
  align?: 'left' | 'right' | 'center';
}

interface HeroCarouselProps {
  slides: HeroSlide[];
  options?: EmblaOptionsType;
}

const springTransition: Transition = {
  type: 'spring',
  stiffness: 200,
  damping: 22,
  mass: 0.8,
};

const bouncyTransition: Transition = {
  type: 'spring',
  stiffness: 300,
  damping: 15,
  mass: 0.6,
};

// ─── Hook: Embla Controls ────────────────────────────────────
function useEmblaControls(emblaApi: EmblaCarouselType | undefined) {
  const [selectedIndex, setSelectedIndex] = React.useState(0);
  const [scrollSnaps, setScrollSnaps] = React.useState<number[]>([]);

  const onDotClick = React.useCallback(
    (index: number) => emblaApi?.scrollTo(index),
    [emblaApi],
  );
  const onPrev = React.useCallback(() => emblaApi?.scrollPrev(), [emblaApi]);
  const onNext = React.useCallback(() => emblaApi?.scrollNext(), [emblaApi]);

  React.useEffect(() => {
    if (!emblaApi) return;

    const onInit = () => {
      setScrollSnaps(emblaApi.scrollSnapList());
      setSelectedIndex(emblaApi.selectedScrollSnap());
    };
    const onSelect = () => setSelectedIndex(emblaApi.selectedScrollSnap());

    onInit();
    emblaApi.on('reInit', onInit).on('select', onSelect);
    return () => {
      emblaApi.off('reInit', onInit).off('select', onSelect);
    };
  }, [emblaApi]);

  return { selectedIndex, scrollSnaps, onDotClick, onPrev, onNext };
}

// ─── Hero Carousel ───────────────────────────────────────────
export function HeroCarousel({ slides, options }: HeroCarouselProps) {
  const autoplayPlugin = React.useMemo(
    () => Autoplay({ delay: 5000, stopOnInteraction: false }),
    [],
  );

  const [emblaRef, emblaApi] = useEmblaCarousel(
    { loop: true, ...options },
    [autoplayPlugin],
  );
  const { selectedIndex, scrollSnaps, onDotClick, onPrev, onNext } =
    useEmblaControls(emblaApi);

  return (
    <section className="relative bg-brand-black overflow-hidden">
      <div className="relative w-full" ref={emblaRef}>
        <div className="flex touch-pan-y">
          {slides.map((slide, i) => {
            const isActive = i === selectedIndex;

            return (
              <motion.div
                key={i}
                className="flex-none w-full relative"
                style={{ minWidth: '100%' }}
              >
                {/* Background Image */}
                <div className="relative h-[350px] md:h-[550px]">
                  <Image
                    src={slide.image}
                    alt={`Banner ${i + 1}`}
                    fill
                    className="object-cover"
                    priority={i === 0}
                    sizes="100vw"
                  />
                  <div className="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent" />

                  {/* Content with animated entrance */}
                  <div
                    className={`absolute inset-0 flex flex-col justify-center px-8 md:px-20 max-w-7xl mx-auto ${
                      slide.align === 'right'
                        ? 'items-end text-right'
                        : slide.align === 'center'
                          ? 'items-center text-center'
                          : ''
                    }`}
                  >
                    {slide.badge && (
                      <motion.span
                        initial={{ opacity: 0, y: -20 }}
                        animate={isActive ? { opacity: 1, y: 0 } : { opacity: 0, y: -20 }}
                        transition={{ ...bouncyTransition, delay: 0.1 }}
                        className="bg-brand-orange text-white text-xs font-bold px-3 py-1.5 w-fit mb-4 uppercase tracking-widest"
                      >
                        {slide.badge}
                      </motion.span>
                    )}

                    <motion.h2
                      initial={{ opacity: 0, x: slide.align === 'right' ? 60 : -60 }}
                      animate={
                        isActive
                          ? { opacity: 1, x: 0 }
                          : { opacity: 0, x: slide.align === 'right' ? 60 : -60 }
                      }
                      transition={{ ...springTransition, delay: 0.2 }}
                      className="text-4xl md:text-7xl font-display font-bold text-white uppercase mb-4 leading-[0.9]"
                    >
                      {slide.title}
                    </motion.h2>

                    <motion.p
                      initial={{ opacity: 0, y: 20 }}
                      animate={isActive ? { opacity: 1, y: 0 } : { opacity: 0, y: 20 }}
                      transition={{ ...springTransition, delay: 0.35 }}
                      className="text-gray-200 mb-8 max-w-lg font-light text-sm md:text-base"
                    >
                      {slide.desc}
                    </motion.p>

                    <motion.div
                      initial={{ opacity: 0, scale: 0.8 }}
                      animate={isActive ? { opacity: 1, scale: 1 } : { opacity: 0, scale: 0.8 }}
                      transition={{ ...bouncyTransition, delay: 0.45 }}
                    >
                      <Link
                        href={slide.ctaHref}
                        className={`px-8 py-3 w-fit font-display font-bold uppercase tracking-wider transition-all duration-300 inline-block hover:scale-105 ${
                          slide.align === 'right'
                            ? 'bg-white text-black hover:bg-gray-200'
                            : 'bg-brand-orange text-white hover:bg-orange-600 shadow-lg shadow-brand-orange/30'
                        }`}
                      >
                        {slide.cta}
                      </Link>
                    </motion.div>
                  </div>
                </div>
              </motion.div>
            );
          })}
        </div>
      </div>

      {/* Navigation Arrows */}
      <motion.button
        onClick={onPrev}
        whileHover={{ scale: 1.1 }}
        whileTap={{ scale: 0.9 }}
        transition={bouncyTransition}
        className="absolute left-4 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-brand-orange/90 text-white p-3 backdrop-blur-sm transition-colors duration-300 z-10"
      >
        <ChevronLeft className="w-6 h-6" />
      </motion.button>

      <motion.button
        onClick={onNext}
        whileHover={{ scale: 1.1 }}
        whileTap={{ scale: 0.9 }}
        transition={bouncyTransition}
        className="absolute right-4 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-brand-orange/90 text-white p-3 backdrop-blur-sm transition-colors duration-300 z-10"
      >
        <ChevronRight className="w-6 h-6" />
      </motion.button>

      {/* Dots */}
      <div className="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10">
        {scrollSnaps.map((_, index) => (
          <motion.button
            key={index}
            type="button"
            onClick={() => onDotClick(index)}
            layout
            initial={false}
            className="cursor-pointer rounded-full bg-white/80"
            animate={{
              width: index === selectedIndex ? 32 : 10,
              height: 10,
              opacity: index === selectedIndex ? 1 : 0.5,
              backgroundColor: index === selectedIndex ? '#D64500' : 'rgba(255,255,255,0.8)',
            }}
            transition={springTransition}
          />
        ))}
      </div>
    </section>
  );
}
