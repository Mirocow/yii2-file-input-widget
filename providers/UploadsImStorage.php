<?php

    namespace mirocow\fileinput\providers;

    use Yii;

    class UploadsImStorage
    {

        const EVENT_UPLOADED = 'uploaded';

        public $attributeName = 'file';

        public $savePathAlias = '@app/web/upload';

        public $scenarios = ['insert', 'update', 'create'];

        public $fileTypes = 'png,jpeg,jpg';

        public $subfolderVar = true;

        public $publicPath = 'upload';

        public $uploadUrl = 'http://uploads.im/api?upload';

        public $fileMode = 0644;

        public $folderChmode = 0755;

        public $multiple = false;

        public $entity_type = '';

        protected $path;

        protected $_path;

        protected $_subfolder = '';

        public function init()
        {

            if (!isset($this->_path))
            {
                $this->_path = Yii::getAlias($this->savePathAlias);
            }

            if (!is_dir($this->_path))
            {

                mkdir($this->_path, octdec($this->folderChmode), true);
                if (!is_dir($this->_path))
                {
                    throw new \HttpException(500, "{$this->path} does not exists.");
                }

                chmod($this->_path, octdec($this->folderChmode));

            } else if (!is_writable($this->_path))
            {

                chmod($this->_path, octdec($this->folderChmode));
                if (!is_writable($this->_path))
                {
                    throw new \HttpException(500, "{$this->_path} is not writable.");
                }

            }

        }

        public function getPath()
        {

            return $this->path;

        }

        public function getPublicPath()
        {
            return $this->getPath();
        }

        public function deleteFile()
        {

        }

        public function save($file)
        {

            $ext = pathinfo($file->name)['extension'];

            $newFileName = uniqid("") . "." . $ext;

            if ($this->subfolderVar)
            {

                $this->_subfolder = substr($newFileName, 0, 2);

            }

            $filePath = $this->getSavePath() . $newFileName;

            if ($file->saveAs($filePath))
            {

                if ($return = $this->localupload($filePath))
                {

                    if ($return->{'status_code'} == 200)
                    {

                        @unlink($filePath);

                        $file_name = basename($return->{'data'}->{'img_url'});

                        $this->path = str_replace($file_name , '', $return->{'data'}->{'img_url'});

                        $this->data = $return;

                        return $file_name;

                    }

                }

            }

            return false;

        }

        public function getSavePath()
        {
            $path = ($this->_subfolder != "") ? "{$this->_path}/{$this->_subfolder}/" : "{$this->_path}/";

            if (!file_exists($path))
            {
                mkdir($path, octdec($this->fileMode), true);
            }
            if (!is_readable($path) && !is_writable($path))
            {
                chmod($path, octdec($this->fileMode));
            }
            if (is_writable($path))
            {
                return $path;
            } else
            {
                return false;
            }

        }

        function localupload($file)
        {
            $execute = 'curl --form "upload=@'.$file.'" '.$this->uploadUrl.'?format=json';

            return json_decode(exec($execute));

        }

    }
