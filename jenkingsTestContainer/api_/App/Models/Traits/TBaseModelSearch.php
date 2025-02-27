<?php

namespace App\Models\Traits;
trait TBaseModelSearch
{
    protected function equals(string $query, string $column, $value = null): string
    {
        $query .= ' WHERE ' . $column;
        $query .= is_array($value)
            ? ' IN ("' . implode('","', $value) . '")'
            : ' ="' . $value . '"';
        return $query;
    }

    protected function wildcard(string $query, $wildCardOptions, $value = null): string
    {
        $query .= (!is_null($value)) ? ' AND' : ' WHERE';

        $wildcards = [];
        foreach (explode(',', $wildCardOptions['columns']) as $column) {
            foreach (explode(' ', $wildCardOptions['needle']) as $needle) {
                $wildcards[] = ' `' . $column . '` LIKE "%' . $needle . '%"';
            }
        }
        return $query . '( ' . implode(' OR ', $wildcards) . ')';
    }

    protected function filter(string $query, $options, $value = null): string
    {
        $query .= (!is_null($value)) ? ' AND' : ' WHERE';

        $filters = [];
        foreach (explode(',', $options['columns']) as $column) {
            foreach (explode(' ', $options['needle']) as $needle) {
                $operator = $options['operators'][$column] ?? '=';
                $filters[] = $needle != 'null'
                    ? ' `' . $column . '` ' . $operator . ' "' . $needle . '" '
                    : ' `' . $column . '` ' . $this->filterByNull($operator);
            }
        }
        return $query . '( ' . implode(' OR ', $filters) . ')';
    }

    private function filterByNull(string $operator): string
    {
        return $operator == '=' ? 'is null' : ' in not null';
    }

    protected function orderBy(string $query, array $orderBy): string
    {
        $orderBys = [];
        foreach ($orderBy as $orderColumn => $direction) {
            $orderBys[] = $orderColumn . ' ' . $direction;
        }
        $query .= ' ORDER BY ' . implode(',', $orderBys);
        return $query;
    }

    protected function with(array $with, $items): array
    {
        foreach ($items as &$item) {
            if ($with) {
                foreach ($with as $option) {
                    $itemOptions = $this->$option($item['id']);
                    if (!empty($itemOptions)) {
                        $item[$option] = $itemOptions;
                    }
                }
            }
        }
        return $items;
    }

    protected function paginate(array $items, array $pagination): array
    {
        $currenPage = $pagination['current_page'] ?? 0;
        $pageSize = $pagination['page_size'] ?? 15;
        return [
            'currenPage' => $currenPage,
            'pageSize' => $pageSize,
            'items' => array_slice($items, $pageSize * $currenPage, $pageSize),
        ];
    }

    protected function paginationResult(
        array $items,
        int   $totalCount,
        int   $pageSize,
        int   $currenPage
    ): array
    {
        return [
            'total' => $totalCount,
            'page' => $currenPage,
            'page_size' => $pageSize,
            'number_of_pages' => ceil($totalCount / $pageSize),
            'items_count' => count($items),
            'items' => $items,
        ];
    }
}
