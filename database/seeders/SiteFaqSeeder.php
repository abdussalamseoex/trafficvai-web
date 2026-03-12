<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteFaq;

class SiteFaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faqs = [
            [
                'question' => 'How long does it take to see results from SEO?',
                'answer' => 'SEO is a long-term strategy. While some improvements can be seen in the first 1-2 months (such as indexing fixes or quick wins), significant ranking changes and traffic growth typically take 3 to 6 months. High-competition keywords may take even longer.',
                'category' => 'General',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'question' => 'What is the difference between On-Page and Off-Page SEO?',
                'answer' => 'On-Page SEO refers to optimizations made directly on your website (like content, keywords, meta tags, and site speed). Off-Page SEO refers to actions taken outside of your website to impact rankings (like building backlinks and social signals). Both are essential for a successful campaign.',
                'category' => 'SEO Basics',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'question' => 'Do you guarantee a #1 ranking on Google?',
                'answer' => 'No reputable SEO agency can guarantee a #1 ranking due to the unpredictable nature of Google\'s algorithm. However, we do guarantee that we use proven, white-hat techniques to significantly improve your visibility, traffic, and search engine positioning.',
                'category' => 'Rankings',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'question' => 'What are your deliverables for a monthly SEO campaign?',
                'answer' => 'Our monthly SEO packages typically include a mix of technical audits, on-page optimization, content creation or refinement, and high-quality backlink building. You will receive a detailed report at the end of each month outlining the work completed and the impact on your rankings.',
                'category' => 'Services',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'question' => 'Are your guest posts permanent?',
                'answer' => 'Yes, our guest posts are placed permanently on high-authority blogs. If a link is ever removed within the first year, we offer a free replacement on a site with equal or higher metrics.',
                'category' => 'Guest Posts',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'question' => 'What is your refund policy?',
                'answer' => 'We offer full refunds if an order cannot be processed or if a guest post is rejected by the publisher. Once work has begun on a monthly campaign, payments are non-refundable, but you can cancel your subscription at any time without long-term contracts.',
                'category' => 'Billing',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'question' => 'How do I track my campaign progress?',
                'answer' => 'You can track all active orders and ongoing campaigns directly from your Client Dashboard. Under the "Reports & Analytics" section, you will see your organic growth, keyword distribution, and overall SEO health.',
                'category' => 'Dashboard',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'question' => 'Can you help if my site has been penalized by Google?',
                'answer' => 'Yes, we offer Penalty Recovery services. We will conduct a thorough backlink audit, disavow toxic links, and fix any on-page manipulative tactics (like keyword stuffing) that may have triggered an algorithmic or manual penalty.',
                'category' => 'Technical',
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($faqs as $faq) {
            SiteFaq::firstOrCreate(['question' => $faq['question']], $faq);
        }
    }
}
