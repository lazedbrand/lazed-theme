# CLAUDE.md

Context for any Claude session working on this repo. Read this first.

## What this is

The design layer for **lazed-official.com**, a hemp and THC store. It is a
**child theme of Storefront** (WooCommerce's own theme, by Automattic).

This repo contains **only presentation**. It does not contain products,
prices, orders, customers or inventory. All of that lives in the WooCommerce
database on the server and is rendered dynamically.

If someone asks to "add a product" or "change a price", that is a WooCommerce
admin task, not a code change. Say so rather than hardcoding it here.

## Stack

| Layer | What | Notes |
|---|---|---|
| CMS | WordPress | Self hosted |
| Commerce | WooCommerce | 11.1.1 as of Oct 2026 |
| Payments | **Bankful** | High-risk gateway. Hemp and THC rule out Stripe, PayPal, Square, Shopify Payments |
| Parent theme | Storefront | Chosen because it tracks WooCommerce releases and does not break on update |
| Host | Bluehost, shared cPanel | Site root: `public_html/website_4651d41a` |

## Hard rules

1. **Never touch payment, tax, shipping or order logic.** This is a theme.
   If a change requires altering checkout behaviour, stop and flag it.
2. **Never commit credentials.** No API keys, no `wp-config.php`, no FTP
   details. Deploy secrets live in GitHub Secrets, entered by the owner.
3. **Cart and checkout get light styling only.** Surfaces, colours, type.
   Do not restructure the markup. The Bankful gateway renders inside those
   templates and the owner cannot afford a broken checkout.
4. **Test on staging first.** Bluehost provides staging. Design changes
   should never land straight on production.
5. **The compliance block in `functions.php` is a legal requirement.**
   Do not remove it. Same for the 21+ age gate, which is handled by a plugin.

## File map

```
lazed-theme/
├── style.css        Theme header + the entire design system
├── functions.php    Enqueues, shop layout, announcement bar, footer compliance
├── woocommerce/     Template overrides. Copy from the WooCommerce plugin, never write from scratch
├── assets/          Images, custom JS, local fonts
└── .github/workflows/deploy.yml
```

## Design system

Everything visual is driven by CSS custom properties at the top of
`style.css`. To restyle the store, change tokens there rather than chasing
individual rules.

- **Palette:** warm cream ground (`--lzd-cream`), near-black structure
  (`--lzd-ink`), candy accents (pink, tangerine, lemon, lime, grape, sky)
- **Signature:** chunky 3px black borders with a hard offset shadow
  (`--lzd-shadow`). This is what makes it feel like one object. Keep it.
- **Type:** Fredoka for display, Nunito for body
- **Logo:** heavy black marker wordmark. Black carries the structure so the
  logo never competes with the accent colours.

## Voice

Warm and friendly, not loud. The audience includes people who find THC
intimidating. Lead with reassurance, be specific about dosing, never hype.

"Start with half and wait an hour" over "THESE HIT HARD".

**Never use em dashes.** Owner preference, applies to all copy.

## Product structure, for layout decisions only

13 products. All variable (flavour or size options) **except Micros 500MG**,
which is the only simple product. That is why variable products say
"Pick your flavor" rather than "Add to cart": WooCommerce cannot add a
variable product without a variation selected.

Categories: THC Gummies, Mushroom Gummies, Sips, Vapes, Flower, Apparel, Samples.

## History worth knowing

In Oct 2026 a WooCommerce auto-update took the site down for most of a day.
Two premium plugins fatal-errored, Flatsome's shortcodes stopped rendering,
and the whole store was offline. That is why the theme is now a Storefront
child and why auto-updates should stay off.

Be conservative. This is a live store with real revenue.
