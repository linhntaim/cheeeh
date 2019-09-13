<?php

namespace App\V1\Http\Controllers;

use App\V1\Http\Requests\Request;
use App\V1\ModelRepositories\ModelRepository;
use App\V1\Utils\PaginationHelper;

class ApiController extends Controller
{
    use ApiResponseTrait, ItemsPerPageTrait;

    /**
     * @var ModelRepository|mixed
     */
    protected $modelRepository;

    /**
     * @var string
     */
    protected $modelTransformerClass;

    public function __construct()
    {
        $this->withThrottlingMiddleware();
    }

    protected function responseCustomModel($model)
    {
        return $this->responseSuccess([
            'model' => $model,
        ]);
    }

    protected function responseModel($model)
    {
        return $this->responseSuccess([
            'model' => $this->modelTransform($this->modelTransformerClass, $model),
        ]);
    }

    protected function responseModels($models)
    {
        return $this->responseSuccess([
            'models' => $this->modelTransform($this->modelTransformerClass, $models),
            'pagination' => PaginationHelper::parse($models),
        ]);
    }

    #region Index
    protected function search(Request $request)
    {
        return [];
    }

    public function index(Request $request)
    {
        $models = $this->modelRepository->search(
            $this->search($request),
            $this->paging(),
            $this->itemsPerPage(),
            $request->input('sort_by'),
            $request->input('sort_order')
        );
        return $this->responseModels($models);
    }
    #endregion

    #region Store
    protected function storeValidatedRules(Request $request)
    {
        return [];
    }

    protected function storeValidated(Request $request)
    {
        $this->validated($request, $this->storeValidatedRules($request));
    }

    protected function storeExecute(Request $request)
    {
        return null;
    }

    public function store(Request $request)
    {
        $this->storeValidated($request);

        $this->transactionStart();
        return $this->responseModel($this->storeExecute($request));
    }

    #endregion

    public function show(Request $request, $id)
    {
        return $this->responseModel($this->modelRepository->model($id));
    }

    #region Update
    protected function updateValidatedRules(Request $request)
    {
        return [];
    }

    protected function updateValidated(Request $request)
    {
        $this->validated($request, $this->updateValidatedRules($request));
    }

    protected function updateExecute(Request $request)
    {
        return null;
    }

    public function update(Request $request, $id)
    {
        $this->modelRepository->model($id);

        $this->updateValidated($request);

        $this->transactionStart();
        return $this->responseModel($this->updateExecute($request));
    }
    #endregion

    #region Destroy
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
        $this->modelRepository->deleteWithIds($request->input('ids'));
        return $this->responseSuccess();
    }

    public function destroy(Request $request, $id)
    {
        $this->modelRepository->model($id);
        $this->modelRepository->deleteWithIds([$id]);
        return $this->responseSuccess();
    }
    #endregion
}
