<?php

namespace App\V1\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\V1\Exceptions\UserException;
use App\V1\Http\Requests\Request;
use App\V1\ModelTransformers\TransformTrait;
use App\V1\Rules\Rule;
use App\V1\Utils\AbortTrait;
use App\V1\Utils\ClassTrait;
use App\V1\Utils\TransactionTrait;

class Controller extends BaseController
{
    use ClassTrait, AbortTrait, TransactionTrait, TransformTrait;

    protected function validated(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make(
            $request->all(),
            $rules,
            array_merge($this->validatedMessages($rules), $messages),
            $customAttributes
        );
        if ($validator->fails()) {
            throw (new UserException($validator->errors()->all()))->setAttachedData($validator->errors()->toArray());
        }
        return true;
    }

    private function validatedMessages(array $rules)
    {
        $messages = [];
        foreach ($rules as $inputName => $subRules) {
            if (is_string($subRules)) {
                $subRules = explode('|', $subRules);
            }
            if (is_array($subRules)) {
                foreach ($subRules as &$subRule) {
                    $rule = '';
                    if (!is_string($subRule)) {
                        if (is_object($subRule) && method_exists($subRule, '__toString')) {
                            $rule = $subRule->__toString();
                        }
                    } else {
                        $rule = $subRule;
                    }

                    $ruleName = explode(':', $rule)[0];
                    $errorName = $inputName . (empty($ruleName) ? '' : '.' . $ruleName);
                    if ($this->__hasTransErrorWithModule($errorName)) {
                        if ($subRule instanceof Rule) {
                            $subRule->setTransPath($this->__transErrorPathWithModule($inputName));
                        }
                        $messages[$errorName] = $this->__transErrorWithModule($errorName);
                    }
                }
            }
        }
        return $messages;
    }

    protected function responseFile($file, array $headers = [])
    {
        return response()->file($file, $headers);
    }

    protected function responseDownload($file, $name = null, array $headers = [], $disposition = 'attachment')
    {
        return response()->download($file, $name, $headers, $disposition);
    }
}
