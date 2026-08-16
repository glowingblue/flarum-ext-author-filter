<?php

/*
 * This file is part of glowingblue/author-filter.
 *
 * Copyright (c) Glowing Blue AG.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace GlowingBlue\AuthorFilter\Middleware;

use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AddAuthorFilter implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // We only want to apply filtering if we show the discussion list.
        if (!$this->isDiscussionListPath($request)) {
            return $handler->handle($request);
        }

        $params = $request->getQueryParams();

        // Translate our `?author=` query param into the `filter[author]` that
        // core's discussion AuthorFilter understands. The `q` gambit is left
        // alone: core applies the author gambit itself.
        if ($author = Arr::pull($params, 'author')) {
            $params['filter'] = array_merge($params['filter'] ?? [], [
                'author' => $author,
            ]);

            $request = $request->withQueryParams($params);
        }

        return $handler->handle($request);
    }

    private function isDiscussionListPath(ServerRequestInterface $request): bool
    {
        $path = $request->getAttribute('originalUri')->getPath();

        // The discussion list lives at `/all`, and additionally at `/` when it
        // is the configured default route.
        if ($path === '/all') {
            return true;
        }

        /** @var SettingsRepositoryInterface $settings */
        $settings = resolve(SettingsRepositoryInterface::class);

        if ($path === '/' && $settings->get('default_route') === '/all') {
            return true;
        }

        // Tag pages (`/t/{slug}`) also render the discussion list.
        return $path === '/t' || str_starts_with($path, '/t/');
    }
}
