# Simple search scope for Laravel models

Search for words in one or more text fields (`string` or `text`) using one of provided search strategies.

Search query is split into words and each of these words must match at least one of given fields. Each field may have different search strategy.

**NB: Some of search strategies may work only with MySQL database driver.**

## Installation

```shell
composer require vits/laravel-simple-search
```

## Usage

`SimpleSearch` trait adds `simpleSearch` scope. Scope accepts search query and optional list of fields to be searched. Fields and default search strategy may be configured as model properties.

```php
use Vits\LaravelSimpleSearch\SimpleSearch;
use Vits\LaravelSimpleSearch\SimpleSearchStrategy;

class Book extends Model
{
    use SimpleSearch;

    protected $simpleSearchStrategy = SimpleSearchStrategy::IN_WORDS;
    protected $simpleSearchFields = 'title,body';
}
```

```php
$books = Book::simpleSearch('alice', 'title,body')->get();
```

### Available search strategies

`SimpleSearch::EXACT` - field value must be exact match of search string.

`SimpleSearch::IN_WORDS` - field must contain search string anywhere in it.

`SimpleSearch::START_OF_WORDS` - field must contain word starting with search string.
This ir default strategy.

`SimpleSearch::START_OF_STRING` - field value must start with search string.

### Different ways to define searched fields

This may be used both in `simpleSearch` scope and as model property.

If field definition does not assign search strategy, `simpleSearchStategy` value or
default strategy is used.

```php
# single field
protected $simpleSearchFields = 'title';
# multiple fields
protected $simpleSearchFields = 'alice', 'title,description';
# multiple fields as array
protected $simpleSearchFields = 'alice', ['title', 'description'];
# multiple fields as array with strategy
protected $simpleSearchFields = 'alice', [
    'title',
    'description',
    'notes' => SimpleSearchStrategy::IN_WORD
];
```
