{{-- Primary Meta Tags --}}
<meta name="title" content="{{ $title }}">
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ config('seo.keywords') }}">
<meta name="author" content="{{ config('seo.author') }}">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ $url }}">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:locale" content="{{ config('seo.locale') }}">
<meta property="og:site_name" content="{{ config('seo.site_name') }}">

{{-- Twitter --}}
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ $url }}">
<meta property="twitter:title" content="{{ $title }}">
<meta property="twitter:description" content="{{ $description }}">
<meta property="twitter:image" content="{{ $image }}">
<meta property="twitter:site" content="{{ config('seo.twitter_handle') }}">

{{-- Schema.org structured data --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "{{ $type === 'product' ? 'Product' : 'WebSite' }}",
    "name": "{{ $title }}",
    "description": "{{ $description }}",
    "url": "{{ $url }}"
}
</script>
