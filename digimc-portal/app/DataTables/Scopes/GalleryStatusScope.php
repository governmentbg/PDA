<?php

namespace App\DataTables\Scopes;

use Yajra\DataTables\Contracts\DataTableScope;

class GalleryStatusScope implements DataTableScope
{
    /**
     * @var string $value
     */
    public $value;

    /**
     * Apply a query scope.
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function apply($query)
    {
        return $query->where('status', $this->value);
    }

    /**
     * @param string $value
     */
    public function setFilter($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function name()
    {
        return GalleryStatusScope::class;
    }
}
