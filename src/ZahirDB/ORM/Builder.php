<?php

namespace ZahirDB\ORM;

use Illuminate\Database\Eloquent\Builder as BaseBuilder;
use ZahirDB\Exceptions\NotFoundException;

class Builder extends BaseBuilder {
    public static $pagination = [
        'default' => 10,
        'max' => 100,
    ];

    /**
     * All of the available clause operators.
     *
     * @var array
     */
    public static $operators = [
        '$ne' => '<>',
        '$gt' => '>',
        '$gte' => '>=',
        '$lte' => '<=',
        '$lt' => '<',
        '$like' => 'like',
        '$nlike' => 'not like',
        '$in' => 'in',
        '$nin' => 'not in',
        '$regexp' => 'regexp',
        '$nregexp' => 'not regexp',
    ];

    /**
     * Exclude for column filters.
     *
     * @var array
     */
    public static $except = [
        'fields',
        'sort',
        'page',
        'per_page',
        'or',
        'search',
        'count',
        'min',
        'max',
        'avg',
        'sum',
    ];

    public $params = [];

    /**
     * Specifies a set of params.
     *
     * @param  array $params
     */
    public function setParams($params=[])
    {
        $ret['fields'] = isset($params['fields']) ? $params['fields'] : [];
        $ret['sorts'] = isset($params['sort']) ? $params['sort'] : [];
        $ret['page'] = (isset($params['page']) && is_numeric($params['page'])) ? (int)$params['page'] : 1;
        $ret['per_page'] = (isset($params['per_page']) && is_numeric($params['per_page'])) ? (int)$params['per_page'] : get_class($this)::$pagination['default'];
        $filters = [];
        foreach ($params as $key => $value) {
            if (!in_array(strtolower($key), get_class($this)::$except)) {
                $filters[$key] = $value;
            }
        }
        $ret['filters'] = $filters;
        $ret['or'] = isset($params['or']) ? $params['or'] : [];
        $ret['search'] = isset($params['search']) ? $params['search'] : [];
        $aggregates = [];
        foreach ($params as $key => $value) {
            if (in_array(strtolower($key), ['count', 'min', 'max', 'avg', 'sum'])) {
                $aggregates[$key] = $value;
            }
        }
        $ret['aggregates'] = $aggregates;

        $this->params = $ret;
    }

    public function getParams() {
        return $this->params;
    }

    public function singleMap($request=[]) {
        $res = $this->multiMap($request);
        return ($res) ? $res[0] : [];
    }

    public function lookupMap($request=[]) {
        $res = $this->multiMap($request);
        if($res) {
            return $res[0];
        } else {
            throw new NotFoundException($this->params['filter']);
        }
    }

    public function paginatedMap($request=[]) {
        $res = $this->multiMap($request);
        $count = $this->countMap($request);
        return $this->getPagination($count,$res,$request);
    }

    public function multiMap($request=[]) {
        $this->setParams($request);
        $query = $this->query;
        $query = $this->getSubQuery($query);

    }

    public function countMap($request=[]) {
        $this->setParams($request);
        $query = $this->query;
        $query = $this->getSubQuery($query);
        return $this->getCountQuery($query);
    }

    protected function getSubQuery($query) {
        $query = $query->table($this->model->table." as ".$this->model->table_alias);

        if(isset($this->model->relations)) {
            foreach ($this->model->relations as $rel) {
                if($rel['type'] == 'inner'){
                    $this->getJoin($query,$rel);
                }
            }
        }
    }

    protected function getCountQuery($query) {
        $count = 0;

        if (count($query)>0) {
            $count_obj = $query->selectRaw('count(DISTINCT('.$this->model->maps['table']['primary_key'].')) as count')->first();
            $count = $count_obj->count;
        }

        return $count;
    }

    public function getPagination($count, $data, $request)
    {
        $params = $this->getParams();
        $request_data = is_array($request) ? $request : $request->all();
        $first = $previous = $next = $last = $this->cleanQueryUrl($request_data);
        $url = is_array($request) ? "" : $request->url();

        $first = array_merge($first, ['page' => 1, 'per_page' => $params['per_page']]);

        $previous = array_merge($previous, ['page' => (int)$params['page']-1, 'per_page' => $params['per_page']]);

        $next = array_merge($next, ['page' => (int)$params['page']+1, 'per_page' => $params['per_page']]);

        $last = array_merge($last, ['page' => ceil($count/$params['per_page']), 'per_page' => $params['per_page']]);

        $return = [
            'count' => $count,
            'page_context' => [
                'page' => $params['page'],
                'per_page' => $params['per_page'],
                'total_pages' => ceil($count/$params['per_page']),
            ],
            'links' => [
                'first' => $url . '?' . urldecode(http_build_query($first)),
                'previous' => $params['page']==1 ? null : $url . '?' . urldecode(http_build_query($previous)),
                'next' => $params['page']==ceil($count/$params['per_page']) ? null : $url . '?' . urldecode(http_build_query($next)),
                'last' => $url . '?' . urldecode(http_build_query($last)),
            ],
            'results' => $data,
        ];

        return $return;
    }

    public static function cleanQueryUrl($param)
    {
        foreach ($param as $key => $value) {
            if(gettype($key) == "integer" || $key == 'page' || $key == 'per_page'){
                unset($param[$key]);
            }
        }
        return $param;
    }
}
