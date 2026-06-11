# GB Author Filter

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/glowingblue/author-filter.svg)](https://packagist.org/packages/glowingblue/author-filter) [![Total Downloads](https://img.shields.io/packagist/dt/glowingblue/author-filter.svg)](https://packagist.org/packages/glowingblue/author-filter)

A [Flarum](https://flarum.org) extension. Filter discussions by the discussion author.

## Features

- **Author filter dropdown** — adds a user-picker dropdown to the discussion list on the index page (and tag pages) so visitors can narrow the list down to discussions started by a particular author.
- **Autocomplete user search** — type to search for a user, with results populated as you go. Selecting a user applies an `author:` filter to the discussion list (and to the search query when one is already active).
- **Works with the URL `?author=` parameter** — a server-side middleware translates an `author` query parameter into Flarum's native discussion filter, so author-filtered lists are linkable and bookmarkable.
- **Permission-aware** — the dropdown only appears for users who have permission to search users (`searchUsers`), so it respects your forum's existing privacy settings.
- **Configurable search behaviour** — admin settings let you tune:
  - the **minimum number of characters** before a search runs (default: `3`), and
  - the **maximum number of results** shown in the dropdown (default: `5`).
- **Translatable** — all interface strings are localised and can be overridden via Flarum's locale system.

## Installation

Install with composer:

```sh
composer require glowingblue/author-filter:"*"
```

## Updating

```sh
composer update glowingblue/author-filter:"*"
php flarum migrate
php flarum cache:clear
```

## Links

- [Packagist](https://packagist.org/packages/glowingblue/author-filter)
- [GitHub](https://github.com/glowingblue/flarum-ext-author-filter)

## Acknowledgements

This extension is a continuation and replacement of [Blomstra's Author Filter](https://discuss.flarum.org/d/30153-blomstra-author-filter), now maintained by Glowing Blue.
