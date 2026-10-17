# LAZED theme

The design layer for [lazed-official.com](https://lazed-official.com). A child
theme of **Storefront**.

Products, prices, photos, stock and orders are **not** in this repo. They live
in WooCommerce and are rendered dynamically. Add a product in wp-admin and it
appears here automatically, correctly styled.

---

## First install

Do this on **staging** before production.

### 1. Install the parent theme

wp-admin → **Appearance → Themes → Add New** → search **Storefront** →
**Install**. Do **not** activate it. The child theme activates instead and
pulls from it.

### 2. Upload this theme

Zip the contents of this repo so the archive contains a single folder named
`lazed-theme`, then **Appearance → Themes → Add New → Upload Theme**.

Or via cPanel File Manager, upload to:

```
public_html/website_4651d41a/wp-content/themes/lazed-theme/
```

### 3. Activate and configure

- **Appearance → Themes** → activate **LAZED**
- **Appearance → Customize → Site Identity** → upload the LAZED logo
- **Appearance → Menus** → assign the primary menu
- Load the shop and a product page and check everything renders

### 4. Check out before anything else

Add a product to the cart and go all the way to the checkout screen.
**Confirm the Bankful payment option appears.** If it doesn't, stop and
investigate before going near production.

---

## Making changes

Edit `style.css`. Nearly everything visual is controlled by the CSS custom
properties at the top of that file.

Want a different accent colour across the entire store? Change one line:

```css
--lzd-pink: #FF3D7F;
```

Common edits:

| Change | Where |
|---|---|
| Colours, fonts, borders, shadows | `:root` block in `style.css` |
| Announcement bar text | `lazed_announcement_bar()` in `functions.php` |
| Products per row | `lazed_loop_columns()` in `functions.php` |
| Products per page | `lazed_products_per_page()` in `functions.php` |
| Compliance text | `lazed_compliance_notice()` in `functions.php` |

---

## Deploying

`.github/workflows/deploy.yml` handles it, once three secrets are added:

**Settings → Secrets and variables → Actions**

- `FTP_SERVER`
- `FTP_USERNAME`
- `FTP_PASSWORD`

Create a **dedicated FTP account** in cPanel scoped to the theme folder.
Don't reuse the main cPanel login.

Then:

- Push to `staging` → deploys to staging automatically
- Production → **Actions** tab → **Deploy theme** → **Run workflow** →
  choose `production`

Production is deliberately manual. Nothing reaches the live store without a
human clicking the button.

---

## If a change breaks something

```bash
git revert HEAD
git push
```

Then redeploy. Because the theme is the only thing versioned here, the worst
case is a site that looks wrong. Products, orders and customers are in the
database and are never touched by a deploy.

---

## Rules worth not breaking

1. Never commit `wp-config.php`, API keys, or FTP credentials
2. Never change payment, tax or shipping logic from the theme
3. Style cart and checkout, don't restructure them. Bankful renders in there
4. Keep the compliance notice and the 21+ gate. Both are legal requirements
5. Keep WooCommerce auto-updates **off**. See `CLAUDE.md` for why

---

## WooCommerce template overrides

To restyle a WooCommerce template, copy the original from
`wp-content/plugins/woocommerce/templates/` into `lazed-theme/woocommerce/`,
preserving the path, then edit the copy.

Always copy, never write from scratch. These templates carry hooks that other
plugins depend on, including the payment gateway.

```
woocommerce/
├── archive-product.php
├── content-product.php
└── single-product/
    └── title.php
```
