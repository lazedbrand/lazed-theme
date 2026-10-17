# assets/images

Artwork that is part of the **design** lives here, versioned in git with the
theme. Product photos do **not** belong here, those stay in WooCommerce.

---

## Files the homepage expects

Drop your two background images in this folder with exactly these names:

| Filename | Used by | Your reference |
|---|---|---|
| `thc-floral.webp` | THC panel of the split banner | the daisy and paisley one |
| `mushroom-swirl.webp` | Mushroom panel | the flowing liquid swirl one |

**Nothing breaks if they're missing.** `lazed_art_url()` checks the file
exists first, so an absent file just leaves a plain pastel panel rather than
a broken image.

---

## Changing which file a panel uses

In `front-page.php`, in the `$lzd['split']` array:

```php
'art'     => 'thc-floral.webp',   // filename in this folder, '' for none
'art_pos' => 'center',           // 'top right', 'bottom left', etc.
```

`art_pos` is worth knowing about. The images are roughly square and the
panels are tall, so the browser crops to fill. If the interesting part of
the artwork ends up cut off, nudge it with `art_pos` rather than re-exporting
the image.

---

## Before you commit them

**Compress them first.** These sit at the top of the homepage, so their
weight is felt on every visit, especially on phones. Run them through
[Squoosh](https://squoosh.app) or TinyPNG and aim for **under 250KB each**.

If either file is a photo-style raster rather than flat shapes, export as
**WebP** and change the filename in `front-page.php` to match. Flat
illustration with few colors usually compresses better as PNG.

Roughly **1600px wide** is plenty. The panels are never wider than about
half a 1200px container, and larger than that is wasted bytes.

---

## A note on the scrim

`style.css` lays a soft cream gradient over the artwork so the headline
stays readable no matter how busy the pattern gets underneath.

If a future image is darker and the text starts to struggle, raise the alpha
values in `.lzd-split-panel.has-art::before`. If the artwork is washing out
too much, lower them.
