<?php

namespace App\V1\Http\Controllers\Api\Admin;

use App\V1\Configuration;
use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Controllers\ItemsPerPageTrait;
use App\V1\Http\Requests\Request;
use App\V1\ModelRepositories\UserRepository;
use App\V1\ModelTransformers\UserTransformer;
use App\V1\Utils\ConfigHelper;
use App\V1\Utils\PaginationHelper;
use Illuminate\Validation\Rule;

class UserController extends ApiController
{
    use ItemsPerPageTrait;

    protected $userRepository;

    public function __construct()
    {
        parent::__construct();

        $this->userRepository = new UserRepository();
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
        $input = $request->input('email');
        if (!empty($input)) {
            $search['email'] = $input;
        }
        $input = $request->input('roles', []);
        if (!empty($input)) {
            $search['roles'] = (array)$input;
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
        $users = $this->userRepository->search(
            $this->search($request),
            Configuration::FETCH_PAGING_YES,
            $this->itemsPerPage(),
            $request->input('sort_by'),
            $request->input('sort_order')
        );
        return $this->responseSuccess([
            'users' => $this->transform(UserTransformer::class, $users),
            'pagination' => (new PaginationHelper($users))->toArray(),
        ]);
    }

    public function store(Request $request)
    {
        $this->validated($request, [
            'name' => 'required|string|max:255|regex:/^[0-9a-zA-Z_\-\.]+$/|unique:users,name',
            'display_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:user_emails,email',
            'password' => 'required|string|min:8',
            'roles' => 'nullable|array|exists:roles,id',
        ]);

        $this->transactionStart();
        return $this->responseSuccess([
            'user' => $this->transform(
                UserTransformer::class,
                $this->userRepository->create(
                    [
                        'name' => $request->input('name'),
                        'display_name' => $request->input('display_name'),
                        'password' => $request->input('password'),
                        'url_avatar' => ConfigHelper::defaultAvatarUrl(),
                    ],
                    $request->input('notified', false) == true,
                    $request->input('email'),
                    $request->input('email_verified', false) == true,
                    $request->input('app_verify_email_path'),
                    $request->input('roles', [])
                )
            )
        ]);
    }

    public function show(Request $request, $id)
    {
        return $this->responseSuccess([
            'user' => $this->transform(
                UserTransformer::class,
                $this->userRepository->model($id)
            )
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $this->userRepository->model($id);

        $this->validated($request, [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[0-9a-zA-Z_\-\.]+$/',
                Rule::unique('users', 'name')->ignore($user->id)
            ],
            'display_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('user_emails', 'email')->ignore($user->email->id)
            ],
            'password' => 'nullable|string|min:8',
            'roles' => 'nullable|array|exists:roles,id',
        ]);

        $this->transactionStart();
        return $this->responseSuccess([
            'user' => $this->transform(
                UserTransformer::class,
                $this->userRepository->update(
                    [
                        'name' => $request->input('name'),
                        'display_name' => $request->input('display_name'),
                        'password' => $request->input('password'),
                    ],
                    $request->input('email'),
                    $request->input('email_verified', false) == true,
                    $request->input('app_verify_email_path'),
                    $request->input('roles')
                )
            )
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $this->validated($request, [
            'ids' => 'required|array',
        ]);

        $this->userRepository->delete($request->input('ids'));

        return $this->responseSuccess();
    }
}
