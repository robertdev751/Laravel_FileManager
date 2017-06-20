<?php

namespace Unisharp\Laravelfilemanager\controllers;

use Intervention\Image\Facades\Image;
use Unisharp\Laravelfilemanager\Events\ImageIsCropping;
use Unisharp\Laravelfilemanager\Events\ImageWasCropped;

class CropController extends LfmController
{
    /**
     * Show crop page
     *
     * @return mixed
     */
    public function getCrop()
    {
        $working_dir = request('working_dir');
        $img = $this->lfm->get(request('img'));

        return view('laravel-filemanager::crop')
            ->with(compact('working_dir', 'img'));
    }


    /**
     * Crop the image (called via ajax)
     */
    public function getCropimage($overWrite = true)
    {
        $dataX      = request('dataX');
        $dataY      = request('dataY');
        $dataHeight = request('dataHeight');
        $dataWidth  = request('dataWidth');
        $image_path = $this->lfm->path('full', request('img'));

        event(new ImageIsCropping($image_path));
        // crop image
        Image::make($image_path)
            ->crop($dataWidth, $dataHeight, $dataX, $dataY)
            ->save($crop_path);

        // make new thumbnail
        Image::make($crop_path)
            ->fit(config('lfm.thumb_img_width', 200), config('lfm.thumb_img_height', 200))
            ->save($this->lfm->thumb()->path('full', parent::getName($image_path)));
        event(new ImageWasCropped($image_path));
    }

    public function getNewCropimage (){

        $this->getCropimage(false);
    }
}
