<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ResourceController extends Controller
{
    /**
     * Contains current guard
     *
     * @var array
     */
    protected $guard;

    protected $orderItemRepository;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Repository object
     *
     * @var \Webkul\Core\Eloquent\Repository
     */
    protected $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
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

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (isset($this->_config['authenticated_customer']) && $this->_config['authenticated_customer']) {


            $request['customer_id'] = auth($this->guard)->user()['id'];
        }


        $query = $this->repository->scopeQuery(function ($query) {
            foreach (request()->except(['page', 'limit', 'pagination', 'sort', 'order', 'token']) as $input => $value) {
                $query = $query->whereIn($input, array_map('trim', explode(',', $value)));
            }

            if ($sort = request()->input('sort')) {
                $query = $query->orderBy($sort, request()->input('order') ?? 'desc');
            } else {
                $query = $query->orderBy('id', 'desc');
            }

            return $query;
        });

        if (is_null(request()->input('pagination')) || request()->input('pagination')) {
            $results = $query->paginate(request()->input('limit') ?? 10);
        } else {
            $results = $query->get();
        }

        return $this->_config['resource']::collection($results);
    }

    /**
     * Returns a individual resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        return new $this->_config['resource'](
            $this->repository->findOrFail($id)
        );

        // return $this->repository->findOneByField('code', 'price');
    }

    public function getbyslug($slug)
    {
        return new $this->_config['resource'](
            $this->repository->findBySlugOrFail($slug)
        );
    }

    /**
     * Delete's a individual resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $wishlistProduct = $this->repository->findOrFail($id);

        $this->repository->delete($id);

        return response()->json([
            'message' => 'Item removed successfully.',
        ]);
    }

    public function getCategoryTree()
    {
        return $this->repository->getCategoryTree(1);
        // return $this->repository->getRootCategories();
    }


    public function getTopSellingProducts()
    {
        return       $this->repository->getModel()
                    ->select(DB::raw('SUM(qty_ordered) as total_qty_ordered'))
                    ->addSelect('id', 'product_id', 'product_type', 'name')
                    ->where('order_items.created_at', '>=', $this->startDate)
                    ->where('order_items.created_at', '<=', $this->endDate)
                    ->whereNull('parent_id')
                    ->groupBy('product_id')
                    ->orderBy('total_qty_ordered', 'DESC')
                    ->limit(5)
                    ->get();
    }
}
