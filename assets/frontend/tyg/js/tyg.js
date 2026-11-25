/* TYG — interactions & motion (GSAP + ScrollTrigger + Lenis) */
(function () {
    'use strict';

    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => [...r.querySelectorAll(s)];
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const hasGsap = !!window.gsap;
    const isDesktop = () => window.matchMedia('(min-width: 901px)').matches;

    /* ---------------- Smooth scroll ---------------- */
    let lenis = null;
    if (window.Lenis && !reduce) {
        lenis = new Lenis({ duration: 1.15, easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)) });
        window.TYGLenis = lenis;
        if (hasGsap && window.ScrollTrigger) {
            lenis.on('scroll', ScrollTrigger.update);
            gsap.ticker.add((time) => lenis.raf(time * 1000));
            gsap.ticker.lagSmoothing(0);
        } else {
            const raf = (t) => { lenis.raf(t); requestAnimationFrame(raf); };
            requestAnimationFrame(raf);
        }
    }

    // Anchor links go through Lenis
    document.addEventListener('click', (e) => {
        const a = e.target.closest('a[href*="#"]');
        if (!a) return;
        const url = new URL(a.href, location.href);
        if (url.pathname !== location.pathname || !url.hash) return;
        const target = document.querySelector(url.hash);
        if (!target) return;
        e.preventDefault();
        header.classList.remove('menu-open');
        lenis ? lenis.scrollTo(target, { offset: -90 }) : target.scrollIntoView({ behavior: 'smooth' });
    });

    /* ---------------- Header ---------------- */
    const header = $('[data-header]');
    let lastY = 0;
    const onScroll = (y) => {
        header.classList.toggle('is-scrolled', y > 30);
        const menuOpen = header.classList.contains('menu-open');
        header.classList.toggle('is-hidden', !menuOpen && y > 400 && y > lastY + 2);
        if (y < lastY - 2) header.classList.remove('is-hidden');
        lastY = y;
    };
    lenis ? lenis.on('scroll', ({ scroll }) => onScroll(scroll)) : window.addEventListener('scroll', () => onScroll(scrollY), { passive: true });

    $('[data-menu-toggle]')?.addEventListener('click', () => header.classList.toggle('menu-open'));

    // Search overlay
    const search = $('[data-search]');
    $('[data-search-open]')?.addEventListener('click', () => { search.classList.add('is-open'); setTimeout(() => search.querySelector('input').focus(), 200); });
    $('[data-search-close]')?.addEventListener('click', () => search.classList.remove('is-open'));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') search?.classList.remove('is-open'); });

    /* ---------------- Cursor + magnetic ---------------- */
    const cursor = $('.tyg-cursor');
    if (cursor && hasGsap && matchMedia('(hover: hover)').matches) {
        const xTo = gsap.quickTo(cursor, 'x', { duration: .35, ease: 'power3' });
        const yTo = gsap.quickTo(cursor, 'y', { duration: .35, ease: 'power3' });
        window.addEventListener('mousemove', (e) => { cursor.style.opacity = 1; xTo(e.clientX); yTo(e.clientY); });
        document.addEventListener('mouseover', (e) => {
            cursor.classList.toggle('is-big', !!e.target.closest('a, button, [data-slide]'));
        });
    }
    if (hasGsap && !reduce) {
        $$('[data-magnetic]').forEach((el) => {
            el.addEventListener('mousemove', (e) => {
                const r = el.getBoundingClientRect();
                gsap.to(el, { x: (e.clientX - r.left - r.width / 2) * .25, y: (e.clientY - r.top - r.height / 2) * .35, duration: .4, ease: 'power3' });
            });
            el.addEventListener('mouseleave', () => gsap.to(el, { x: 0, y: 0, duration: .7, ease: 'elastic.out(1, .4)' }));
        });
    }

    /* ---------------- Accordion ---------------- */
    $$('[data-accordion]').forEach((acc) => {
        const items = $$('.tyg-acc__item', acc);
        const toggle = (item, open) => {
            const panel = $('.tyg-acc__a', item);
            item.classList.toggle('is-open', open);
            if (hasGsap) gsap.to(panel, { height: open ? 'auto' : 0, duration: .55, ease: 'power3.inOut', onComplete: () => window.ScrollTrigger && ScrollTrigger.refresh() });
            else panel.style.height = open ? 'auto' : 0;
        };
        items.forEach((item) => $('.tyg-acc__q', item).addEventListener('click', () => {
            const open = !item.classList.contains('is-open');
            items.forEach((i) => i !== item && i.classList.contains('is-open') && toggle(i, false));
            toggle(item, open);
        }));
    });

    /* ---------------- Sliders ---------------- */
    function slider(root, { auto = 0, perView = () => 1 } = {}) {
        const track = $('[data-track]', root);
        const slides = $$('[data-slide]', track);
        const dotsWrap = $('[data-dots]', root);
        let index = 0, timer;
        const max = () => Math.max(0, slides.length - perView());
        const step = () => {
            if (slides.length < 2) return 0;
            return slides[1].offsetLeft - slides[0].offsetLeft;
        };
        const dots = [];
        const buildDots = () => {
            if (!dotsWrap) return;
            dotsWrap.innerHTML = '';
            dots.length = 0;
            for (let i = 0; i <= max(); i++) {
                const b = document.createElement('button');
                b.type = 'button';
                b.setAttribute('aria-label', `Go to slide ${i + 1}`);
                b.addEventListener('click', () => go(i, true));
                dotsWrap.appendChild(b);
                dots.push(b);
            }
        };
        const go = (i, user) => {
            index = (i + max() + 1) % (max() + 1);
            const x = -index * step();
            if (hasGsap) gsap.to(track, { x, duration: .9, ease: 'expo.out' });
            else track.style.transform = `translateX(${x}px)`;
            dots.forEach((d, k) => d.classList.toggle('is-active', k === index));
            if (hasGsap) {
                const s = slides[index];
                gsap.fromTo(s.querySelectorAll('h3, p, li, .tyg-btn, img'), { y: 24, opacity: 0 }, { y: 0, opacity: 1, duration: .7, stagger: .025, ease: 'power3.out', delay: .1 });
            }
            if (user) restart();
        };
        const restart = () => { if (!auto) return; clearInterval(timer); timer = setInterval(() => go(index + 1), auto); };
        $('[data-next]', root)?.addEventListener('click', () => go(index + 1, true));
        $('[data-prev]', root)?.addEventListener('click', () => go(index - 1, true));

        // Drag / swipe
        let sx = null;
        track.addEventListener('pointerdown', (e) => { sx = e.clientX; });
        window.addEventListener('pointerup', (e) => {
            if (sx === null) return;
            const dx = e.clientX - sx; sx = null;
            if (Math.abs(dx) > 50) go(index + (dx < 0 ? 1 : -1), true);
        });
        root.addEventListener('mouseenter', () => clearInterval(timer));
        root.addEventListener('mouseleave', restart);
        window.addEventListener('resize', () => { buildDots(); go(Math.min(index, max())); });

        buildDots();
        dots[0]?.classList.add('is-active');
        restart();
    }
    $$('[data-slider="services"]').forEach((r) => slider(r, { auto: 6000 }));
    $$('[data-slider="reviews"]').forEach((r) => slider(r, { auto: 4500, perView: () => (isDesktop() ? 2 : 1) }));

    /* ---------------- Tabs (SEO process) ---------------- */
    $$('[data-tabs]').forEach((tabs) => {
        const panelsRoot = $('[data-tab-panel]');
        const btns = $$('[data-tab]', tabs);
        let current = 0, timer;
        const show = (i) => {
            if (i === current && panelsRoot.querySelector('.is-active')) {
                // still animate bars on first run
            }
            btns.forEach((b, k) => b.classList.toggle('is-active', k === i));
            const panels = $$('[data-panel]', panelsRoot);
            const prev = panels[current], next = panels[i];
            if (hasGsap && prev !== next) {
                gsap.to(prev, { autoAlpha: 0, y: -30, duration: .4, ease: 'power2.in', onComplete: () => prev.classList.remove('is-active') });
            } else if (prev !== next) prev.classList.remove('is-active');
            next.classList.add('is-active');
            if (hasGsap) {
                gsap.fromTo(next, { autoAlpha: 0, y: 40 }, { autoAlpha: 1, y: 0, duration: .7, delay: .2, ease: 'power3.out' });
                gsap.fromTo(panelsRoot, { backgroundColor: ['#ddabec', '#d9e6c5', '#fdd9d5', '#f6d79e'][i] }, { backgroundColor: ['#ddabec', '#d9e6c5', '#fdd9d5', '#f6d79e'][i], duration: .1 });
            }
            current = i;
        };
        btns.forEach((b, i) => b.addEventListener('click', () => { show(i); clearInterval(timer); }));
        // auto-advance while in view
        if (window.IntersectionObserver) {
            new IntersectionObserver(([en]) => {
                clearInterval(timer);
                if (en.isIntersecting) timer = setInterval(() => show((current + 1) % btns.length), 4000);
            }, { threshold: .4 }).observe(panelsRoot);
        }
    });

    /* ---------------- Typer (SEO search pill) ---------------- */
    $$('[data-typer]').forEach((el) => {
        const words = el.dataset.typer.split('|');
        let w = 0, c = words[0].length, del = true;
        const tick = () => {
            const word = words[w];
            c += del ? -1 : 1;
            el.textContent = word.slice(0, c) || ' ';
            let d = del ? 70 : 120;
            if (!del && c === word.length) { del = true; d = 1800; }
            else if (del && c === 0) { del = false; w = (w + 1) % words.length; d = 300; }
            setTimeout(tick, d);
        };
        setTimeout(tick, 2500);
    });

    /* ---------------- Hero word rotator ---------------- */
    $$('[data-rotator]').forEach((el) => {
        const words = $$('em', el);
        let i = 0;
        if (!hasGsap || words.length < 2) return;
        gsap.set(words, { yPercent: 110, opacity: 1 });
        gsap.set(words[0], { yPercent: 0 });
        setInterval(() => {
            const cur = words[i]; i = (i + 1) % words.length; const nxt = words[i];
            gsap.to(cur, { yPercent: -110, duration: .7, ease: 'expo.inOut' });
            gsap.fromTo(nxt, { yPercent: 110 }, { yPercent: 0, duration: .7, ease: 'expo.inOut' });
        }, 2400);
    });

    /* ---------------- Card fan (getquoti-style) ----------------
       Desktop: fanned cards, hovering one makes it the centre of the fan.
       Phones: a swipeable carousel that centres the focused card. */
    function fan(root) {
        const cards = $$('[data-fan-card]', root);
        if (!cards.length) return;
        const rot = Number(root.dataset.fanRot) || 4;
        const yStep = Number(root.dataset.fanY) || 26;
        const mid = Math.floor((cards.length - 1) / 2);
        let focus = mid, hover = null, timer = null, visible = false;

        const layout = () => {
            const mobile = !isDesktop();
            const f = mobile ? focus : (hover === null ? mid : hover);
            cards.forEach((c, i) => {
                const d = i - f, ad = Math.abs(d);
                c.style.setProperty('--tr', `${Math.max(-2, Math.min(2, d)) * rot}deg`);
                c.style.setProperty('--ty', `${Math.min(ad, 2) * yStep}px`);
                c.style.setProperty('--ts', d === 0 ? 1.06 : 1 - Math.min(ad, 2) * .02);
                c.style.setProperty('--tx', !mobile && hover !== null && d !== 0 ? `${Math.sign(d) * 28}px` : '0px');
                c.style.zIndex = String(20 - ad * 2);
                c.classList.toggle('is-focus', d === 0);
            });
            if (mobile) {
                const c = cards[f];
                const x = root.parentElement.clientWidth / 2 - (c.offsetLeft + c.offsetWidth / 2);
                root.style.setProperty('--fx', `${x}px`);
            } else {
                root.style.setProperty('--fx', '0px');
            }
        };
        const go = (i) => { focus = (i + cards.length) % cards.length; layout(); };
        const auto = () => {
            clearInterval(timer);
            if (!isDesktop() && visible) timer = setInterval(() => go(focus + 1), 3200);
        };

        cards.forEach((c, i) => {
            c.addEventListener('mouseenter', () => { if (isDesktop()) { hover = i; layout(); } });
            c.addEventListener('click', (e) => {
                if (!isDesktop() && i !== focus) { e.preventDefault(); go(i); auto(); }
            });
        });
        root.addEventListener('mouseleave', () => { hover = null; layout(); });

        let sx = null;
        root.addEventListener('pointerdown', (e) => { sx = e.clientX; });
        root.addEventListener('pointerup', (e) => {
            if (sx === null) return;
            const dx = e.clientX - sx; sx = null;
            if (!isDesktop() && Math.abs(dx) > 40) { go(focus + (dx < 0 ? 1 : -1)); auto(); }
        });

        if (window.IntersectionObserver) {
            new IntersectionObserver(([en]) => { visible = en.isIntersecting; auto(); }, { threshold: .3 }).observe(root);
        }
        window.addEventListener('resize', () => { layout(); auto(); });
        layout();
    }
    $$('[data-fan]').forEach(fan);

    if (!hasGsap || reduce) {
        $$('[data-split], [data-reveal]').forEach((el) => (el.style.opacity = 1));
        return;
    }

    /* ======================================================
       GSAP motion
       ====================================================== */
    gsap.registerPlugin(ScrollTrigger);

    // Split headings into masked words (keeps <em> / <br> / <span>)
    function splitWords(el) {
        const walk = (node) => {
            [...node.childNodes].forEach((n) => {
                if (n.nodeType === 3) {
                    const frag = document.createDocumentFragment();
                    n.textContent.split(/(\s+)/).forEach((part) => {
                        if (!part) return;
                        if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(' ')); return; }
                        const w = document.createElement('span'); w.className = 'tyg-word';
                        const inner = document.createElement('span'); inner.textContent = part;
                        w.appendChild(inner); frag.appendChild(w);
                    });
                    n.replaceWith(frag);
                } else if (n.nodeType === 1 && n.tagName !== 'BR') walk(n);
            });
        };
        walk(el);
        return $$('.tyg-word > span', el);
    }

    $$('[data-split]').forEach((el) => {
        const words = splitWords(el);
        gsap.from(words, {
            yPercent: 115, rotate: 4, duration: 1.1, ease: 'expo.out', stagger: .06,
            scrollTrigger: { trigger: el, start: 'top 85%' },
        });
    });

    $$('[data-reveal]').forEach((el) => {
        gsap.from(el, { y: 40, opacity: 0, duration: 1, ease: 'power3.out', scrollTrigger: { trigger: el, start: 'top 90%' } });
    });

    // ---- Hero intro
    const hero = $('.tyg-hero');
    if (hero) {
        const tl = gsap.timeline({ defaults: { ease: 'expo.out' }, delay: .15 });
        tl.from('.tyg-nav', { y: -80, opacity: 0, duration: 1.1 })
          .from('[data-hero-blob]', { scale: 0, duration: 1.6, transformOrigin: '50% 50%' }, '<.1')
          .from('.tyg-hero__title .tyg-line > *', { yPercent: 120, duration: 1.2, stagger: .12 }, '<.2')
          .from('[data-hero-laptop]', { x: 160, y: 80, rotate: 12, opacity: 0, duration: 1.6 }, '<.1')
          .from('[data-hero-fade]', { y: 30, opacity: 0, duration: 1, stagger: .1 }, '<.5')
          .from('.tyg-trust__faces img', { scale: 0, duration: .6, stagger: .06, ease: 'back.out(2)' }, '<.2')
          .from('[data-chip]', { scale: 0, opacity: 0, duration: .9, stagger: .12, ease: 'back.out(1.8)' }, '<')
          .from('.tyg-marquee', { opacity: 0, y: 30, duration: 1 }, '<.2');

        $$('[data-oval]').forEach((o, i) => {
            const el = $('ellipse', o);
            const len = el.getTotalLength ? el.getTotalLength() : 600;
            gsap.set(el, { strokeDasharray: len, strokeDashoffset: len });
            tl.from(o, { opacity: 0, scale: .6, duration: .8, ease: 'back.out(2)' }, 1 + i * .2)
              .to(el, { strokeDashoffset: 0, duration: 1.2, ease: 'power2.inOut' }, '<');
            gsap.to(o, { y: i % 2 ? 10 : -10, duration: 2.4 + i * .4, yoyo: true, repeat: -1, ease: 'sine.inOut', delay: 2 });
        });

        // Hand-drawn arrows from the labels to the laptop
        $$('.tyg-hero__arrows path').forEach((path, i) => {
            const len = path.getTotalLength();
            gsap.set(path, { strokeDasharray: len, strokeDashoffset: len });
            tl.to(path, { strokeDashoffset: 0, duration: .7, ease: 'power2.out' }, 1.6 + Math.floor(i / 2) * .22 + (i % 2) * .45);
        });

        // Home hero: the red orbit draws itself, the sparks pop in, then both idle gently
        const orbit = $('[data-hero-orbit] ellipse');
        if (orbit) {
            const len = orbit.getTotalLength ? orbit.getTotalLength() : 3000;
            gsap.set(orbit, { strokeDasharray: len, strokeDashoffset: len });
            tl.to(orbit, { strokeDashoffset: 0, duration: 1.8, ease: 'power2.inOut' }, .6);
            gsap.to('[data-hero-orbit]', { rotate: 1.5, duration: 6, yoyo: true, repeat: -1, ease: 'sine.inOut', delay: 2.4, transformOrigin: '50% 50%' });
        }
        $$('[data-hero-sparks] path').forEach((p, i) => {
            const len = p.getTotalLength();
            gsap.set(p, { strokeDasharray: len, strokeDashoffset: len });
            tl.to(p, { strokeDashoffset: 0, duration: .45, ease: 'power2.out' }, 1.5 + i * .12);
        });
        if ($('[data-hero-sparks]')) gsap.to('[data-hero-sparks]', { scale: 1.08, duration: 1.2, yoyo: true, repeat: -1, ease: 'sine.inOut', delay: 2.6, transformOrigin: '20% 80%' });

        // Gentle idle float. The green circle deliberately stays still on scroll.
        gsap.to('[data-hero-laptop]', { y: -14, rotate: -1.2, duration: 3, yoyo: true, repeat: -1, ease: 'sine.inOut', delay: 2 });
        $$('[data-chip]').forEach((c, i) => gsap.to(c, { y: i % 2 ? 12 : -12, duration: 2.2 + i * .3, yoyo: true, repeat: -1, ease: 'sine.inOut', delay: 2 }));

        // Labels drift slightly with the mouse (desktop only)
        if (isDesktop()) {
            hero.addEventListener('mousemove', (e) => {
                const x = (e.clientX / innerWidth - .5);
                gsap.to('[data-oval], [data-chip]', { x: x * 30, duration: 1, ease: 'power3' });
            });
        }
    }

    // ---- Marquee (velocity-reactive)
    $$('[data-marquee]').forEach((m) => {
        const track = $('.tyg-marquee__track', m);
        const group = $('.tyg-marquee__group', m);
        let x = 0, speed = 1.5, boost = 0;
        const loop = () => {
            const w = group.offsetWidth;
            x -= speed + boost;
            boost *= .92;
            if (x <= -w) x += w;
            if (x > 0) x -= w;
            track.style.transform = `translate3d(${x}px,0,0)`;
        };
        gsap.ticker.add(loop);
        if (lenis) lenis.on('scroll', ({ velocity }) => { boost = Math.max(-6, Math.min(8, velocity * .4)); });
        m.addEventListener('mouseenter', () => (speed = .4));
        m.addEventListener('mouseleave', () => (speed = 1.5));
    });

    // ---- Pillars: pin the section and slide the cards right -> left (getquoti pin-spacer style).
    // Each card's tilt / drop is driven by its distance from the viewport centre, giving the wave.
    $$('[data-pillars]').forEach((row) => {
        const section = row.closest('.tyg-pillars');
        const cards = $$('.tyg-pillar', row);
        const wave = () => {
            const vw = innerWidth;
            cards.forEach((c) => {
                const r = c.getBoundingClientRect();
                const dx = Math.max(-1.2, Math.min(1.2, (r.left + r.width / 2 - vw / 2) / vw));
                const k = isDesktop() ? 1 : .45; // gentler wave on phones
                c.style.setProperty('--wr', `${(dx * 26 * k).toFixed(2)}deg`);
                c.style.setProperty('--wy', `${(dx * dx * 240 * k).toFixed(1)}px`);
            });
        };
        const endX = () => {
            const vw = innerWidth, last = cards[cards.length - 1];
            return row.scrollWidth <= vw ? (vw - row.scrollWidth) / 2 : vw / 2 - (last.offsetLeft + last.offsetWidth / 2);
        };
        const startX = () => innerWidth * .9;
        const travel = () => Math.max(innerHeight * .8, (startX() - endX()) * 1.1);

        ScrollTrigger.create({ trigger: section, start: 'top top', end: () => `+=${travel()}`, pin: true, invalidateOnRefresh: true });
        gsap.fromTo(row, { x: startX }, {
            x: endX, ease: 'none',
            scrollTrigger: {
                trigger: section, start: 'top bottom', end: () => `+=${innerHeight + travel()}`,
                scrub: 1, invalidateOnRefresh: true, onUpdate: wave, onRefresh: wave,
            },
        });
        wave();
    });

    // ---- Powering: decorative motion
    $$('[data-wiggle]').forEach((el) => gsap.to(el, { rotate: -14, scale: 1.08, duration: .9, yoyo: true, repeat: -1, ease: 'sine.inOut' }));
    $$('[data-float]').forEach((el) => gsap.to(el, { y: -18, rotate: 8, duration: 2.6, yoyo: true, repeat: -1, ease: 'sine.inOut' }));
    $$('.tyg-mega').forEach((el) => gsap.fromTo(el, { scale: .86 }, { scale: 1, ease: 'none', scrollTrigger: { trigger: el, start: 'top bottom', end: 'center center', scrub: true } }));
    $$('.tyg-slider').forEach((el) => gsap.from(el, { y: 120, rotate: 2, opacity: 0, duration: 1.4, ease: 'expo.out', scrollTrigger: { trigger: el, start: 'top 85%' } }));

    // ---- Process: draw the red road as you scroll.
    // Desktop uses the hand-tuned SVG; phones get a path generated through the staggered cards.
    function mobileRoad(wrap, svg) {
        const W = wrap.clientWidth, H = wrap.scrollHeight;
        const steps = $$('[data-step]', wrap);
        svg.setAttribute('width', W);
        svg.setAttribute('height', H);
        svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
        let px = W / 2, py = steps[0].offsetTop - 46;
        let d = `M ${px} ${py}`;
        steps.forEach((s) => {
            const x = s.classList.contains('is-right') ? W * .075 : W * .925;
            const t = s.offsetTop, b = t + s.offsetHeight, my = (py + t) / 2;
            d += ` C ${px} ${my + 14}, ${x} ${my - 14}, ${x} ${t + 22} L ${x} ${b - 22}`;
            px = x; py = b - 22;
        });
        d += ` C ${px} ${py + 70}, ${W / 2} ${py + 40}, ${W / 2} ${py + 96}`;
        $('path', svg).setAttribute('d', d);
    }
    $$('[data-process]').forEach((wrap) => {
        const dPath = $('.tyg-process__path path', wrap);
        const mSvg = $('.tyg-process__mpath', wrap);
        let path = dPath, len = 0, progress = 0;
        const setup = () => {
            if (!isDesktop() && mSvg) { mobileRoad(wrap, mSvg); path = $('path', mSvg); } else { path = dPath; }
            [dPath, mSvg && $('path', mSvg)].forEach((p) => p && (p.style.strokeDasharray = 'none'));
            len = path.getTotalLength();
            path.style.strokeDasharray = len;
            path.style.strokeDashoffset = len * (1 - progress);
        };
        ScrollTrigger.create({
            trigger: wrap, start: 'top 75%', end: 'bottom 70%', scrub: 1,
            onRefresh: setup,
            onUpdate: (self) => { progress = self.progress; path.style.strokeDashoffset = len * (1 - progress); },
        });
        setup();
        $$('[data-step]', wrap).forEach((s) => {
            const right = s.classList.contains('is-right');
            gsap.from(s, { x: right ? 140 : -140, rotate: right ? 5 : -5, opacity: 0, duration: 1.2, ease: 'expo.out', scrollTrigger: { trigger: s, start: 'top 82%' } });
            gsap.from($('.tyg-step__no', s), { scale: 2.2, opacity: 0, duration: 1, ease: 'expo.out', scrollTrigger: { trigger: s, start: 'top 78%' } });
        });
    });
    $$('.tyg-spark i').forEach((s, i) => gsap.from(s, { scale: 0, opacity: 0, duration: .6, ease: 'back.out(3)', delay: (i % 3) * .1, scrollTrigger: { trigger: '.tyg-gameplan', start: 'top 75%' } }));

    // ---- Fans: cards are dealt out from the centre when they scroll in
    $$('[data-fan]').forEach((root) => {
        const cards = $$('[data-fan-card]', root);
        const mid = (cards.length - 1) / 2;
        gsap.from(cards, {
            translate: (i) => `${isDesktop() ? (mid - i) * 180 : 0}px 160px`,
            opacity: 0, duration: 1.4, ease: 'expo.out', stagger: .07,
            scrollTrigger: { trigger: root, start: 'top 85%' },
        });
    });

    // ---- Packages grid
    $$('[data-packages]').forEach((grid) => {
        ScrollTrigger.batch($$('[data-pkg]', grid), {
            start: 'top 90%',
            // animate row by row: cards sharing a row move together so the row stays level
            onEnter: (batch) => {
                const rows = {};
                batch.forEach((c) => { (rows[c.offsetTop] = rows[c.offsetTop] || []).push(c); });
                Object.keys(rows).map(Number).sort((a, b) => a - b).forEach((top, r) => {
                    gsap.from(rows[top], { y: 70, opacity: 0, duration: 1, delay: r * .12, ease: 'expo.out' });
                });
            },
            once: true,
        });
        // subtle tilt on hover
        if (isDesktop()) $$('[data-pkg]', grid).forEach((card) => {
            card.addEventListener('mousemove', (e) => {
                const r = card.getBoundingClientRect();
                gsap.to(card, { rotateY: ((e.clientX - r.left) / r.width - .5) * 6, rotateX: -((e.clientY - r.top) / r.height - .5) * 6, transformPerspective: 900, duration: .5 });
            });
            card.addEventListener('mouseleave', () => gsap.to(card, { rotateX: 0, rotateY: 0, duration: .8, ease: 'elastic.out(1,.5)' }));
        });
    });

    // ---- Team
    $$('[data-team]').forEach((t) => {
        const tl = gsap.timeline({ scrollTrigger: { trigger: t, start: 'top 75%' } });
        tl.from($('.tyg-team__blob', t), { scale: 0, rotate: -40, duration: 1.4, ease: 'expo.out' })
          .from($$('img', t), { yPercent: 40, opacity: 0, duration: 1.2, stagger: .15, ease: 'expo.out' }, '<.2');
        gsap.to($('.tyg-team__blob', t), { borderRadius: '58% 42% 55% 45% / 45% 58% 42% 55%', rotate: 8, duration: 5, yoyo: true, repeat: -1, ease: 'sine.inOut' });
    });

    // ---- FAQ image & SEO process panel parallax
    $$('.tyg-faq__img img, .tyg-slide__img img').forEach((img) => gsap.fromTo(img, { scale: 1.15 }, { scale: 1, ease: 'none', scrollTrigger: { trigger: img, start: 'top bottom', end: 'bottom top', scrub: true } }));
    $$('[data-tab-panel]').forEach((p) => gsap.from(p, { xPercent: 30, opacity: 0, duration: 1.4, ease: 'expo.out', scrollTrigger: { trigger: p, start: 'top 80%' } }));
    $$('.tyg-tab').forEach((t, i) => gsap.from(t, { x: -40, opacity: 0, duration: .9, delay: i * .08, ease: 'expo.out', scrollTrigger: { trigger: t, start: 'top 92%' } }));
    $$('.tyg-acc__item').forEach((t, i) => gsap.from(t, { y: 30, opacity: 0, duration: .8, delay: (i % 4) * .05, ease: 'power3.out', scrollTrigger: { trigger: t, start: 'top 95%' } }));

    // ---- Reviews
    $$('.tyg-review').forEach((r, i) => gsap.from(r, { x: 120, opacity: 0, duration: 1.1, delay: i * .08, ease: 'expo.out', scrollTrigger: { trigger: '.tyg-reviews', start: 'top 70%' } }));

    // ---- Footer
    gsap.from('.tyg-footer__logo', { scale: 0, rotate: -30, duration: 1, ease: 'back.out(2)', scrollTrigger: { trigger: '.tyg-footer', start: 'top 90%' } });
    gsap.from('.tyg-footer__cols > div', { y: 40, opacity: 0, duration: .9, stagger: .08, ease: 'power3.out', scrollTrigger: { trigger: '.tyg-footer__cols', start: 'top 95%' } });


    window.addEventListener('load', () => ScrollTrigger.refresh());
})();
