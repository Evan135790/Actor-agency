<!DOCTYPE html>

<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "primary": "#ffffff",
                    "secondary": "#c6c6c6",
                    "surface-container-highest": "#353535",
                    "primary-fixed-dim": "#cdcd00",
                    "surface-container": "#1f1f1f",
                    "secondary-fixed": "#e3e2e2",
                    "on-error-container": "#ffdad6",
                    "surface": "#131313",
                    "tertiary-fixed": "#e2e2e2",
                    "surface-dim": "#131313",
                    "on-primary-fixed": "#1d1d00",
                    "tertiary-container": "#e2e2e2",
                    "surface-container-low": "#1b1b1b",
                    "on-secondary-fixed-variant": "#464747",
                    "on-primary": "#323200",
                    "inverse-surface": "#e2e2e2",
                    "surface-container-lowest": "#0e0e0e",
                    "surface-variant": "#353535",
                    "surface-bright": "#393939",
                    "secondary-container": "#484949",
                    "primary-container": "#eaea00",
                    "on-primary-container": "#686800",
                    "secondary-fixed-dim": "#c6c6c6",
                    "on-secondary-container": "#b8b8b8",
                    "error-container": "#93000a",
                    "surface-tint": "#cdcd00",
                    "on-secondary-fixed": "#1a1c1c",
                    "background": "#131313",
                    "on-primary-fixed-variant": "#494900",
                    "on-surface-variant": "#cac8aa",
                    "tertiary-fixed-dim": "#c6c6c7",
                    "surface-container-high": "#2a2a2a",
                    "on-surface": "#e2e2e2",
                    "on-secondary": "#2f3131",
                    "inverse-on-surface": "#303030",
                    "inverse-primary": "#626200",
                    "on-tertiary": "#2f3131",
                    "on-tertiary-fixed": "#1a1c1c",
                    "on-tertiary-container": "#636565",
                    "on-error": "#690005",
                    "primary-fixed": "#eaea00",
                    "on-tertiary-fixed-variant": "#454747",
                    "tertiary": "#ffffff",
                    "outline": "#939277",
                    "error": "#ffb4ab",
                    "on-background": "#e2e2e2",
                    "outline-variant": "#484831"
            },
            "borderRadius": {
                    "DEFAULT": "0px",
                    "lg": "0px",
                    "xl": "0px",
                    "full": "9999px"
            },
            "fontFamily": {
                    "headline": ["Inter"],
                    "body": ["Inter"],
                    "label": ["Inter"]
            }
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #131313;
            color: #e2e2e2;
        }
        * { border-radius: 0 !important; }
    </style>
</head>
<body class="bg-surface text-on-surface">
<nav class="flex justify-between items-center w-full px-6 py-4 bg-[#131313] border-b-2 border-[#939277] font-['Inter'] font-bold tracking-tight uppercase">
<a class="text-2xl font-black tracking-tighter text-[#eaea00] uppercase hover:opacity-80" href="index.html">JAYWALK STUDIOS</a>
<div class="hidden md:flex gap-8 items-center">
<a class="text-[#eaea00] border-b-2 border-[#eaea00] hover:bg-[#eaea00] hover:text-[#000000] transition-none" href="#">TALENT</a>
<a class="text-[#c6c6c6] hover:bg-[#eaea00] hover:text-[#000000] transition-none" href="#">DEPARTMENTS</a>
<a class="text-[#c6c6c6] hover:bg-[#eaea00] hover:text-[#000000] transition-none" href="#">ABOUT</a>
<a class="text-[#c6c6c6] hover:bg-[#eaea00] hover:text-[#000000] transition-none" href="#">HIRE</a>
</div>
<button class="bg-[#eaea00] text-[#000000] px-6 py-2 font-black border-2 border-[#000000] hover:bg-[#ffffff] hover:text-[#000000] transition-none">
            LOGIN
        </button>
</nav>
<main class="max-w-[1440px] mx-auto px-6 py-12 space-y-24">
<section class="w-full">
<div class="mb-4 flex justify-between items-end">
<h2 class="text-[3.5rem] font-black leading-none tracking-tighter uppercase text-primary">LATEST NEWS</h2>
<div class="flex gap-2">
<button class="w-12 h-12 flex items-center justify-center border-2 border-outline hover:bg-primary-fixed hover:text-on-primary-fixed transition-none">
<span class="material-symbols-outlined">arrow_back</span>
</button>
<button class="w-12 h-12 flex items-center justify-center border-2 border-outline hover:bg-primary-fixed hover:text-on-primary-fixed transition-none">
<span class="material-symbols-outlined">arrow_forward</span>
</button>
</div>
</div>
<div class="relative w-full aspect-[21/9] border-2 border-outline overflow-hidden bg-surface-container">
<img alt=""/>
<div class="absolute bottom-0 left-0 p-12 w-full bg-gradient-to-t from-black/80 to-transparent">
<span class="inline-block bg-primary-fixed text-on-primary-fixed px-3 py-1 text-[0.6875rem] font-bold tracking-widest uppercase mb-4">TBA</span>
<h3 class="text-4xl font-black uppercase max-w-2xl leading-tight">TBA</h3>
</div>
</div>
</section>
<section class="w-full">
<div class="mb-12">
<h2 class="text-4xl font-black uppercase text-primary mb-2">FEATURED ACTORS</h2>
<div class="h-1 w-24 bg-primary-fixed"></div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
<a class="group border-2 border-outline-variant bg-surface-container hover:border-primary-fixed transition-none" href="profile.html">
<div class="aspect-[3/4] overflow-hidden border-b-2 border-outline-variant">
<img alt="">
</div>
<div class="p-6">
<h4 class="text-xl font-bold uppercase text-primary mb-1">TBA</h4>
<p class="text-secondary text-[0.875rem] uppercase tracking-wider">TBA</p>
<div class="mt-4 flex items-center text-primary-fixed font-bold text-[0.6875rem] tracking-widest opacity-0 group-hover:opacity-100 transition-none">
                            VIEW PROFILE <span class="material-symbols-outlined ml-2 text-sm">arrow_forward</span>
</div>
</div>
</a>
<div class="border-2 border-outline-variant bg-surface-container">
<div class="aspect-[3/4] overflow-hidden border-b-2 border-outline-variant">
<img alt=""/>
</div>
<div class="p-6">
<h4 class="text-xl font-bold uppercase text-primary mb-1">TBA</h4>
<p class="text-secondary text-[0.875rem] uppercase tracking-wider">TBA</p>
<div class="mt-4 h-[14px]"></div>
</div>
</div>
<div class="border-2 border-outline-variant bg-surface-container">
<div class="aspect-[3/4] overflow-hidden border-b-2 border-outline-variant">
<img alt=""/>
</div>
<div class="p-6">
<h4 class="text-xl font-bold uppercase text-primary mb-1">TBA</h4>
<p class="text-secondary text-[0.875rem] uppercase tracking-wider">TBA</p>
<div class="mt-4 h-[14px]"></div>
</div>
</div>
<div class="border-2 border-outline-variant bg-surface-container">
<div class="aspect-[3/4] overflow-hidden border-b-2 border-outline-variant">
<img alt=""/>
</div>
<div class="p-6">
<h4 class="text-xl font-bold uppercase text-primary mb-1">TBA</h4>
<p class="text-secondary text-[0.875rem] uppercase tracking-wider">TBA</p>
<div class="mt-4 h-[14px]"></div>
</div>
</div>
</div>
<div class="mt-12 flex justify-center">
<button class="px-12 py-4 bg-surface text-primary border-2 border-outline font-black uppercase hover:bg-primary-fixed hover:text-on-primary-fixed hover:border-black transition-none">
                    EXPLORE FULL ROSTER
                </button>
</div>
</section>
<section class="grid grid-cols-1 md:grid-cols-3 gap-0 border-2 border-outline">
<div class="p-12 border-b-2 md:border-b-0 md:border-r-2 border-outline bg-surface-container hover:bg-surface-container-high transition-none">
<span class="material-symbols-outlined text-4xl text-primary-fixed mb-6">TBA</span>
<h3 class="text-2xl font-black uppercase mb-4">TBA</h3>
<p class="text-secondary text-sm mb-6">TBA</p>
<a class="text-[0.6875rem] font-bold tracking-widest text-primary-fixed uppercase underline underline-offset-4" href="#">LEARN MORE</a>
</div>
<div class="p-12 border-b-2 md:border-b-0 md:border-r-2 border-outline bg-surface-container hover:bg-surface-container-high transition-none">
<span class="material-symbols-outlined text-4xl text-primary-fixed mb-6">TBA</span>
<h3 class="text-2xl font-black uppercase mb-4">TBA</h3>
<p class="text-secondary text-sm mb-6">TBA</p>
<a class="text-[0.6875rem] font-bold tracking-widest text-primary-fixed uppercase underline underline-offset-4" href="#">LEARN MORE</a>
</div>
<div class="p-12 bg-surface-container hover:bg-surface-container-high transition-none">
<span class="material-symbols-outlined text-4xl text-primary-fixed mb-6">TBA</span>
<h3 class="text-2xl font-black uppercase mb-4">TBA</h3>
<p class="text-secondary text-sm mb-6">TBA</p>
<a class="text-[0.6875rem] font-bold tracking-widest text-primary-fixed uppercase underline underline-offset-4" href="#">LEARN MORE</a>
</div>
</section>
</main>
<footer class="bg-[#0e0e0e] border-t-2 border-[#939277] w-full px-8 py-12 flex flex-col md:flex-row justify-between items-center gap-4">
<a class="text-lg font-bold text-[#c6c6c6] uppercase tracking-tighter hover:text-[#eaea00]" href="index.html">JAYWALK STUDIOS</a>
<div class="text-[0.6875rem] font-['Inter'] uppercase tracking-widest text-[#c6c6c6] text-center md:text-left">
        </div>
<div class="flex gap-8 text-[0.6875rem] font-['Inter'] uppercase tracking-widest font-bold">
<a class="text-[#c6c6c6] hover:text-[#eaea00] transition-none" href="#">PRIVACY</a>
<a class="text-[#c6c6c6] hover:text-[#eaea00] transition-none" href="#">TERMS</a>
<a class="text-[#c6c6c6] hover:text-[#eaea00] transition-none" href="#">CONTACT</a>
</div>
</footer>
</body></html>