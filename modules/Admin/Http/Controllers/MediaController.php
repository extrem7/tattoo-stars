<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//use Spatie\ImageOptimizer\OptimizerChain;

class MediaController extends Controller
{

    protected $model = Media::class;

    public function upload(Request $request)
    {
        $this->validate($request, [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,bmp,png'],
        ]);

        $image = $request->file('image');
        $file = $image->storeAs('public/uploads', $image->hashName());
        //app(OptimizerChain::class)->optimize(\Storage::path($file));

        return ['location' => \Storage::url($file)];
    }
}
