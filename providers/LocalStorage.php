<?php

namespace mirocow\fileinput\providers;

use Yii;

class LocalStorage {

		const EVENT_UPLOADED = 'uploaded';

		public $attributeName = 'file';

		public $savePathAlias = '@app/web/upload';

		public $scenarios = ['insert', 'update', 'create'];

		public $fileTypes = 'png,jpeg,jpg';

		public $subfolderVar = true;

		public $publicPath = 'upload';

		public $fileMode = 0644;

        public $folderChmode = 0755;

		public $multiple = false;

		public $entity_type = '';

		protected $path;

		protected $_subfolder = '';

		public function init( )
		{

				if( !isset($this->path) ) {
						$this->path = Yii::getAlias($this->savePathAlias);
				}

				if( !is_dir( $this->path ) ) {

						mkdir( $this->path, octdec($this->folderChmode), true );
						if( !is_dir( $this->path ) ) {
							throw new \HttpException(500, "{$this->path} does not exists.");
						}

						chmod ( $this->path , octdec($this->folderChmode) );

				} else if( !is_writable( $this->path ) ) {

						chmod( $this->path, octdec($this->folderChmode) );
						if(!is_writable($this->path)){
							throw new \HttpException(500, "{$this->path} is not writable.");
						}

				}

		}

		public function getPath()
		{

			return $this->path;

		}

		public function getSavePath()
		{
				$path = ($this->_subfolder != "") ? "{$this->path}/{$this->_subfolder}/" : "{$this->path}/";

				if(!file_exists($path)){
						mkdir($path, octdec($this->fileMode), true);
				}
				if(!is_readable($path) && !is_writable($path)){
						chmod($path, octdec($this->fileMode));
				}
				if(is_writable($path))
				{
						return $path;
				}
				 else {
						return false;
				}

		}

		public function getPublicPath()
		{
				return '/' . (($this->_subfolder != "") ? "{$this->publicPath}/{$this->_subfolder}/" : "{$this->publicPath}/");
		}

		public function deleteFile()
		{
				$filePath = $this->savePath . $this->owner->getAttribute($this->attributeName);
				if (@is_file($filePath))
						@unlink($filePath);
		}

		public function save($file){

				$ext = pathinfo($file->name)['extension'];

				$newFileName = uniqid("") . "." . $ext;

				if($this->subfolderVar){

					$this->_subfolder = substr($newFileName, 0, 2);

				}

				if($file->saveAs($this->getSavePath() . $newFileName))
				{

					return $newFileName;

				}

				return false;

		}

}
