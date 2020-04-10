<?php

namespace App\OutgoingRequests;

use App\Http\Headers;
use Carbon\Carbon;
use Illuminate\Support\Env;

/**
 * Class TransactionRequest
 *
 * @package App\Requests
 */
class TransactionRequest extends BaseRequest
{
	/**
	 * TransactionRequest constructor.
	 *
	 * @param array $data
	 * @param array $headers
	 *
	 * @throws \Exception
	 */
	public function __construct(array $data, array $headers)
	{
		parent::__construct($data, $headers, Env::get('HOST_TRANSACTION_REQUESTS_SERVICE') . 'transactionRequests');

		$this->method = 'POST';

		$this->headers['Date'] = Headers::getXDate();
		$this->headers['Accept'] = 'application/vnd.interoperability.transactionRequests+json;version=1.0';
		$this->headers['Content-Type'] = 'application/vnd.interoperability.transactionRequests+json;version=1.0';
		$this->headers['FSPIOP-Source'] = Env::get('FSPIOP_SOURCE');
		$this->headers['FSPIOP-Destination'] = Env::get('FSPIOP_DESTINATION');
		$this->headers['FSPIOP-Signature'] = '{"signature":"iU4GBXSfY8twZMj1zXX1CTe3LDO8Zvgui53icrriBxCUF_wltQmnjgWLWI4ZUEueVeOeTbDPBZazpBWYvBYpl5WJSUoXi14nVlangcsmu2vYkQUPmHtjOW-yb2ng6_aPfwd7oHLWrWzcsjTF-S4dW7GZRPHEbY_qCOhEwmmMOnE1FWF1OLvP0dM0r4y7FlnrZNhmuVIFhk_pMbEC44rtQmMFv4pm4EVGqmIm3eyXz0GkX8q_O1kGBoyIeV_P6RRcZ0nL6YUVMhPFSLJo6CIhL2zPm54Qdl2nVzDFWn_shVyV0Cl5vpcMJxJ--O_Zcbmpv6lxqDdygTC782Ob3CNMvg\\",\\"protectedHeader\\":\\"eyJhbGciOiJSUzI1NiIsIkZTUElPUC1VUkkiOiIvdHJhbnNmZXJzIiwiRlNQSU9QLUhUVFAtTWV0aG9kIjoiUE9TVCIsIkZTUElPUC1Tb3VyY2UiOiJPTUwiLCJGU1BJT1AtRGVzdGluYXRpb24iOiJNVE5Nb2JpbGVNb25leSIsIkRhdGUiOiIifQ"}';
	}
}
