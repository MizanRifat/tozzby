<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Repositories\ProductReviewRepository;
use Webkul\API\Http\Resources\Catalog\ProductReview as ProductReviewResource;

class ReviewController extends Controller
{
    /**
     * Contains current guard
     *
     * @var array
     */
    protected $guard;

    /**
     * ProductReviewRepository object
     *
     * @var \Webkul\Product\Repositories\ProductReviewRepository
     */
    protected $reviewRepository;

    /**
     * Controller instance
     *
     * @param  Webkul\Product\Repositories\ProductReviewRepository  $reviewRepository
     */
    public function __construct(ProductReviewRepository $reviewRepository)
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);
        $this->middleware('auth:' . $this->guard);

        $this->reviewRepository = $reviewRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $customer = Auth::guard($this->guard)->user();

        $this->validate(request(), [
            'comment' => 'required',
            'rating'  => 'required|numeric|min:1|max:5',
        ]);



        $data = array_merge(request()->all(), [
            'customer_id' => $customer->id,
            'name'        => $customer->name,
            'status'      => 'approved',
            'product_id'  => $id,
        ]);

        $productReview = $this->reviewRepository->create($data);

        return response()->json([
            'message' => 'Your review submitted successfully.',
            'data'    => new ProductReviewResource($this->reviewRepository->find($productReview->id)),
        ]);
    }
}
