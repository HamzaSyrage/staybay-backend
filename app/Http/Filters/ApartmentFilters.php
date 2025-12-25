<?php

namespace App\Http\Filters;

use Illuminate\Http\Request;

class ApartmentFilters
{
    protected $request;
    protected $builder;

    protected $filters = [
        'search',
        'governorate_id',
        'city_id',
        'price_min',
        'price_max',
        'bathrooms',
        'bedrooms',
        'has_pool',
        'has_wifi',
        'size_min',
        'size_max',
        'ratting_min',
        'ratting_max',
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($builder)
    {
        $this->builder = $builder;

        foreach ($this->filters as $filter) {
            if (method_exists($this, $filter) && $this->request->filled($filter)) {
                $this->$filter($this->request->get($filter));
            }
        }

        return $this->builder;
    }

    public function search($value)
    {
        $this->builder->where(function ($q) use ($value) {
            $q->where('title', 'like', "%$value%")
                ->orWhere('description', 'like', "%$value%");
        });
    }

    public function governorate_id($value)
    {
        $this->builder->whereHas('city.governorate', function ($q) use ($value) {
            $q->where('id', $value);
        });
    }

    public function city_id($value)
    {
        $this->builder->where('city_id', $value);
    }

    public function price_min($value)
    {
        $this->builder->where('price', '>=', $value);
    }

    public function price_max($value)
    {
        $this->builder->where('price', '<=', $value);
    }

    public function bathrooms($value)
    {
        $this->builder->where('bathrooms', $value);
    }

    public function bedrooms($value)
    {
        $this->builder->where('bedrooms', $value);
    }

    public function has_pool($value)
    {
        $this->builder->where('has_pool', $value);
    }

    public function has_wifi($value)
    {
        $this->builder->where('has_wifi', $value);
    }

    public function size_min($value)
    {
        $this->builder->where('size', '>=', $value);
    }

    public function size_max($value)
    {
        $this->builder->where('size', '<=', $value);
    }

    public function ratting_min($value)
    {
        $this->builder->where('rating', '>=', $value);
    }

    public function ratting_max($value)
    {
        $this->builder->where('rating', '<=', $value);
    }
}
