<?php

namespace Modules\Popup\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Popup extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public $timestamps = false;

    public function __construct()
    {
        $this->table = 'popups';
    }

    public function getIndexParams()
    {
        $popups = Popup::all();
        return [
            'popups' => $popups
        ];
    }

    public function getCreateParams()
    {
        // 팝업 레이어 생성할 때 넣어줄 기본값
        $default = [
            'device' => 'both',
            'disable_hours' => 24,
            'left' => 10,
            'top' => 10,
            'width' => 450,
            'height' => 500,
            'content_html' => 2,
            'color' => '#000000',
            'color_button' => '#393939',
            'color_button_font' => '#ffffff',
        ];
        return [
            'default' => $default,
            'type' => 'create'
        ];
    }

    public function storePopup($request)
    {
        $data = $request->all();
        $data = array_except($data, ['begin_chk', 'end_chk', '_token', '_method']);

        return Popup::insertGetId($data);
    }

    public function getEditParams($id)
    {
        $popup = Popup::find($id);

        return [
            'popup' => $popup,
            'type' => 'update'
        ];
    }

    public function updatePopup($request, $id)
    {
        $data = $request->all();
        $data = array_except($data, ['id', 'begin_chk', 'end_chk', '_token', '_method']);

        return Popup::where('id', $id)->update($data);
    }

    public function deletePopup($id)
    {
        return Popup::destroy($id);
    }

}
