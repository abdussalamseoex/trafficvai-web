<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HomeSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'key' => 'hero',
                'name' => 'Hero Section',
                'order' => 1,
                'content' => json_encode([
                    'badge_text' => 'New Packages Available',
                    'badge_subtext' => 'See our latest guest post stock',
                    'badge_link' => '#',
                    'headline' => 'Dominant SEO & <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-indigo-600">Premium Links</span>',
                    'subheadline' => 'Propel your organic visibility with white-hat, contextual link building and elite guest posts on real publisher websites. We build authority that sticks.',
                    'primary_button_text' => 'View Ranking Plans',
                    'primary_button_link' => '/services',
                    'secondary_button_text' => 'Browse Inventory',
                    'secondary_button_link' => '/guest-posts',
                    'image' => 'images/hero-seo.png',
                ]),
            ],
            [
                'key' => 'trust_stats',
                'name' => 'Trust Stats Banner',
                'order' => 2,
                'content' => json_encode([
                    'stat_1_value' => '15,000+',
                    'stat_1_label' => 'Placements Built',
                    'stat_2_value' => '1,200+',
                    'stat_2_label' => 'Active Agencies',
                    'stat_3_value' => '45+',
                    'stat_3_label' => 'Average DA',
                    'stat_4_value' => '99.8%',
                    'stat_4_label' => 'Delivery Success',
                ]),
            ],
            [
                'key' => 'services',
                'name' => 'Core Services Grid',
                'order' => 3,
                'content' => json_encode([
                    'super_title' => 'Our Solutions',
                    'headline' => 'Comprehensive Digital Authority',
                    'subheadline' => 'From fully-managed SEO to high-traffic guest placements, we provide the raw ranking power your brand needs.',
                    // Content for individual service cards could be managed as repeater or nested JSON, but for simplicity we can manage the grid heading here, and let the real services from DB populate it if we wanted, or hardcode the cards content here. Let's make them dynamic:
                    'cards' => [
                        [
                            'title' => 'Fully Managed SEO',
                            'description' => 'Complete, hands-off ranking campaigns. We analyze, strategize, and execute a custom link building masterplan to dominate your niche.',
                            'link_text' => 'Explore Campaigns',
                            'link_url' => '/campaigns/seo-campaigns',
                        ],
                        [
                            'title' => 'Premium Guest Posts',
                            'description' => 'Browse our live inventory of thousands of real websites. Strict metric requirements guarantee you only place content on sites that drive authority.',
                            'link_text' => 'Browse Inventory',
                            'link_url' => '/guest-posts',
                        ],
                        [
                            'title' => 'High DA Link Building',
                            'description' => 'Powerful, contextual editorial links acquired through genuine manual outreach. We strictly vet sites for organic traffic and domain health.',
                            'link_text' => 'View Packages',
                            'link_url' => '/services',
                        ],
                        [
                            'title' => 'Targeted Website Traffic',
                            'description' => 'Boost your organic behavioral signals. We deliver high-quality, targeted geographic traffic to improve CTR, bounce rate, and overall engagement.',
                            'link_text' => 'Boost Traffic',
                            'link_url' => '/traffic',
                        ],
                    ]
                ]),
            ],
            [
                'key' => 'why_choose_us',
                'name' => 'Why Choose Us',
                'order' => 4,
                'content' => json_encode([
                    'super_title' => 'The White-Hat Difference',
                    'headline' => 'Safe, Sustainable, and Scalable Organic Growth',
                    'description_top' => 'At TrafficVai, we distance ourselves from outdated, high-risk tactics. Our entire methodology is rooted in authentic relationship building and rigorous quality control.',
                    'list_items' => [
                        ['title' => 'Strict Metric Requirements:', 'text' => 'We don\'t just look at Domain Authority (DA). We analyze real organic traffic, referring domains, and spam scores to ensure maximum link equity.'],
                        ['title' => 'Zero PBNs or Spam:', 'text' => 'Our placements are secured on genuine editorial publications, reputable blogs, and established corporate waitlists. We never use Private Blog Networks.'],
                        ['title' => '100% Human-Written Content:', 'text' => 'Every article is researched and written by native speaking SEO copywriters to ensure contextual relevance and editorial approval.'],
                    ],
                    'description_bottom' => 'Whether you\'re a boutique agency looking for a reliable white-label partner, or an enterprise brand demanding top-tier placements, our transparent dashboard puts you in full control.',
                ]),
            ],
            [
                'key' => 'testimonials',
                'name' => 'Success Stories (Testimonials)',
                'order' => 5,
                'content' => json_encode([
                    'super_title' => 'Success Stories',
                    'headline' => 'Don\'t just take our word for it',
                    'items' => [
                        [
                            'quote' => 'TrafficVai\'s link building completely transformed our client\'s organic visibility. We saw a 300% increase in non-branded search traffic within 4 months.',
                            'initials' => 'MC',
                            'name' => 'Michael Chen',
                            'role' => 'Director of SEO'
                        ],
                        [
                            'quote' => 'The transparency of their guest post inventory is unmatched. I know exactly what metrics the site has before I buy, and the content is consistently excellent.',
                            'initials' => 'SJ',
                            'name' => 'Sarah Jenkins',
                            'role' => 'E-commerce Founder'
                        ],
                        [
                            'quote' => 'No PBNs, no hidden fees, and lightning-fast delivery. TrafficVai is now the only link building partner we use for our high-end corporate clients.',
                            'initials' => 'DT',
                            'name' => 'David Thompson',
                            'role' => 'Marketing Lead'
                        ],
                    ]
                ]),
            ],
            [
                'key' => 'how_it_works',
                'name' => 'Process / How It Works',
                'order' => 6,
                'content' => json_encode([
                    'super_title' => 'Process',
                    'headline' => 'How We Rank Your Site',
                    'steps' => [
                        [
                            'number' => '1',
                            'title' => 'Choose Your Strategy',
                            'description' => 'Select a link building package tailored to your goals or hand-pick specific websites directly from our live guest post inventory.'
                        ],
                        [
                            'number' => '2',
                            'title' => 'Submit Requirements',
                            'description' => 'Provide your target URLs, preferred anchor texts, and any specific notes securely through your intuitive client dashboard.'
                        ],
                        [
                            'number' => '3',
                            'title' => 'We Complete the Work',
                            'description' => 'Our elite outreach team secures placements, writes native content, and delivers a transparent white-label report immediately upon completion.'
                        ],
                    ]
                ]),
            ],
            [
                'key' => 'cta',
                'name' => 'Call To Action',
                'order' => 7,
                'content' => json_encode([
                    'headline' => 'Ready to outflow your competitors?',
                    'subheadline' => 'Create a free account in seconds and get immediate access to our exclusive agency-level outreach and traffic services.',
                    'button_text' => 'Start Growing Today',
                    'button_link' => '/register'
                ]),
            ],
        ];

        foreach ($sections as $section) {
            \App\Models\HomeSection::updateOrCreate(
            ['key' => $section['key']],
            [
                'name' => $section['name'],
                'order' => $section['order'],
                'content' => json_decode($section['content'], true),
                'status' => 'published'
            ]
            );
        }
    }
}
