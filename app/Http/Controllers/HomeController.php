<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Faq;
use App\Models\HeroSlide;
use App\Models\Post;
use App\Models\Service;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('home', [
            'slides' => HeroSlide::active()->get(),
            'services' => Service::active()->get(),
            'posts' => Post::published()->withMediaCounts()->limit(3)->get(),
            'testimonials' => Testimonial::active()->get(),
            'faqs' => Faq::active()->get(),
            'branches' => Branch::active()->get(),
        ]);
    }
}
