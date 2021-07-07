<?php

declare(strict_types=1);

namespace Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HttpFilter
{
    /**
     * Column names are alphanumeric strings that can contain
     * underscores (`_`) but can't start with a number.
     */
    private const VALID_COLUMN_NAME_REGEX = '/^(?![\d])[A-Za-z0-9_>-]*$/';

    /**
     * Date range looks like "YYYY-MM-DD to YYYY-MM-DD"
     */
    private const VALID_DATE_RANGE_REGEX = '/\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}/';
    private const VALID_DATE_SINGLE_REGEX = '/\d{4}-\d{2}-\d{2}/';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Collection
     */
    protected $filters;

    /**
     * @var Collection
     */
    protected $sorts;

    /**
     * Model options and allowed params.
     *
     * @var Collection
     */
    protected $options;

    /**
     * Filter constructor.
     *
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        $this->request = $request ?? request();

        $this->filters = collect($this->request->get('filter', []))->map(function ($item) {
            return $this->parseHttpValue($item);
        });
        $this->sorts = collect($this->request->get('sort', []));
    }

    /**
     * @param string|null $query
     *
     * @return string|array|null
     */
    protected function parseHttpValue($query)
    {
        if ($query === null) {
            return null;
        }

        $item = explode(',', $query);

        if (count($item) > 1) {
            return $item;
        }

        return $query;
    }

    /**
     * @param string $column
     *
     * @return string
     */
    public static function sanitize(string $column): string
    {
        abort_unless(preg_match(self::VALID_COLUMN_NAME_REGEX, $column), Response::HTTP_BAD_REQUEST);

        return $column;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function build(Builder $builder): Builder
    {
        $this->options = $builder->getModel()->getOptionsFilter();
        $this->addFiltersToQuery($builder);
        $this->addSortsToQuery($builder);

        return $builder;
    }

    /**
     * @param Builder $builder
     */
    protected function addFiltersToQuery(Builder $builder)
    {
        $allowedFilters = $this->options->get('allowedFilters')->toArray();

        $this->filters->each(function ($value, $property) use ($builder, $allowedFilters) {
            $allowProperty = $property;
            if (strpos($property, '.') !== false) {
                $allowProperty = strstr($property, '.', true);
            }

            if (in_array($allowProperty, $allowedFilters, true)) {
                $property = str_replace('.', '->', $property);
                $this->filtersExact($builder, $value, $property);
            }
        });
    }

    /**
     * @param Builder $query
     * @param mixed   $value
     * @param string  $property
     *
     * @return Builder
     */
    protected function filtersExact(Builder $query, $value, string $property): Builder
    {
        $property = self::sanitize($property);

        switch (true) {

            case is_array($value):
                $query->whereIn($property, $value);
                break;

            case is_numeric($value):
                $query->where($property, $value);
                break;

            case preg_match(self::VALID_DATE_RANGE_REGEX, $value):
                // extract dates
                preg_match_all(self::VALID_DATE_SINGLE_REGEX, $value, $match);
                $query->whereBetween($property, [$match[0][0]." 00:00:00", $match[0][1]." 23:59:59"]);
                break;

            default:
                $query->where($property, 'like', "%$value%");

        }

        return $query;
    }

    /**
     * @param Builder $builder
     */
    protected function addSortsToQuery(Builder $builder)
    {
        $allowedSorts = $this->options->get('allowedSorts')->toArray();

        $this->sorts
            ->each(function (string $sort) use ($builder, $allowedSorts) {
                $descending = strpos($sort, '-') === 0;
                $key = ltrim($sort, '-');
                $property = Str::before($key, '.');
                $key = str_replace('.', '->', $key);

                if (in_array($property, $allowedSorts, true)) {
                    $key = $this->sanitize($key);
                    $builder->orderBy($key, $descending ? 'desc' : 'asc');
                }
            });
    }

    /**
     * @param null|string $property
     *
     * @return bool
     */
    public function isSort(string $property = null): bool
    {
        if ($property === null) {
            return $this->sorts->isEmpty();
        }

        if ($this->sorts->search($property, true) !== false) {
            return true;
        }

        if ($this->sorts->search('-'.$property, true) !== false) {
            return true;
        }

        return false;
    }

    /**
     * @param string $property
     *
     * @return string
     */
    public function revertSort(string $property): string
    {
        return $this->getSort($property) === 'asc'
            ? '-'.$property
            : $property;
    }

    /**
     * @param string $property
     *
     * @return string
     */
    public function getSort(string $property): string
    {
        return $this->sorts->search($property, true) !== false
            ? 'asc'
            : 'desc';
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function getFilter(string $property)
    {
        return Arr::get($this->filters, $property);
    }
}
