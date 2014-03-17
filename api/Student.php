<?php

namespace api;

Class Student extends \library\Module
{
    public function init()
    {
        $this->app->group('/api/student', function() {
            $this->app->post('/:name', array($this, 'updateStudent'));
            $this->app->get('/search/:name', array($this,'searhStudentByName'));
        });
    }

    /*
     * /api/student/search/:name
     *  search student by name
     *  if not exists, return 404
     */
    function searhStudentByName($name) 
    {
        $student = \ORM::forTable('student')->whereLike('name', '%'.$name.'%')->findArray();
        if($student) {
            $this->helper->sendJson(200, array(
                'result' => $student
            ));
            return;
        }

        $this->helper->sendJson(404, array(
            'status' => 404,
            'message' => '找不到該位學生'
        ));
    }

    /*
     * [POST] /app/student/name
     *  update a student data if exists by name, or add a new one
     *
     */
    function updateStudent($name)
    {
        //check the student exists or not
        $student = \ORM::forTable('student')->where('name', $name)->findOne();
        if (!$student) {
            $student = \ORM::forTable('student')->create();
            $student->phone = $this->app->request->post('phone');
        }
        $student->phone = $this->app->request->post('phone');
        $student->comment = $this->app->request->post('comment');
        $result = $student->save();
        
        if($result) {
            $this->helper->sendJson(200, array(
                'status' => 200
            ));
        } else {
            $this->helper->sendJson(500, array(
                'status' => 200,
                'message'=> '無法新增學生'
            ));
        }
    }
}
?>
