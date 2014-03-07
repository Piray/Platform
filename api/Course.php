<?php

namespace api;

class Course extends \library\Module
{
    public static $defaultLimit = 30;
    public static $defaultPage = 1;
    public static $allowQueryField = array(
        'teacher',
        'teacher_id',
        'name',
        'room',
        'from',
        'end',
        'limit',
        'page'
    );
    public function init()
    {
        $this->app->group('/api/course', function () {
            $this->app->get('/search/:queryString+', array($this, 'getCourseByQuery'));
        });
    }
    /*
     * /api/course/search rest api
     *
     * this api support condiction append, allow query should be pair of ${condiction}/${value}
     * allow condiction { teacher, name, room, from, end, limit, page }
     *
     * example:
     *      /api/course/search/teacher/john
     *      /api/course/search/name/course1
     *      /api/course/search/room/404
     * 
     * and can chain it condiction
     *
     * example:
     *      /api/course/search/teacher/john/name/programming1
     *      /api/course/search/name/programming1/teacher/john is same above query
     *
     * and query support page and limit default limit is 30
     *
     * example:
     *      /api/course/search/teacher/john/name/programming1/limit/3/page/2
     *      /api/course/search/teacher/john/name/programming1/page/2
     *
     */
    public function getCourseByQuery($queryString)
    {
        $queryCondiction = array();
        if (0 !== count($queryString) % 2) {
            $this->helper->sendJson(500, array(
                'status' => 500,
                'message' => "Error query"
            ));
            return;
        }

        // bulid and valid query condiction key value
        foreach ($queryString as $index => $queryData) {
            if (0 == $index % 2) {
                if (!in_array($queryData, self::$allowQueryField)) {
                    $this->helper->sendJson(403, array(
                        'status' => 403,
                        'message' => "Unknow field name " . $queryData
                    ));
                    return;
                }
                $queryCondiction[$queryData] = $queryString[$index+1];
            }
        }

        $queryLimit = self::$defaultLimit;
        $queryPage = self::$defaultPage;
        $course = \ORM::forTable('course');
        $course->join('teacher', array('course.teacher_id', '=', 'teacher.id'));
        foreach ($queryCondiction as $condiction => $value) {
            switch ($condiction) {
                case 'limit':
                    $queryLimit = intval($value);
                    if ($queryLimit < 1) {
                        $this->helper->sendJson(403, array(
                            'status' => 500,
                            'message' => "Query limit is " . $queryLimit
                        ));
                        return;
                    }
                    break;
                case 'page':
                    $queryPage = intval($value);
                    if ($queryPage < 1) {
                        $this->helper->sendJson(403, array(
                            'status' => 500,
                            'message' => "Query page is " . $queryPage
                        ));
                        return;
                    }
                    break;
                case 'teacher_id':
                    $course->where('teacher.id', $value);
                    break;
                case 'teacher':
                    $course->whereLike('teacher.name', '%'.$value.'%');
                    break;
                case 'name':
                    $course->whereLike('name', '%'.$value.'%');
                    break;
                case 'from':
                    $recordFrom = strtotime($value);
                    $course->whereGte('record_start_time', $recordFrom);
                    break;
                case 'end':
                    $recordEnd = strtotime($value);
                    $course->whereLte('record_end_time', $recordEnd);
                    break;
                default:
                    $course->where($condiction, $value);
                    break;
            }
        }
        // all course should be valid
        $course->where('is_valid', 1)->where('teacher.is_valid', 1);
        // select all column
        $course->selectMany('course.*', array(
            'teacher' => 'teacher.name',
        ))->selectManyExpr(array(
            'start_time' => 'FROM_UNIXTIME(record_start_time)',
            'end_time' => 'FROM_UNIXTIME(record_end_time)'
        ));
        // find all course count and check query range
        $queryResultCount = $course->count();
        if ($queryResultCount > 0) {
            $totalPage = ceil($queryResultCount / $queryLimit);
            if ($queryPage > $totalPage) {
                $this->helper->sendJson(403, array(
                    'status' => 500,
                    'message' => "Query out of range " . $queryPage . "/" . $totalPage
                ));
                return;
            }

            // limit and page
            $limitStartFrom = ($queryPage - 1) * $queryLimit;
            $course->limit($queryLimit)->offset($limitStartFrom);
            // execute query
            $queryResult = $course->findArray();

            $this->helper->sendJson(200, array(
                'total_page' => ceil($queryResultCount / $queryLimit),
                'page' => $queryPage,
                'result' => $queryResult
            ));
        } else {
            $this->helper->sendJson(404, array(
                'status' => 404,
                'message' => "No data exist"
            ));
        }
    }
}

