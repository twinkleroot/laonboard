<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'appUrl' => 'required|regex:'. config('laon.URL_REGEX'),
            'mysqlHost' => 'required|regex:/^[a-zA-Z0-9.]+$/',
            'mysqlPort' => 'required|numeric',
            'mysqlDb' => 'required|regex:/^[a-zA-Z0-9_]+$/',
            'mysqlUser' => 'required|regex:/^[a-zA-Z0-9_]+$/',
            'mysqlPass' => 'required',
            'tablePrefix' => 'required|regex:/^[a-z][a-z0-9_]+$/',
            'adminEmail' => 'required|email|max:255',
            'adminPass' => 'required',
            'adminNick' => 'required|nick_length:2,4',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => '필수 입력 항목입니다.',
            'regex' => '올바른 형식으로 입력해 주세요.',
            'numeric' => '숫자만 입력해 주세요.',
            'email' => '이메일 형식으로 입력해 주세요.',
            'max' => '최대 :max글자까지만 입력을 허용합니다.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'appUrl' => 'App Url',
            'mysqlHost' => 'MySQL Host',
            'mysqlPort' => 'MySQL Port',
            'mysqlDb' => 'MySQL Database',
            'mysqlUser' => 'MySQL User Name',
            'mysqlPass' => 'MySQL Password',
            'tablePrefix' => 'Table 접두사',
            'adminEmail' => '최고관리자 Email',
            'adminPass' => '최고관리자 Password',
            'adminNick' => '최고관리자 Nickname',
        ];
    }
}
