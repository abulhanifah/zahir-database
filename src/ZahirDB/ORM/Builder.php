<?php

namespace ZahirDB\ORM;

use Illuminate\Database\Eloquent\Builder as BaseBuilder;

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

    }

    public function lookupMap($request=[]) {

    }

    public function multiMap($request=[]) {
        
    }

    public function paginatedMap($request=[]) {
        
    }

    public function countMap($request=[]) {

    }

    public function getPagination($count, $data, $request)
    {
        $params = $this->getParams();

        $first = $this->cleanQueryUrl($request->all());
        unset($first['page']);
        unset($first['per_page']);
        $first = array_merge($first, ['page' => 1, 'per_page' => $params['per_page']]);

        $previous = $this->cleanQueryUrl($request->all());
        unset($previous['page']);
        unset($previous['per_page']);
        $previous = array_merge($previous, ['page' => (int)$params['page']-1, 'per_page' => $params['per_page']]);

        $next = $this->cleanQueryUrl($request->all());
        unset($next['page']);
        unset($next['per_page']);
        $next = array_merge($next, ['page' => (int)$params['page']+1, 'per_page' => $params['per_page']]);

        $last = $this->cleanQueryUrl($request->all());
        unset($last['page']);
        unset($last['per_page']);
        $last = array_merge($last, ['page' => ceil($count/$params['per_page']), 'per_page' => $params['per_page']]);

        $return = [
            'count' => $count,
            'page_context' => [
                'page' => $params['page'],
                'per_page' => $params['per_page'],
                'total_pages' => ceil($count/$params['per_page']),
            ],
            'links' => [
                'first' => $request->url() . '?' . urldecode(http_build_query($first)),
                'previous' => $params['page']==1 ? null : $request->url() . '?' . urldecode(http_build_query($previous)),
                'next' => $params['page']==ceil($count/$params['per_page']) ? null : $request->url() . '?' . urldecode(http_build_query($next)),
                'last' => $request->url() . '?' . urldecode(http_build_query($last)),
            ],
            'results' => $data,
        ];

        return $return;
    }

    public static function cleanQueryUrl($param)
    {
        foreach ($param as $key => $value) {
            if(gettype($key) == "integer"){
                unset($param[$key]);
            }
        }
        return $param;
    }
}
