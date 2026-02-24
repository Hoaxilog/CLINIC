<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Show General Checkup service page
     */
    public function generalCheckup(): View
    {
        $service = [
            'title' => 'General Checkup',
            'description' => 'Routine examination with a professional dentist to keep your oral health at peak condition.',
            'image' => 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&q=80',
            'details' => [
                'Complete oral examination',
                'Plaque and tartar removal',
                'Professional teeth cleaning',
                'Digital X-rays',
                'Early cavity detection',
                'Personalized dental advice'
            ],
            'benefits' => [
                'Prevents tooth decay and gum disease',
                'Detects problems early',
                'Maintains overall oral health',
                'Cost-effective preventive care'
            ],
            'price' => '₱500 - ₱1,000',
            'duration' => '30 - 45 minutes'
        ];

        return view('services.general-checkup', compact('service'));
    }

    /**
     * Show Orthodontics service page
     */
    public function orthodontics(): View
    {
        $service = [
            'title' => 'Orthodontics',
            'description' => 'Modern braces and aligners to correct your smile and improve long-term dental health.',
            'image' => 'https://images.unsplash.com/photo-1598256989800-fe5f95da9787?auto=format&fit=crop&q=80',
            'details' => [
                'Traditional braces',
                'Invisible aligners (Clear aligners)',
                'Lingual braces',
                'Ceramic braces',
                'Bite correction',
                'Smile alignment'
            ],
            'benefits' => [
                'Improved smile appearance',
                'Better bite alignment',
                'Easier teeth cleaning',
                'Reduced risk of tooth decay',
                'Improved overall health'
            ],
            'price' => '₱80,000 - ₱250,000',
            'duration' => '18 - 36 months'
        ];

        return view('services.orthodontics', compact('service'));
    }

    /**
     * Show Teeth Whitening service page
     */
    public function teethWhitening(): View
    {
        $service = [
            'title' => 'Teeth Whitening',
            'description' => 'Advanced teeth whitening to brighten your smile up to 8 shades in a single session.',
            'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSc_b0xAG8cB0EQbHARkpSNVrA9ZzmqX42kvw&s',
            'details' => [
                'Professional-grade whitening gel',
                'LED light activation',
                'Safe and effective formula',
                'Results in one session',
                'Lasting up to 6 months',
                'No sensitivity pain'
            ],
            'benefits' => [
                'Whiter teeth in hours',
                'Professional results',
                'Boosts confidence',
                'Safe for all tooth types',
                'Long-lasting effects',
                'Non-invasive procedure'
            ],
            'price' => '₱5,000 - ₱12,000',
            'duration' => '45 - 60 minutes'
        ];

        return view('services.teeth-whitening', compact('service'));
    }

    /**
     * Show Oral Surgery service page
     */
    public function oralSurgery(): View
    {
        $service = [
            'title' => 'Oral Surgery',
            'description' => 'Safe and precise minor oral surgery procedures for impacted teeth, infections, and advanced dental concerns.',
            'image' => 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?auto=format&fit=crop&q=80',
            'details' => [
                'Wisdom tooth extraction',
                'Simple and surgical tooth removal',
                'Treatment for impacted teeth',
                'Management of oral infections',
                'Post-operative care instructions',
                'Follow-up healing checks'
            ],
            'benefits' => [
                'Relieves pain and discomfort',
                'Prevents infection spread',
                'Protects surrounding teeth and gums',
                'Improves long-term oral health',
                'Performed with modern safety protocols'
            ],
            'price' => 'P3,000 - P20,000',
            'duration' => '45 - 90 minutes'
        ];

        return view('services.oral-surgery', compact('service'));
    }
}
