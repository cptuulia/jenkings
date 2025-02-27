<?php

namespace App\Models;
/**
 * Base model to include the common functionalities for all services
 */

use App\Enums\EMatchType;
use App\Lib\DbConnection as Connection;
use App\Models\Traits\TBaseModelSearch;
use App\Models\Traits\TBaseModelHelpers;
use App\Traits\TString;


abstract class BaseModel
{
    use TBaseModelSearch;
    use TBaseModelHelpers;
    use TString;


    /**
     * @var string
     */
    protected $table;

    /** var Db */
    protected $db;


    public function __construct()
    {
        $this->db = Connection::getConnection();
    }


    public function realEscape(string $string): string
    {
        $c = $this->db->getConnection();
        $d=  $c->prepare($string);
        return $c->prepare($string);

    }

    /**
     * Get records by value
     *
     * As a default we search by id and equal operator.
     *
     * param null,string|array $value Value to filter if the value we ise oprator 'IN' insetead of '='
     *                                 If value = null , select all
     * param array $options 'column'    to filter (default id)
     *
     *                                  'with'      relations
     *
     *                                  'paginate'  if set use pagination
     *                                  'paginate' => [
     *                                      'current_page'   // optional
     *                                      'page_size'     // optional, defaults 15
     *                                  ]
     *
     *                                  'wildcard' wildcard search
     *                                  'wildcard' => [
     *                                      'needle' => 'whatever'   // required
     *                                      'columns' =>  'column1,columns2' //required
     *                                    ]
     *                                  'filter' filter by equals search
     *                                  'filter' => [
     *                                      'needle' => 'whatever1,whatever2,'   // required
     *                                      'columns' =>  'column1,column2' //required
     *                                      'operators' => ['column2' => '>']
     *                                    ]
     *
     *                                  'order' order by
     *                                  'order' => [
     *                                      ['name' => 'asc'
     *                                      'tag' => 'desc'
     *                                  ]
     * @return array
     */
    public function get($value = null, array $options = []): array
    {
        $column = $options['column'] ?? 'id';
        $query = 'SELECT * FROM ' . $this->table;
        if (!is_null($value)) {
            $query = $this->equals($query, $column, $value);
        }

        if (isset($options['wildcard'])) {
            $query = $this->wildcard($query, $options['wildcard'], $value);
        }

        if (isset($options['filter'])) {
            $query = $this->filter($query, $options['filter'], $value);
        }

        if (isset($options['order'])) {
            $query = $this->orderBy($query, $options['order']);
        }

        $items = $this->db->executeSelectQuery($query);;
        $totalCount = count($items);
        if (isset($options['paginate'])) {
            list(
                'currenPage' => $currenPage,
                'pageSize' => $pageSize,
                'items' => $items) = $this->paginate($items, $options['paginate']);
        } else {
            $pageSize = 0;
            $currenPage = 0;
        }

        if (isset($options['with'])) {
            $items = $this->with($options['with'], $items);
        }

        return isset($options['paginate'])
            ? $this->paginationResult($items, $totalCount, $pageSize, $currenPage)
            : $items;
    }

    /**
     * Insert a row
     *
     * @param array $columns columns to insert, if this array has columns, which are not properties of the model
     *                        they are filtered out
     * @param $filterColumns  if true, only items defined in the model properties are inserted
     * @return int|null
     */
    public function insert(array $columns, bool $filterColumns = true): ?int
    {
        foreach (['created_at', 'updated_at'] as $timestamp) {
            if (property_exists(get_class($this), $this->snakeToCamel($timestamp))) {
                $columns[$timestamp] = date("Y-m-d H:i:s");;
            }
        }

        $columnsToInsert = ($filterColumns) ? $this->filterColumns($columns) : $columns;

        if (empty($columnsToInsert)) {
            return null;
        }

        $columnNames = '`' . implode('`,`', array_keys($columnsToInsert)) . '`';
        $values = implode(
            ',',
            array_fill(0, count($columnsToInsert), '?')
        );

        $query = 'INSERT INTO ' . $this->table .
            ' (' . $columnNames . ') ' .
            ' VALUES(' . $values . ')';

        $this->db->executeQuery($query, array_values($columnsToInsert));
        return $this->db->getLastInsertedId();
    }

    /**
     * Insert a row
     *
     * @param array $columns columns to insert, if this array has columns, which are not properties of the model
     *                        they are filtered out
     * @return int|null
     */
    public function update(array $columns, $primaryKey = 'id', $filterColumns = true): ?int
    {
        if (array_key_exists('created_at', $columns)) {
            unset ($columns['created_at']);
        }

        $timestamp = 'updated_at';
        if (property_exists(get_class($this), $this->snakeToCamel($timestamp))) {
            $columns[$timestamp] = date("Y-m-d H:i:s");
        }

        $columnsToUpdate = ($filterColumns) ? $this->filterColumns($columns) : $columns;

        if (empty($columnsToUpdate)) {
            return null;
        }

        $sets = [];
        foreach (array_keys($columnsToUpdate) as $key) {
            $sets[] = "`" . $key. "`" . ' = ?';
        }
        $query = 'UPDATE  ' . $this->table .
            ' SET ' . implode(',', $sets) .
            ' WHERE ' . $primaryKey . ' = "' . $columns[$primaryKey] . '"';

        $this->db->executeQuery($query, array_values($columnsToUpdate));
        return $this->db->getLastInsertedId();
    }

    /**
     * Filter columns, which are properties of the current class
     *
     * @param array $columns
     * @return array
     */
    private function filterColumns(array $columns): array
    {
        $propertyColumns = [];
        foreach ($columns as $key => $value) {
            $dbColumn = $this->snakeToCamel($key);
            if (property_exists($this, $dbColumn)) {
                $propertyColumns[$key] = $value;
            }
        }
        return $propertyColumns;
    }

    /**
     * Delete row
     *
     * @param string|array $value value to filter
     * @param string $column column to filter (default is id)
     * @return void
     */
    public function delete($value, string $column = 'id'): void
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE ' . $column;
        $query .= is_string($value) ? ' = ?' : ' IN (?)';
        $value = is_array($value) ? implode(',', $value) : $value;
        $this->db->executeQuery($query, [$value]);
    }


    /**
     * @param array $options 'orderBy' array of order bys
     *                          example:
     *                              'orderBy' =>
     *                                  [
     *                                      ['field' => 'name' , 'dir' => 'asc'],
     *                                      ['field' => 'value'],
     *
     *                                  ]
     * @return array
     */
    protected function oneToMany(int $id, string $foreignKey, BaseModel $model, array $options = []): array
    {
        $query = 'SELECT * FROM ' . $model->table .
            ' WHERE ' . $foreignKey . '=' . $id;

        if (isset($options['orderBy'])) {

            $orderBys = [];
            foreach ($options['orderBy'] as $orderBy) {
                $orderBys[] = $orderBy['field'] . ' ' . $orderBy['dir'] ?? '';
            }
            $query .= ' ORDER BY ' . implode($orderBys);
        }

        return $this->db->executeSelectQuery($query);

    }

    /**
     * Check that the ordering is correct
     */
    public function updateOrdering(string $value = null, string $filterColumn = null): void
    {
        $params = [
            'order' => [
                'ordering' => 'asc'
            ],
            'filter' => [
                'needle' => '0',
                'columns' => 'ordering',
                'operators' => ['ordering' => '>=']
            ]
        ];
        if ($filterColumn) {
            $params['column'] = $filterColumn;
        }
        $items = $this->get($value, $params);

        $ordering = 1;
        foreach ($items as $item) {
            $item['ordering'] = $ordering;
            $this->update($item);
            $ordering++;
        }
    }

}