<?php

namespace api;

class User extends \library\Module
{
    public static $userLevelList = array(
        'root',
        'admin',
        'user'
    );
    public function init()
    {
        $this->app->group('/api/user', function () {
            $this->app->get('', array($this, 'getUsers'));
            $this->app->get('/search/:name', array($this, 'getSearchUser'));
            $this->app->post('/create', array($this, 'postCreateUser'));
            $this->app->delete('/delete/:name', array($this, 'deleteUser'));
            $this->app->put('/update/:name', array($this, 'updateUser'));
        });
    }
    /*
     * /api/user/update/:name Json input spec
     *
     * {
     *     "password": "update password",
     *     "comment": "",
     *     "level": "user"
     * }
     *
     * {
     *     "password": "update password",
     * }
     *
     * {
     *     "comment": "test user",
     *     "level": "user"
     * }
     */
    public function updateUser($name)
    {
        $acceptField = array(
            'password',
            'level',
            'comment'
        );
        $updateUserData = $this->helper->receiveJson();
        if (NULL !== $updateUserData) {
            $allowUpdateData = array();
            foreach ($updateUserData as $updateUserKey => $updateUserValue) {
                if (in_array($updateUserKey, $acceptField)) {
                    $allowUpdateData[$updateUserKey] = $updateUserValue;
                } else {
                    $this->helper->sendJson(403, array(
                        'status' => 403,
                        'message' => "Unknow field name " . $updateUserKey
                    ));
                    return;
                }

                if ('level' == $updateUserKey) {
                    if (!in_array($updateUserValue, self::$userLevelList)) {
                        $this->helper->sendJson(403, array(
                            'status' => 403,
                            'message' => "Unknow level " . $updateUserValue . " is invalid."
                        ));
                        return;
                    }
                }
            }

            if (!empty($allowUpdateData)) {
                if ($this->userExist($name)) {
                    $userRow = \ORM::forTable('user')->where('name', $name)->findOne();
                    foreach ($allowUpdateData as $field => $data) {
                        $userRow[$field] =  $data;
                    }
                    $userRow->save();

                    $this->helper->sendJson(200, array(
                        'status' => 200,
                        'message' => "User " . $name . " is update done."
                    ));
                    return;
                } else {
                    $this->helper->sendJson(403, array(
                        'status' => 403,
                        'message' => "User " . $name . " is not exist."
                    ));
                    return;
                }
            } else {
                $this->helper->sendJson(403, array(
                    'status' => 403,
                    'message' => "User " . $name . " no data need update."
                ));
                return;
            }
        }
        
        $this->helper->sendJson(500, array(
            'status' => 500,
            'message' => "Error input"
        ));
    }
    public function deleteUser($name)
    {
        if ($this->userExist($name)) {
            \ORM::forTable('user')->where('name', $name)->deleteMany();
            
            $this->helper->sendJson(200, array(
                'status' => 200,
                'message' => "User " . $name . " is delete done."
            ));
            return;
        }
        
        $this->helper->sendJson(403, array(
            'status' => 403,
            'message' => "User " . $name . " is not exist."
        ));
    }
    /*
     * /api/user/create Json input spec
     *
     * {
     *     "name": "new user",
     *     "password": "new password",
     *     "comment": "",
     *     "level": "admin"
     * }
     *
     */
    public function postCreateUser()
    {
        $createUserData = $this->helper->receiveJson();
        if (isset($createUserData['name']) && isset($createUserData['password']) && 
            isset($createUserData['level']) && in_array($createUserData['level'], self::$userLevelList)) {

            if (!$this->userExist($createUserData['name'])) {
                // user not exist, so create user
                $newUser = \ORM::forTable('user')->create();
                $newUser->name = $createUserData['name'];
                $newUser->password = md5($createUserData['password']);
                $newUser->level = $createUserData['level'];
                $newUser->comment = $createUserData['comment'];
                $newUser->save();

                $this->helper->sendJson(200, array(
                    'status' => 200,
                    'message' => "Create user " . $createUserData['name'] . " done."
                ));
                return;
            } else {
                // user already exist
                $this->helper->sendJson(403, array(
                    'status' => 403,
                    'message' => "Can not create exist " . $createUserData['name'] . "."
                ));
                return;
            }
        }
        
        $this->helper->sendJson(500, array(
            'status' => 500,
            'message' => "Error input"
        ));
    }
    /*
     * /api/user/search/:name Json output spec
     *
     * {
     *     "id": "1",
     *     "name": "dachichang",
     *     "level": "root",
     *     "comment": "Piray super user and system designer."
     * }
     *
     * for error
     *
     * {
     *     "status": 404,
     *     "message": "dachichan user is not found."
     * }
     *
     */
    public function getSearchUser($name)
    {
        $userRows = \ORM::forTable('user')
            ->where('name', $name)
            ->selectMany('id', 'name', 'level', 'comment')
            ->findArray();

        if (count($userRows) > 0) {
            $this->helper->sendJson(200, $userRows[0]);
            return;
        }

        $this->helper->sendJson(404, array(
            'status' => 404,
            'message' => $name . " user is not found."
        ));
    }
    /*
     * /api/user Json output spec
     *
     * [
     *     {
     *          "id": "1",
     *          "name": "dachichang",
     *          "level": "root",
     *          "comment": "Piray super user and system designer."
     *     },
     *     {
     *          "id": "2",
     *          "name": "test",
     *          "level": "normal",
     *          "comment": "Piray test user"
     *     }
     * ]
     *
     */
    public function getUsers()
    {
        $this->helper->sendJson(200, \ORM::forTable('user')
            ->selectMany('id', 'name', 'level', 'comment')
            ->findArray()
        );
    }
    private function userExist($name)
    {
        $existUserCount = \ORM::forTable('user')->where('name', $name)->count();
        if (0 < $existUserCount) {
            return true;
        }
        return false;
    }
}

