/**
 * Hair By ReneNeme — Modern Animation Engine
 * Scroll reveals, stagger, ripple, progress bar, counters
 */
(function () {
    'use strict';

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const prefersFinePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    const motionCardSelector = [
        '.glow-card',
        '.service-card-hover',
        '.lift-card',
        '.about-card',
        '.reference-card',
        '.price-stat',
        '.booking-info-box',
        '.booking-choice-card',
        '.booking-date-card',
        '.booking-time-card',
        '.booking-summary-card',
        '.reservation-result__box',
    ].join(',');

    /* ── Scroll progress bar ── */
    function initScrollProgress() {
        const bar = document.getElementById('scrollProgress');
        if (!bar || prefersReducedMotion.matches) return;

        function update() {
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
            bar.style.width = pct.toFixed(2) + '%';
        }

        window.addEventListener('scroll', update, { passive: true });
        update();
    }

    /* ── Consistent native smooth anchor scroll ── */
    function initSmoothScroll() {
        const headerOffset = 84;

        document.addEventListener('click', event => {
            const link = event.target.closest?.('a[href^="#"], a[href*=".php#"]');
            if (!link) return;

            const href = link.getAttribute('href') || '';
            const hash = href.includes('#') ? href.slice(href.indexOf('#')) : href;
            if (!hash || hash === '#') return;

            const isSamePage = !href.includes('.php') || href.split('#')[0] === window.location.pathname.split('/').pop();
            const target = isSamePage ? document.querySelector(hash) : null;
            if (!target) return;

            event.preventDefault();
            const top = target.getBoundingClientRect().top + window.scrollY - headerOffset;
            window.scrollTo({
                top: Math.max(0, top),
                behavior: prefersReducedMotion.matches ? 'auto' : 'smooth',
            });
            history.pushState(null, '', hash);
        });
    }

    /* ── Enhanced Intersection Observer for reveals ── */
    function initScrollReveals() {
        if (prefersReducedMotion.matches) {
            document.querySelectorAll('.section-reveal, .stagger-reveal, .reveal-item').forEach(el => {
                el.classList.add('is-visible');
            });
            return;
        }

        const revealObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '0px 0px -8% 0px',
            threshold: 0.1,
        });

        // Section reveals
        document.querySelectorAll('.section-reveal').forEach(el => {
            revealObserver.observe(el);
        });

        // Stagger reveals
        document.querySelectorAll('.stagger-reveal').forEach(el => {
            revealObserver.observe(el);
        });

        // Individual reveal items
        document.querySelectorAll('.reveal-item').forEach((el, i) => {
            el.style.transitionDelay = (i * 80) + 'ms';
            revealObserver.observe(el);
        });
    }

    /* ── Text cascade reveals for headings/copy ── */
    function initTextCascades() {
        const cascadeBlocks = Array.from(document.querySelectorAll([
            '.homepage-hero__copy',
            '.homepage-hero__card',
            '#about .max-w-2xl',
            '.reference-showcase .max-w-lg',
            '#services > .section-reveal > .max-w-2xl',
            '#services .glow-card > div > div:first-child',
            '#booking .section-reveal > p + h2',
            'footer .max-w-md',
        ].join(','))).filter(Boolean);

        const normalizedBlocks = new Set();
        cascadeBlocks.forEach(block => {
            const target = block.matches('h1, h2, h3') ? block.parentElement : block;
            if (target) {
                normalizedBlocks.add(target);
            }
        });

        normalizedBlocks.forEach(block => {
            block.classList.add('text-cascade');
            Array.from(block.children)
                .filter(child => {
                    const tag = child.tagName.toLowerCase();
                    return ['p', 'h1', 'h2', 'h3', 'div', 'a', 'details'].includes(tag);
                })
                .forEach((child, index) => {
                    child.classList.add('text-cascade-item');
                    child.style.setProperty('--cascade-index', String(index));
                });
        });

        function reveal(block) {
            block.classList.remove('is-replaying');
            block.classList.add('is-visible');
        }

        function replay(container) {
            if (prefersReducedMotion.matches) return;
            const scope = container || document;
            const blocks = scope.matches?.('.text-cascade')
                ? [scope]
                : Array.from(scope.querySelectorAll?.('.text-cascade') || []);

            blocks.forEach(block => {
                block.classList.add('is-replaying');
                block.classList.remove('is-visible');
                window.setTimeout(() => {
                    reveal(block);
                }, 70);
            });
        }

        window.replayTextCascades = replay;

        if (prefersReducedMotion.matches) {
            normalizedBlocks.forEach(reveal);
            return;
        }

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    reveal(entry.target);
                }
            });
        }, {
            rootMargin: '0px 0px -10% 0px',
            threshold: 0.14,
        });

        normalizedBlocks.forEach(block => observer.observe(block));

        document.addEventListener('click', event => {
            const link = event.target.closest?.('a[href^="#"]');
            if (!link) return;

            const hash = link.getAttribute('href');
            if (!hash || hash === '#') return;

            const target = document.querySelector(hash);
            if (!target) return;

            window.setTimeout(() => replay(target), 520);
        });

        window.addEventListener('hashchange', () => {
            const target = document.querySelector(window.location.hash);
            if (target) {
                window.setTimeout(() => replay(target), 260);
            }
        });

        if (window.location.hash) {
            const target = document.querySelector(window.location.hash);
            if (target) {
                window.setTimeout(() => replay(target), 520);
            }
        }
    }

    /* ── Ripple effect on buttons ── */
    function initRipple() {
        document.querySelectorAll('a.inline-flex[href="#booking"], a.inline-flex[href="index.php#booking"], a.inline-flex[href="rezervace.php"], a.inline-flex[href^="rezervace.php?"]').forEach(btn => {
            btn.classList.add('booking-shine');
        });

        document.querySelectorAll('.ripple-btn, .ui-button, .ui-button-secondary, .ui-button-ghost-dark').forEach(btn => {
            btn.classList.add('ripple-btn');
            btn.addEventListener('click', function (e) {
                if (prefersReducedMotion.matches) return;

                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height) * 2;
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                const wave = document.createElement('span');
                wave.classList.add('ripple-wave');
                wave.style.cssText = `width:${size}px;height:${size}px;left:${x}px;top:${y}px;`;
                this.appendChild(wave);

                setTimeout(() => wave.remove(), 620);
            });
        });
    }

    /* ── Mobile menu slide animation ── */
    function initMobileMenuAnimation() {
        const btn = document.getElementById('mobileMenuButton');
        const menu = document.getElementById('mobileMenu');
        const menuIconOpen = document.getElementById('menuIconOpen');
        const menuIconClose = document.getElementById('menuIconClose');
        if (!btn || !menu) return;

        function setMenuState(isOpen) {
            menu.classList.toggle('hidden', !isOpen);
            menu.classList.toggle('is-open', isOpen);
            menu.style.display = isOpen ? 'block' : 'none';
            menuIconOpen?.classList.toggle('hidden', isOpen);
            menuIconClose?.classList.toggle('hidden', !isOpen);
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            btn.setAttribute('aria-label', isOpen ? 'Zavřít menu' : 'Otevřít menu');
        }

        setMenuState(false);

        btn.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            const isOpen = menu.classList.contains('is-open');
            if (!isOpen) {
                menu.classList.remove('hidden');
                menu.style.display = 'block';
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => setMenuState(true));
                });
                return;
            }

            if (prefersReducedMotion.matches) {
                setMenuState(false);
            } else {
                menu.classList.remove('is-open');
                setTimeout(() => {
                    if (!menu.classList.contains('is-open')) {
                        setMenuState(false);
                    }
                }, 340);
            }
        }, true);

        menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => setMenuState(false), true);
        });
    }

    /* ── Nav active section highlight ── */
    function initNavHighlight() {
        const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
        if (!navLinks.length) return;

        const sections = Array.from(navLinks)
            .map(link => document.querySelector(link.getAttribute('href')))
            .filter(Boolean);

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    navLinks.forEach(link => {
                        const isActive = link.getAttribute('href') === '#' + entry.target.id;
                        link.classList.toggle('is-active', isActive);
                    });
                }
            });
        }, { rootMargin: '-30% 0px -60% 0px', threshold: 0 });

        sections.forEach(s => observer.observe(s));
    }

    /* ── Counter animation ── */
    function animateCounter(el) {
        const target = parseInt(el.dataset.countTo, 10);
        const duration = parseInt(el.dataset.countDuration || '1400', 10);
        const suffix = el.dataset.countSuffix || '';
        const start = performance.now();

        function step(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            // Ease out cubic
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.round(eased * target);
            el.textContent = current + suffix;

            if (progress < 1) {
                requestAnimationFrame(step);
            }
        }

        requestAnimationFrame(step);
    }

    function initCounters() {
        if (prefersReducedMotion.matches) return;

        const counters = document.querySelectorAll('[data-count-to]');
        if (!counters.length) return;

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(el => observer.observe(el));
    }

    /* ── Smooth hover glow on service cards ── */
    function initCardGlow() {
        document.querySelectorAll('.glow-card, .service-card-hover').forEach(card => {
            card.addEventListener('mousemove', function (e) {
                if (prefersReducedMotion.matches) return;
                const rect = this.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                this.style.setProperty('--mouse-x', x + '%');
                this.style.setProperty('--mouse-y', y + '%');
            });
        });
    }

    /* ── Pointer tilt for cards and booking choices ── */
    function initCardTilt(root = document) {
        if (prefersReducedMotion.matches || !prefersFinePointer.matches) return;

        const cards = Array.from(root.querySelectorAll?.(motionCardSelector) || []);
        cards.forEach(card => {
            if (card.dataset.tiltReady === 'true' || card.closest('.gallery-lightbox')) return;

            card.dataset.tiltReady = 'true';
            card.classList.add('tilt-card');

            let bounds = null;
            let raf = 0;
            const maxTilt = card.matches('.booking-choice-card, .booking-date-card, .booking-time-card') ? 4.5 : 7;
            const maxLift = card.matches('.booking-choice-card, .booking-date-card, .booking-time-card') ? -3 : -6;
            const resetTransform = 'perspective(900px) translate3d(0, 0, 0) rotateX(0deg) rotateY(0deg) scale(1)';

            function update(event) {
                raf = 0;
                if (!bounds) bounds = card.getBoundingClientRect();
                const localX = event.clientX - bounds.left;
                const localY = event.clientY - bounds.top;
                const pctX = Math.max(0, Math.min(1, localX / bounds.width));
                const pctY = Math.max(0, Math.min(1, localY / bounds.height));
                const rotateY = ((pctX - 0.5) * maxTilt * 2).toFixed(2) + 'deg';
                const rotateX = ((0.5 - pctY) * maxTilt * 2).toFixed(2) + 'deg';

                card.style.setProperty('--mouse-x', (pctX * 100).toFixed(1) + '%');
                card.style.setProperty('--mouse-y', (pctY * 100).toFixed(1) + '%');
                card.style.transform = `perspective(900px) translate3d(0, ${maxLift}px, 0) rotateX(${rotateX}) rotateY(${rotateY}) scale(1.012)`;
            }

            card.addEventListener('pointerenter', () => {
                bounds = card.getBoundingClientRect();
                card.classList.add('is-tilting');
                card.style.transition = 'transform 90ms linear, box-shadow 260ms ease, border-color 260ms ease, background 260ms ease';
            });

            card.addEventListener('pointermove', event => {
                if (card.matches(':disabled, .is-unavailable, .is-closed, .is-loading')) return;
                if (!raf) {
                    raf = requestAnimationFrame(() => update(event));
                }
            }, { passive: true });

            card.addEventListener('pointerleave', () => {
                bounds = null;
                card.classList.remove('is-tilting');
                card.style.transition = 'transform 360ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 260ms ease, border-color 260ms ease, background 260ms ease';
                card.style.transform = resetTransform;
                window.setTimeout(() => {
                    if (!card.classList.contains('is-tilting')) {
                        card.style.transform = '';
                        card.style.transition = '';
                    }
                }, 380);
            });
        });
    }

    function observeDynamicMotionCards() {
        if (prefersReducedMotion.matches || !prefersFinePointer.matches || !window.MutationObserver) return;

        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        initCardTilt(node);
                        if (node.matches?.(motionCardSelector)) {
                            initCardTilt(node.parentElement || document);
                        }
                    }
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    function initBookingStepMotion() {
        window.animateBookingStepChange = function (step) {
            if (prefersReducedMotion.matches || !step) return;

            Array.from(step.children).forEach((child, index) => {
                child.animate([
                    { opacity: 0, transform: 'translate3d(0, 16px, 0)' },
                    { opacity: 1, transform: 'translate3d(0, 0, 0)' },
                ], {
                    duration: 440,
                    delay: index * 45,
                    easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
                    fill: 'both',
                });
            });
        };
    }

    /* ── Parallax on hero image (enhanced) ── */
    function initHeroParallax() {
        const hero = document.querySelector('.homepage-hero');
        const media = document.querySelector('.homepage-hero__media');
        if (!hero || !media || prefersReducedMotion.matches) return;

        let ticking = false;
        function update() {
            const rect = hero.getBoundingClientRect();
            const progress = Math.min(1, Math.max(0, -rect.top / Math.max(1, rect.height)));
            media.style.setProperty('--hero-media-y', `${progress * 96}px`);
            ticking = false;
        }

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(update);
                ticking = true;
            }
        }, { passive: true });
    }

    /* ── Booking form field animations ── */
    function initFormAnimations() {
        const form = document.getElementById('bookingForm');
        if (!form) return;

        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('focus', function () {
                this.closest('div')?.classList.add('field-focused');
            });
            field.addEventListener('blur', function () {
                this.closest('div')?.classList.remove('field-focused');
            });
        });
    }

    /* ── Scroll-triggered ambient glow ── */
    function initAmbientGlows() {
        if (prefersReducedMotion.matches) return;

        document.querySelectorAll('.has-ambient-glow').forEach(section => {
            const glow = section.querySelector('.ambient-glow');
            if (!glow) return;

            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    glow.style.opacity = entry.isIntersecting ? '1' : '0';
                });
            }, { threshold: 0.2 });

            observer.observe(section);
        });
    }

    /* ── Init all ── */
    function init() {
        initScrollProgress();
        initSmoothScroll();
        initScrollReveals();
        initTextCascades();
        initRipple();
        initNavHighlight();
        initCounters();
        initCardGlow();
        initCardTilt();
        observeDynamicMotionCards();
        initHeroParallax();
        initFormAnimations();
        initAmbientGlows();
        initBookingStepMotion();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
