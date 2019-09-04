<?php

namespace App\V1\Http\Controllers\Api;

use App\V1\Configuration;
use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Controllers\ItemsPerPageTrait;
use App\V1\Http\Requests\Request;
use App\V1\ModelRepositories\UserEmailRepository;
use App\V1\ModelTransformers\UserEmailTransformer;
use App\V1\Utils\PaginationHelper;
use Illuminate\Validation\Rule;

class UserEmailController extends ApiController
{
    use ItemsPerPageTrait;

    protected $userEmailRepository;

    public function __construct()
    {
        parent::__construct();

        $this->userEmailRepository = new UserEmailRepository();
    }

    protected function search(Request $request)
    {
        return [];
    }

    public function index(Request $request)
    {
        $roles = $this->userEmailRepository->search(
            $this->search($request),
            Configuration::FETCH_PAGING_YES,
            $this->itemsPerPage(),
            $request->input('sort_by'),
            $request->input('sort_order')
        );
        return $this->responseSuccess([
            'user_emails' => $this->transform(UserEmailTransformer::class, $roles),
            'pagination' => (new PaginationHelper($roles))->toArray(),
        ]);
    }

    protected function storeValidatedRules(Request $request)
    {
        return [
            'user_id' => 'required|exists:users,id',
            'email' => 'required|email|max:255|unique:user_emails,email',
        ];
    }

    protected function storeValidated(Request $request)
    {
        $this->validated($request, $this->storeValidatedRules($request));
    }

    protected function storeExecute(Request $request)
    {
        return $this->userEmailRepository->create(
            $request->input('user_id'),
            $request->input('email'),
            $request->input('is_alias', false) == true,
            $request->input('verified', false) == true,
            $request->input('app_verify_email_path')
        );
    }

    public function store(Request $request)
    {
        $this->storeValidated($request);

        $this->transactionStart();
        return $this->responseSuccess([
            'user_email' => $this->transform(
                UserEmailTransformer::class,
                $this->storeExecute($request)
            )
        ]);
    }

    public function show(Request $request, $id)
    {
        return $this->responseSuccess([
            'user_email' => $this->transform(
                UserEmailTransformer::class,
                $this->userEmailRepository->model($id)
            )
        ]);
    }

    protected function updateValidatedRules(Request $request)
    {
        return [
            'user_id' => 'required|exists:users,id',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('user_emails', 'email')->ignore($this->userEmailRepository->getId())
            ],
        ];
    }

    protected function updateValidated(Request $request)
    {
        $this->validated($request, $this->updateValidatedRules($request));
    }

    protected function updateExecute(Request $request)
    {
        return $this->userEmailRepository->update(
            $request->input('user_id'),
            $request->input('email'),
            $request->input('is_alias', false) == true,
            $request->input('verified', false) == true,
            $request->input('app_verify_email_path')
        );
    }

    public function update(Request $request, $id)
    {
        $this->userEmailRepository->model($id);

        $this->updateValidated($request);

        $this->transactionStart();
        return $this->responseSuccess([
            'user_email' => $this->transform(
                UserEmailTransformer::class,
                $this->updateExecute($request)
            )
        ]);
    }

    protected function bulkDestroyValidatedRules(Request $request)
    {
        return [
            'ids' => 'required|array',
        ];
    }

    protected function bulkDestroyValidated(Request $request)
    {
        $this->validated($request, $this->bulkDestroyValidatedRules($request));
    }

    public function bulkDestroy(Request $request)
    {
        $this->bulkDestroyValidated($request);

        $this->userEmailRepository->delete($request->input('ids'));

        return $this->responseSuccess();
    }
}
