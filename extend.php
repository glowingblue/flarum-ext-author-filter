<?php

/*
 * This file is part of glowingblue/author-filter.
 *
 * Copyright (c) Glowing Blue AG.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace GlowingBlue\AuthorFilter;

use Flarum\Api\Context;
use Flarum\Api\Resource;
use Flarum\Api\Schema;
use Flarum\Extend;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/resources/less/admin.less'),

    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\Middleware('forum'))
        ->add(Middleware\AddAuthorFilter::class),

    (new Extend\Settings())
        ->default('glowingblue-author-filter.min_search_length', 3)
        ->default('glowingblue-author-filter.max_results', 5)
        ->serializeToForum('authorFilterMinSearchLength', 'glowingblue-author-filter.min_search_length', 'intval')
        ->serializeToForum('authorFilterMaxResults', 'glowingblue-author-filter.max_results', 'intval'),

    (new Extend\ApiResource(Resource\ForumResource::class))
        ->fields(fn () => [
            Schema\Boolean::make('canUseAuthorFilter')
                ->get(fn (object $model, Context $context) => $context->getActor()->can('searchUsers')),
        ]),
];
