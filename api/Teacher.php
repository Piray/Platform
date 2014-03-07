<?php

namespace api;

class Teacher extends \library\Module
{
    public function init()
    {
        $this->app->group('/api/teacher', function () {
            $this->app->get('', array($this, 'getTeachers'));
        });
    }
    public function getTeachers()
    {
        $this->helper->sendJson(200, \ORM::forTable('teacher')
            ->selectMany('id', 'name')
            ->where('is_valid', 1)
            ->findArray()
        );
    }
}

