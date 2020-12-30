<?php

namespace Webkul\API\Http\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\API\Http\Resources\Catalog\Attribute as AttributeResource;
use Webkul\API\Http\Resources\Catalog\Product as ProductResource;
use Illuminate\Support\Facades\DB;

class Category extends JsonResource
{



    public $productFlatRepository;
    
    public function __construct($resource)
    {
        $this->productFlatRepository = app('Webkul\Product\Repositories\ProductFlatRepository');
    
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'code'             => $this->code,
            'name'             => $this->name,
            'slug'             => $this->slug,
            'display_mode'     => $this->display_mode,
            'description'      => $this->description,
            'meta_title'       => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords'    => $this->meta_keywords,
            'status'           => $this->status,
            'image_url'        => $this->image_url,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'attributes'       => AttributeResource::collection($this->filterableAttributes),
            'maxprice'         => $this->productFlatRepository->getCategoryProductMaximumPriceById($this->id),

            // 'maxprice'         => DB::table('product_flat')
            //             ->leftJoin('product_categories', 'product_flat.product_id', 'product_categories.product_id')
            //             ->where('product_categories.category_id', $this->id)
            //             ->max('max_price')
            // 'products'         => ProductResource::collection($this->products),
        ];
    }
}
