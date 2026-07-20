<?php

namespace App\Services;

use App\Enums\CulturalObjectEnum;
use App\Models\CulturalObject;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SearchService
{
    /**
     * @var array<string, int> Field weights for prioritizing results during full-text search (higher weight means higher importance).
     */
    const WEIGHTS = [
        'title' => 10,
        'description' => 5,
        'cultural_object_provided_by' => 3,
        'theme' => 1,
        'subject_heading' => 1,
        'keywords' => 1,
    ];

    /**
     * @var array<string, string> Map of fields used for Advanced Filters (user alias => database column).
     */
    const ADVANCED_FIELDS = [
        'alternative_title' => 'cultural_object.other_title',
        'object_type'       => 'cultural_object.type',
        'issued_date'       => 'cultural_object.creation_date',
        'creation_date'     => 'cultural_object.creation_date',
        'title'             => 'cultural_object.title',
        'publisher'         => 'provider.title',
        'source'            => 'provider.title',
        'current_location'  => 'cultural_object.current_location',
        'description'       => 'cultural_object.description',
        'rights'            => 'cultural_object.rights_holder',
        'provenance'        => 'cultural_object.previous_owner',
        'material'          => 'cultural_object.medium',
        'creator'           => 'cultural_object.artist',
        'theme'             => 'cultural_object.theme',
        'format'            => 'cultural_object.physical_dimensions',
        'provider'          => 'provider.title',
    ];

    /**
     * @var array<string, string> Fields used for Quick Filters (Facets).
     */
    const FACET_FIELDS = [
        'theme' => 'cultural_object.theme',
        'type' => 'cultural_object.type',
        'rights_holder' => 'cultural_object.rights_holder',
        'provider' => 'provider.title',
//        'country_of_origin' => 'cultural_object.country_of_origin',
    ];

    /**
     * Executes the search for Cultural Objects based on a text query, advanced, and quick filters.
     *
     * @param string|null $query The main search text query.
     * @param int $perPage Number of results per page.
     * @param array<string, array<string, string>> $advancedFilters Associative array of advanced filters.
     * @param array<string, string|array<string>> $quickFilters Associative array of quick filters (facets).
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search(?string $query, int $perPage = 1000, array $advancedFilters = [], array $quickFilters = []): LengthAwarePaginator
    {
        $searchText = trim($query ?? '');

        $searchQuery = CulturalObject::query()
            ->with('provider')
            ->leftJoin('provider', 'cultural_object.cultural_object_provided_by', '=', 'provider.id')
            ->select('cultural_object.*');

        if (!empty($searchText)) {
            $isExactPhrase = Str::startsWith($searchText, '"') && Str::endsWith($searchText, '"');
            $processedText = $this->normalizeText($searchText, $isExactPhrase);

            $searchTerms = $isExactPhrase
                ? [$processedText]
                : array_filter(preg_split('/\s+/', $processedText));

            if (!empty($searchTerms)) {
                $this->applySearchConditions($searchQuery, $searchTerms);
                $this->applySortingLogic($searchQuery, $searchTerms, $searchText);
            }
        }

        if (!empty($advancedFilters)) {
            $this->applyAdvancedFilters($searchQuery, $advancedFilters);
        }

        if (!empty($quickFilters)) {
            $this->applyQuickFilters($searchQuery, $quickFilters);
        }

        $searchQuery->orderBy('cultural_object.id', 'ASC');


        return $searchQuery->paginate($perPage)->withQueryString();
    }

    /**
     * Cleans up the search text and handles exact phrase quotes.
     *
     * @param string $text The raw search text.
     * @param bool $isExactPhrase Whether the search is for an exact phrase.
     * @return string The normalized search text.
     */
    private function normalizeText(string $text, bool $isExactPhrase): string
    {
        if ($isExactPhrase) {
            $text = trim(substr($text, 1, -1));
        }

        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', trim($text));

        return $text;
    }

    /**
     * Applies search conditions (WHERE clauses) to the query builder based on search terms.
     * The conditions use OR logic and apply to weighted fields.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The Eloquent query builder instance.
     * @param array<string> $searchTerms An array of individual terms to search for.
     * @return void
     */
    private function applySearchConditions(Builder $query, array $searchTerms): void
    {
        $operator = $this->likeOperator();

        $query->where(function (Builder $q) use ($searchTerms, $operator) {

            foreach ($searchTerms as $term) {
                $termPattern = "%$term%";

                $q->where(function (Builder $subQuery) use ($termPattern, $operator) {

                    foreach (self::WEIGHTS as $field => $weight) {

                        $column = ($field === 'cultural_object_provided_by')
                            ? 'provider.title'
                            : "cultural_object.$field";

                        $subQuery->orWhereRaw("$column $operator ?", [$termPattern]);
                    }
                });
            }
        });
    }

    /**
     * Applies Quick Filters (facets and date range) to the query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The Eloquent query builder instance.
     * @param array<string, string|array<string>> $filters Array of quick filters.
     * @return void
     */
    private function applyQuickFilters(Builder $query, array $filters): void
    {
//        $dateFrom = $filters['date_from'] ?? null;
//        $dateTo = $filters['date_to'] ?? null;
//
//        if ($dateFrom && $dateTo) {
//            $query->whereBetween('cultural_object.creation_date', [$dateFrom, $dateTo]);
//        } elseif ($dateFrom) {
//            $query->where('cultural_object.creation_date', '>=', $dateFrom);
//        } elseif ($dateTo) {
//            $query->where('cultural_object.creation_date', '<=', $dateTo);
//        }

        foreach ($filters as $field => $values) {
            if ($field === 'date_from' || $field === 'date_to' || empty($values)) {
                continue;
            }

            if ($field === 'type') {
                $selectedLabels = is_array($values) ? $values : explode(',', $values);

                $selectedTypes = [];
                foreach ($selectedLabels as $label) {
                    $matchingTypes = array_keys(
                        array_filter(
                            CulturalObjectEnum::getReadableVisualisation(),
                            fn($readableLabel) => $readableLabel === $label
                        )
                    );

                    if (!empty($matchingTypes)) {
                        $selectedTypes = array_merge($selectedTypes, $matchingTypes);
                    } else {
                        $selectedTypes[] = $label;
                    }
                }

                $selectedTypes = array_unique(array_filter($selectedTypes));

                if (!empty($selectedTypes)) {
                    $query->whereHas('has_web_view_resource', function ($q) use ($selectedTypes) {
                        $q->whereIn('web_resource.visualizationtype', $selectedTypes);
                    });
                }

                continue;
            }

            if (!isset(self::FACET_FIELDS[$field])) {
                continue;
            }

            $column = self::FACET_FIELDS[$field];

            if (is_string($values)) {
                $selectedValues = array_filter(explode(',', $values));
            } else {
                $selectedValues = (array) $values;
                $selectedValues = array_filter($selectedValues, fn($v) => $v !== '');
            }

            if (empty($selectedValues)) {
                continue;
            }

            $query->whereIn($column, $selectedValues);
        }
    }

    /**
     * Applies advanced filtering conditions to the query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The Eloquent query builder instance.
     * @param array<string, array<string, string>> $filters Associative array of advanced filters.
     * @return void
     */
    private function applyAdvancedFilters(Builder $query, array $filters): void
    {
        $likeOperator = $this->likeOperator();

        foreach ($filters as $field => $filter) {
            if ($field === 'format') {

                $value = trim($filter['value'] ?? '');

                if ($value === '') {
                    continue;
                }

                $mode = $filter['mode'] ?? '1';

                if ($mode === '1') {
                    $query->whereHas('has_web_view_resource', function ($q) use ($value) {

                        $q->where(function ($subQuery) use ($value) {

                            $subQuery
                                ->where('web_resource.format', $value)
                                ->orWhere('web_resource.mimetype_trailer', $value)
                                ->orWhere('web_resource.mimetype_download', $value);
                        });
                    });

                } elseif ($mode === '0') {

                    $query->whereDoesntHave('has_web_view_resource', function ($q) use ($value) {

                        $q->where(function ($subQuery) use ($value) {

                            $subQuery
                                ->where('web_resource.format', $value)
                                ->orWhere('web_resource.mimetype_trailer', $value)
                                ->orWhere('web_resource.mimetype_download', $value);
                        });
                    });
                }

                continue;
            }

            if (!isset(self::ADVANCED_FIELDS[$field])) {
                continue;
            }

            $column = self::ADVANCED_FIELDS[$field];
            $value = trim($filter['value'] ?? '');
            $value = $this->normalizeText($value, false);

            $mode = $filter['mode'] ?? '1';

            if ($value === '' || strlen($value) < 3 || strlen($value) > 255) {
                continue;
            }

            $pattern = "%$value%";

            $normalizedColumn = "REGEXP_REPLACE(CAST($column AS TEXT), '[^[:alnum:][:space:]]', '', 'g')";

            if ($mode === '1') {
                $query->whereRaw("$normalizedColumn $likeOperator ?", [$pattern]);
            } elseif ($mode === '0') {
                $query->where(function (Builder $q) use ($normalizedColumn, $pattern, $likeOperator) {
                    $q->whereRaw("$normalizedColumn NOT $likeOperator ?", [$pattern])
                        ->orWhereRaw("$normalizedColumn IS NULL");
                });
            }
        }
    }

    /**
     * Applies custom sorting logic based on term weight, match case, and ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The Eloquent query builder instance.
     * @param array<string> $searchTerms An array of individual search terms.
     * @param string $originalQuery The original, un-normalized query string.
     * @return void
     */
    private function applySortingLogic(Builder $query, array $searchTerms, string $originalQuery): void
    {
        $likeOperator = $this->likeOperator();
        $scoreQueryParts = [];
        $bindings = [];

        foreach ($searchTerms as $term) {
            $termPattern = "%$term%";

            foreach (self::WEIGHTS as $field => $weight) {

                $column = ($field === 'cultural_object_provided_by')
                    ? 'provider.title'
                    : "cultural_object.$field";

                $scoreQueryParts[] = "(CASE WHEN $column $likeOperator ? THEN $weight ELSE 0 END)";
                $bindings[] = $termPattern;
            }
        }

        $scoreExpression = implode(' + ', $scoreQueryParts);

        if ($this->isAllUppercase($originalQuery)) {
            $query->orderByRaw("CASE WHEN cultural_object.title = ? THEN 0 ELSE 1 END", [$originalQuery]);
        }

        $query->orderByRaw("$scoreExpression DESC", $bindings);

        $query->orderBy('cultural_object.id', 'ASC');
    }

    /**
     * Checks if a string consists only of uppercase letters and numbers (case-sensitive check).
     *
     * @param string $text The text to check.
     * @return bool
     */
    private function isAllUppercase(string $text): bool
    {
        $cleanText = preg_replace('/[^a-zA-Zа-яА-Я0-9]/u', '', $text);
        return !empty($cleanText) && Str::upper($cleanText) === $cleanText;
    }

    /**
     * Determines the appropriate LIKE operator based on the database driver.
     * Uses ILIKE for PostgreSQL for case-insensitivity, and LIKE otherwise.
     *
     * @return string
     */
    private function likeOperator(): string
    {
        return DB::connection('secondary')->getDriverName() === 'pgsql'
            ? 'ILIKE'
            : 'LIKE';
    }

    /**
     * Calculates unique values (facets) for quick filters based on the active filters.
     *
     * @param array<string, array<string, string>> $advancedFilters The advanced filters currently applied.
     * @param array<string, string|array<string>> $quickFilters The quick filters currently applied.
     * @return array<string, array<string>> Array of available unique values for each facet field.
     */
    public function getFacets(array $advancedFilters, array $quickFilters): array
    {
        $facets = [];

        $baseQuery = CulturalObject::query()
            ->with('provider')
            ->leftJoin('provider', 'cultural_object.cultural_object_provided_by', '=', 'provider.id');

        if (!empty($advancedFilters)) {
            $this->applyAdvancedFilters($baseQuery, $advancedFilters);
        }

        foreach (self::FACET_FIELDS as $field => $column) {

            $facetQuery = clone $baseQuery;

            $tempQuickFilters = $quickFilters;
            unset($tempQuickFilters[$field]);

            if (!empty($tempQuickFilters)) {
                $this->applyQuickFilters($facetQuery, $tempQuickFilters);
            }

            if ($field === 'type') {
                $results = $facetQuery
                    ->leftJoin('has_web_view', 'cultural_object.id', '=', 'has_web_view.cultural_object_id')
                    ->leftJoin('web_resource', 'has_web_view.web_resource_id', '=', 'web_resource.id')
                    ->whereNotNull('web_resource.visualizationtype')
                    ->distinct()
                    ->pluck('web_resource.visualizationtype')
                    ->map(fn($type) => CulturalObjectEnum::getReadableVisualisation($type))
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values()
                    ->toArray();

                $facets[$field] = $results;
                continue;
            }

            $results = $facetQuery
                ->select(DB::raw("DISTINCT $column"))
                ->whereNotNull($column)
                ->where($column, '!=', '')
                ->pluck(Str::afterLast($column, '.'));

            $facets[$field] = $results->filter()->unique()->sort()->values()->toArray();
        }

        return $facets;
    }

    /**
     * Extracts and formats Advanced Filters from the request data array.
     *
     * @param array<string, mixed> $requestData The HTTP request data.
     * @return array<string, array<string, string>>
     */
    public function extractAdvancedFilters(array $requestData): array
    {
        $filters = [];

        foreach (self::ADVANCED_FIELDS as $field => $column) {
            $prefixedField = 'adv_' . $field;

            $value = $requestData[$prefixedField] ?? '';
            $mode  = $requestData[$prefixedField . '_mode'] ?? '1';

            if (!empty($value)) {
                $filters[$field] = [
                    'value' => $value,
                    'mode'  => $mode
                ];
            }
        }

        return $filters;
    }

    /**
     * Extracts Quick Filters (facets and date range) from the request data array.
     *
     * @param array<string, mixed> $requestData The HTTP request data.
     * @return array<string, mixed>
     */
    public function extractQuickFilters(array $requestData): array
    {
        $facetKeys = array_keys(self::FACET_FIELDS);
        $quickKeys = array_merge($facetKeys, ['date_from', 'date_to']);

        $quickFilters = [];
        foreach ($quickKeys as $key) {
            if (array_key_exists($key, $requestData)) {
                $quickFilters[$key] = $requestData[$key];
            }
        }

        return $quickFilters;
    }

    /**
     * Executes the search, limits results to 1000, and prepares a callable function
     * for streaming the search results as a CSV file.
     *
     * The CSV export includes all defined metadata columns and a UTF-8 BOM.
     *
     * @param string|null $query The main search query.
     * @param array $advancedFilters Advanced filter parameters.
     * @param array $quickFilters Quick filter parameters.
     * @return callable A closure that outputs the CSV content to the stream.
     * @throws \Exception If the search returns no results.
     */
    public function prepareCsvExport(?string $query, array $advancedFilters, array $quickFilters): callable
    {
        $results = $this->search($query, 1000, $advancedFilters, $quickFilters);
        $items = collect($results->items());

        if ($items->isEmpty()) {
            throw new Exception(__('search.no_results_export'));
        }

        $columns = array_keys((new CulturalObject())->casts);

        return function() use ($items, $columns) {
            $output = fopen('php://output', 'w');

            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($output, $columns);

            foreach ($items as $item) {
                fputcsv($output, array_map(fn($col) => $item[$col] ?? '', $columns));
            }

            fclose($output);
        };
    }
}
