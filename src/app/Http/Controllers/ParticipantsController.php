<?php

namespace App\Http\Controllers;

use App\Http\Headers;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

/**
 * Class ParticipantsController
 * @package App\Http\Controllers
 */
class ParticipantsController extends Controller
{
    /**
     * @param Request $request
     * @param $type
     * @param $id
     * @return Response
     */
    public function update(Request $request, $type, $id)
    {
        return new Response(
            200,
            [
                'Content-Type' => 'application/json',
                'X-Date' => Headers::getXDate()
            ]
        );
    }
    /**
     * @param Request $request
     * @param $type
     * @param $id
     * @return Response
     */
    public function error(Request $request, $type, $id)
    {
        return new Response(
            200,
            [
                'Content-Type' => 'application/json',
                'X-Date' => Headers::getXDate()
            ]
        );
    }
}
