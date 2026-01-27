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
        if (! $this->isDiscussionListPath($request)) {
            return $handler->handle($request);
        }

        $params = $request->getQueryParams();

        if ($author = Arr::pull($params, 'author')) {
            $params['filter'] = array_merge($params['filter'] ?? [], [
                'author' => $author,
            ]);

            if (!empty($params['q'])) {
                $params['q'] = trim($params['q']) . ' author:' . $author;
            }

            $request = $request->withQueryParams($params);

            return $handler->handle($request);
        }

        return $handler->handle($request);
    }

    private function isDiscussionListPath(ServerRequestInterface $request): bool
    {
        $path = $request->getAttribute('originalUri')->getPath();

        // Check for the 'index' route (showing all discussions)
        /** @var SettingsRepositoryInterface */
        $settings = resolve(SettingsRepositoryInterface::class);
        $defaultRoute = $settings->get('default_route');

        if ($defaultRoute === '/all') {
            if ($path === '/') {
                return true;
            }
        } elseif ($path === '/all') {
            return true;
        }

        // Check for the 'tag' route (tag page)
        if (substr($path, 0, 2) === '/t') {
            return true;
        }

        return false;
    }
}
