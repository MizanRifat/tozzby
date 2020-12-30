<?php

namespace Webkul\Attribute\Http\Controllers;

use Illuminate\Support\Facades\Event;
use Webkul\Attribute\Repositories\AttributeRepository;

class AttributeController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * AttributeRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeRepository
     */
    protected $attributeRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Attribute\Repositories\AttributeRepository  $attributeRepository
     * @return void
     */
    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;

        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->_config['view']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view($this->_config['view']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'code'       => ['required', 'unique:attributes,code', new \Webkul\Core\Contracts\Validations\Code],
            'admin_name' => 'required',
            'type'       => 'required',
        ]);

        $data = request()->all();
        // dd($data);
        $data['is_user_defined'] = 1;

        $attribute = $this->attributeRepository->create($data);

        session()->flash('success', trans('admin::app.response.create-success', ['name' => 'Attribute']));

        return redirect()->route($this->_config['redirect']);
    }

    public function store2()
    {

        $array = [
            [
                'code' => 'ram',
                'options' => [
                    '1GB', '2GB', '4GB', '8GB', '16GB'
                ]
            ],
            [
                'code' => 'rom',
                'options' => [
                    '16GB', '32GB', '64GB'
                ]
            ],
        ];

        $newArray = collect($array)->map(function ($item) {

            $opa = [
                "option_1" => [
                    "admin_name" => "no-option",
                    "en" =>  [
                        "label" => "",
                    ],
                    "fr" =>  [
                        "label" => "",
                    ],
                    "nl" =>  [
                        "label" => "",
                    ],
                    "sort_order" => "0",
                ],
            ];

            $optionArray = collect($item['options'])->map(function ($option, $index) {
                return
                    [
                        "option_" . ($index + 2) =>  [
                            "admin_name" => $option,
                            "en" => [
                                "label" => $option,
                            ],
                            "fr" =>  [
                                "label" => "",
                            ],
                            "nl" => [
                                "label" => "",
                            ],
                            "sort_order" => $index + 1,
                        ],
                    ];
            });

            array_push($opa, $optionArray->toArray());

            return
                [
                    "code" => $item['code'],
                    "type" => "select",
                    "admin_name" => ucwords($item['code']),
                    "en" =>  [
                        "name" => ucwords($item['code']),
                    ],
                    "fr" =>  [
                        "name" => "",
                    ],
                    "nl" => [
                        "name" => "",
                    ],
                    "swatch_type" => "dropdown",
                    "default-null-option" => "on",
                    "options" => $opa,
                    "is_required" => "0",
                    "is_unique" => "0",
                    "validation" => "",
                    "value_per_locale" => "0",
                    "value_per_channel" => "0",
                    "is_filterable" => "0",
                    "is_configurable" => "0",
                    "is_visible_on_front" => "0",
                    "use_in_flat" => "0",
                    "is_comparable" => "0",
                    'is_user_defined' => 1
                ];
        });

        // return $newArray;
        // $data = [
        //     "code" => "newdfbfd",
        //     "type" => "select",
        //     "admin_name" => "Hard Disk",
        //     "en" =>  [
        //         "name" => "Hard Disk",
        //     ],
        //     "fr" =>  [
        //         "name" => "",
        //     ],
        //     "nl" => [
        //         "name" => "",
        //     ],
        //     "swatch_type" => "dropdown",
        //     "default-null-option" => "on",
        //     "options" => [
        //         "option_1" => [
        //             "admin_name" => "no-option",
        //             "en" =>  [
        //                 "label" => "",
        //             ],
        //             "fr" =>  [
        //                 "label" => "",
        //             ],
        //             "nl" =>  [
        //                 "label" => "",
        //             ],
        //             "sort_order" => "0",
        //         ],

        //         "option_2" =>  [
        //             "admin_name" => "second",
        //             "en" => [
        //                 "label" => "second",
        //             ],
        //             "fr" =>  [
        //                 "label" => "",
        //             ],
        //             "nl" => [
        //                 "label" => "",
        //             ],
        //             "sort_order" => "2",
        //         ],
        //     ],
        //     "is_required" => "0",
        //     "is_unique" => "0",
        //     "validation" => "",
        //     "value_per_locale" => "0",
        //     "value_per_channel" => "0",
        //     "is_filterable" => "0",
        //     "is_configurable" => "0",
        //     "is_visible_on_front" => "0",
        //     "use_in_flat" => "0",
        //     "is_comparable" => "0",
        //     'is_user_defined' => 1
        // ];

        collect($newArray)->map(function ($item) {
            $attribute = $this->attributeRepository->create($item);
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $attribute = $this->attributeRepository->findOrFail($id);

        return view($this->_config['view'], compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'code'       => ['required', 'unique:attributes,code,' . $id, new \Webkul\Core\Contracts\Validations\Code],
            'admin_name' => 'required',
            'type'       => 'required',
        ]);

        $attribute = $this->attributeRepository->update(request()->all(), $id);

        session()->flash('success', trans('admin::app.response.update-success', ['name' => 'Attribute']));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attribute = $this->attributeRepository->findOrFail($id);

        if (!$attribute->is_user_defined) {
            session()->flash('error', trans('admin::app.response.user-define-error', ['name' => 'Attribute']));
        } else {
            try {
                $this->attributeRepository->delete($id);

                session()->flash('success', trans('admin::app.response.delete-success', ['name' => 'Attribute']));

                return response()->json(['message' => true], 200);
            } catch (\Exception $e) {
                session()->flash('error', trans('admin::app.response.delete-failed', ['name' => 'Attribute']));
            }
        }

        return response()->json(['message' => false], 400);
    }

    /**
     * Remove the specified resources from database
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        $suppressFlash = false;

        if (request()->isMethod('post')) {
            $indexes = explode(',', request()->input('indexes'));

            foreach ($indexes as $key => $value) {
                $attribute = $this->attributeRepository->find($value);

                try {
                    if ($attribute->is_user_defined) {
                        $suppressFlash = true;

                        $this->attributeRepository->delete($value);
                    } else {
                        session()->flash('error', trans('admin::app.response.user-define-error', ['name' => 'Attribute']));
                    }
                } catch (\Exception $e) {
                    report($e);

                    $suppressFlash = true;

                    continue;
                }
            }

            if ($suppressFlash) {
                session()->flash('success', trans('admin::app.datagrid.mass-ops.delete-success', ['resource' => 'attributes']));
            } else {
                session()->flash('info', trans('admin::app.datagrid.mass-ops.partial-action', ['resource' => 'attributes']));
            }

            return redirect()->back();
        } else {
            session()->flash('error', trans('admin::app.datagrid.mass-ops.method-error'));

            return redirect()->back();
        }
    }
}
