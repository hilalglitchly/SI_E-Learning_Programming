(() => {
    const prefersMotion = window.matchMedia('(prefers-reduced-motion: no-preference)').matches;

    // 1. Scroll progress bar fallback when CSS scroll-timeline is unsupported
    const progressEl = document.getElementById('homeScrollProgress');
    if (progressEl && prefersMotion && !CSS.supports('animation-timeline', 'scroll()')) {
        let ticking = false;
        const updateProgress = () => {
            const scrollable = document.documentElement.scrollHeight - window.innerHeight;
            const ratio = scrollable > 0 ? Math.min(1, Math.max(0, window.scrollY / scrollable)) : 0;
            progressEl.style.transform = `scaleX(${ratio})`;
            ticking = false;
        };
        window.addEventListener('scroll', () => {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(updateProgress);
            }
        }, { passive: true });
        updateProgress();
    }

    // 2. Scroll reveal fallback when CSS view() timeline is unsupported
    const supportsViewTimeline = CSS.supports('(animation-timeline: view()) and (animation-range: entry)');
    const revealEls = document.querySelectorAll('[data-home-reveal]');
    if (!supportsViewTimeline && prefersMotion && revealEls.length && 'IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries, obs) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        revealEls.forEach((el) => {
            el.classList.add('reveal-fallback-pending');
            revealObserver.observe(el);
        });
    }

    // 3. One-shot count-up animation for real DB stats
    const statEls = document.querySelectorAll('[data-count-target]');
    if (prefersMotion && statEls.length && 'IntersectionObserver' in window) {
        const animateCount = (el) => {
            const target = parseInt(el.dataset.countTarget || '0', 10);
            const suffix = el.dataset.countSuffix || '';
            if (isNaN(target) || target <= 0) return;

            const duration = 680;
            const start = performance.now();
            const step = (now) => {
                const t = Math.min(1, (now - start) / duration);
                const eased = 1 - Math.pow(1 - t, 3);
                el.textContent = `${Math.round(target * eased)}${suffix}`;
                if (t < 1) {
                    requestAnimationFrame(step);
                } else {
                    el.textContent = `${target}${suffix}`;
                }
            };
            requestAnimationFrame(step);
        };

        const statObserver = new IntersectionObserver((entries, obs) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    animateCount(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.35 });

        statEls.forEach((el) => statObserver.observe(el));
    }

    // 4. Course catalog filter, search, manual prev/next & pauseable auto-scroll
    const scrollContainer = document.getElementById('courseScroll');
    const filterButtons = document.querySelectorAll('[data-course-filter]');
    const searchInput = document.getElementById('courseSearch');
    const emptyBox = document.getElementById('courseEmptyState');
    const resetBtn = document.getElementById('courseResetBtn');
    const countLabel = document.getElementById('courseVisibleCount');
    const prevBtn = document.getElementById('coursePrevBtn');
    const nextBtn = document.getElementById('courseNextBtn');
    const pauseBtn = document.getElementById('coursePauseBtn');

    let activeFilter = 'all';
    let isManuallyPaused = !prefersMotion;
    let isHovered = false;
    let isFocused = false;
    let isDragging = false;
    let startX = 0;
    let startScrollLeft = 0;

    const updatePauseButton = () => {
        if (!pauseBtn) return;
        const icon = pauseBtn.querySelector('i');
        const label = pauseBtn.querySelector('[data-pause-label]');
        if (isManuallyPaused) {
            if (icon) icon.className = 'bx bx-play';
            if (label) label.textContent = 'Putar';
        } else {
            if (icon) icon.className = 'bx bx-pause';
            if (label) label.textContent = 'Jeda';
        }
    };

    const applyCatalogFilter = () => {
        if (!scrollContainer) return;
        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        const cards = scrollContainer.querySelectorAll('[data-course-card]');
        let visible = 0;

        cards.forEach((card) => {
            const tier = card.dataset.tier || 'gratis';
            const haystack = (card.dataset.search || '').toLowerCase();
            const tierMatch = activeFilter === 'all' || tier === activeFilter;
            const queryMatch = query === '' || haystack.includes(query);
            const show = tierMatch && queryMatch;
            card.hidden = !show;
            if (show) visible++;
        });

        if (countLabel) {
            countLabel.textContent = `Menampilkan ${visible} dari ${cards.length} kelas`;
        }
        if (emptyBox) {
            emptyBox.hidden = visible > 0;
        }
        scrollContainer.hidden = visible === 0;
        scrollContainer.scrollLeft = 0;
    };

    filterButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            activeFilter = btn.dataset.courseFilter || 'all';
            filterButtons.forEach((b) => {
                const active = b === btn;
                b.classList.toggle('is-active', active);
                b.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            applyCatalogFilter();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyCatalogFilter);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            activeFilter = 'all';
            if (searchInput) searchInput.value = '';
            filterButtons.forEach((b) => {
                const active = b.dataset.courseFilter === 'all';
                b.classList.toggle('is-active', active);
                b.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            applyCatalogFilter();
        });
    }

    if (scrollContainer) {
        const stepSize = () => Math.min(374, Math.max(280, scrollContainer.clientWidth * 0.8));

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                scrollContainer.scrollBy({ left: -stepSize(), behavior: prefersMotion ? 'smooth' : 'auto' });
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                scrollContainer.scrollBy({ left: stepSize(), behavior: prefersMotion ? 'smooth' : 'auto' });
            });
        }
        if (pauseBtn) {
            updatePauseButton();
            pauseBtn.addEventListener('click', () => {
                isManuallyPaused = !isManuallyPaused;
                updatePauseButton();
            });
        }

        // Gentle auto-scroll only when no search/filter is active and not paused
        const autoTick = () => {
            const hasActiveQuery = activeFilter !== 'all' || (searchInput && searchInput.value.trim() !== '');
            const maxScroll = scrollContainer.scrollWidth - scrollContainer.clientWidth;
            if (!isManuallyPaused && !isHovered && !isFocused && !isDragging && !hasActiveQuery && maxScroll > 8) {
                scrollContainer.scrollLeft += 0.7;
                if (scrollContainer.scrollLeft >= maxScroll - 1) {
                    scrollContainer.scrollLeft = 0;
                }
            }
            requestAnimationFrame(autoTick);
        };
        if (prefersMotion) {
            requestAnimationFrame(autoTick);
        }

        scrollContainer.addEventListener('mouseenter', () => { isHovered = true; });
        scrollContainer.addEventListener('mouseleave', () => { isHovered = false; isDragging = false; });
        scrollContainer.addEventListener('focusin', () => { isFocused = true; });
        scrollContainer.addEventListener('focusout', () => { isFocused = false; });
        scrollContainer.addEventListener('touchstart', () => { isHovered = true; }, { passive: true });
        scrollContainer.addEventListener('touchend', () => { isHovered = false; }, { passive: true });

        scrollContainer.addEventListener('mousedown', (e) => {
            if (e.target.closest('a, button')) return;
            isDragging = true;
            startX = e.pageX - scrollContainer.offsetLeft;
            startScrollLeft = scrollContainer.scrollLeft;
        });
        window.addEventListener('mouseup', () => { isDragging = false; });
        scrollContainer.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const x = e.pageX - scrollContainer.offsetLeft;
            scrollContainer.scrollLeft = startScrollLeft - (x - startX) * 1.5;
        });
    }

    // 5. Accessible Pause/Play control for contact marquee
    const marqueeTrack = document.getElementById('contactMarqueeTrack');
    const marqueePauseBtn = document.getElementById('marqueePauseBtn');
    if (marqueeTrack && marqueePauseBtn) {
        let marqueePaused = !prefersMotion;
        const syncMarquee = () => {
            marqueeTrack.classList.toggle('is-paused', marqueePaused);
            const label = marqueePauseBtn.querySelector('[data-marquee-label]');
            const icon = marqueePauseBtn.querySelector('i');
            if (label) label.textContent = marqueePaused ? 'Putar' : 'Jeda';
            if (icon) icon.className = marqueePaused ? 'bx bx-play' : 'bx bx-pause';
        };
        syncMarquee();
        marqueePauseBtn.addEventListener('click', () => {
            marqueePaused = !marqueePaused;
            syncMarquee();
        });
    }
})();
