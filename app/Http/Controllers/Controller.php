<?php

namespace App\Http\Controllers;
  /**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API Documentasiyası",
 *      description="Laravel Swagger API Documentasiya",
 *      @OA\Contact(
 *          email="support@example.com"
 *      )
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_APP_URL,
 *      description="Local Server"
 * )
 */

abstract class Controller

{


}
