<?php
namespace App\Http\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CorsMiddleware{
	private $headers;
	private $allow_origin;

	public function handle(Request $request, \Closure $next)
	{
		//下方$headers、$allow_origin依需求設定
		$this->headers = [
			'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
			'Access-Control-Allow-Headers' => 'Accept, Accept-Language, Connection, Cookie, Host, Origin, Referer, User-Agent, X-Requested-With, Content-Type, Authorization, access_token',
			'Access-Control-Expose-Headers' => '*',
			'Access-Control-Allow-Credentials' => 'true',//是否允許跨域cookies
			'Access-Control-Max-Age' => 1728000
		];
		$this->allow_origin = '*';/*[
			'http://localhost',
		];*/

		if(!is_array($this->allow_origin) && $this->allow_origin!='*'){
			$this->allow_origin = [$this->allow_origin];
		}
		$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

		//如果origin不在允許清單內，回傳403
		if ($this->allow_origin!='*' && !in_array($origin, $this->allow_origin) && !empty($origin)){
			return new Response('Forbidden', 403);
		}

		if ($request->isMethod('options')){
			return $this->setCorsHeaders(new Response('OK', 200), $origin);
		}

		$response = $next($request);
		$methodVariable = array($response, 'header');

		if (is_callable($methodVariable, false, $callable_name)) {
			return $this->setCorsHeaders($response, $origin);
		}
		return $response;
	}

	/**
	 * @param $response
	 * @return mixed
	 */
	public function setCorsHeaders($response, $origin)
	{
		foreach ($this->headers as $key => $value) {
			$response->header($key, $value);
		}
		if ($this->allow_origin=='*' ) {
			$response->header('Access-Control-Allow-Origin', empty($origin)?'*':$origin);
		} elseif(in_array($origin, $this->allow_origin)){
			$response->header('Access-Control-Allow-Origin', $origin);
		} else {
			$response->header('Access-Control-Allow-Origin', '');
		}
		return $response;
	}
}