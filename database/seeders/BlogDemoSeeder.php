<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Services\CategoryService;
use App\Services\CommentService;
use App\Services\PostService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BlogDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = $this->upsertUser([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'verified' => true,
        ]);

        $author = $this->upsertUser([
            'name' => 'Blog Author',
            'email' => 'author@author.com',
            'password' => 'password',
            'role' => User::ROLE_AUTHOR,
            'verified' => true,
        ]);

        $commenter = $this->upsertUser([
            'name' => 'Sample Reader',
            'email' => 'reader@reader.com',
            'password' => 'password',
            'role' => User::ROLE_VIEWER,
            'verified' => true,
        ]);

        $pendingUser = $this->upsertUser([
            'name' => 'Pending Reader',
            'email' => 'pending@pending.com',
            'password' => 'password',
            'role' => User::ROLE_VIEWER,
            'verified' => false,
        ]);

        $categoryService = app(CategoryService::class);
        $postService = app(PostService::class);
        $commentService = app(CommentService::class);

        $technology = $categoryService->create([
            'status' => 'active',
            'sort_order' => 1,
            'translations' => [
                'en' => [
                    'name' => 'Technology',
                    'slug' => 'technology',
                    'description' => 'Articles about Laravel, tools, and platform decisions.',
                    'seo_title' => 'Technology',
                    'meta_description' => 'Technology focused blog category.',
                ],
                'de' => [
                    'name' => 'Technologie',
                    'slug' => 'technologie',
                    'description' => 'Artikel über Laravel, Tools und Plattformentscheidungen.',
                ],
            ],
        ]);

        $guides = $categoryService->create([
            'status' => 'active',
            'sort_order' => 2,
            'translations' => [
                'en' => [
                    'name' => 'Guides',
                    'slug' => 'guides',
                    'description' => 'Practical how-to content.',
                    'seo_title' => 'Guides',
                    'meta_description' => 'Practical blog guides.',
                ],
            ],
        ]);

        $laravel = $categoryService->create([
            'parent_id' => $technology->id,
            'status' => 'active',
            'sort_order' => 1,
            'translations' => [
                'en' => [
                    'name' => 'Laravel',
                    'slug' => 'laravel',
                    'description' => 'Laravel tutorials and implementation notes.',
                    'seo_title' => 'Laravel',
                    'meta_description' => 'Laravel category for the blog.',
                ],
                'de' => [
                    'name' => 'Laravel',
                    'slug' => 'laravel',
                    'description' => 'Laravel Tutorials und Implementierungsnotizen.',
                ],
            ],
        ]);

        $seo = $categoryService->create([
            'parent_id' => $technology->id,
            'status' => 'active',
            'sort_order' => 2,
            'translations' => [
                'en' => [
                    'name' => 'SEO',
                    'slug' => 'seo',
                    'description' => 'Search and discovery content.',
                    'seo_title' => 'SEO',
                    'meta_description' => 'SEO category for blog posts.',
                ],
            ],
        ]);

        $postOne = $postService->create([
            'user_id' => $author->id,
            'status' => 'published',
            'is_featured' => true,
            'published_at' => Carbon::parse('2026-07-20 10:00:00'),
            'category_ids' => [$technology->id, $laravel->id],
            'translations' => [
                'en' => [
                    'title' => 'Building a multilingual blog in Laravel',
                    'slug' => 'building-a-multilingual-blog-in-laravel',
                    'excerpt' => 'A practical walkthrough for language-based content, fallback logic, and reusable media.',
                    'content' => '<p>This sample post explains the blog structure, translation fallback, and the service layer behind the system.</p><h2>Key points</h2><ul><li>Language-first content</li><li>Shared services</li><li>Reusable media</li></ul>',
                    'seo_title' => 'Build a multilingual Laravel blog',
                    'meta_description' => 'Sample post for the multilingual blog platform.',
                    'og_title' => 'Build a multilingual Laravel blog',
                    'og_description' => 'Language-based publishing with fallback support.',
                    'canonical_url' => url('/blog/building-a-multilingual-blog-in-laravel'),
                    'preview_image_alt' => 'Multilingual blog preview',
                ],
                'de' => [
                    'title' => 'Einen mehrsprachigen Blog in Laravel aufbauen',
                    'slug' => 'einen-mehrsprachigen-blog-in-laravel-aufbauen',
                    'excerpt' => 'Ein praktischer Einstieg in sprachbasierte Inhalte und Fallbacks.',
                ],
            ],
        ]);

        $postTwo = $postService->create([
            'user_id' => $author->id,
            'status' => 'published',
            'is_featured' => false,
            'published_at' => Carbon::parse('2026-07-18 09:30:00'),
            'category_ids' => [$guides->id, $seo->id],
            'translations' => [
                'en' => [
                    'title' => 'Tiptap content workflow for authors',
                    'slug' => 'tiptap-content-workflow-for-authors',
                    'excerpt' => 'How editors can draft and publish content with HTML output.',
                    'content' => '<p>Authors can write directly in the editor and store trusted HTML in the <strong>content</strong> column.</p>',
                    'seo_title' => 'Tiptap workflow for authors',
                    'meta_description' => 'Sample workflow using Tiptap for blog content.',
                    'og_title' => 'Tiptap workflow for authors',
                    'og_description' => 'Editor-driven HTML content for the blog.',
                    'canonical_url' => url('/blog/tiptap-content-workflow-for-authors'),
                    'preview_image_alt' => 'Tiptap editor workflow',
                ],
            ],
        ]);

        $postThree = $postService->create([
            'user_id' => $admin->id,
            'status' => 'published',
            'is_featured' => false,
            'published_at' => Carbon::parse('2026-07-14 15:45:00'),
            'category_ids' => [$seo->id],
            'translations' => [
                'en' => [
                    'title' => 'Publishing and SEO basics',
                    'slug' => 'publishing-and-seo-basics',
                    'excerpt' => 'Metadata, slugs, and locale-aware publishing in one place.',
                    'content' => '<p>This post is a sample article about SEO metadata, structured data, and publishing discipline.</p>',
                    'seo_title' => 'Publishing and SEO basics',
                    'meta_description' => 'Sample SEO post for the blog system.',
                    'og_title' => 'Publishing and SEO basics',
                    'og_description' => 'Metadata and publishing workflow sample.',
                    'canonical_url' => url('/blog/publishing-and-seo-basics'),
                    'preview_image_alt' => 'SEO and publishing preview',
                ],
                'de' => [
                    'title' => 'Grundlagen für Veröffentlichung und SEO',
                    'slug' => 'grundlagen-fuer-veroeffentlichung-und-seo',
                    'excerpt' => 'Metadaten, Slugs und lokalisierte Veröffentlichung.',
                    'content' => '<p>Dies ist ein Beispieltext für SEO und Veröffentlichung.</p>',
                ],
            ],
        ]);

        $firstComment = $commentService->create($postOne, $commenter, [
            'content' => 'This is a sample top-level comment for the first article.',
        ]);

        $firstReply = $commentService->create($postOne, $author, [
            'parent_id' => $firstComment->id,
            'content' => 'Thanks for reading. This reply shows the first level of threading.',
        ]);

        $commentService->create($postOne, $commenter, [
            'parent_id' => $firstReply->id,
            'content' => 'Replying to the reply to demonstrate nested discussion.',
        ]);

        Comment::query()->create([
            'post_id' => $postTwo->id,
            'user_id' => $admin->id,
            'content' => 'Sample discussion for the second post.',
        ]);
    }

    private function upsertUser(array $data): User
    {
        $user = User::query()->firstOrNew(['email' => $data['email']]);

        $user->forceFill([
            'name' => $data['name'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'email_verified_at' => $data['verified'] ? now() : null,
            'remember_token' => Str::random(10),
        ])->save();

        return $user;
    }
}
