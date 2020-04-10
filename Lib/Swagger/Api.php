<?php

/**
 * @OA\Info(
 *     description="Swagger测试接口",
 *     version="1.0.0",
 *     title="Swagger 测试",
 *     termsOfService="http://swagger.io/terms/",
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */
/**
 * @OA\Server(
 *     description="测试接口请求地址",
 *     url="https://wx.xhsx88.com/api/api"
 * )
 */
/**
 * @OA\Server(
 *     description="xyc本地接口请求地址",
 *     url="http://test.xhgy.com/api/api"
 * )
 */

/**
 * 
 * @OA\Tag(
 *     name="用户",
 *     description="用户接口",
 * )
 * @OA\Tag(
 *     name="商品",
 *     description="商品接口",
 * )
 *  */

/**
 *	@OA\Parameter(
 *   parameter="appid",
 *   name="appid",
 *   description="appid",
 *   @OA\Schema(
 *     type="integer",
 *     format="int64",
 *   ),
 *   in="query",
 *   required=true,
 *   example=1
 * )
 *
 * @OA\Parameter(
 *   parameter="PHPSESSID",
 *   name="PHPSESSID",
 *   description="PHPSESSID",
 *   @OA\Schema(
 *     type="string"
 *   ),
 *   in="query",
 *   required=true,
 *   example="1111111111"
 * )
 *
 * @OA\Parameter(
 *   parameter="token",
 *   name="token",
 *   description="token",
 *   @OA\Schema(
 *     type="string"
 *   ),
 *   in="query",
 *   required=true,
 *   example="11111111111111111111111111111111"
 * )
 *
 * @OA\Parameter(
 *   parameter="firstRow",
 *   name="firstRow",
 *   description="分页",
 *   @OA\Schema(
 *     type="integer",
 *   ),
 *   in="query",
 *   required=true,
 *   example="0"
 * )
 *
 * @OA\Parameter(
 *   parameter="pageSize",
 *   name="pageSize",
 *   description="分页",
 *   @OA\Schema(
 *     type="integer",
 *   ),
 *   in="query",
 *   required=true,
 *   example="0"
 * )
 */