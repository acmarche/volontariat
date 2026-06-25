# Help

## Compile email CSS (Tailwind for emails)

```bash
npx tailwindcss --content src/AcMarche/Volontariat/templates/emails/**/*.html.twig -i assets/styles/app.css -o src/AcMarche/Volontariat/public/assets/css/email.css --watch
```

## Assets : install & compile (production)

This project uses **AssetMapper + SymfonyCasts Tailwind** (no Webpack/Encore).

Two pieces:

- **Tailwind CSS** is compiled by `symfonycasts/tailwind-bundle` (standalone binary). In dev it compiles on the fly; in prod it must be built ahead of time.
- **JS & other assets** are handled by AssetMapper. There is no bundling step: `importmap.php` is the source of truth, vendor packages live in `assets/vendor/` and are served as native ES modules. For prod you "compile" by dumping versioned, digested copies into `public/assets/`.

### Production deploy sequence

```bash
# 1. Install PHP deps without dev, optimized autoloader
composer install --no-dev --optimize-autoloader

# 2. Download importmap JS vendors into assets/vendor/
php bin/console importmap:install

# 3. Build Tailwind in minified/production mode
php bin/console tailwind:build --minify

# 4. Compile the asset map: writes digested files to public/assets/
#    + manifest.json + importmap.json (with content hashes)
php bin/console asset-map:compile
```

Set `APP_ENV=prod` for steps 3–4.

### What each step produces

- **`tailwind:build --minify`** → writes the compiled CSS (output target configured in `config/packages/symfonycasts_tailwind.yaml`). Without `--minify` you get unminified dev output.
- **`asset-map:compile`** → reads `importmap.php` + all asset paths, copies each file to `public/assets/` with a content hash in the filename (cache-busting), and writes `manifest.json` / `importmap.json`. After this, Symfony serves the static files directly — it does not resolve assets at runtime.

### Gotchas

- **Order matters**: run `tailwind:build` *before* `asset-map:compile`, so the compiled CSS gets digested. If you rebuild Tailwind after compiling, recompile the asset map.
- **`composer install` auto-runs** `assets:install` and `importmap:install` (via the `auto-scripts` hook in `composer.json`), but **not** `tailwind:build` or `asset-map:compile` — those are manual/CI steps.
- **`assets:install`** (classic Symfony command) only copies *bundle* public assets into `public/bundles/` — separate concern from AssetMapper.
- In **dev**, skip `asset-map:compile` and run `php bin/console tailwind:build --watch`; assets are served live.
