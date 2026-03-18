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
                'question' => 'How can I help you?',
                'answer' => 'You can browse our frequently asked questions to find quick answers about our services, processes, and tools.',
                'category' => 'General',
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'question' => 'How do I navigate the dashboard?',
                'answer' => 'Use the sidebar on the left to access all features like Orders, Invoices, and Services. The dashboard gives you a bird\'s eye view of your current orders and wallet balance.',
                'category' => 'Dashboard Basics',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'question' => 'Where can I see my notifications?',
                'answer' => 'Click the bell icon at the top right of your screen. You will receive notifications for order status updates, new messages, and payment approvals.',
                'category' => 'Dashboard Basics',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'question' => 'How do I place an order?',
                'answer' => 'Navigate to the <b>Shop & Services</b> section in the sidebar. Choose your desired service (SEO, Guest Post, Website Traffic, etc.), select a package that fits your needs, and click <b>Checkout</b>.',
                'category' => 'Ordering & Services',
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'question' => 'How do I top up my balance?',
                'answer' => 'Go to <b>Payments & Billing</b> > <b>Add Balance</b>. You can choose from automatic gateways like Stripe or use manual methods. For manual payments, please upload your proof of payment for admin verification.',
                'category' => 'Payments & Wallet',
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'question' => 'How do I manage new orders as an Admin?',
                'answer' => 'Navigate to <b>Sales & Marketing</b> > <b>All Orders</b>. New orders will appear with a "Pending" status. You can open any order to view detailed requirements and update its status.',
                'category' => 'Admin: Order Management',
                'is_active' => true,
                'sort_order' => 100,
            ],
            [
                'question' => 'How do I approve Wallet top-ups?',
                'answer' => 'Go to <b>Payments Hub</b> > <b>Top-up Requests</b>. Review the client\'s payment information or uploaded proof, then click <b>Approve</b> to instantly credit their wallet.',
                'category' => 'Admin: Finances',
                'is_active' => true,
                'sort_order' => 101,
            ],
            [
                'question' => 'How to add new Guest Post sites?',
                'answer' => 'In the <b>Services</b> menu, go to <b>Guest Posts</b>. Use the "Add New" button to list a new site with its domain authority (DA), niche, and price metrics.',
                'category' => 'Admin: Service Management',
                'is_active' => true,
                'sort_order' => 102,
            ],
            [
                'question' => 'How can I create discount coupons?',
                'answer' => 'Navigate to <b>Sales & Marketing</b> > <b>Coupons & Discounts</b>. You can create percentage-based or fixed-amount coupons with expiry dates and usage limits.',
                'category' => 'Admin: Management',
                'is_active' => true,
                'sort_order' => 103,
            ],
            [
                'question' => 'How do I send site-wide announcements?',
                'answer' => 'Go to <b>Blog & Content</b> > <b>Announcements</b>. You can create a new announcement that will be displayed in the client dashboard once published.',
                'category' => 'Admin: Management',
                'is_active' => true,
                'sort_order' => 104,
            ],
            [
                'question' => 'How to view website analytics?',
                'answer' => 'Under <b>Sales & Marketing</b>, click on <b>Website Analytics</b>. Here you can see traffic trends, popular services, and user engagement metrics.',
                'category' => 'Admin: Management',
                'is_active' => true,
                'sort_order' => 105,
            ],
            [
                'question' => 'What is the Affiliate Program?',
                'answer' => 'Our affiliate program allows you to earn commissions by referring new clients. You can find your unique referral link under <b>Earn</b> > <b>Affiliate Program</b>.',
                'category' => 'Earn & Support',
                'is_active' => true,
                'sort_order' => 200,
            ],
            [
                'question' => 'Where can I find free SEO tools?',
                'answer' => 'In the <b>Support</b> section, navigate to <b>Free SEO Tools</b>. We provide a variety of helpful tools to analyze your website, check rankings, and more.',
                'category' => 'Earn & Support',
                'is_active' => true,
                'sort_order' => 201,
            ],
        ];

        foreach ($faqs as $faq) {
            SiteFaq::firstOrCreate(['question' => $faq['question']], $faq);
        }
    }
}
