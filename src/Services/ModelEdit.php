<?php

/*
 * Model编辑处理
 */

namespace Laravel\Crbac\Services;

use Closure,
    Exception,
    Illuminate\Support\Facades\DB,
    Illuminate\Support\Facades\Request,
    Illuminate\Support\Facades\Validator,
    Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Database\Eloquent\Model as Eloquent;

class ModelEdit extends Service {

    /**
     * 验证处理
     * @param string|Model $model
     * @param array $input
     * @param array $option
     * @return false|array
     */
    public function validation($model, array $input = [], array $option = []) {
        $input = array_filter($input, function($val) {
            return !is_null($val);
        });
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

    /**
     * 修改数据
     * @param Model|string $model
     * @param array $input
     * @param array $option
     * @param Closure $before
     * @param Closure $after
     * @return false|Model
     */
    public function edit($model, array $input = [], array $option = [], Closure $before = null, Closure $after = null) {
        $data = $this->validation($model, $input, $option);
        if ($data === false) {//验证失败
            return false;
        }
        try {
            DB::beginTransaction();
            if ($before && $before($data, $this, $model) === false) {
                DB::rollBack();
                return false;
            }
            //上传处理
            foreach ($data as &$item) {
                if ($item instanceof UploadedFile) {
                    $item = $this->upload($item);
                    if ($item === false) {//上传失败
                        DB::rollBack();
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
                DB::rollBack();
                return false;
            }
            DB::commit();
            return $model;
        } catch (\Exception $err) {
            DB::rollBack();
            throw $err;
        }
    }

    /**
     * 修改数据将请求数据体作为数据源
     * @param Model|string $model
     * @param array $option
     * @param Closure $before
     * @param Closure $after
     * @return false|Model
     */
    public function requestEdit($model, array $option = [], Closure $before = null, Closure $after = null) {
        return $this->edit($model, Request::all(), $option, $before, $after);
    }

    /**
     * 上传处理
     * @param UploadedFile $file
     * @return false|string
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

    /**
     * 获取数据验证规则
     * @param string $modelClass
     * @return array
     */
    protected function getRules($modelClass) {
        return $this->getModelConstAttribute($modelClass, '_validator_rules');
    }

    /**
     * 获取验证异常说明
     * @param string $modelClass
     * @return array
     */
    protected function getMessages($modelClass) {
        return $this->getModelConstAttribute($modelClass, '_validator_description');
    }

    /**
     * 获取统一验证说明（不计较是哪个验证条件失败，统一说明）
     * @param string $modelClass
     * @return array
     */
    protected function getUnifyMessages($modelClass) {
        return $this->getModelConstAttribute($modelClass, '_validator_messages');
    }

    /**
     * 获取Model配置的静态属性
     * @param string $modelClass
     * @param string $attribute
     * @return array
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

    /**
     * 获取Model的类名
     * @param Model|string $model
     * @return string
     * @throws Exception
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
