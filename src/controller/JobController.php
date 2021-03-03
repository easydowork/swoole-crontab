<?php

namespace easydowork\crontab\controller;

use easydowork\crontab\job\JobTable;

/**
 * Class JobController
 * @package easydowork\crontab
 * @property JobTable $jobTable
 */
class JobController extends Controller
{

    /**
     * @var JobTable
     */
    protected $jobTable;

    public function init()
    {
        parent::init();
        $this->jobTable = JobTable::getInstance();
    }

    /**
     * get
     * @return array
     */
    public function get(): array
    {
        $id = (string)($this->request->post['id'] ?? 0);
        if (!empty($data = $this->jobTable->get($id))) {
            return $this->success($data);
        }
        return $this->error();
    }

    /**
     * set
     * @return array
     */
    public function set(): array
    {
        $data = $this->request->post;
        $id = uniqid();
        if ($this->jobTable->set(uniqid(), $data)) {
            return $this->success([
                'id' => $id,
            ]);
        }
        return $this->error();
    }

    public function all(): array
    {
        $dataList = [];
        foreach ($this->jobTable->getTable() as $key => $value) {
            $dataList[$key] = $value;
        }
        return $this->success($dataList);
    }

    /**
     * del
     * @return array
     */
    public function del(): array
    {
        $id = (string)($this->request->post['id'] ?? 0);
        if ($this->jobTable->del($id)) {
            return $this->success();
        }
        return $this->error();
    }

    public function count(): array
    {
        return $this->success([
            'count' => $this->jobTable->count(),
        ]);
    }
}