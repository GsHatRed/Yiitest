<?php

/**
 * TImage class file.
 *
 */
Yii::import('core.components.image_drivers.Image');
class TImage extends TComponentFactory {
    
    // 缩放约束
    const RESIZE_NONE = 0x01;
    const RESIZE_WIDTH = 0x02;
    const RESIZE_HEIGHT = 0x03;
    const RESIZE_AUTO = 0x04;
    const RESIZE_INVERSE = 0x05;
    const RESIZE_PRECISE = 0x06;

    // 翻转方向
    const FLIP_HORIZONTAL = 0x11;
    const FLIP_VERTICAL = 0x12;

    public $driverClass = 'GD'; // GD, Imagick
    public $interfaceClass = 'Image';
    
    public $quality = 100;
    public $cachePath = '/assets/easyimage/'; //relative web root (recommended: /assets/easyimage/)
    public $cacheTime = 2592000; // 30 days
    public $retinaSupport = false;

    public function load($file = null, $driver = null) {
        if ($file) {
            return $this->_component = Image::factory($this->detectPath($file), $driver ? $driver : $this->driverClass);
        }
    }

    public function __toString() {
        try {
            // Render the current image
            return $this->image()->render();
        } catch (CException $e) {
            // Showing any kind of error will be "inside" image data
            return '';
        }
    }

    public function init() {
        // Publish "retina.js" library (http://retinajs.com/)
        if ($this->retinaSupport) {
            Yii::app()->clientScript->registerScriptFile(
                    Yii::app()->core->getAssetsUrl() . '/js/retina.js', CClientScript::POS_HEAD
            );
        }
    }

    public function detectPath($file) {
        $fullPath = dirname(Yii::app()->basePath) . $file;
        if (is_file($fullPath)) {
            return $fullPath;
        }
        return $file;
    }

    private function _doThumbOf($file, $newFile, $params) {
        if ($file instanceof $this->interfaceClass) {
            $this->_component = $file;
        } else {
            $this->_component = Image::factory($this->detectPath($file), $this->driver);
        }
        foreach ($params as $key => $value) {
            switch ($key) {
                case 'resize':
                    $this->resize(
                            isset($value['width']) ? $value['width'] : NULL, isset($value['height']) ? $value['height'] : NULL, isset($value['master']) ? $value['master'] : NULL
                    );
                    break;
                case 'crop':
                    if (!isset($value['width']) || !isset($value['height'])) {
                        throw new CException('Params "width" and "height" is required for action "' . $key . '"');
                    }
                    $this->crop(
                            $value['width'], $value['height'], isset($value['offset_x']) ? $value['offset_x'] : NULL, isset($value['offset_y']) ? $value['offset_y'] : NULL
                    );
                    break;
                case 'rotate':
                    if (is_array($value)) {
                        if (!isset($value['degrees'])) {
                            throw new CException('Param "degrees" is required for action "' . $key . '"');
                        }
                        $this->rotate($value['degrees']);
                    } else {
                        $this->rotate($value);
                    }
                    break;
                case 'flip':
                    if (is_array($value)) {
                        if (!isset($value['direction'])) {
                            throw new CException('Param "direction" is required for action "' . $key . '"');
                        }
                        $this->flip($value['direction']);
                    } else {
                        $this->flip($value);
                    }
                    break;
                case 'sharpen':
                    if (is_array($value)) {
                        if (!isset($value['amount'])) {
                            throw new CException('Param "amount" is required for action "' . $key . '"');
                        }
                        $this->sharpen($value['amount']);
                    } else {
                        $this->sharpen($value);
                    }
                    break;
                case 'reflection':
                    $this->reflection(
                            isset($value['height']) ? $value['height'] : NULL, isset($value['opacity']) ? $value['opacity'] : 100, isset($value['fade_in']) ? $value['fade_in'] : FALSE
                    );
                    break;
                case 'watermark':
                    if (is_array($value)) {
                        $this->watermark(
                                isset($value['watermark']) ? $value['watermark'] : NULL, isset($value['offset_x']) ? $value['offset_x'] : NULL, isset($value['offset_y']) ? $value['offset_y'] : NULL, isset($value['opacity']) ? $value['opacity'] : 100
                        );
                    } else {
                        $this->watermark($value);
                    }
                    break;
                case 'background':
                    if (is_array($value)) {
                        if (!isset($value['color'])) {
                            throw new CException('Param "color" is required for action "' . $key . '"');
                        }
                        $this->background(
                                $value['color'], isset($value['opacity']) ? $value['opacity'] : 100
                        );
                    } else {
                        $this->background($value);
                    }
                    break;
                case 'quality':
                    if (!isset($value)) {
                        throw new CException('Param "' . $key . '" can\'t be empty');
                    }
                    $this->quality = $value;
                    break;
                case 'type':
                    break;
                default:
                    throw new CException('Action "' . $key . '" is not found');
            }
        }
        return $this->save($newFile, $this->quality);
    }

    public function thumbSrcOf($file, $params = array()) {
        // Paths
        $hash = md5($file . serialize($params));
        $cachePath = dirname(Yii::app()->basePath) . $this->cachePath . $hash{0};
        $cacheFileExt = isset($params['type']) ? $params['type'] : pathinfo($file, PATHINFO_EXTENSION);
        $cacheFileName = $hash . '.' . $cacheFileExt;
        $cacheFile = $cachePath . DIRECTORY_SEPARATOR . $cacheFileName;
        $webCacheFile = $this->cachePath . $hash{0} . '/' . $cacheFileName;

        // Return cache image URL
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $this->cacheTime)) {
            return $webCacheFile;
        }

        // Make cache dir
        if (!is_dir($cachePath)) {
            mkdir($cachePath, 0755, true);
        }

        // Create and caching thumb by params
        $image = Image::factory($this->detectPath($file), $this->driver);
        $originWidth = $image->width;
        $originHeight = $image->height;
        $result = $this->_doThumbOf($image, $cacheFile, $params);
        unset($image);

        // Same for high-resolution image
        if ($this->retinaSupport && $result) {
            if ($this->image()->width * 2 <= $originWidth && $this->image()->height * 2 <= $originHeight) {
                $retinaFile = $cachePath . DIRECTORY_SEPARATOR . $hash . '@2x.' . $cacheFileExt;
                if (isset($params['resize']['width']) && isset($params['resize']['height'])) {
                    $params['resize']['width'] = $this->image()->width * 2;
                    $params['resize']['height'] = $this->image()->height * 2;
                }
                $this->_doThumbOf($file, $retinaFile, $params);
            }
        }

        return $webCacheFile;
    }

    public function thumbOf($file, $params = array(), $htmlOptions = array()) {
        return CHtml::image($this->thumbSrcOf($file, $params), null, $htmlOptions);
    }

}
