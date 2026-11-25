<div class="tyg-cart" data-cart aria-hidden="true">
    <div class="tyg-cart__overlay" data-cart-close></div>
    <aside class="tyg-cart__panel" role="dialog" aria-label="Your cart">
        <div class="tyg-cart__head">
            <h3>Your <em>Cart</em></h3>
            <button type="button" class="tyg-cart__close" data-cart-close aria-label="Close cart">×</button>
        </div>

        <div class="tyg-cart__items" data-cart-items></div>

        <div class="tyg-cart__empty" data-cart-empty>
            <p>Your cart is empty.</p>
            <a href="{{ route('tyg.seo-services') }}#packages" class="tyg-btn tyg-btn--red">Browse Services</a>
        </div>

        <div class="tyg-cart__foot" data-cart-foot>
            <div class="tyg-cart__row"><span>Subtotal</span><strong data-cart-total>$0</strong></div>
            <p class="tyg-cart__note">Project details and payment method are collected at checkout.</p>
            <a href="{{ route('user.order.services') }}" class="tyg-btn tyg-btn--red tyg-btn--block" data-checkout>
                Proceed to Checkout
                <svg viewBox="0 0 16 16" width="14" height="14"><path d="M2 8h11M9 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
            </a>
            <button type="button" class="tyg-cart__clear" data-cart-clear>Clear cart</button>
        </div>
    </aside>
</div>
<div class="tyg-toast" data-toast></div>
