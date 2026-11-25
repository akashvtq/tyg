/**
 * TYG cart.
 *
 * Stored in localStorage under "order_summary" — the exact shape the existing
 * checkout page (resources/views/frontend/order/updatedata.blade.php) already
 * reads and merges:  { [slug]: { name, price, value: qty, projects: {} } }
 * so "Proceed to Checkout" lands on user.order.services with the cart prefilled,
 * and a successful order there clears it. No backend changes required.
 */
(function () {
    'use strict';

    const KEY = 'order_summary';
    const cfg = window.TYG || {};
    const money = (n) => (cfg.currency || '$') + Number(n).toLocaleString(undefined, { maximumFractionDigits: 2 });

    const read = () => {
        try { return JSON.parse(localStorage.getItem(KEY)) || {}; } catch (e) { return {}; }
    };
    const write = (data) => {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) { /* storage blocked */ }
        render();
        document.dispatchEvent(new CustomEvent('tyg:cart', { detail: data }));
    };

    const Cart = {
        items() {
            return Object.entries(read())
                .filter(([, i]) => i && Number(i.value) > 0)
                .map(([slug, i]) => ({ slug, name: i.name, price: Number(i.price) || 0, qty: Number(i.value) }));
        },
        count() { return this.items().reduce((n, i) => n + i.qty, 0); },
        total() { return this.items().reduce((n, i) => n + i.qty * i.price, 0); },
        add(slug, name, price, qty = 1) {
            const data = read();
            const cur = data[slug] || { name, price, value: 0, projects: {} };
            cur.name = name || cur.name;
            cur.price = Number(price);
            cur.value = Number(cur.value || 0) + qty;
            cur.projects = cur.projects || {};
            data[slug] = cur;
            write(data);
        },
        set(slug, qty) {
            const data = read();
            if (!data[slug]) return;
            data[slug].value = Math.max(0, qty);
            write(data);
        },
        remove(slug) { this.set(slug, 0); },
        clear() {
            const data = read();
            Object.keys(data).forEach((k) => { data[k].value = 0; data[k].projects = {}; });
            write(data);
        },
    };
    window.TYGCart = Cart;

    /* ---------- DOM ---------- */
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => [...r.querySelectorAll(s)];
    const drawer = $('[data-cart]');
    const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    function render() {
        const items = Cart.items();
        const count = Cart.count();

        $$('[data-cart-count]').forEach((el) => {
            el.textContent = count;
            el.classList.toggle('has-items', count > 0);
        });

        // Reflect cart state on Add to Cart buttons
        $$('[data-add-to-cart]').forEach((btn) => {
            const inCart = items.find((i) => i.slug === btn.dataset.slug);
            const label = btn.querySelector('span');
            if (label) label.textContent = inCart ? `In Cart (${inCart.qty}) · Add More` : 'Add to Cart';
            btn.classList.toggle('is-added', !!inCart);
        });

        if (!drawer) return;
        drawer.classList.toggle('is-empty', items.length === 0);
        $('[data-cart-items]', drawer).innerHTML = items.map((i) => `
            <div class="tyg-cart-item" data-slug="${esc(i.slug)}">
                <div>
                    <h5>${esc(i.name)}</h5>
                    <div class="tyg-cart-item__price">${money(i.price)} each</div>
                </div>
                <div class="tyg-cart-item__line">${money(i.price * i.qty)}</div>
                <div class="tyg-qty">
                    <button type="button" data-qty="-1" aria-label="Decrease">−</button>
                    <span>${i.qty}</span>
                    <button type="button" data-qty="1" aria-label="Increase">+</button>
                </div>
                <button type="button" class="tyg-cart-item__remove" data-remove>Remove</button>
            </div>`).join('');
        $('[data-cart-total]', drawer).textContent = money(Cart.total());
    }

    function open() {
        if (!drawer) return;
        drawer.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
        window.TYGLenis && window.TYGLenis.stop();
    }
    function close() {
        if (!drawer) return;
        drawer.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        window.TYGLenis && window.TYGLenis.start();
    }
    Cart.open = open;
    Cart.close = close;

    let toastTimer;
    function toast(msg) {
        const t = $('[data-toast]');
        if (!t) return;
        t.textContent = msg;
        t.classList.add('is-on');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => t.classList.remove('is-on'), 2200);
    }

    // Dot that flies from the button into the header cart icon
    function fly(fromEl) {
        const target = $('.tyg-cart-btn');
        if (!target || !window.gsap) return Promise.resolve();
        const a = fromEl.getBoundingClientRect();
        const b = target.getBoundingClientRect();
        const dot = document.createElement('div');
        dot.className = 'tyg-fly';
        document.body.appendChild(dot);
        const x0 = a.left + a.width / 2 - 8, y0 = a.top + a.height / 2 - 8;
        const x1 = b.left + b.width / 2 - 8, y1 = b.top + b.height / 2 - 8;
        gsap.set(dot, { x: x0, y: y0 });
        return new Promise((resolve) => {
            const tl = gsap.timeline({ onComplete: () => { dot.remove(); resolve(); } });
            tl.to(dot, { x: x1, duration: .8, ease: 'power1.inOut' }, 0)
              .to(dot, { y: Math.min(y0, y1) - 120, duration: .35, ease: 'power2.out' }, 0)
              .to(dot, { y: y1, duration: .45, ease: 'power2.in' }, .35)
              .to(dot, { scale: .3, duration: .2 }, .6);
        });
    }

    function bump() {
        const btn = $('.tyg-cart-btn');
        if (btn && window.gsap) gsap.fromTo(btn, { scale: 1 }, { scale: 1.35, duration: .18, yoyo: true, repeat: 1, ease: 'power2.out' });
    }

    document.addEventListener('click', (e) => {
        const add = e.target.closest('[data-add-to-cart]');
        if (add) {
            e.preventDefault();
            const { slug, name, price } = add.dataset;
            fly(add).then(() => {
                Cart.add(slug, name, price, 1);
                bump();
                toast(`${name} added to cart`);
            });
            return;
        }
        if (e.target.closest('[data-cart-open]')) { e.preventDefault(); open(); return; }
        if (e.target.closest('[data-cart-close]')) { close(); return; }
        if (e.target.closest('[data-cart-clear]')) { Cart.clear(); return; }

        const item = e.target.closest('.tyg-cart-item');
        if (item) {
            const slug = item.dataset.slug;
            const cur = Cart.items().find((i) => i.slug === slug);
            const q = e.target.closest('[data-qty]');
            if (q && cur) Cart.set(slug, cur.qty + Number(q.dataset.qty));
            if (e.target.closest('[data-remove]')) Cart.remove(slug);
        }
    });

    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
    // Keep tabs in sync
    window.addEventListener('storage', (e) => { if (e.key === KEY) render(); });

    render();
})();
