<?php


namespace App\Http\Controllers;

use App\Http\Requests\QuoteCreate;
use App\Http\Requests\QuoteUpdate;

/**
 * Class TransfersController
 * @package App\Http\Controllers
 */
class QuotesController extends Controller
{
    /**
     * @param QuoteCreate $request
     * @return string
     */
    public function store(QuoteCreate $request)
    {
        // TODO quotes mapping and request to mojaloop
    }


    /**
     * @param QuoteUpdate $request
     * @param $id
     */
    public function update(QuoteUpdate $request, $id)
    {
        // TODO receive put /quotes from mojaloop
    }
}
