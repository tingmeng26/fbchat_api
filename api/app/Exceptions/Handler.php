<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		AuthorizationException::class,
		HttpException::class,
		ModelNotFoundException::class,
		ValidationException::class,
		CustomException::class,
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{//將錯誤訊息調為json回覆

		//預設錯誤訊息格式
		$data = '未知錯誤';
		$msg = sprintf("%s:%s:%s", $e->getFile(), $e->getLine(), $e->getMessage());
		$code = $e->getCode() == 0 ? 500 : $e->getCode();
		$status_code = 200;

		//依繼承的class分別處理
		if($e instanceof \App\Exceptions\CustomException){//<-客製化 app/Exceptions/CustomException.php
			$data = '';
			$code = $e->getCustomCode();
			$msg = $e->getCustomMessage();
			$status_code = $e->getStatusCode();
		}elseif($e instanceof \Illuminate\Database\QueryException){
			if(env('APP_ENV') != 'local'){
				$msg = 'SQL Error!';
			}
		}elseif($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException){
			$data = '';
			$code = 404;
			$msg = 'Sorry, the page you are looking for could not be found.';
		}elseif($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException){
			$data = '';
			$code = 405;
			$msg = 'Sorry, the page you are looking for could not be found (Method Not Allowed).';
		}else{
		}

		return response()->json(
			array('success'=>'false','data'=>$data, 'msg' => $msg,'msgcode' => $code),$status_code
		);

		//return parent::render($request, $e);
	}
}
