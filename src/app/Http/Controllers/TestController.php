<?php


namespace App\Http\Controllers;

use App\Http\Requests\TestRequest;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;

/**
 * Test Request controller
 *
 * Class TransactionRequestsController
 * @package App\Http\Controllers
 */
class TestController extends Controller
{
    /**
     * Test post request
     * @param TestRequest $request
     * @return string
     */
    public function post(TestRequest $request)
    {
        Log::info(
            'POST /test:' . PHP_EOL
            . \GuzzleHttp\json_decode($request->getContent()) . PHP_EOL
        );

        return new Response();
    }
}
