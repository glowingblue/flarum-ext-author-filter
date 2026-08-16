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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;

class SlugDriverTest extends TestCase
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

    private function seenDiscussions(ResponseInterface $response): string
    {
        $body = (string) $response->getBody();

        return (str_contains($body, 'ByAdmin') ? 'A' : '').(str_contains($body, 'ByNormal') ? 'N' : '');
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function slugDrivers(): array
    {
        return [
            'default (username)'   => ['default', 'normal'],
            'id'                   => ['id', '2'],
            'id_with_display_name' => ['id_with_display_name', '2-normal'],
        ];
    }

    #[Test]
    #[DataProvider('slugDrivers')]
    public function author_filter_works_with_each_user_slug_driver(string $driver, string $slug)
    {
        $this->setting('slug_driver_Flarum\User\User', $driver);

        $request = $this->request('GET', '/all')->withQueryParams(['author' => $slug]);

        $this->assertSame('N', $this->seenDiscussions($this->send($request)));
    }
}
