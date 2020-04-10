<?php

class User {

	/**
     * @OA\Post(
     *     path="/?api_name=swagger.user.login",
     *     summary="用户登录",
     *     description="成功返回session_id",
     *     operationId="swagger.user.login",
     *     tags={"用户"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @OA\Parameter(ref="#/components/parameters/appid"),
     *     @OA\Parameter(ref="#/components/parameters/PHPSESSID"),
     *     @OA\Parameter(ref="#/components/parameters/token"),
     *     @OA\Parameter(
     *         description="api_name",
     *         in="query",
     *         name="api_name",
     *         required=true,
     *         @OA\Schema(
     *              type="string",
     *              default="xhgy.User.login"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="手机号",
     *         in="query",
     *         name="mobile",
     *         required=true,
     *         @OA\Schema(
     *              type="string",
     *         ),
     *         example="13738778857"
     *     ),
     *     @OA\Parameter(
     *         description="验证码",
     *         in="query",
     *         name="verify_code",
     *         required=true,
     *         @OA\Schema(
     *              type="string",
     *         ),
     *         example="111111"
     *     ),
     *     @OA\Response(
     *         response=0,
     *         description="登录成功",
     *         @OA\JsonContent(
     *                   @OA\Property(
      *                      property="code",
      *                      type="integer",
      *                      description="phpsessid",
      *                      example=0  
      *                   ),
     *             		@OA\Property(
	 *                      property="data",
	 *                      type="array",
	 *                      description="",
     *                      @OA\Items(
     *                          @OA\Property(
              *                      property="user_id",
              *                      type="integer",
              *                      description="用户id",
              *                      example="123"    
          *                     ),
          *                     @OA\Property(
              *                      property="phpsessid",
              *                      type="string",
              *                      description="phpsessid",
              *                      example="1231411312312"    
          *                     ),
     *                      )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="-1",
     *         description="登录失败"
     *     )
     * )
     */
	public function login(){}

	
}


