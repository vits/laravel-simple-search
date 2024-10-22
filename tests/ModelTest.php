<?php

use Illuminate\Contracts\Database\Eloquent\Builder;
use Vits\LaravelSimpleSearch\SimpleSearch;
use Vits\LaravelSimpleSearch\SimpleSearchStrategy;
use Vits\LaravelSimpleSearch\Tests\Support\TestModel;

it('uses SimpleSearch trait', function () {
    expect(SimpleSearch::class)
        ->toBeIn(class_uses_recursive(TestModel::class));
});

it('has simpleSearch scope', function () {
    expect(TestModel::simpleSearch('search'))
        ->toBeInstanceOf(Builder::class);
});

it('calls getSimpleSearchFields() passing fields value', function () {
    $spy = Mockery::mock(TestModel::class)->makePartial();

    /** @disregard P1013 Undefined method */
    $spy->simpleSearch('search', 'spy,fields');

    /** @disregard P1013 Undefined method */
    $spy
        ->shouldHaveReceived('getSimpleSearchFields')
        ->with('spy,fields');
});

it('returns all records if no search fields given', function () {
    $sql = TestModel::simpleSearch('search', '')->toSql();

    expect($sql)
        ->toBe('select * from "test_models"');
});

it('returns all records for empty search', function () {
    $sql = TestModel::simpleSearch('', 'name')->toSql();

    expect($sql)
        ->toBe('select * from "test_models"');
});

it('builds query for default START_OF_WORDS strategy', function () {
    $builder = TestModel::simpleSearch('search words', 'first,second');

    expect($builder)
        ->toSql()
        ->toBe('select * from "test_models" where ("first" regexp ? or "second" regexp ?) and ("first" regexp ? or "second" regexp ?)')
        ->getBindings()
        ->toBe([
            '\\bsearch',
            '\\bsearch',
            '\\bwords',
            '\\bwords',
        ]);
});

it('builds query for IN_WORDS strategy', function () {
    $builder = TestModel::simpleSearch('search words', [
        'first' => SimpleSearchStrategy::IN_WORDS,
        'second' => SimpleSearchStrategy::IN_WORDS,
    ]);

    expect($builder)
        ->toSql()
        ->toBe('select * from "test_models" where ("first" like ? or "second" like ?) and ("first" like ? or "second" like ?)')
        ->getBindings()
        ->toBe([
            '%search%',
            '%search%',
            '%words%',
            '%words%',
        ]);
});

it('builds query for START_OF_STRING strategy', function () {
    $builder = TestModel::simpleSearch('search words', [
        'first' => SimpleSearchStrategy::START_OF_STRING,
        'second' => SimpleSearchStrategy::START_OF_STRING,
    ]);


    expect($builder)
        ->toSql()
        ->toBe('select * from "test_models" where ("first" like ? or "second" like ?) and ("first" like ? or "second" like ?)')
        ->getBindings()
        ->toBe([
            'search%',
            'search%',
            'words%',
            'words%',
        ]);
});

it('builds query for EXACT strategy', function () {
    $builder = TestModel::simpleSearch('search words', [
        'first' => SimpleSearchStrategy::EXACT,
        'second' => SimpleSearchStrategy::EXACT,
    ]);


    expect($builder)
        ->toSql()
        ->toBe('select * from "test_models" where ("first" = ? or "second" = ?) and ("first" = ? or "second" = ?)')->getBindings()
        ->toBe([
            'search',
            'search',
            'words',
            'words',
        ]);
});
