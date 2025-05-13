<?php

/*
 * Model查询处理
 */

namespace Laravel\Crbac\Services;

use Closure,
    Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model as Eloquent;

class ModelSelect {

    protected $modelClass; //要修改的Model类名
    protected $input; //要修改的请求数据
    protected $builder; //Illuminate\Database\Eloquent\Builder
    protected $orderKey = 'order'; //排序键名
    protected $byKey = 'by'; //排序类型键名

    /**
     * 初始化
     * @param Eloquent $model
     * @param array $input
     * @param array $default
     * @throws Exception
     */
    public function __construct($model, array $input = [], array $default = []) {
        if (is_string($model)) {
            $this->modelClass = $model;
        } elseif ($model instanceof Eloquent) {
            $this->modelClass = get_class($model);
        } else {
            throw new Exception('程序异常！');
        }
        foreach ($default as $key => $val) {
            Request::input($key) !== null || Request::merge([$key => $val]);
        }
        $this->input = $input ?: Request::all();
        $this->builder = call_user_func($this->modelClass . '::query');
    }

    /**
     * 调用 Builder 方法
     * @param string $method
     * @param array $parameters
     * @return $this
     */
    public function __call($method, $parameters) {
        call_user_func_array([$this->builder, $method], $parameters);
        return $this;
    }

    /**
     * 添加where规则处理
     * @param array $where
     * @return $this
     */
    public function where(array $where) {
        return $this->rule($where, function ($field, $operator, $val) {
                    if ($operator == 'not in') {//in查询处理
                        $this->builder->whereNotIn($field, $val);
                    } elseif ($operator == 'in') {//in查询处理
                        $this->builder->whereIn($field, $val);
                    } else {
                        $this->builder->where($field, $operator, $val);
                    }
                });
    }

    /**
     * 添加having规则处理
     * @param array $having
     * @return $this
     */
    public function having(array $having) {
        return $this->rule($having, function ($field, $operator, $val) {
                    $this->builder->having($field, $operator, $val);
                });
    }

    /**
     * 添加order规则处理
     * @param array $orderBy
     * @return $this
     */
    public function order(array $orderBy) {
        $name = array_get($this->input, $this->orderKey);
        if ($name && $field = array_get($orderBy, $name)) {//存在排序字段名
            $by = array_get($this->input, $this->byKey); //取出排序值
            if (!in_array($by, ['desc', 'asc'], true)) {
                $by = 'desc';
                $this->input = array_merge($this->input, [$this->byKey => $by]);
            }
            $this->builder->orderBy($field, $by);
        }
        return $this;
    }

    /**
     * 设置排序键名
     * @param string $orderKey
     * @param string $byKey
     */
    public function setOrderKey(string $orderKey = 'order', string $byKey = 'by') {
        $this->orderKey = $orderKey;
        $this->byKey = $byKey;
    }

    /**
     * 获取排序串
     * @param string $descClassName
     * @param string $ascClassName
     * @param string $defaultClassName
     * @return Closure
     */
    public function orderToString($descClassName = 'order-desc', $ascClassName = 'order-asc', $defaultClassName = 'order-by') {
        $input = $this->input;
        array_forget($input, [$this->orderKey, $this->byKey]);
        $query = http_build_query($input);
        return function ($name, $getUrl = true, $defaultBy = 'asc') use ($query, $descClassName, $ascClassName, $defaultClassName) {
            $by = null;
            if (isset($this->input[$this->orderKey]) && $this->input[$this->orderKey] === $name && isset($this->input[$this->byKey])) {
                $by = $this->input[$this->byKey];
            }
            if (!$getUrl) {
                return is_null($by) ? $defaultClassName : $defaultClassName . ' ' . ${$by . 'ClassName'};
            }
            if (!in_array($by, ['desc', 'asc'], true)) {
                $by = $defaultBy;
            }
            return Request::url() . ($query ? '?' . $query . '&' : '?') . http_build_query([$this->orderKey => $name, $this->byKey => $by == 'asc' ? 'desc' : 'asc']);
        };
    }

    /**
     * 规则处理
     * @param array $rules
     * @param Closure $callback
     * @return $this
     */
    protected function rule(array $rules, Closure $callback) {
        foreach ($rules as $field => $rule) {
            if (is_string($rule)) {
                if (is_int($field)) {//直接=处理
                    $field = $rule;
                    $rule = null;
                } else {
                    $rule = [1 => $rule];
                }
            }
            $val = array_get($this->input, $field);
            if (empty($val)) {
                continue;
            }
            if (is_callable($rule)) {//加高处理
                $rule($this->builder, $val);
                continue;
            }
            if (isset($rule[0])) {//字段名
                $field = $rule[0];
            }
            if (isset($rule[1])) {//条件
                $operator = $rule[1];
            } else {
                $operator = '=';
            }
            if (isset($rule[2]) && is_callable($rule[2])) {//回调值处理
                $val = $rule[2]($val);
            }
            if ($operator == 'like') {//like查询处理
                $val = '%' . $val . '%';
            }
            $callback($field, $operator, $val);
        }
        return $this;
    }

    /**
     * 修改数据
     * @param Closure $callback
     * @param int $perPage
     * @return \Illuminate\Pagination\AbstractPaginator
     */
    public function lists(Closure $callback = null, $perPage = 20) {
        if ($callback) {
            $callback($this->builder, $this);
        }
        if (is_null($perPage)) {
            return $this->builder->get();
        }
        if ($perPage < 1) {
            $perPage = $this->builder->getModel()->getPerPage();
        }
        if ($this->builder->getQuery()->groups) {
            $BuilderPage = clone $this->builder;
            $BuilderPage->getQuery()->orders = null; //去掉无意义的排序
            $total = \DB::Connection($this->builder->getModel()->getConnectionName())
                            ->selectOne('select count(1) as num from (' . $BuilderPage->toSql() . ') as t', $BuilderPage->getBindings())->num; //取出总记录数
            //兼容老板框架
            if (method_exists($this->builder->getQuery()->getConnection(), 'getPaginator')) {
                $paginator = $this->builder->getQuery()->getConnection()->getPaginator();
                $page = $paginator->getCurrentPage($total);
            } else {
                $page = Paginator::resolveCurrentPage();
            }
            $results = $this->builder->forPage($page, $perPage)->get();
            //兼容老板框架
            if (isset($paginator)) {
                $lists = $paginator->make($results, $total, $perPage);
            } else {
                $lists = new LengthAwarePaginator($results, $total, $perPage, $page, [
                    'path' => Paginator::resolveCurrentPath(),
                    'pageName' => 'page',
                ]);
            }
        } else {
            $lists = $this->builder->paginate($perPage);
        }
        return $lists->appends(Request::all());
    }

}
