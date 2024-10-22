<?php

use Illuminate\Database\Eloquent\Model;
use Vits\LaravelSimpleSearch\SimpleSearch;
use Vits\LaravelSimpleSearch\SimpleSearchStrategy;


it('returns empty array when no model properties or fields are given', function () {
    $model = new class extends Model {
        use SimpleSearch;
    };

    $fields = invokePrivateMethod($model, 'getSimpleSearchFields');

    expect($fields)
        ->toBe([]);
});

it('returns fields array from model properties', function () {
    $model = new class extends Model {
        use SimpleSearch;
        protected $simpleSearchFields = [
            'model' => SimpleSearchStrategy::EXACT,
            'second' => SimpleSearchStrategy::IN_WORDS,
        ];
    };

    $fields = invokePrivateMethod($model, 'getSimpleSearchFields');

    expect($fields)->toBe([
        'model' => SimpleSearchStrategy::EXACT,
        'second' => SimpleSearchStrategy::IN_WORDS,
    ]);
});

it('returns received fields possibly overriding model properties', function () {
    $model = new class extends Model {
        use SimpleSearch;
        protected $simpleSearchFields = 'model';
    };

    $fields = invokePrivateMethod($model, 'getSimpleSearchFields', [
        'first' => SimpleSearchStrategy::EXACT,
        'second' => SimpleSearchStrategy::IN_WORDS,
    ]);

    expect($fields)->toBe([
        'first' => SimpleSearchStrategy::EXACT,
        'second' => SimpleSearchStrategy::IN_WORDS,
    ]);
});

it('builds fields array from string using default strategy', function () {
    $model = new class extends Model {
        use SimpleSearch;
    };

    $fields = invokePrivateMethod($model, 'getSimpleSearchFields', ' first , second');

    expect($fields)->toBe([
        'first' => SimpleSearchStrategy::START_OF_WORDS,
        'second' => SimpleSearchStrategy::START_OF_WORDS,
    ]);
});

it('applies default strategy to fields with no strategy', function () {
    $model = new class extends Model {
        use SimpleSearch;
    };

    $fields = invokePrivateMethod($model, 'getSimpleSearchFields', [
        'default',
        'exact' => SimpleSearchStrategy::EXACT,
    ]);

    expect($fields)->toBe([
        'default' => SimpleSearchStrategy::START_OF_WORDS,
        'exact' => SimpleSearchStrategy::EXACT,
    ]);
});

it('uses default strategy from model property', function () {
    $model = new class extends Model {
        use SimpleSearch;
        protected $simpleSearchStrategy = SimpleSearchStrategy::IN_WORDS;
    };

    $fields = invokePrivateMethod($model, 'getSimpleSearchFields', [
        'first' => SimpleSearchStrategy::EXACT,
        'second'
    ]);

    expect($fields)->toBe([
        'first' => SimpleSearchStrategy::EXACT,
        'second' => SimpleSearchStrategy::IN_WORDS,
    ]);
});
