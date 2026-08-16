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
use Psr\Http\Message\ResponseInterface;

class ForumTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    public function setUp(): void
    {
        parent::setUp();

        $this->extension('glowingblue-author-filter');

        $this->prepareDatabase([
            'users' => [
                $this->normalUser(),
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
     * Which of the seeded discussions appear in a response, as a compact string
     * ('A' = admin-authored, 'N' = normal-authored).
     */
    private function seenDiscussions(ResponseInterface $response): string
    {
        $body = (string) $response->getBody();

        return (str_contains($body, 'ByAdmin') ? 'A' : '').(str_contains($body, 'ByNormal') ? 'N' : '');
    }

    #[Test]
    public function extension_boots_and_serializes()
    {
        $response = $this->send($this->request('GET', '/'));

        $this->assertEquals(200, $response->getStatusCode());

        $body = (string) $response->getBody();

        $this->assertStringStartsWith('<!doctype html>', $body);
        $this->assertStringContainsString('</html>', $body);
    }

    #[Test]
    public function forum_resource_exposes_author_filter_attribute()
    {
        $response = $this->send($this->request('GET', '/api', ['authenticatedAs' => 1]));

        $this->assertEquals(200, $response->getStatusCode());

        $attributes = json_decode($response->getBody()->getContents(), true)['data']['attributes'];

        $this->assertArrayHasKey('canUseAuthorFilter', $attributes);
        $this->assertTrue($attributes['canUseAuthorFilter']);
    }

    #[Test]
    public function forum_resource_serializes_settings_defaults()
    {
        $response = $this->send($this->request('GET', '/api', ['authenticatedAs' => 1]));

        $attributes = json_decode($response->getBody()->getContents(), true)['data']['attributes'];

        $this->assertSame(3, $attributes['authorFilterMinSearchLength']);
        $this->assertSame(5, $attributes['authorFilterMaxResults']);
    }

    #[Test]
    public function author_query_param_filters_the_discussion_list()
    {
        $request = $this->request('GET', '/all')->withQueryParams(['author' => 'normal']);

        $this->assertSame('N', $this->seenDiscussions($this->send($request)));
    }

    #[Test]
    public function author_query_param_filters_the_index_when_it_is_the_default_route()
    {
        $this->setting('default_route', '/all');

        $request = $this->request('GET', '/')->withQueryParams(['author' => 'normal']);

        $this->assertSame('N', $this->seenDiscussions($this->send($request)));
    }

    #[Test]
    public function discussion_list_is_unfiltered_without_an_author_param()
    {
        $this->assertSame('AN', $this->seenDiscussions($this->send($this->request('GET', '/all'))));
    }
}
