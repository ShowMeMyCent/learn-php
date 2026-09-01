# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

reveal.js — the open source HTML presentation framework (revealjs.com). Core is vanilla JS/TS, no framework. A separate `@revealjs/react` wrapper package lives in `react/`.

## Commands

Run from the repo root unless noted.

- `npm install` — install dependencies (Node >= 22.12.0 required)
- `npm start` / `npm run dev` — start the Vite dev server (default port 8000) to preview `index.html` / `demo.html`
- `npm run build` — full production build: type-check (`tsc`), build core (`dist/reveal.js`/`.mjs`), build styles, then build each plugin (highlight, markdown, math, notes, search, zoom) individually
- `npm run build:core` — build just core JS + styles (skips plugins), faster for core-only iteration
- `npm run build:styles` — build only the CSS/Sass output
- `npm run build:es5` — build core, then additionally emit an ES5-compatible bundle via `scripts/build-es5.js`
- `npm test` — runs `scripts/test.js`: starts a local Vite server and runs every `test/*.html` file through QUnit in headless Puppeteer. Add a new test by dropping a `test-*.html` file in `test/` (see existing ones for the QUnit harness pattern) — no separate registration needed, they're globbed automatically
- `npm run package` — zip a distributable release (`scripts/zip.js`)

### React wrapper (`react/`)

The react package has its own npm project and its own guidance file at [react/AGENTS.md](react/AGENTS.md) — read it before touching anything under `react/`. Key rules from there:
- Run `npm test --prefix react` and `npm run build --prefix react` after any change in `react/`
- `Deck` owns the Reveal lifecycle/config/event wiring and structure-level `sync()`; `sync()` is expensive and must not be called on every render — only when slide structure (added/removed/reordered/regrouped) actually changes
- Keep markdown/highlight behavior aligned with the core markdown plugin unless intentionally diverging
- Update the colocated `*.test.tsx` file and `react/README.md` alongside any behavior change

From the root you can also run `npm run react:build`, `npm run react:demo`, `npm run react:test` (these just shell out with `--prefix react`).

## Architecture

### Core (`js/`)

`js/reveal.js` is the heart of the framework: a large factory function (`export default function(revealElement, options) {...}`) that returns a `Reveal` instance. Multiple independent presentations can run on one page because state (config, indices, DOM refs, etc.) is closed over per-instance rather than global. `js/index.ts` wraps this in a thin backwards-compatible singleton shell that mimics the pre-4.0 global `Reveal.initialize()` API (queues `configure`/`on`/`off`/`registerPlugin` calls made before init).

Responsibilities are split into **controller classes** (`js/controllers/*.js`), each instantiated once per deck and given a direct reference back to the owning `Reveal` instance (dependency injection via constructor, not singletons):

- `slidecontent`, `slidenumber`, `jumptoslide` — slide DOM/content handling
- `backgrounds`, `autoanimate` — slide backgrounds and Auto-Animate transitions
- `scrollview`, `printview`, `overview` — alternate rendering/navigation modes
- `fragments` — step-by-step fragment reveal logic
- `keyboard`, `pointer`, `touch`, `focus` — input handling
- `location` — URL hash / deep-linking sync
- `controls`, `progress` — on-screen navigation UI
- `plugins` — loads/registers external plugins and dependencies (see `js/controllers/plugins.js`)
- `overlay` — help/iframe overlays
- `notes` — speaker notes broadcast channel

`js/components/playback.js` is a smaller standalone UI component (playback indicator). `js/utils/` holds cross-cutting helpers: `util.ts` (DOM/misc helpers), `device.ts` (feature/platform detection), `color.ts`, `constants.ts` (selectors, blacklists), `loader.ts` (dynamic script loading used by the plugins controller). `js/config.ts` defines `defaultConfig` and the `RevealConfig` type; `js/reveal.d.ts` is the hand-maintained public API type surface.

When adding behavior, prefer extending the relevant controller over adding logic directly to `reveal.js`; controllers communicate with the core and each other only through the shared `Reveal` instance reference passed into their constructors.

### Plugins (`plugin/*`)

Each plugin (`highlight`, `markdown`, `math`, `notes`, `search`, `zoom`) is a self-contained package: `plugin.js` (the `Plugin()` factory implementing `init(deck)`/etc.), `index.ts` (entry point), and its own `vite.config.ts` that builds independently into `dist/plugin/<name>.js`/`.mjs`. `plugin/vite-plugin-dts.ts` provides a shared `createPluginDts()` helper used by each plugin's Vite config for type declarations. Plugins are registered with a deck via the `plugins` config array and loaded/initialized asynchronously by `js/controllers/plugins.js`.

### Build system

Vite + TypeScript (`tsconfig.json` covers `js/` and `plugin/**/index.ts`; `noEmit: true`, type-checking only — `tsc` in the build script does the real emit via `vite-plugin-dts`). `vite.config.ts` builds the core library (ES + UMD, aliases `reveal.js`/`reveal.js/plugin`/`reveal.css` to source dirs for local dev). `vite.config.styles.ts` handles Sass (`css/reveal.scss`, `css/layout.scss`, themes in `css/theme/`) via the modern Sass compiler API. Each plugin's own `vite.config.ts` reuses `appendExtension` from the root config to produce matching `.js`/`.mjs` filenames. `scripts/add-banner.js` prepends the license banner to the final `dist/reveal.js` after build.

### Tests (`test/`)

Plain QUnit tests run in headless Chromium via Puppeteer against real HTML fixtures — no Jest/Vitest for core. Each `test-*.html` file loads the built/dev deck plus QUnit and exercises a specific scenario (e.g. `test-multiple-instances.html`, `test-scroll.html`, `test-auto-animate.html`, `test-markdown.html`, `test-destroy.html`). `test/simple.md` is fixture content for markdown tests. `test/types/` contains TypeScript type-checking fixtures. The React package instead uses Vitest with colocated `*.test.tsx` files (see `react/AGENTS.md`).

## Style

- Formatting is enforced by Prettier (`.prettierrc`): tabs (width 2), single quotes, semicolons per Prettier defaults, print width 100. Legacy core files (`js/reveal.js`, controllers) use older tab-indent conventions established in the existing code — match the surrounding file's style rather than reformatting wholesale.
- TypeScript is `strict: true` with `noUnusedLocals`/`noUnusedParameters` — new `.ts` files must satisfy this.
- Spelling is checked via `codespell` (`.codespellrc`); `react/` and generated/minified assets are excluded.
