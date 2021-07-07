<?php

declare(strict_types=1);

namespace Orchid\Screen;

use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TD extends Cell
{
    /**
     * Align the cell to the left.
     */
    public const ALIGN_LEFT = 'start';

    /**
     * Align the cell to the center.
     */
    public const ALIGN_CENTER = 'center';

    /**
     * Align the cell to the right.
     */
    public const ALIGN_RIGHT = 'end';

    public const FILTER_TEXT = 'text';
    public const FILTER_NUMERIC = 'numeric';
    public const FILTER_DATE = 'date';
    public const FILTER_DATE_RANGE = 'date_range';

    /**
     * @var string|null|int
     */
    protected $width;

    /**
     * @var string
     */
    protected $filter;

    /**
     * @var bool
     */
    protected $sort;

    /**
     * @var string
     */
    protected $align = 'left';

    /**
     * @var int
     */
    protected $colspan = 1;

    /**
     * Displays whether the user can hide
     * or show the column in the browser.
     *
     * @var bool
     */
    protected $allowUserHidden = true;

    /**
     * Should the user independently enable
     * the display of the column.
     *
     * @var bool
     */
    protected $defaultHidden = false;

    /**
     * @param string|int $width
     *
     * @return TD
     */
    public function width($width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @param string $filter
     *
     * @return TD
     */
    public function filter(string $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @param bool $sort
     *
     * @return TD
     */
    public function sort(bool $sort = true): self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @param string $align
     *
     * @return $this
     */
    public function align(string $align): self
    {
        $this->align = $align;

        return $this;
    }

    /**
     * @param int $colspan
     *
     * @return $this
     */
    public function colspan(int $colspan): self
    {
        $this->colspan = $colspan;

        return $this;
    }

    /**
     * Builds a column heading.
     *
     * @return Factory|View
     */
    public function buildTh()
    {
        return view('platform::partials.layouts.th', [
            'width'        => $this->width,
            'align'        => $this->align,
            'sort'         => $this->sort,
            'sortUrl'      => $this->buildSortUrl(),
            'column'       => $this->column,
            'title'        => $this->title,
            'filter'       => $this->filter,
            'filterString' => get_filter_string($this->column),
            'slug'         => $this->sluggable(),
            'popover'      => $this->popover,
        ]);
    }

    /**
     * Builds content for the column.
     *
     * @param Repository|Model $repository
     *
     * @return Factory|View
     */
    public function buildTd($repository)
    {
        $value = $this->render
            ? $this->handler($repository)
            : $repository->getContent($this->name);

        return view('platform::partials.layouts.td', [
            'align'   => $this->align,
            'value'   => $value,
            'render'  => $this->render,
            'slug'    => $this->sluggable(),
            'width'   => $this->width,
            'colspan' => $this->colspan,
        ]);
    }

    /**
     * @return bool
     */
    public function isAllowUserHidden(): bool
    {
        return $this->allowUserHidden;
    }

    /**
     * Builds item menu for show/hiden column.
     *
     * @return Factory|View|null
     */
    public function buildItemMenu()
    {
        if (! $this->isAllowUserHidden()) {
            return;
        }

        return view('platform::partials.layouts.selectedTd', [
            'title'         => $this->title,
            'slug'          => $this->sluggable(),
            'defaultHidden' => var_export($this->defaultHidden, true),
        ]);
    }

    /**
     * @return string
     */
    private function sluggable(): string
    {
        return Str::slug($this->name);
    }

    /**
     * Prevents the user from hiding a column in the interface.
     *
     * @param bool $hidden
     *
     * @return TD
     */
    public function cantHide($hidden = false): self
    {
        $this->allowUserHidden = $hidden;

        return $this;
    }

    /**
     * @param bool $hidden
     *
     * @return $this
     */
    public function defaultHidden($hidden = true): self
    {
        $this->defaultHidden = $hidden;

        return $this;
    }

    /**
     * @return string
     */
    public function buildSortUrl(): string
    {
        $query = request()->query();
        $query['sort'] = revert_sort($this->column);

        return url()->current().'?'.http_build_query($query);
    }

    /**
     * @param TD[] $columns
     *
     * @return bool
     */
    public static function isShowVisibleColumns($columns): bool
    {
        return collect($columns)->filter(function ($column) {
            return $column->isAllowUserHidden();
        })->isNotEmpty();
    }
}
