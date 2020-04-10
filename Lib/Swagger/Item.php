<?php

class Item {

	/**
     * @OA\Post(
     *     path="/?api_name=swagger.user.getItemList",
     *     summary="商品列表",
     *     description="成功返回商品列表",
     *     operationId="swagger.Item.getItemList",
     *     tags={"商品"},
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
     *              default="xhgy.Item.getItemList"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="一级分类id",
     *         in="query",
     *         name="class_id",
     *         required=false,
     *         @OA\Schema(
     *              type="integer",
     *         ),
     *         example="9"
     *     ),
     *     @OA\Parameter(
     *         description="商品名称",
     *         in="query",
     *         name="item_name",
     *         required=false,
     *         @OA\Schema(
     *              type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="二级分类id",
     *         in="query",
     *         name="sort_id",
     *         required=false,
     *         @OA\Schema(
     *              type="integer",
     *         ),
     *         example="19"
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/firstRow"),
     *     @OA\Parameter(ref="#/components/parameters/pageSize"),
     *     @OA\Response(
     *         response=0,
     *         description="商品列表",
     *         @OA\JsonContent(
     *                   @OA\Property(
      *                      property="code",
      *                      type="integer",
      *                      description="code",
      *                      example=0  
      *                   ),
     *             		@OA\Property(
	 *                      property="data",
	 *                      type="array",
	 *                      description="",
     *                      @OA\Items(
     *                          @OA\Property(
              *                      property="total",
              *                      type="integer",
              *                      description="总数",
              *                      example="35"    
          *                     ),
          *                     @OA\Property(
              *                      property="item_list",
              *                      type="array",
              *                      description="商品列表",
              *                      @OA\Items(
             *                          @OA\Property(
                      *                      property="item_id",
                      *                      type="integer",
                      *                      description="商品id",
                      *                      example="1"    
                  *                     ),
                  *                     @OA\Property(
                      *                      property="item_name",
                      *                      type="array",
                      *                      description="商品名称",
                      *                      example="帝皇鲜 澳洲牛腱 1000g"    
                  *                     ),
                  *                     @OA\Property(
                      *                      property="mall_price",
                      *                      type="array",
                      *                      description="商品价格",
                      *                      example="10.00"    
                  *                     )
             *                      )
          *                     )
     *                      )
	 *                   )
     *         )
     *     ),
     *     @OA\Response(
     *         response="-1",
     *         description="失败"
     *     )
     * )
     */
	public function get_item_list(){}

	
}


