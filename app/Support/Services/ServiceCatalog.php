<?php

namespace App\Support\Services;

class ServiceCatalog
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            [
                'num' => '01',
                'slug' => 'general-checkup',
                'title' => 'General Checkup',
                'summary' => 'Comprehensive oral exams, professional cleaning, X-rays, and preventive care to keep your teeth healthy long-term.',
                'description' => 'A preventive dental visit focused on checking your overall oral health, spotting early concerns, and helping you maintain healthy teeth and gums.',
                'duration' => '30 to 45 minutes',
                'price' => 'Varies by assessment and required diagnostics',
                'features' => [
                    'Complete oral examination and consultation',
                    'Professional cleaning and plaque removal',
                    'Digital X-rays when clinically needed',
                    'Early cavity, gum, and bite assessment',
                    'Personalized hygiene and care recommendations',
                ],
                'expected_outputs' => [
                    'A clear picture of your current oral health',
                    'Early detection of cavities, gum issues, or wear',
                    'Cleaner teeth and fresher breath after the visit',
                    'A recommended treatment or maintenance plan when needed',
                ],
            ],
            [
                'num' => '02',
                'slug' => 'orthodontics',
                'title' => 'Orthodontics',
                'summary' => 'Braces and modern clear aligners crafted to straighten teeth and correct bite issues for children, teens, and adults.',
                'description' => 'Orthodontic care helps align teeth and jaws to improve appearance, bite function, and long-term oral health.',
                'duration' => 'Initial assessment plus ongoing adjustment visits',
                'price' => 'Based on appliance type and treatment complexity',
                'features' => [
                    'Orthodontic assessment and bite analysis',
                    'Treatment planning for braces or aligners',
                    'Monitoring visits and progress adjustments',
                    'Guidance for cleaning and appliance care',
                    'Retention planning after alignment is complete',
                ],
                'expected_outputs' => [
                    'Straighter teeth and improved smile alignment',
                    'Better bite balance and jaw function',
                    'A phased treatment roadmap with milestones',
                    'Retention guidance to help maintain results',
                ],
            ],
            [
                'num' => '03',
                'slug' => 'teeth-whitening',
                'title' => 'Teeth Whitening',
                'summary' => 'Professional-grade whitening that safely lifts stubborn stains and restores the natural brightness of your smile.',
                'description' => 'A cosmetic treatment designed to reduce staining and brighten teeth using dentist-supervised whitening methods.',
                'duration' => '45 to 60 minutes',
                'price' => 'Depends on whitening method and stain severity',
                'features' => [
                    'Smile shade evaluation before treatment',
                    'Professional whitening application',
                    'Protection for gums and sensitive areas',
                    'Guidance on post-treatment care',
                    'Recommendations for maintaining brightness longer',
                ],
                'expected_outputs' => [
                    'A visibly brighter smile after treatment',
                    'Reduced surface staining from food, drinks, or smoking',
                    'A new shade baseline for future maintenance',
                    'Aftercare steps to help preserve results',
                ],
            ],
            [
                'num' => '04',
                'slug' => 'oral-surgery',
                'title' => 'Oral Surgery',
                'summary' => 'Tooth extractions, implant placement, and minor surgical procedures performed by our experienced dental team.',
                'description' => 'Minor oral surgical care for cases that require extraction, tissue management, or more advanced treatment beyond routine procedures.',
                'duration' => 'Varies depending on procedure complexity',
                'price' => 'Based on procedure type and case difficulty',
                'features' => [
                    'Clinical evaluation and treatment planning',
                    'Tooth extraction or minor surgical procedure',
                    'Local anesthesia and comfort-focused care',
                    'Post-procedure instructions and follow-up guidance',
                    'Monitoring of healing and recovery progress',
                ],
                'expected_outputs' => [
                    'Removal or treatment of the primary oral concern',
                    'Reduced pain, pressure, or infection risk',
                    'A structured aftercare and recovery plan',
                    'Recommendations for next-step restoration if needed',
                ],
            ],
            [
                'num' => '05',
                'slug' => 'dental-fillings',
                'title' => 'Dental Fillings',
                'summary' => 'Tooth-colored composite fillings that repair cavities and restore strength while keeping a natural look.',
                'description' => 'Composite fillings restore teeth affected by cavities, minor fractures, or worn areas while preserving a natural appearance.',
                'duration' => '30 to 60 minutes',
                'price' => 'Depends on number, size, and location of fillings',
                'features' => [
                    'Decay removal and tooth preparation',
                    'Tooth-colored composite restoration',
                    'Shade matching for a natural finish',
                    'Bite adjustment and polishing',
                    'Advice on caring for restored teeth',
                ],
                'expected_outputs' => [
                    'Repaired tooth structure and function',
                    'Reduced sensitivity caused by decay or damage',
                    'A natural-looking restoration matched to your smile',
                    'Better protection against further cavity progression',
                ],
            ],
            [
                'num' => '06',
                'slug' => 'root-canal-therapy',
                'title' => 'Root Canal Therapy',
                'summary' => 'Pain-relieving root canal treatment that saves infected teeth and prevents further oral complications.',
                'description' => 'Root canal therapy removes infected pulp inside the tooth to relieve pain, control infection, and help preserve the natural tooth.',
                'duration' => '60 to 90 minutes, sometimes across multiple visits',
                'price' => 'Depends on tooth type and treatment complexity',
                'features' => [
                    'Assessment of tooth infection and nerve involvement',
                    'Cleaning and disinfection of root canals',
                    'Sealing of the treated tooth',
                    'Pain management and comfort measures',
                    'Guidance on final restoration such as a crown if needed',
                ],
                'expected_outputs' => [
                    'Relief from infection-related tooth pain',
                    'Preservation of the natural tooth when possible',
                    'Reduced spread of infection to surrounding tissues',
                    'A restored tooth ready for long-term protection',
                ],
            ],
            [
                'num' => '07',
                'slug' => 'dental-crowns',
                'title' => 'Dental Crowns',
                'summary' => 'Custom-made crowns that protect damaged teeth and restore full function and appearance.',
                'description' => 'Dental crowns cover weakened or heavily restored teeth to improve durability, shape, and chewing function.',
                'duration' => 'Usually 2 visits depending on case and material',
                'price' => 'Varies by crown material and tooth condition',
                'features' => [
                    'Tooth evaluation and crown planning',
                    'Preparation of the affected tooth',
                    'Custom crown fit and shade selection',
                    'Temporary restoration when needed',
                    'Final placement and bite adjustment',
                ],
                'expected_outputs' => [
                    'A stronger and more protected tooth',
                    'Improved chewing comfort and tooth stability',
                    'A crown shaped to blend with nearby teeth',
                    'Longer-term support for damaged tooth structure',
                ],
            ],
            [
                'num' => '08',
                'slug' => 'dental-bridges',
                'title' => 'Dental Bridges',
                'summary' => 'Fixed bridge solutions to replace missing teeth and bring back comfortable chewing and confidence.',
                'description' => 'Dental bridges replace one or more missing teeth using neighboring teeth or implants for support.',
                'duration' => 'Typically completed over multiple visits',
                'price' => 'Depends on span length and support requirements',
                'features' => [
                    'Assessment of missing tooth spaces',
                    'Bridge design and support planning',
                    'Preparation of supporting teeth when indicated',
                    'Custom bridge fabrication and fitting',
                    'Occlusion checks and home-care guidance',
                ],
                'expected_outputs' => [
                    'Replacement of missing teeth with a fixed solution',
                    'Improved chewing and speaking function',
                    'Better smile continuity and support',
                    'Reduced shifting of surrounding teeth over time',
                ],
            ],
            [
                'num' => '09',
                'slug' => 'dentures',
                'title' => 'Dentures',
                'summary' => 'Full and partial dentures designed for comfort, stability, and natural-looking smiles.',
                'description' => 'Full and partial dentures restore function and appearance for patients missing several or all teeth.',
                'duration' => 'Multiple visits for fitting and adjustments',
                'price' => 'Depends on denture type and case requirements',
                'features' => [
                    'Evaluation for full or partial denture options',
                    'Impressions and bite registration',
                    'Try-in appointments for fit and appearance',
                    'Delivery with comfort and use instructions',
                    'Follow-up adjustments as needed',
                ],
                'expected_outputs' => [
                    'Restored ability to chew and speak more comfortably',
                    'Support for facial profile and smile appearance',
                    'A removable prosthesis tailored to your needs',
                    'Adjustment guidance for improved day-to-day wear',
                ],
            ],
            [
                'num' => '10',
                'slug' => 'pediatric-dentistry',
                'title' => 'Pediatric Dentistry',
                'summary' => 'Gentle dental care for children focused on prevention, comfort, and healthy oral habits.',
                'description' => 'Child-focused dental care centered on preventive visits, oral development, and positive dental experiences.',
                'duration' => '30 to 45 minutes',
                'price' => 'Depends on visit type and treatment needs',
                'features' => [
                    'Child-friendly oral examination',
                    'Monitoring of growth and tooth development',
                    'Preventive cleaning and hygiene guidance',
                    'Cavity-risk assessment and habit counseling',
                    'Parent education for home care support',
                ],
                'expected_outputs' => [
                    'A comfortable and supportive dental visit for children',
                    'Early identification of developing oral concerns',
                    'Clear home-care guidance for parents and guardians',
                    'Healthier brushing, diet, and prevention habits',
                ],
            ],
            [
                'num' => '11',
                'slug' => 'periodontal-care',
                'title' => 'Periodontal Care',
                'summary' => 'Specialized gum treatment to manage gingivitis and periodontitis and protect long-term oral health.',
                'description' => 'Periodontal care focuses on gum health, controlling inflammation, and preventing progression of gum disease.',
                'duration' => 'Depends on severity and treatment approach',
                'price' => 'Based on condition severity and treatment scope',
                'features' => [
                    'Gum health evaluation and pocket assessment',
                    'Deep cleaning or periodontal maintenance',
                    'Management planning for inflammation and bleeding',
                    'Monitoring of gum response over time',
                    'Education on brushing, flossing, and maintenance',
                ],
                'expected_outputs' => [
                    'Healthier gums with reduced inflammation',
                    'Lower bleeding and discomfort during cleaning',
                    'A maintenance plan to protect supporting tissues',
                    'Better long-term stability for natural teeth',
                ],
            ],
            [
                'num' => '12',
                'slug' => 'tooth-extraction',
                'title' => 'Tooth Extraction',
                'summary' => 'Safe extractions for severely damaged or problematic teeth with careful aftercare support.',
                'description' => 'Tooth extraction removes teeth that are severely damaged, infected, impacted, or no longer restorable.',
                'duration' => 'Usually 30 to 60 minutes depending on difficulty',
                'price' => 'Depends on extraction type and complexity',
                'features' => [
                    'Pre-extraction clinical evaluation',
                    'Simple or surgical extraction procedure',
                    'Local anesthesia for comfort',
                    'Bleeding control and site protection',
                    'Detailed aftercare and healing instructions',
                ],
                'expected_outputs' => [
                    'Removal of the painful or problematic tooth',
                    'Relief from pressure, infection, or damage-related symptoms',
                    'A healing plan with recovery guidance',
                    'Recommendations for replacement options when appropriate',
                ],
            ],
            [
                'num' => '13',
                'slug' => 'dental-implants',
                'title' => 'Dental Implants',
                'summary' => 'Durable implant restorations that replace missing teeth and restore bite stability.',
                'description' => 'Dental implants provide a long-term tooth replacement option that supports function, appearance, and jawbone preservation.',
                'duration' => 'Staged treatment over several appointments',
                'price' => 'Depends on implant site, restoration, and supporting procedures',
                'features' => [
                    'Implant consultation and treatment planning',
                    'Evaluation of bone and site readiness',
                    'Implant placement or implant restoration coordination',
                    'Healing and integration monitoring',
                    'Final crown or prosthetic planning',
                ],
                'expected_outputs' => [
                    'A stable replacement for a missing tooth or teeth',
                    'Improved bite support and chewing confidence',
                    'A restoration designed to look natural',
                    'A long-term plan for implant maintenance',
                ],
            ],
            [
                'num' => '14',
                'slug' => 'veneers',
                'title' => 'Veneers',
                'summary' => 'Thin porcelain veneers to improve shape, color, and overall smile symmetry.',
                'description' => 'Veneers are cosmetic restorations placed on the front surfaces of teeth to improve the appearance of your smile.',
                'duration' => 'Usually completed over 2 or more visits',
                'price' => 'Based on number of veneers and smile goals',
                'features' => [
                    'Smile assessment and cosmetic planning',
                    'Preparation of selected teeth',
                    'Shade, shape, and symmetry customization',
                    'Temporary restorations when needed',
                    'Final veneer bonding and finishing',
                ],
                'expected_outputs' => [
                    'Improved tooth color, shape, and smile balance',
                    'A more polished and uniform front-tooth appearance',
                    'A cosmetic plan aligned with your preferred smile outcome',
                    'Guidance for protecting veneers long term',
                ],
            ],
            [
                'num' => '15',
                'slug' => 'tmj-management',
                'title' => 'TMJ Management',
                'summary' => 'Assessment and treatment options for jaw pain, clenching, and bite-related discomfort.',
                'description' => 'TMJ management addresses jaw joint discomfort, muscle tension, bite strain, and habits such as clenching or grinding.',
                'duration' => 'Initial assessment plus follow-up depending on response',
                'price' => 'Depends on diagnostic and treatment needs',
                'features' => [
                    'Jaw function and bite assessment',
                    'Evaluation of clenching or grinding patterns',
                    'Discussion of conservative treatment options',
                    'Monitoring of pain triggers and mobility',
                    'Home-care and habit-modification guidance',
                ],
                'expected_outputs' => [
                    'A clearer understanding of likely jaw-pain triggers',
                    'A practical management plan for symptoms',
                    'Reduced strain during chewing or jaw movement',
                    'Recommendations for longer-term monitoring if needed',
                ],
            ],
            [
                'num' => '16',
                'slug' => 'emergency-dental-care',
                'title' => 'Emergency Dental Care',
                'summary' => 'Urgent dental treatment for sudden pain, trauma, and other immediate oral concerns.',
                'description' => 'Emergency dental care focuses on quick assessment and immediate treatment for urgent pain, swelling, trauma, or sudden oral issues.',
                'duration' => 'As soon as possible, based on urgency and required treatment',
                'price' => 'Varies depending on emergency findings and treatment performed',
                'features' => [
                    'Urgent pain or trauma assessment',
                    'Immediate stabilization of the dental issue',
                    'Short-term relief and infection control measures',
                    'Diagnostic imaging when needed',
                    'Recommendations for follow-up definitive treatment',
                ],
                'expected_outputs' => [
                    'Fast evaluation of the urgent dental problem',
                    'Relief of acute pain or pressure where possible',
                    'Immediate steps to control damage or infection',
                    'A next-step plan for full treatment and recovery',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findBySlug(string $slug): ?array
    {
        foreach (self::all() as $service) {
            if ($service['slug'] === $slug) {
                return $service;
            }
        }

        return null;
    }
}
