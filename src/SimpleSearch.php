<?php

declare(strict_types=1);

namespace Vits\LaravelSimpleSearch;

trait SimpleSearch
{
    /**
     * Return array of searchable field strategies indexed by field name.
     *
     * @param string|array[] $fields
     * @return array
     */
    protected function getSimpleSearchFields(string|array $fields = []): array
    {
        if (!$fields && $this->simpleSearchFields) {
            $fields = $this->simpleSearchFields;
        }

        if (!$fields) {
            return [];
        }

        $strategy = $this->simpleSearchStrategy ?? SimpleSearchStrategy::START_OF_WORDS;

        if (is_string($fields)) {
            $fields = array_filter(
                array_values(array_map('trim', explode(',', $fields)))
            );
        }

        $fields = array_reduce(array_keys($fields), function ($carry, $key) use ($fields, $strategy) {
            if (is_string($key)) {
                $carry[$key] = $fields[$key];
            } else {
                $carry[$fields[$key]] = $strategy;
            }
            return $carry;
        }, []);

        return $fields;
    }

    public function scopeSimpleSearch($query, ?string $search, array|string $fields = [])
    {
        if (!$search) {
            return $query;
        }

        $fields = $this->getSimpleSearchFields($fields);
        if (empty($fields)) {
            return $query;
        }

        $driver = $query->getConnection()->getConfig()["driver"];
        $words = explode(' ', $search);

        foreach ($words as $word) {
            $query = $query->where(function ($query) use ($word, $fields, $driver) {
                foreach ($fields as $field => $strategy) {
                    switch ($strategy) {
                        case SimpleSearchStrategy::IN_WORDS:
                            $query->orWhere($field, 'like', '%' . $word . '%');
                            break;
                        case SimpleSearchStrategy::START_OF_WORDS:
                            if ($driver === "pgsql") {
                                $query->orWhereRaw($field . ' ~* ?', '\y' . $word);
                            } else {
                                $query->orWhere($field, 'regexp', '\\b' . $word);
                            }
                            break;
                        case SimpleSearchStrategy::START_OF_STRING:
                            $query->orWhere($field, 'like', $word . '%');
                            break;
                        default:
                            $query->orWhere($field, $word);
                    }
                }
            });
        }

        return $query;
    }
}
