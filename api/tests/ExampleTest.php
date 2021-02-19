<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
   public function testShouldReturnAPIVersion(){
     $result = $this->get('api/');
     $result->assertEquals(env('API_VERSION'),$result->response->original);
     
    //  $this->assert()
   }
}
