<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use App\Services\ShortLinkService;

class ShortLinkController extends Controller
{
    public function redirect_by_short_code($code) {
        $url = ShortLinkService::getUrlByShortCode($code);

        if ($url) {
            if (!filter_var($url, FILTER_VALIDATE_URL)) $url = 'http://'.$url;
            return redirect()->to($url);
        } else abort(404);
    }

    public function create_short_code(Request $request) {
        $return_data = ['status' => true];
        $service = new ShortLinkService($request->url);
        $check_in_db = $service->checkUrlExistsInDB();
        if ($check_in_db['status']) {
            $return_data['message'] = $check_in_db['short_code'];
            return response($return_data);
        }

        if ( !$service->checkUrlByCurl() ) {
            $return_data['status'] = false;
            $return_data['message'] = 'Не корректный url-адрес';
            return response($return_data);
        }

        $short_link = $service->createNewShortCode();
        $return_data['message'] = $short_link;
        return response($return_data);
    }
}
