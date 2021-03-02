<?php

namespace easydowork\crontab;

/**
 * Class JobController
 * @package easydowork\crontab
 */
class JobController extends Controller
{

    public function get(): array
    {
        $id = (int)($this->request->get['id']??0);
        if(empty($id))
            return [];
    }

    public function set(): array
    {
        return [];
    }

    public function all(): array
    {
        return [];
    }

    public function del(): array
    {
        return [];
    }

    public function count(): array
    {
        return [];
    }
}