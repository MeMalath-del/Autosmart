<?php

return [
    'site_name' => 'AutoSmart',
    'site_name_ar' => 'أوتوسمارت',
    'tagline' => 'سوق قطع غيار السيارات الذكي',
    'description' => 'سوق إلكتروني ذكي متخصص في بيع ووساطة وتوصيل قطع غيار السيارات في المملكة العربية السعودية',
    'keywords' => 'قطع غيار, سيارات, قطع سيارات, spare parts, auto parts, السعودية, شراء قطع غيار',
    'author' => 'AutoSmart',
    'locale' => 'ar_SA',
    'twitter_handle' => '@autosmart_sa',
    
    'og' => [
        'type' => 'website',
        'image' => '/images/og-image.jpg',
    ],
    
    'schema' => [
        'organization' => [
            '@type' => 'Organization',
            'name' => 'AutoSmart',
            'url' => env('APP_URL'),
            'logo' => env('APP_URL') . '/images/logo.png',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+966500000000',
                'contactType' => 'customer service',
                'areaServed' => 'SA',
                'availableLanguage' => ['Arabic', 'English'],
            ],
        ],
    ],
];
