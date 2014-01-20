<?php

namespace library;

class Helper
{
    public function __construct($platform)
    {
        $this->app = $platform->app;
    }
    public function sendJson($statusCode, $arrayData)
    {
        $response = $this->app->response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatus($statusCode);
        $response->setBody(json_encode($arrayData));
        $response->finalize();
    }
    public function receiveJson()
    {
        $request = $this->app->request();
        $requestRawData = $request->getBody();
        return json_decode($requestRawData, true);
    }
    public function httpPost($apiUrl, $data)
    {
        $curl = curl_init($apiUrl);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }
    public function httpGet($apiUrl)
    {
        $curl = curl_init($apiUrl);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }
    public function genBreadcrumbs($routeNameMap = array())
    {
        $breadcrumbs = array();
        $request = $this->app->request();
        $routes = explode('/', $request->getResourceUri());

        $link = array();
        foreach ($routes as $route) {
            if (empty($route)) {
                continue;
            }

            // replace by routeNameMap if it's defined.
            $title = $route;
            if (isset($routeNameMap[$route])) {
                $title = $routeNameMap[$route];
            }

            // re-organize link for every crumb
            $link[] = $route;

            // append every crumb to crumbs
            $breadcrumbs[] = array(
                'link' => '/' . implode('/', $link),
                'title' => $title
            );
        }
        return $breadcrumbs;
    }
}

