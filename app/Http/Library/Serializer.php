<?php

namespace App\Http\Library;

use Illuminate\Http\Request;

class Serializer
{

    /**
     * JsonApiSerializer constructor.
     *
     * @param string $baseUrl
     */
    public function __construct($baseUrl = null)
    {
        $this->baseUrl = $baseUrl;
        $this->rootObjects = [];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function serializeItem($status =false, $message = null,$data=null)
    {
        $resource = [
            'status'=>$status,
            'message'=> $message,
            'data'=>$data
        ];

        return $resource;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function serializeCollection($status =false, $message = null,$data=null)
    {
        if(is_object($data))
            $data = $data->toArray();
        $resource = [
            'status'=>$status,
            'message'=> $message,
            'data'=>$data,
            'count'=>count($data)
        ];

        return $resource;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function serializePaginate($status =false, $message = null,$data=null)
    {
        if(is_object($data))
            $data = $data->toArray();
        $resource = [
            'status'=>$status,
            'message'=> $message,
        ];
        $resource = array_merge($resource,$data);
        $resource['count'] = count($resource['data']);
        return $resource;
    }
}
