<?php

namespace api;

class Device extends \library\Module
{
    public static $deviceType = array(
        'picam',
        'piplayer'
    );
    public function init()
    {
        $this->app->group('/api/device', function () {
            $this->app->get('', array($this, 'getDevices'));
            $this->app->get('/search/:deviceId', array($this, 'getDeviceById'));
            $this->app->get('/search/name/:deviceName', array($this, 'getDeviceByName'));
            $this->app->get('/search/type/:deviceType', array($this, 'getDeviceByType'));
            $this->app->post('/create', array($this, 'postCreateDevice'));
            $this->app->delete('/delete/:deviceId', array($this, 'deleteDevice'));
            $this->app->put('/update/:deviceId', array($this, 'updateDevice'));
        });
    }
    /*
     * /api/device/update/:deviceId Json input spec
     *
     * {
     *     "name": "test1",
     *     "type": "piplayer",
     *     "comment": "404 room"
     * }
     *
     * {
     *     "name": "test1",
     * }
     *
     * {
     *     "type": "piplayer",
     *     "comment": "404 room"
     * }
     *
     */
    public function updateDevice($deviceId)
    {
        $acceptField = array(
            'name',
            'type',
            'comment'
        );
        $updateDeviceData = $this->helper->receiveJson();
        if (NULL !== $updateDeviceData) {
            $allowUpdateData = array();
            foreach ($updateDeviceData as $updateDeviceKey => $updateDeviceValue) {
                if (in_array($updateDeviceKey, $acceptField)) {
                    $allowUpdateData[$updateDeviceKey] = $updateDeviceValue;
                } else {
                    $this->helper->sendJson(403, array(
                        'status' => 403,
                        'message' => "Unknow field name " . $updateDeviceKey
                    ));
                    return;
                }

                if ('type' == $updateDeviceKey) {
                    if (!in_array($updateDeviceValue, self::$deviceType)) {
                        $this->helper->sendJson(403, array(
                            'status' => 403,
                            'message' => "Unknow type " . $updateDeviceValue . " is invalid."
                        ));
                        return;
                    }
                }
            }

            if (!empty($allowUpdateData)) {
                if ($this->deviceExist($deviceId)) {
                    $deviceRow = \ORM::forTable('device')->where('device_id', $deviceId)->findOne();
                    foreach ($allowUpdateData as $field => $data) {
                        $deviceRow[$field] = $data;
                    }
                    $deviceRow->save();

                    $this->helper->sendJson(200, array(
                        'status' => 200,
                        'message' => "Device " . $deviceId . " is update done."
                    ));
                    return;
                } else {
                    $this->helper->sendJson(403, array(
                        'status' => 403,
                        'message' => "Device " . $deviceId . " is not exist."
                    ));
                    return;
                }
            } else {
                $this->helper->sendJson(403, array(
                    'status' => 403,
                    'message' => "Device " . $deviceId . " no data need update."
                ));
                return;
            }
        }

        $this->helper->sendJson(500, array(
            'status' => 500,
            'message' => "Error input"
        ));
    }
    public function deleteDevice($deviceId)
    {
        if ($this->deviceExist($deviceId)) {
            \ORM::forTable('device')->where('device_id', $deviceId)->deleteMany();

            $this->helper->sendJson(200, array(
                'status' => 200,
                'message' => "Device " . $deviceId . " is delete done."
            ));
            return;
        }
        
        $this->helper->sendJson(403, array(
            'status' => 403,
            'message' => "Device" . $deviceId . " is not exist."
        ));
    }
    /*
     * /api/device/create Json input spec
     *
     * {
     *     "mac": "00:00:00:00:00:00",
     *     "name": "device 1",
     *     "type": "piplayer",
     *     "comment": "",
     * }
     *
     */
    public function postCreateDevice()
    {
        $createDeviceData = $this->helper->receiveJson();
        if (isset($createDeviceData['mac']) && isset($createDeviceData['name']) && isset($createDeviceData['type'])) {
            if (!in_array($createDeviceData['type'], self::$deviceType)) {
                // device type invalid
                $this->helper->sendJson(403, array(
                    'status' => 403,
                    'message' => "Device type unknow"
                ));
                return;
            }

            if (!$this->checkMacValid($createDeviceData['mac'])) {
                // mac address invalid
                $this->helper->sendJson(403, array(
                    'status' => 403,
                    'message' => "Device mac address invalid"
                ));
                return;
            }

            if (!$this->deviceExist($createDeviceData['mac'])) {
                // device not exist and create device
                $newDevice = \ORM::forTable('device')->create();
                $newDevice->device_id = $createDeviceData['mac'];
                $newDevice->name = $createDeviceData['name'];
                $newDevice->type = $createDeviceData['type'];
                $newDevice->comment = $createDeviceData['comment'];
                $newDevice->save();
            
                $this->helper->sendJson(200, array(
                    'status' => 200,
                    'message' => "Create device " . $createDeviceData['name'] . " done."
                ));
                return;
            } else {
                // device already exist
                $this->helper->sendJson(403, array(
                    'status' => 403,
                    'message' => "Can not create exist " . $createDeviceData['name'] . "."
                ));
                return;
            }
        } 

        $this->helper->sendJson(500, array(
            'status' => 500,
            'message' => "Error input"
        ));
    }
    public function getDeviceById($deviceId)
    {
        $this->helper->sendJson(200, \ORM::forTable('device')
            ->where('device_id', $deviceId)
            ->findArray()
        );
    }
    public function getDeviceByType($deviceType)
    {
        $this->helper->sendJson(200, \ORM::forTable('device')
            ->whereLike('type', $deviceType)
            ->findArray()
        );
    }
    public function getDeviceByName($deviceName)
    {
        $this->helper->sendJson(200, \ORM::forTable('device')
            ->whereLike('name', '%'.$deviceName.'%')
            ->findArray()
        );
    }
    public function getDevices()
    {
        $this->helper->sendJson(200, \ORM::forTable('device')
            ->findArray()
        );
    }
    private function deviceExist($deviceMac)
    {
        $existDeviceCount = \ORM::forTable('device')->where('device_id', $deviceMac)->count();
        if (0 < $existDeviceCount) {
            return true;
        }
        return false;
    }
    private function checkMacValid($macAddress)
    {
        if (preg_match('/^([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}$/', $macAddress)) {
            return true;
        }
        return false;
    }
}

