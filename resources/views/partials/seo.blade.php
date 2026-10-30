{{-- Metas para buscadores y redes. Las páginas pueden definir las secciones title, description, og_image y og_type. --}}
@php
    $siteName = setting('site_name', 'LyK Asesoría y Consultoría de Pensiones y AFORE');
    $metaTitle = trim($__env->yieldContent('title')) ?: $siteName;
    $metaDescription = trim($__env->yieldContent('description')) ?: setting('seo_description', setting('tagline', ''));
    $metaImage = trim($__env->yieldContent('og_image')) ?: asset('images/brand/portada.jpg');
    $metaType = trim($__env->yieldContent('og_type')) ?: 'website';
@endphp
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="es_MX">
<meta property="og:type" content="{{ $metaType }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta name="twitter:card" content="summary_large_image">
