<?php

/*
 * 日志
 */

namespace Laravel\Crbac\Controllers\Power;

use Laravel\Crbac\Controllers\Controller;
use Laravel\Crbac\Models\Power\UpdateLog;

class LogController extends Controller {

    //备注说明
    protected $description = '修改日志';

    /**
     * 权限项列表
     * @return view
     * @methods(GET)
     */
    public function lists() {
        $where = [
            'type',
        ];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(UpdateLog::class, $where, $order, $default, function($builder) {
            $builder->with('admin');
        });
        $description = $this->description;
        return view('power.log.lists', compact('lists', 'description', 'toOrder'));
    }

}
