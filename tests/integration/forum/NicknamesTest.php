<?php

/*
 * This file is part of glowingblue/author-filter.
 *
 * Copyright (c) Glowing Blue AG.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace GlowingBlue\AuthorFilter\Tests\integration\forum;

use Flarum\Discussion\Discussion;
use Flarum\Post\Post;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use PHPUnit\Framework\Attributes\Test;

class NicknamesTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    public function setUp(): void
    {
        parent::setUp();

        $this->extension('flarum-nicknames', 'glowingblue-author-filter');

        $this->setting('display_name_driver', 'nickname');

        $this->prepareDatabase([
            'users' => [
                $this->normalUser() + ['nickname' => 'Cool Nick'],
            ],
            Discussion::class => [
                ['id' => 1, 'title' => 'ByAdmin', 'user_id' => 1, 'comment_count' => 1],
                ['id' => 2, 'title' => 'ByNormal', 'user_id' => 2, 'comment_count' => 1],
            ],
            Post::class => [
                ['id' => 1, 'discussion_id' => 1, 'user_id' => 1, 'type' => 'comment', 'content' => '<t><p>a</p></t>', 'number' => 1],
                ['id' => 2, 'discussion_id' => 2, 'user_id' => 2, 'type' => 'comment', 'content' => '<t><p>b</p></t>', 'number' => 1],
            ],
        ]);
    }

    /**
     * The slug stays username-based under the nickname display name driver, so
     * the author filter must key off the slug rather than the display name.
     */
    #[Test]
    public function slug_and_display_name_diverge_under_the_nickname_driver()
    {
        $response = $this->send($this->request('GET', '/api/users/2', ['authenticatedAs' => 1]));

        $attributes = json_decode($response->getBody()->getContents(), true)['data']['attributes'];

        $this->assertSame('normal', $attributes['slug']);
        $this->assertSame('Cool Nick', $attributes['displayName']);
    }

    #[Test]
    public function author_filter_works_under_the_nickname_driver()
    {
        $request = $this->request('GET', '/all')->withQueryParams(['author' => 'normal']);

        $body = (string) $this->send($request)->getBody();

        $this->assertStringContainsString('ByNormal', $body);
        $this->assertStringNotContainsString('ByAdmin', $body);
    }
}
