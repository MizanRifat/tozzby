<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\API\Http\Resources\Catalog\Product as ProductResource;
use Webkul\Sales\Repositories\OrderItemRepository;


class MyController extends Controller
{

    protected $guard;

    protected $orderItemRepository;

    protected $_config;

    protected $repository;


    protected $startDate;
    protected $lastStartDate;
    protected $endDate;
    protected $lastEndDate;


    public function __construct(Request $request)
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        $this->_config = request('_config');


        if (isset($this->_config['authorization_required']) && $this->_config['authorization_required']) {

            auth()->setDefaultDriver($this->guard);

            $this->middleware('auth:' . $this->guard);
        }



        $this->repository = app($this->_config['repository']);
    }

    public function getTopSellingProducts()
    {
        $result = app(OrderItemRepository::class)->scopeQuery(function ($query) {

            $qb = $query->select(DB::raw('SUM(qty_ordered) as total_qty_ordered'))
                ->addSelect('id', 'product_id', 'product_type', 'name')
                ->where('order_items.created_at', '>=', $this->startDate)
                ->where('order_items.created_at', '<=', $this->endDate)
                ->whereNull('parent_id')
                ->groupBy('product_id')
                ->orderBy('total_qty_ordered', 'DESC')
                ->limit(5);

            return $qb;
        });

        // return $result;
        return ProductResource::collection($this->productRepository->getAll(request()->input('category_id')));
    }

    public function setStartEndDate()
    {
        $this->startDate = request()->get('start')
            ? Carbon::createFromTimeString(request()->get('start') . " 00:00:01")
            : Carbon::createFromTimeString(Carbon::now()->subDays(30)->format('Y-m-d') . " 00:00:01");

        $this->endDate = request()->get('end')
            ? Carbon::createFromTimeString(request()->get('end') . " 23:59:59")
            : Carbon::now();

        if ($this->endDate > Carbon::now()) {
            $this->endDate = Carbon::now();
        }

        $this->lastStartDate = clone $this->startDate;
        $this->lastEndDate = clone $this->startDate;

        $this->lastStartDate->subDays($this->startDate->diffInDays($this->endDate));
        $this->lastEndDate->subDays($this->lastStartDate->diffInDays($this->lastEndDate));
    }
}
