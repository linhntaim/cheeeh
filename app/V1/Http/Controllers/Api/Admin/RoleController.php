<?php

namespace App\V1\Http\Controllers\Api\Admin;

use App\V1\Configuration;
use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Controllers\ItemsPerPageTrait;
use App\V1\Http\Requests\Request;
use App\V1\ModelRepositories\RoleRepository;
use App\V1\ModelTransformers\RoleTransformer;
use App\V1\Utils\PaginationHelper;
use Illuminate\Validation\Rule;

class RoleController extends ApiController
{
    use ItemsPerPageTrait;

    protected $roleRepository;

    public function __construct()
    {
        parent::__construct();

        $this->roleRepository = new RoleRepository();
    }

    private function search(Request $request)
    {
        $search = [];
        $input = $request->input('name');
        if (!empty($input)) {
            $search['name'] = $input;
        }
        $input = $request->input('display_name');
        if (!empty($input)) {
            $search['display_name'] = $input;
        }
        $input = $request->input('permissions', []);
        if (!empty($input)) {
            $search['permissions'] = (array)$input;
        }
        $search['except_protected'] = 1;
        return $search;
    }

    public function index(Request $request)
    {
        $roles = $this->roleRepository->search(
            $this->search($request),
            Configuration::FETCH_PAGING_YES,
            $this->itemsPerPage(),
            $request->input('sort_by'),
            $request->input('sort_order')
        );
        return $this->responseSuccess([
            'roles' => $this->transform(RoleTransformer::class, $roles),
            'pagination' => (new PaginationHelper($roles))->toArray(),
        ]);
    }

    public function store(Request $request)
    {
        $this->validated($request, [
            'name' => 'required|string|max:255|regex:/^[0-9a-z_]+$/|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'permissions' => 'required|array|exists:permissions,id',
        ]);

        $this->transactionStart();
        return $this->responseSuccess([
            'role' => $this->transform(
                RoleTransformer::class,
                $this->roleRepository->create([
                    'name' => $request->input('name'),
                    'display_name' => $request->input('display_name'),
                    'description' => $request->input('description'),
                ], $request->input('permissions'))
            )
        ]);
    }

    public function show(Request $request, $id)
    {
        return $this->responseSuccess([
            'role' => $this->transform(
                RoleTransformer::class,
                $this->roleRepository->model($id)
            )
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = $this->roleRepository->model($id);

        $this->validated($request, [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[0-9a-z_]+$/',
                Rule::unique('roles', 'name')->ignore($role->id)
            ],
            'display_name' => 'required|string|max:255',
            'permissions' => 'required|array|exists:permissions,id',
        ]);

        $this->transactionStart();
        return $this->responseSuccess([
            'role' => $this->transform(
                RoleTransformer::class,
                $this->roleRepository->update([
                    'name' => $request->input('name'),
                    'display_name' => $request->input('display_name'),
                    'description' => $request->input('description'),
                ], $request->input('permissions'))
            )
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $this->validated($request, [
            'ids' => 'required|array',
        ]);

        $this->roleRepository->delete($request->input('ids'));

        return $this->responseSuccess();
    }
}
