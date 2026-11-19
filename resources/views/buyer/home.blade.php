@extends('layouts.app')

@section('title', 'Tix and You - Your Local Event Ticketing Partner')

@section('content')
@php
    $banners = [
        [
            'image' => 'images/hero-banner.png',
            'alt' => 'Tix and You - Tiket aman Anda Tenang',
            'bg' => '#14151A',
            'link' => null,
        ],
        [
            'image' => 'images/hero-banner-2.png',
            'alt' => 'Event Pilihan Tix and You',
            'bg' => '#14151A',
            'link' => null,
        ],
    ];
@endphp

<!-- Hero Banner Slider -->
<section id="hero-slider" class="relative overflow-hidden rounded-2xl bg-[#14151A] mb-10 border border-zinc-800 group select-none">
    <div id="hero-track" class="flex transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]">
        @foreach($banners as $banner)
            <div class="w-full shrink-0 flex items-center justify-center" style="background-color: {{ $banner['bg'] }};">
                @if($banner['link'])
                    <a href="{{ $banner['link'] }}" class="w-full flex items-center justify-center">
                        <img src="{{ asset($banner['image']) }}"
                             alt="{{ $banner['alt'] }}"
                             style="-webkit-mask-image: linear-gradient(to right, transparent 0%, black 1.5%, black 98.5%, transparent 100%); mask-image: linear-gradient(to right, transparent 0%, black 1.5%, black 98.5%, transparent 100%);"
                             class="w-auto h-[230px] sm:h-[340px] lg:h-[440px] max-w-full object-contain block">
                    </a>
                @else
                    <img src="{{ asset($banner['image']) }}"
                         alt="{{ $banner['alt'] }}"
                         style="-webkit-mask-image: linear-gradient(to right, transparent 0%, black 1.5%, black 98.5%, transparent 100%); mask-image: linear-gradient(to right, transparent 0%, black 1.5%, black 98.5%, transparent 100%);"
                         class="w-auto h-[230px] sm:h-[340px] lg:h-[440px] max-w-full object-contain block">
                @endif
            </div>
        @endforeach
    </div>

    <button type="button" id="hero-prev" aria-label="Slide sebelumnya"
            class="absolute left-3 sm:left-5 top-1/2 -translate-y-1/2 z-10 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-black/45 hover:bg-black/75 text-white border border-white/15 backdrop-blur-md flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 cursor-pointer">
        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>

    <button type="button" id="hero-next" aria-label="Slide berikutnya"
            class="absolute right-3 sm:right-5 top-1/2 -translate-y-1/2 z-10 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-black/45 hover:bg-black/75 text-white border border-white/15 backdrop-blur-md flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 cursor-pointer">
        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>

    <div class="absolute bottom-3.5 sm:bottom-4 left-1/2 -translate-x-1/2 z-10 flex items-center gap-2 px-3 py-1.5 rounded-full bg-black/30 backdrop-blur-xs border border-white/10">
        @foreach($banners as $index => $banner)
            <button type="button" data-slide="{{ $index }}" aria-label="Slide {{ $index + 1 }}"
                    class="hero-dot h-1.5 rounded-full transition-all duration-500 ease-out cursor-pointer {{ $index === 0 ? 'w-6 bg-white' : 'w-1.5 bg-white/40 hover:bg-white/70' }}">
            </button>
        @endforeach
    </div>
</section>

<!-- Katalog Event (6 Card per Slide: Grid 3 Kolom x 2 Baris + Smooth Slider) -->
<section id="katalog-event">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#0A0A0A]">Event Pilihan Untukmu</h2>
            <p class="text-sm text-[#52525B] mt-0.5">Semua event telah terverifikasi resmi oleh tim Tix and You</p>
        </div>

        <!-- Filter Kategori & Kontrol Slider -->
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="filterEventCarousel('all', this)"
                    class="cat-pill px-4 py-1.5 text-xs font-semibold rounded-full transition bg-[#0A0A0A] text-white cursor-pointer">
                Semua Event
            </button>
            @foreach($categories as $cat)
                <button type="button" onclick="filterEventCarousel('{{ strtolower($cat) }}', this)"
                        class="cat-pill px-4 py-1.5 text-xs font-semibold rounded-full transition bg-white text-[#52525B] border border-zinc-200 hover:border-[#0A0A0A] hover:text-[#0A0A0A] cursor-pointer">
                    {{ $cat }}
                </button>
            @endforeach

            <div id="event-nav-controls" class="flex items-center gap-1.5 ml-1 pl-2 border-l border-zinc-200">
                <button type="button" id="event-grid-prev" aria-label="Halaman Sebelumnya"
                        class="w-8 h-8 rounded-full bg-white border border-zinc-200 hover:border-[#0A0A0A] hover:bg-[#0A0A0A] hover:text-white flex items-center justify-center text-xs transition cursor-pointer">
                    &larr;
                </button>
                <button type="button" id="event-grid-next" aria-label="Halaman Berikutnya"
                        class="w-8 h-8 rounded-full bg-white border border-zinc-200 hover:border-[#0A0A0A] hover:bg-[#0A0A0A] hover:text-white flex items-center justify-center text-xs transition cursor-pointer">
                    &rarr;
                </button>
            </div>
        </div>
    </div>

    @if($events->isEmpty())
        <div class="bg-white rounded-2xl p-10 text-center border border-zinc-200/80">
            <p class="text-sm font-semibold text-[#0A0A0A]">Event tidak ditemukan</p>
            <p class="text-xs text-[#52525B] mt-1">Coba kata kunci lain atau tampilkan semua kategori event.</p>
            <a href="{{ route('home') }}" class="inline-block mt-4 px-4 py-2 text-xs font-semibold text-white bg-[#0A0A0A] rounded-lg">Reset Filter</a>
        </div>
    @else
        <!-- Sumber Seluruh Card Event -->
        <div id="event-cards-pool" class="hidden">
            @foreach($events as $event)
                @php
                    $minPrice = $event->ticketCategories->min('price');
                    $eventCat = $event->category ?? 'Musik & Konser';
                    $posterFile = $event->banner_path ?? $event->poster ?? $event->image ?? null;
                    $posterSrc = $posterFile
                        ? (str_starts_with($posterFile, 'http') ? $posterFile : asset('storage/' . ltrim(preg_replace('#^/storage/#', '', $posterFile), '/')))
                        : ($event->banner_url ?? null);
                @endphp

                <a href="{{ route('event.detail', ['event' => $event->slug]) }}"
                   data-event-card
                   data-category="{{ strtolower($eventCat) }}"
                   class="group bg-white rounded-2xl overflow-hidden border border-zinc-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col">

                    <!-- Header Poster Seragam (Tinggi Fix 220px + Badge Kategori + Judul Overlay) -->
                    <div style="height: 220px;"
                         class="relative w-full bg-gradient-to-br from-[#0A0A0A] to-zinc-800 p-5 flex flex-col justify-between overflow-hidden shrink-0">
                        @if($posterSrc)
                            <img src="{{ $posterSrc }}"
                                 alt="{{ $event->title }}"
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/25 to-black/15"></div>
                        @endif

                        <span class="relative z-10 self-start px-3 py-1 text-[11px] font-semibold bg-white text-[#0A0A0A] rounded-full shadow-xs">
                            {{ $eventCat }}
                        </span>

                        <span class="relative z-10 text-white font-bold text-base leading-snug truncate">
                            {{ $event->title }}
                        </span>
                    </div>

                    <!-- Detail Ringkas (Judul, Tanggal, Harga) -->
                    <div class="p-5 flex flex-col gap-2">
                        <h3 class="text-base font-bold text-[#0A0A0A] truncate group-hover:text-zinc-700 transition">
                            {{ $event->title }}
                        </h3>

                        <div class="flex items-center gap-1.5 text-xs text-[#52525B]">
                            <svg class="w-4 h-4 text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ \Carbon\Carbon::parse($event->event_date)->format('d F Y') }}</span>
                        </div>

                        <p class="text-base font-bold text-[#0A0A0A] pt-0.5">
                            @if($minPrice !== null && (float) $minPrice > 0)
                                Rp {{ number_format((float) $minPrice, 0, ',', '.') }}
                            @else
                                Gratis
                            @endif
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Viewport Carousel (6 Card per Slide: Grid 3x2) -->
        <div id="event-carousel-viewport" class="overflow-hidden py-1 -my-1">
            <div id="event-carousel-track" style="align-items: flex-start;" class="flex transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"></div>
        </div>

        <!-- Indikator Halaman Slide -->
        <div id="event-carousel-dots" class="flex items-center justify-center gap-2 mt-6"></div>
    @endif
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Hero Banner Slider
        const slider = document.getElementById('hero-slider');
        const track = document.getElementById('hero-track');
        const dots = document.querySelectorAll('.hero-dot');
        const prevBtn = document.getElementById('hero-prev');
        const nextBtn = document.getElementById('hero-next');

        if (slider && track && dots.length > 1) {
            let currentIndex = 0;
            const totalSlides = dots.length;
            let autoSlideTimer;
            let touchStartX = 0;

            function goToSlide(index) {
                currentIndex = (index + totalSlides) % totalSlides;
                track.style.transform = `translateX(-${currentIndex * 100}%)`;

                dots.forEach((dot, idx) => {
                    if (idx === currentIndex) {
                        dot.classList.remove('w-1.5', 'bg-white/40');
                        dot.classList.add('w-6', 'bg-white');
                    } else {
                        dot.classList.remove('w-6', 'bg-white');
                        dot.classList.add('w-1.5', 'bg-white/40');
                    }
                });
            }

            function startAutoSlide() {
                stopAutoSlide();
                autoSlideTimer = setInterval(() => goToSlide(currentIndex + 1), 3500);
            }

            function stopAutoSlide() {
                if (autoSlideTimer) clearInterval(autoSlideTimer);
            }

            prevBtn?.addEventListener('click', () => {
                goToSlide(currentIndex - 1);
                startAutoSlide();
            });

            nextBtn?.addEventListener('click', () => {
                goToSlide(currentIndex + 1);
                startAutoSlide();
            });

            dots.forEach((dot) => {
                dot.addEventListener('click', () => {
                    goToSlide(parseInt(dot.dataset.slide, 10));
                    startAutoSlide();
                });
            });

            slider.addEventListener('mouseenter', stopAutoSlide);
            slider.addEventListener('mouseleave', startAutoSlide);

            slider.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
                stopAutoSlide();
            }, { passive: true });

            slider.addEventListener('touchend', (e) => {
                const diff = touchStartX - e.changedTouches[0].screenX;
                if (Math.abs(diff) > 40) {
                    goToSlide(diff > 0 ? currentIndex + 1 : currentIndex - 1);
                }
                startAutoSlide();
            }, { passive: true });

            startAutoSlide();
        }

        // 2. Katalog Event Smooth Carousel (6 Card per Slide: Grid 3x2)
        const pool = document.getElementById('event-cards-pool');
        const carouselViewport = document.getElementById('event-carousel-viewport');
        const carouselTrack = document.getElementById('event-carousel-track');
        const dotsContainer = document.getElementById('event-carousel-dots');
        const gridPrevBtn = document.getElementById('event-grid-prev');
        const gridNextBtn = document.getElementById('event-grid-next');
        const navControls = document.getElementById('event-nav-controls');

        if (pool && carouselTrack) {
            const allCards = Array.from(pool.querySelectorAll('[data-event-card]'));
            const CARDS_PER_SLIDE = 6;
            let activePage = 0;
            let totalPages = 1;
            let touchStartGridX = 0;

            function updateCarouselPosition(pageIndex) {
                activePage = Math.max(0, Math.min(pageIndex, totalPages - 1));
                carouselTrack.style.transform = `translateX(-${activePage * 100}%)`;

                if (gridPrevBtn) {
                    gridPrevBtn.disabled = activePage === 0;
                    gridPrevBtn.style.opacity = activePage === 0 ? '0.35' : '1';
                }
                if (gridNextBtn) {
                    gridNextBtn.disabled = activePage >= totalPages - 1;
                    gridNextBtn.style.opacity = activePage >= totalPages - 1 ? '0.35' : '1';
                }

                if (dotsContainer) {
                    dotsContainer.querySelectorAll('button').forEach((dot, idx) => {
                        if (idx === activePage) {
                            dot.classList.remove('w-2', 'bg-zinc-300');
                            dot.classList.add('w-6', 'bg-[#0A0A0A]');
                        } else {
                            dot.classList.remove('w-6', 'bg-[#0A0A0A]');
                            dot.classList.add('w-2', 'bg-zinc-300');
                        }
                    });
                }
            }

            window.renderEventSlides = function(category = 'all') {
                const filtered = allCards.filter(card => {
                    const cardCat = (card.getAttribute('data-category') || '').toLowerCase();
                    return category === 'all' || cardCat === category;
                });

                carouselTrack.innerHTML = '';
                if (dotsContainer) dotsContainer.innerHTML = '';

                if (filtered.length === 0) {
                    carouselTrack.innerHTML = `
                        <div class="w-full shrink-0 bg-white rounded-2xl p-10 text-center border border-zinc-200/80">
                            <p class="text-sm font-semibold text-[#0A0A0A]">Belum ada event di kategori ini</p>
                            <p class="text-xs text-[#52525B] mt-1">Pilih kategori lain untuk melihat jadwal event yang tersedia.</p>
                        </div>
                    `;
                    totalPages = 1;
                    if (navControls) navControls.classList.add('hidden');
                    return;
                }

                totalPages = Math.ceil(filtered.length / CARDS_PER_SLIDE);

                for (let i = 0; i < filtered.length; i += CARDS_PER_SLIDE) {
                    const slidePage = document.createElement('div');
                    slidePage.className = 'w-full shrink-0 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
                    filtered.slice(i, i + CARDS_PER_SLIDE).forEach(card => {
                        slidePage.appendChild(card.cloneNode(true));
                    });
                    carouselTrack.appendChild(slidePage);
                }

                if (totalPages > 1) {
                    if (navControls) navControls.classList.remove('hidden');
                    if (dotsContainer) {
                        for (let p = 0; p < totalPages; p++) {
                            const dot = document.createElement('button');
                            dot.type = 'button';
                            dot.setAttribute('aria-label', `Halaman ${p + 1}`);
                            dot.className = `h-2 rounded-full transition-all duration-300 cursor-pointer ${p === 0 ? 'w-6 bg-[#0A0A0A]' : 'w-2 bg-zinc-300 hover:bg-zinc-400'}`;
                            dot.addEventListener('click', () => updateCarouselPosition(p));
                            dotsContainer.appendChild(dot);
                        }
                    }
                } else {
                    if (navControls) navControls.classList.add('hidden');
                }

                updateCarouselPosition(0);
            };

            gridPrevBtn?.addEventListener('click', () => updateCarouselPosition(activePage - 1));
            gridNextBtn?.addEventListener('click', () => updateCarouselPosition(activePage + 1));

            carouselViewport?.addEventListener('touchstart', (e) => {
                touchStartGridX = e.changedTouches[0].screenX;
            }, { passive: true });

            carouselViewport?.addEventListener('touchend', (e) => {
                if (totalPages <= 1) return;
                const diff = touchStartGridX - e.changedTouches[0].screenX;
                if (Math.abs(diff) > 45) {
                    updateCarouselPosition(diff > 0 ? activePage + 1 : activePage - 1);
                }
            }, { passive: true });

            renderEventSlides('all');
        }
    });

    function filterEventCarousel(category, btn) {
        document.querySelectorAll('.cat-pill').forEach(el => {
            el.classList.remove('bg-[#0A0A0A]', 'text-white');
            el.classList.add('bg-white', 'text-[#52525B]', 'border', 'border-zinc-200');
        });
        btn.classList.remove('bg-white', 'text-[#52525B]', 'border', 'border-zinc-200');
        btn.classList.add('bg-[#0A0A0A]', 'text-white');

        if (typeof window.renderEventSlides === 'function') {
            window.renderEventSlides(category);
        }
    }
</script>
@endpush
@endsection