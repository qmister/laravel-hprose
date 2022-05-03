<?php

namespace whereof\laravel\Hprose;

class DemoService
{
    /**
     * @var array[]
     */
    protected $list = [
        [
            'id'   => 1,
            'name' => 'php'
        ],
        [
            'id'   => 2,
            'name' => 'hprose'
        ],
        [
            'id'   => 3,
            'name' => 'PhpScript'
        ],
        [
            'id'   => 4,
            'name' => 'LaravelHprose'
        ],
    ];

    /**
     * @return array
     */
    public function index()
    {
        return $this->list;
    }

    /**
     * @return string
     */
    public function create()
    {
        return 'create success';
    }


    /**
     * @param $id
     * @return array
     */
    public function show($id)
    {
        return $this->list[$id] ?? $this->list[4];
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function edit($id)
    {
        return $id . ' edit success';
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function destroy($id)
    {
        return $id . ' destroy success';
    }
}
