<?php

declare(strict_types=1);

namespace Vits\LaravelSimpleSearch;

enum SimpleSearchStrategy
{
    case EXACT;
    case IN_WORDS;
    case START_OF_WORDS;
    case START_OF_STRING;
}
