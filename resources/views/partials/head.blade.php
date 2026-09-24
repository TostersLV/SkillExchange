<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@fluxAppearance
{{-- Dark background from the very first frame of a page load (before the stylesheet arrives). --}}
<style>
    html.dark { background-color: #0e1117; }
</style>
{{-- Remember the current theme in a cookie so the server renders <html class="dark"> on the next page (no white flash). --}}
<script>
    (() => {
        const html = document.documentElement;
        const saveAppearance = () => {
            document.cookie = 'appearance=' + (html.classList.contains('dark') ? 'dark' : 'light') + ';path=/;max-age=31536000;SameSite=Lax';
        };

        saveAppearance();
        new MutationObserver(saveAppearance).observe(html, { attributes: true, attributeFilter: ['class'] });
    })();
</script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
