<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Seo extends Component
{
    public string $title;
    public string $description;
    public ?string $image;
    public ?string $url;
    public string $type;

    public function __construct(
        string $title = '',
        string $description = '',
        ?string $image = null,
        ?string $url = null,
        string $type = 'website'
    ) {
        $siteName = config('seo.site_name');
        
        $this->title = $title ? "{$title} - {$siteName}" : "{$siteName} - " . config('seo.tagline');
        $this->description = $description ?: config('seo.description');
        $this->image = $image ?: asset(config('seo.og.image'));
        $this->url = $url ?: url()->current();
        $this->type = $type;
    }

    public function render()
    {
        return view('components.seo');
    }
}
