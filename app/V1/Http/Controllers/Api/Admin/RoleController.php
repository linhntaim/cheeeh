<?php

namespace App\V1\Http\Controllers\Api\Admin;

use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Requests\Request;
use App\V1\ModelRepositories\RoleRepository;
use App\V1\ModelTransformers\RoleTransformer;
use Illuminate\Validation\Rule;

class RoleController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new RoleRepository();
        $this->modelTransformerClass = RoleTransformer::class;
    }

    protected function search(Request $request)
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

    protected function storeValidatedRules(Request $request)
    {
        return [
            'name' => 'required|string|max:255|regex:/^[0-9a-z_]+$/|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'permissions' => 'required|array|exists:permissions,id',
        ];
    }

    protected function storeExecute(Request $request)
    {
        return $this->modelRepository->create(
            [
                'name' => $request->input('name'),
                'display_name' => $request->input('display_name'),
                'description' => $request->input('description'),
            ],
            $request->input('permissions')
        );
    }

    protected function updateValidatedRules(Request $request)
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[0-9a-z_]+$/',
                Rule::unique('roles', 'name')->ignore($this->modelRepository->getId()),
            ],
            'display_name' => 'required|string|max:255',
            'permissions' => 'required|array|exists:permissions,id',
        ];
    }

    protected function updateExecute(Request $request)
    {
        return $this->modelRepository->update(
            [
                'name' => $request->input('name'),
                'display_name' => $request->input('display_name'),
                'description' => $request->input('description'),
            ],
            $request->input('permissions')
        );
    }
}
