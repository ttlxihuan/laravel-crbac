<?php

/*
 * Model编辑处理
 */

namespace XiHuan\Crbac\Services;

use Validator,
    Closure,
    Exception,
    Request,
    Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Database\Eloquent\Model as Eloquent;

class ModelEdit extends Service {
    /*
     * 作用：验证处理
     * 参数：$model string|Model 要修改的Model对象或类名
     *      $input array 要输入的参数，默认全部请求参数
     *      $option array 要验证的参数名，默认全部
     * 返回值：false|array
     */
    public function validation($model, array $input = [], array $option = []) {
        $modelClass = $this->getModelClass($model);
        $rules = $this->getRules($modelClass);
        if (count($option)) {//只修改指定参数
            $_input = array_only($input, $option);
        } else {
            $_input = array_only($input, array_keys($rules));
            $option = array_keys($rules);
        }
        $onlyRules = array_only($rules, $option);
        if ($model instanceof Eloquent && $model->getKey() > 0) {
            foreach ($onlyRules as $name => &$rule) {
                if (is_int(strpos($rule, 'unique:'))) {//唯一处理
                    $rule = preg_replace_callback('/unique:\w+(,\w+)?/is', function($matches)use($name, $model) {
                        return $matches[0] . (isset($matches[1]) ? '' : ',' . $name) . ',' . $model->getKey() . ',' . $model->getKeyName();
                    }, $rule);
                }
            }
        }
        $validator = Validator::make($input, $onlyRules, [], $this->getMessages($modelClass));
        if ($validator->passes()) {//通过处理
            return $_input;
        }
        $unifyMessages = $this->getUnifyMessages($modelClass);
        //示通过的处理异常参数
        foreach ($validator->errors()->toArray() as $field => $message) {
            $this->setError('validator.' . $field, isset($unifyMessages[$field]) ? [$unifyMessages[$field]] : $message);
        }
        return false;
    }
    /*
     * 作用：修改数据
     * 参数：$model Model|string 要修改的Model对象或类名
     *      $input array 要输入的参数，默认全部请求参数
     *      $option array 要修改的参数名，默认全部
     *      $before Closure|null 写入修改数据之前回调处理，用于额外添加修改数据
     *      $after Closure|null 写入修改数据之后回调处理，用于关联修改处理
     * 返回值：false|Model
     */
    public function edit($model, array $input = [], array $option = [], Closure $before = null, Closure $after = null) {
        $data = $this->validation($model, $input, $option);
        if ($data === false) {//验证失败
            return false;
        }
        if ($before && $before($data, $this, $model) === false) {
            return false;
        }
        //上传处理
        foreach ($data as &$item) {
            if ($item instanceof UploadedFile) {
                $item = $this->upload($item);
                if ($item === false) {//上传失败
                    return false;
                }
            }
        }
        if ($model instanceof Eloquent) {
            $model->update($data);
        } else {
            $model = $model::create($data);
        }
        if ($after && $after($model, $this) === false) {
            return false;
        }
        return $model;
    }
    /*
     * 作用：修改数据
     * 参数：$model Model|string 要修改的数据Model类名或Model对象
     *      $input array 要输入的参数，默认全部请求参数
     *      $option array 要修改的参数名，默认全部
     *      $before Closure|null 写入修改数据之前回调处理，用于额外添加修改数据
     *      $after Closure|null 写入修改数据之后回调处理，用于关联修改处理
     * 返回值：false|Model
     */
    public function requestEdit($model, array $option = [], Closure $before = null, Closure $after = null) {
        return $this->edit($model, Request::all(), $option, $before, $after);
    }
    /*
     * 作用：上传处理
     * 参数：$file Symfony\Component\HttpFoundation\File\UploadedFile 上传对象
     * 返回值：bool
     */
    public function upload(UploadedFile $file) {
        $directory = getcwd(); //获取上传文件基本目录
        $urlPath = DIRECTORY_SEPARATOR . date('Y/m/d/');
        do {
            $name = uniqid(md5(microtime())) . '.' . $file->guessExtension();
        } while (file_exists($directory . $urlPath . DIRECTORY_SEPARATOR . $name));
        if ($target = $file->move($directory . $urlPath, $name)) {
            return str_replace('\\', '/', Request::getSchemeAndHttpHost() . $urlPath . $target->getBasename());
        }
        return $this->setError('upload', $file->getClientOriginalName() . ' 文件上传失败');
    }
    /*
     * 作用：获取数据验证规则
     * 参数：$modelClass string Model类名
     * 返回值：array
     */
    protected function getRules($modelClass) {
        return $this->getModelConstAttribute($modelClass, '_validator_rules');
    }
    /*
     * 作用：获取验证异常说明
     * 参数：$modelClass string Model类名
     * 返回值：array
     */
    protected function getMessages($modelClass) {
        return $this->getModelConstAttribute($modelClass, '_validator_description');
    }
    /*
     * 作用：获取统一验证说明（不计较是哪个验证条件失败，统一说明）
     * 参数：$modelClass string Model类名
     * 返回值：array
     */
    protected function getUnifyMessages($modelClass) {
        return $this->getModelConstAttribute($modelClass, '_validator_messages');
    }
    /*
     * 作用：获取Model的静态属性
     * 参数：$modelClass string Model类名
     *      $attribute string 属性名
     * 返回值：array
     */
    protected function getModelConstAttribute($modelClass, $attribute) {
        if (is_callable([$modelClass, studly_case('get' . strtolower($attribute)) . 'Validator'], true, $callable_name) && function_exists($callable_name)) {
            return call_user_func($callable_name);
        }
        if (isset($modelClass::$$attribute)) {
            return $modelClass::$$attribute;
        }
        return [];
    }
    /*
     * 作用：获取Model的类名
     * 参数：$model Model|string Model对象或类名
     * 返回值：string
     */
    public function getModelClass($model) {
        if (is_string($model)) {
            return $model;
        } elseif ($model instanceof Eloquent) {
            return get_class($model);
        } else {
            throw new Exception('程序异常！');
        }
    }
}
