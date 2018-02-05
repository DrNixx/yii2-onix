<?php
namespace onix\data;

use Yii;
use yii\db\Connection;

class ActiveQueryResult
{
    /**
     * @var ActiveQueryEx
     */
    private $query;

    /**
     * @var Connection
     */
    private $db;

    private $stringOperators = array(
        'eq' => 'LIKE',
        'neq' => 'NOT LIKE',
        'doesnotcontain' => 'NOT LIKE',
        'contains' => 'LIKE',
        'startswith' => 'LIKE',
        'endswith' => 'LIKE'
    );

    private $operators = array(
        'eq' => '=',
        'gt' => '>',
        'gte' => '>=',
        'lt' => '<',
        'lte' => '<=',
        'neq' => '!='
    );

    private $aggregateFunctions = array(
        'average' => 'AVG',
        'min' => 'MIN',
        'max' => 'MAX',
        'count' => 'COUNT',
        'sum' => 'SUM'
    );

    /**
     * @param ActiveQueryEx $query
     * @param Connection|null $db
     */
    public function __construct(ActiveQueryEx $query, $db = null)
    {
        if ($db !== null) {
            $this->db = $db;
        } else {
            $this->db = Yii::$app->db;
        }

        $this->query = $query;
    }

    /**
     * @param $where
     * @return int
     */
    private function total($where = null)
    {
        if (!empty($where)) {
            return (int) $this->query->andWhere($where)->count();
        } else {
            return (int) $this->query->count();
        }
    }

    private function group($data, $groups, $request, $properties)
    {
        if (count($data) > 0) {
            return $this->groupBy($data, $groups, $request, $properties);
        }
        return array();
    }

    private function mergeSortDescriptors($request)
    {
        $sort = isset($request->sort) && count($request->sort) ? $request->sort : array();
        $groups = isset($request->group) && count($request->group) ? $request->group : array();

        return array_merge($sort, $groups);
    }

    private function groupBy($data, $groups, $request, $properties)
    {
        if (count($groups) > 0) {
            $field = $groups[0]->field;
            $count = count($data);
            $result = array();
            $value = $data[0][$field];
            $aggregates = isset($groups[0]->aggregates) ? $groups[0]->aggregates : array();

            $hasSubgroups = count($groups) > 1;
            $groupItem = $this->createGroup($field, $value, $hasSubgroups, $aggregates, $request, $properties);

            for ($index = 0; $index < $count; $index++) {
                $item = $data[$index];
                if ($item[$field] != $value) {
                    if (count($groups) > 1) {
                        $groupItem["items"] = $this->groupBy(
                            $groupItem["items"],
                            array_slice($groups, 1),
                            $request,
                            $properties
                        );
                    }

                    $result[] = $groupItem;

                    $groupItem = $this->createGroup(
                        $field,
                        $data[$index][$field],
                        $hasSubgroups,
                        $aggregates,
                        $request,
                        $properties
                    );
                    $value = $item[$field];
                }
                $groupItem["items"][] = $item;
            }

            if (count($groups) > 1) {
                $groupItem["items"] = $this->groupBy(
                    $groupItem["items"],
                    array_slice($groups, 1),
                    $request,
                    $properties
                );
            }

            $result[] = $groupItem;

            return $result;
        }
        return array();
    }

    private function addFilterToRequest($field, $value, $request)
    {
        $filter = (object)array(
            'logic' => 'and',
            'filters' => array(
                (object)array(
                    'field' => $field,
                    'operator' => 'eq',
                    'value' => $value
                ))
        );

        if (isset($request->filter)) {
            $filter->filters[] = $request->filter;
        }

        return (object) array('filter' => $filter);
    }

    private function addFieldToProperties($field, $properties)
    {
        if (!in_array($field, $properties)) {
            $properties[] = $field;
        }
        return $properties;
    }

    private function createGroup($field, $value, $hasSubgroups, $aggregates, $request, $properties)
    {
        if (count($aggregates) > 0) {
            $request = $this->addFilterToRequest($field, $value, $request);
            $properties = $this->addFieldToProperties($field, $properties);
        }

        $groupItem = array(
            'field' => $field,
            'aggregates' => $this->calculateAggregates($aggregates, $request, $properties),
            'hasSubgroups' => $hasSubgroups,
            'value' => $value,
            'items' => array()
        );

        return $groupItem;
    }

    private function calculateAggregates($aggregates, $request, $properties)
    {
        $count = count($aggregates);

        if (count($aggregates) > 0) {
            $functions = array();

            for ($index = 0; $index < $count; $index++) {
                $aggregate = $aggregates[$index];
                $name = $this->aggregateFunctions[$aggregate->aggregate];
                $functions[] = $name.'('.$aggregate->field.') as '.$aggregate->field.'_'.$aggregate->aggregate;
            }

            $sql = sprintf('SELECT %s FROM %s', implode(', ', $functions));

            if (isset($request->filter)) {
                $sql .= $this->filter($properties, $request->filter);
            }

            $statement = $this->db->prepare($sql);

            if (isset($request->filter)) {
                $this->bindFilterValues($statement, $request->filter);
            }

            $statement->execute();

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $this->convertAggregateResult($result[0]);
        }
        return (object)array();
    }

    private function convertAggregateResult($properties)
    {
        $result = array();

        foreach ($properties as $property => $value) {
            $item = array();
            $split = explode('_', $property);
            $field = $split[0];
            $function = $split[1];
            if (array_key_exists($field, $result)) {
                $result[$field][$function] = $value;
            } else {
                $result[$field] = array($function => $value);
            }
        }

        return $result;
    }

    private function where($properties, $filter, $all)
    {
        if (isset($filter->filters)) {
            $logic = ' AND ';

            if ($filter->logic == 'or') {
                $logic = ' OR ';
            }

            $filters = $filter->filters;

            $where = array();

            for ($index = 0; $index < count($filters); $index++) {
                $where[] = $this->where($properties, $filters[$index], $all);
            }

            $where = implode($logic, $where);

            return "($where)";
        }

        $field = $filter->field;

        if (!empty($properties[$field])) {
            $db = Yii::$app->getDb();
            $field = $db->quoteColumnName($properties[$field]);
            $index = array_search($filter, $all);

            $value = ":filter$index";

            //if ($this->isDate($filter->value)) {
            //    $field = "date($field)";
            //    $value = "date($value)";
            //}

            if ($this->isString($filter->value)) {
                $operator = $this->stringOperators[$filter->operator];
            } else {
                $operator = $this->operators[$filter->operator];
            }

            return "$field $operator $value";
        }

        return "";
    }

    private function flatten(&$all, $filter)
    {
        if (isset($filter->filters)) {
            $filters = $filter->filters;
            for ($index = 0; $index < count($filters); $index++) {
                $this->flatten($all, $filters[$index]);
            }
        } else {
            $all[] = $filter;
        }
    }

    private function filter($properties, $filter)
    {
        $all = array();
        $this->flatten($all, $filter);
        $where = $this->where($properties, $filter, $all);
        return "$where";
    }

    private function isDate($value)
    {
        if (strlen($value) > 6) {
            $result = date_parse($value);
            return $result["error_count"] < 1;
        } else {
            return false;
        }
    }

    private function isString($value)
    {
        return !is_bool($value) && !is_numeric($value) && !$this->isDate($value);
    }

    private function bindFilterValues($filter)
    {
        $params = array();
        $filters = array();
        $this->flatten($filters, $filter);

        for ($index = 0; $index < count($filters); $index++) {
            $value = $this->query->formatImportValue($filters[$index]->field, $filters[$index]->value);
            $operator = $filters[$index]->operator;


            if ($operator == 'contains' || $operator == 'doesnotcontain') {
                $value = "%$value%";
            } elseif ($operator == 'startswith') {
                $value = "$value%";
            } elseif ($operator == 'endswith') {
                $value = "%$value";
            }

            $params[":filter$index"] = $value;
        }

        $this->query->addParams($params);
    }

    public function create($table, $properties, $models, $key)
    {
        $result = array();
        $data = array();

        if (!is_array($models)) {
            $models = array($models);
        }

        $errors = array();

        foreach ($models as $model) {
            $columns = array();
            $values = array();
            $input_parameters = array();

            foreach ($properties as $property) {
                if ($property != $key) {
                    $columns[] = $property;
                    $values[] = '?';
                    $input_parameters[] = $model->$property;
                }
            }

            $columns = implode(', ', $columns);
            $values = implode(', ', $values);

            $sql = "INSERT INTO $table ($columns) VALUES ($values)";

            $statement = $this->db->prepare($sql);

            $statement->execute($input_parameters);

            $status = $statement->errorInfo();

            if ($status[1] > 0) {
                $errors[] = $status[2];
            } else {
                $model->$key = $this->db->lastInsertId();
                $data[] = $model;
            }
        }

        if (count($errors) > 0) {
            $result['errors'] = $errors;
        } else {
            $result['data'] = $data;
        }

        return $result;
    }

    public function destroy($table, $models, $key)
    {
        $result = array();

        if (!is_array($models)) {
            $models = [$models];
        }

        $errors = array();

        foreach ($models as $model) {
            $sql = "DELETE FROM $table WHERE $key=?";

            $statement = $this->db->prepare($sql);

            $statement->execute(array($model->$key));

            $status = $statement->errorInfo();

            if ($status[1] > 0) {
                $errors[] = $status[2];
            }
        }

        if (count($errors) > 0) {
            $result['errors'] = $errors;
        }

        return $result;
    }

    public function update($table, $properties, $models, $key)
    {
        $result = array();

        if (in_array($key, $properties)) {
            if (!is_array($models)) {
                $models = array($models);
            }

            $errors = array();

            foreach ($models as $model) {
                $set = array();

                $input_parameters = array();

                foreach ($properties as $property) {
                    if ($property != $key) {
                        $set[] = "$property=?";
                        $input_parameters[] = $model->$property;
                    }
                }

                $input_parameters[] = $model->$key;

                $set = implode(', ', $set);

                $sql = "UPDATE $table SET $set WHERE $key=?";

                $statement = $this->db->prepare($sql);

                $statement->execute($input_parameters);

                $status = $statement->errorInfo();

                if ($status[1] > 0) {
                    $errors[] = $status[2];
                }
            }

            if (count($errors) > 0) {
                $result['errors'] = $errors;
            }
        }

        if (count($result) == 0) {
            $result = "";
        }

        return $result;
    }

    /**
     * @param $properties
     * @param null $request
     * @param bool $strip
     *
     * @return array
     *
     * @throws \yii\db\Exception
     */
    public function read($properties, $request = null, $strip = false)
    {
        $defaults = $this->query->getDefaultScope();

        $where = "";
        if (isset($request->filter)) {
            $where = $this->filter($properties, $request->filter);
            $this->bindFilterValues($request->filter);
        }

        $result = [];
        $result['total'] = $this->total($where);

        $select = [];
        foreach ($properties as $key => $value) {
            $select[] = "$value as $key";
        }

        $this->query = $this->query->select($select);

        $sort = $this->mergeSortDescriptors($request);

        if (count($sort) > 0) {
            $orders = [];
            foreach ($sort as $s) {
                if (!empty($properties[$s->field])) {
                    $orders[$s->field] = ($s->dir == 'desc') ? SORT_DESC : SORT_ASC;
                }
            }

            $this->query = $this->query->orderBy($orders);
        } elseif (isset($defaults['order'])) {
            $this->query = $this->query->orderBy($defaults['order']);
        }

        if (isset($request->skip)) {
            $this->query = $this->query->offset($request->skip);
        }

        if (isset($request->take)) {
            $this->query = $this->query->limit($request->take);
        } else {
            if (isset($defaults['limit'])) {
                $this->query = $this->query->limit($defaults['limit']);
            } else {
                if (defined("YII_DEBUG")) {
                    $this->query = $this->query->limit(500);
                }
            }
        }

        $data = $this->query->createCommand($this->db)->queryAll();

        for ($i = 0; $i < count($data); $i++) {
            foreach ($data[$i] as $name => $value) {
                $data[$i][$name] = $this->query->formatExportValue($name, $value, $strip);
            }
        }

        if (isset($request->group) && count($request->group) > 0) {
            $data = $this->group($data, $request->group, $request, $properties);
            $result['groups'] = $data;
        } else {
            $result['data'] = $data;
        }

        if (isset($request->aggregate)) {
            $result["aggregates"] = $this->calculateAggregates($request->aggregate, $request, $properties);
        }

        return $result;
    }
}
